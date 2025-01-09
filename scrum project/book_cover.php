<?php
class BookCoverManager {
    private $conn;
    private $cacheTimeout = 2592000; // 30 Tage in Sekunden

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function getCover($bookId, $title, $author) {
        // Prüfe zuerst den Cache in der Datenbank
        $stmt = $this->conn->prepare("SELECT cover_url, UNIX_TIMESTAMP(last_cover_update) as update_time 
                                    FROM book WHERE ID = ?");
        $stmt->bind_param("i", $bookId);
        $stmt->execute();
        $result = $stmt->get_result();
        $book = $result->fetch_assoc();

        // Wenn URL existiert und nicht zu alt ist, verwende sie
        if ($book['cover_url'] && 
            $book['update_time'] && 
            (time() - $book['update_time'] < $this->cacheTimeout)) {
            return $book['cover_url'];
        }

        // Ansonsten hole neues Cover von der API
        $coverUrl = $this->fetchFromAPI($title, $author);
        
        // Speichere das neue Cover in der Datenbank
        $stmt = $this->conn->prepare("UPDATE book SET cover_url = ?, last_cover_update = CURRENT_TIMESTAMP 
                                    WHERE ID = ?");
        $stmt->bind_param("si", $coverUrl, $bookId);
        $stmt->execute();

        return $coverUrl;
    }

    public function fetchFromAPI($title, $author) {
        // prüft API-Ratenlimit
        $this->checkRateLimit();

        // Suchanfrage mit Titel und Autor
        $query = urlencode("\"$title\" \"$author\"");
        // Sucht bei Google Books nach den ersten 5 passenden Büchern
        $url = "https://www.googleapis.com/books/v1/volumes?q=" . $query . "&orderBy=relevance&maxResults=5";

        try {
            $response = file_get_contents($url);
            if ($response === false) {
                return $this->getDefaultCover();
            }
    
            $data = json_decode($response, true);
            
            // Durchsuche die ersten 5 Ergebnisse nach dem besten Match
            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $bookInfo = $item['volumeInfo'];
                    // Prüfe auf exakte Übereinstimmung von Titel und Autor
                    if (isset($bookInfo['imageLinks']['thumbnail']) &&
                        stripos($bookInfo['title'], $title) !== false &&
                        isset($bookInfo['authors']) &&
                        stripos(implode(', ', $bookInfo['authors']), $author) !== false) {
                        // Hole das größere Cover-Bild wenn verfügbar
                        if (isset($bookInfo['imageLinks']['large'])) {
                            return $bookInfo['imageLinks']['large'];
                        } elseif (isset($bookInfo['imageLinks']['medium'])) {
                            return $bookInfo['imageLinks']['medium'];
                        } else {
                            return $bookInfo['imageLinks']['thumbnail'];
                        }
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Error fetching book cover: " . $e->getMessage());
        }

        return $this->getDefaultCover();
    }

    private function checkRateLimit() {
        // Speichert API-Anfragen in einer temporären Datei
        $cacheFile = sys_get_temp_dir() . '/api_requests.txt';
        $currentTime = time();
        $requests = [];

        if (file_exists($cacheFile)) {
            $requests = unserialize(file_get_contents($cacheFile));
            // Entferne alte Requests die älter als 1 Stunde sind
            $requests = array_filter($requests, function($time) use ($currentTime) {
                return $currentTime - $time < 3600; // 1 Stunde
            });
        }

        if (count($requests) >= 1000) { // Limit von 1000 Requests pro Stunde
            throw new Exception("API rate limit reached");
        }

        $requests[] = $currentTime;
        file_put_contents($cacheFile, serialize($requests));
    }

    private function getDefaultCover() {
        return "https://via.placeholder.com/128x190";
    }
}