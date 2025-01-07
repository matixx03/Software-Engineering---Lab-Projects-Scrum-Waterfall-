<?php
session_start();
if ((!isset($_SESSION["id"]))) {
    echo "no Access";
    echo "<script> location.href='login.php'; </script>";
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'library');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$borrower_email = $_SESSION["email"];
$id = "SELECT ID from borrower WHERE E_mail = '$borrower_email'";
$id_result = $conn->query($id);
$row = $id_result->fetch_assoc();
$borrower_id = $row['ID'];


$sql = "SELECT b.Title, b.ID as Book_ID, bb.Return_Date, bb.is_read, bb.Borrow_Date,
        (SELECT COUNT(*) FROM borrowed WHERE Borrower_ID = '$borrower_id' AND is_read = 0) as unread_count
        FROM book b, borrowed bb 
        WHERE b.ID = bb.Book_ID
        AND bb.Borrower_ID = '$borrower_id'
        ORDER BY bb.Return_Date DESC";
$result = $conn->query($sql);

$books = [];
$unread_count = 0;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = [
            'title' => $row['Title'],
            'returnDate' => $row['Return_Date'],
            'bookId' => $row['Book_ID'],
            'isRead' => $row['is_read'] == 0,
            'borrow_date' => $row['Borrow_Date']
        ];
        $unread_count = $row['unread_count'];
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="Manage_Catalog_css.css">
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="messages.css">
    <title>Library</title>
</head>
<body>
    <nav class="navigation">
        <div class="navlist">
            <ul>
                <li class="navelement"><a href="index_user.php" class="navlink">Home</a></li>
                <li class="navelement"><a href="return_user.php" class="navlink">Return Books</a></li>
                <li class="navelement"><a href="messages.php" class="navlink">Messages <span id="unread-badge" class="badge"></span></a></li>
                <li class="navelement logout"><a href="logout.php" class="navlink">Logout</a></li>
            </ul>
        </div>
    </nav>
    <div id="messages-container" class="messages-container"></div>

    <?php    
       echo '<footer><div class="footer">User: ' . $_SESSION["email"] . '<br><a href="logout.php">Logout</a></div></footer>';
    ?>

<script>
    const books = <?php echo json_encode($books); ?>;
    const unreadCount = <?php echo $unread_count; ?>;
    const now = new Date();
    const messagesContainer = document.getElementById('messages-container');
    const unreadBadge = document.getElementById('unread-badge');

    function updateUnreadBadge(count) {
        if (count > 0) {
            unreadBadge.style.display = 'block';
            unreadBadge.textContent = count;
        } else {
            unreadBadge.style.display = 'none';
        }
    }

    updateUnreadBadge(unreadCount);

    books.forEach(book => {
        const returnDate = new Date(book.returnDate);
        const timeDiff = returnDate - now;

        let category;
        if (timeDiff > 24 * 5 * 60 * 60 * 1000) {
            category = 'more_than_5_days';
        } else if (timeDiff > 24 * 5 * 60 * 60 * 1000) {
            category = 'in_5_days';
        } else if (timeDiff > 24 * 3 * 60 * 60 * 1000) {
            category = 'in_3_days';
        } else if (timeDiff > 24 * 60 * 60 * 1000) {
            category = 'in_1_day';
        } else if (timeDiff > 60 * 60 * 1000) {
            category = 'less_than_a_day';
        } else {
            category = 'already_due';
        }

        const formattedDate = returnDate.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        const message = document.createElement('div');
        message.className = 'message';
        if(book.isRead) {
            message.classList.add('unread');
        } else {
            message.classList.add('read');
        }
        message.setAttribute('data-book-id', book.bookId);

        switch (category) {
            case 'more_than_5_days':
                message.innerHTML = `<strong>${book.title}</strong> is due on <strong>${formattedDate}</strong>, which is in more than 5 days. <button class="view-more">View More</button><div class="dropdown-content">The book ${book.title} which you borrowed on ${book.borrow_date} is to be returned on the date that is shown above. If you want to increase the lending period click <a class="here" href="">here</a>.</div>`;
                break;
            case 'in_5_days':
                message.innerHTML = `<strong>${book.title}</strong> is due on <strong>${formattedDate}</strong>, which is in 5 days. <button class="view-more">View More</button><div class="dropdown-content">The book ${book.title} which you borrowed on ${book.borrow_date} is to be returned on the date that is shown above. If you want to increase the lending period click <a class="here" href="">here</a>.</div>`;
                break;
            case 'in_3_days':
                message.innerHTML = `<strong>${book.title}</strong> is due on <strong>${formattedDate}</strong>, which is in 3 or more days. <button class="view-more">View More</button><div class="dropdown-content">The book ${book.title} which you borrowed on ${book.borrow_date} is to be returned on the date that is shown above. If you want to increase the lending period click <a class="here" href="">here</a>.</div>`;
                break;
            case 'in_1_day':
                message.innerHTML = `<strong>${book.title}</strong> is due on <strong>${formattedDate}</strong>, which is in 1 or more day. <button class="view-more">View More</button><div class="dropdown-content">The book ${book.title} which you borrowed on ${book.borrow_date} is to be returned on the date that is shown above. If you want to increase the lending period click <a class="here" href="">here</a>.</div>`;
                break;
            case 'less_than_a_day':
                message.innerHTML = `<strong>${book.title}</strong> is due on <strong>${formattedDate}</strong>, which is in less than 24 hours. <button class="view-more">View More</button><div class="dropdown-content">The book ${book.title} which you borrowed on ${book.borrow_date} is to be returned on the date that is shown above. If you want to increase the lending period click <a class="here" href="">here</a>.</div>`;
                break;
            case 'already_due':
                message.innerHTML = `<strong>${book.title}</strong> was due on <strong>${formattedDate}</strong> and is already overdue. <button class="view-more">View More</button><div class="dropdown-content">The book ${book.title} which you borrowed on ${book.borrow_date} was to be returned on the date shown above. The date is overdue. If you want to borrow the book again you have to go to the main page and borrow it from there.</div>`;
                break;
            default:
                message.innerHTML = `<strong>${book.title}</strong> has an unknown return date.`;
        }

        messagesContainer.appendChild(message);

        const viewMoreButton = message.querySelector('.view-more');
        const dropdownContent = message.querySelector('.dropdown-content');

        viewMoreButton.addEventListener('click', async () => {
            dropdownContent.classList.toggle('show');
            const bookId = message.getAttribute('data-book-id');
            
            try {
                const response = await fetch('update_read_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `book_id=${bookId}`
                });
                
                const data = await response.json();
                
                if (data.success) {
                    dropdownContent.classList.toggle('show');
                    message.classList.add('read');
                    updateUnreadBadge(unreadCount - 1);
                } else {
                    console.error('Failed to update read status:', data.message);
                }
            } catch (error) {
                console.error('Error updating read status:', error);
            }
        });
    });
</script>
</body>
</html>