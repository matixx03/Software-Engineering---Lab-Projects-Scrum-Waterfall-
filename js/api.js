// TimeManagementAPI für Datenbankanbindung
const TimeManagementAPI = {
    // Speichert neuen Zeiteintrag
    async saveTimeEntry(entry) {
        // fetch sendet HTTP-Anfrage
        const response = await fetch('save_time.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',     // wir senden JSON-Daten
            },
            body: JSON.stringify(entry)
        });
        
        const data = await response.json();     // wandelt Javascript-Objekt in JSON-String um
        return true;
    },

    async getTimeEntries() {
        const response = await fetch('save_time.php');
        return await response.json();       // wandelt den JSON-String in Javascript um
    }
};

export default TimeManagementAPI;