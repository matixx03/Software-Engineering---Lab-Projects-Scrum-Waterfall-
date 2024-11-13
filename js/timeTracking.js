import TimeManagementAPI from "./api.js";

// TimeTracker Klasse
class TimeTracker {
    constructor() {
        this.currentEntry = {       // speichert den aktuellen Zeiterfassungs-Status
            startTime: null,
            breakStart: null,
            breakDuration: 0,
            isWorking: false,
            isOnBreak: false,
            timerInterval: null
        };
        this.initialize();
    }

    // formatiert Sekunden in HH:MM:SS
    formatElapsedTime(totalSeconds) {
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }

    updateTimer() {
        const statusElement = document.getElementById('timeStatus');    // holt das Status Element
        if (!statusElement.querySelector('.timer')) {
            const timerSpan = document.createElement('span');
            timerSpan.className = 'timer';
            statusElement.appendChild(timerSpan);
        }
        
        const timerElement = statusElement.querySelector('.timer');     // holt das Timer Element
        const currentTime = new Date();     // aktuelle Zeit
        let elapsedSeconds = Math.floor((currentTime - this.currentEntry.startTime) / 1000);
        elapsedSeconds -= Math.floor(this.currentEntry.breakDuration * 60);
        timerElement.textContent = this.formatElapsedTime(elapsedSeconds);      // aktualisiert Anzeige
    }

    initialize() {
        // sucht das Element mit der ID breakbuttons und macht es unsichtbar
        document.getElementById('breakButtons').style.display = 'none';
    }

    // formatiert ein Datum in die Uhrzeit (HH:MM)
    formatTime(date) {
        return date.toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' });
    }

    // startet die Arbeitszeiterfassung
    async startWork() {
        if (!this.currentEntry.isWorking) {
            this.currentEntry.startTime = new Date();
            this.currentEntry.isWorking = true;
            this.updateTimeStatus('Working since: ' + this.formatTime(this.currentEntry.startTime));
            const startButton = document.getElementById('startButton');
            startButton.textContent = 'Stop Working';
            startButton.classList.add('active');
            document.getElementById('breakButtons').style.display = 'block';

            // startet den Timer
            // setInterval ruft eine Funktion wiederholt auf (alle 1000ms)
            this.currentEntry.timerInterval = setInterval(function() {
                this.updateTimer();
            }.bind(this), 1000);        // wid mit bind(this) an TimeTracker-Klasse gebunden
        } else {
            await this.endWork();       // await wartet auf das Ergebnis einer asynchronen Operation
        }
    }

    startBreak(duration) {
        if (!this.currentEntry.isOnBreak && this.currentEntry.isWorking) {
            this.currentEntry.breakStart = new Date();
            this.currentEntry.isOnBreak = true;
            this.updateTimeStatus('Break since: ' + this.formatTime(this.currentEntry.breakStart), true);
            
            // setTimeout führt Funktion nach der übergebenen Verzögerung aus
            // bei 30 Minuten Pause z.B nach 30*60*1000 = 1.800.000 ms also 30 Minuten
            setTimeout(function() {
                if (this.currentEntry.isOnBreak) {
                    this.endBreak();
                }
            }.bind(this), duration * 60 * 1000);
        }
    }

    endBreak() {
        if (this.currentEntry.isOnBreak) {
            const breakEnd = new Date();
            const breakDuration = (breakEnd - this.currentEntry.breakStart) / (1000 * 60);
            this.currentEntry.breakDuration += breakDuration;   // addiert Pausendauer zu Gesamtpausendauer
            this.currentEntry.isOnBreak = false;
            this.updateTimeStatus('Working since: ' + this.formatTime(this.currentEntry.startTime));
        }
    }

    async endWork() {
        if (this.currentEntry.isWorking) {
            // stoppt den Timer
            if (this.currentEntry.timerInterval) {
                clearInterval(this.currentEntry.timerInterval);
                this.currentEntry.timerInterval = null;
            }

            const endTime = new Date();
            if (this.currentEntry.isOnBreak) {
                this.endBreak();
            }
            
            const totalMinutes = (endTime - this.currentEntry.startTime) / (1000 * 60);
            const workedTime = totalMinutes - this.currentEntry.breakDuration;
            
            // erstellt neuen Zeiteintrag
            const timeEntry = {
                date: this.currentEntry.startTime.toISOString().split('T')[0],      // Datum in YYYY-MM-DD Format
                weekday: this.currentEntry.startTime.toLocaleDateString('de-DE', { weekday: 'long' }),
                timeStarted: this.formatTime(this.currentEntry.startTime),
                timeEnded: this.formatTime(endTime),
                timeBreak: `${Math.floor(this.currentEntry.breakDuration / 60)}:${(this.currentEntry.breakDuration % 60).toFixed(0).padStart(2, '0')}`,
                workedTime: `${Math.floor(workedTime / 60)}:${(workedTime % 60).toFixed(0).padStart(2, '0')}`,
                comment: ''
            };

            await TimeManagementAPI.saveTimeEntry(timeEntry);   // speichert den Eintrag über die API
            addTimeEntryToTable(timeEntry);     // fügt den Eintrag in die Tabelle ein

            // setzt alle Werte zurück
            this.currentEntry = {
                startTime: null,
                breakStart: null,
                breakDuration: 0,
                isWorking: false,
                isOnBreak: false,
                timerInterval: null
            };

            // aktualisiert UI
            this.updateTimeStatus('Not clocked in');
            const startButton = document.getElementById('startButton');
            startButton.textContent = 'Start working';
            startButton.classList.remove('active');
            document.getElementById('breakButtons').style.display = 'none';
        }
    }

    updateTimeStatus(message, isBreak = false) {
        const statusElement = document.getElementById('timeStatus');
        const timerElement = statusElement.querySelector('.timer');
        statusElement.textContent = message;
        statusElement.className = 'time-status ' + (isBreak ? 'time-break' : 'time-active');
        if (timerElement && this.currentEntry.isWorking) {
            statusElement.appendChild(timerElement);
        }
    }
}

// Funktionen für manuelle Zeiteinträge
function initializeManualEntryForm() {
    // holt Elemente von der Website
    const manualEntryButton = document.getElementById('manualEntryButton');
    const manualEntryForm = document.getElementById('manualEntryForm');
    const saveButton = document.getElementById('saveManualEntry');
    const cancelButton = document.getElementById('cancelManualEntry');
    
    // aktuelles Datum als Standardwert
    document.getElementById('entryDate').valueAsDate = new Date();

    // wenn man den "Zeit nachtragen" Button drückt, wird das Formular sichtbar
    manualEntryButton.addEventListener('click', function() {
        manualEntryForm.classList.remove('hidden');
    });

    // Abbrechen Button
    cancelButton.addEventListener('click', function() {
        manualEntryForm.classList.add('hidden');
        resetForm();
    });

    // Speichern Button
    saveButton.addEventListener('click', saveManualEntry);
}

// setzt Formular auf Sandardwerte zurück
function resetForm() {
    document.getElementById('entryDate').valueAsDate = new Date();
    document.getElementById('startTime').value = '';
    document.getElementById('endTime').value = '';
    document.getElementById('breakTime').value = '30';
    document.getElementById('comment').value = '';
}

async function saveManualEntry() {
    // holt Werte aus den Formularfeldern
    const date = document.getElementById('entryDate').value;
    const startTime = document.getElementById('startTime').value;
    const endTime = document.getElementById('endTime').value;
    const breakTime = document.getElementById('breakTime').value;
    const comment = document.getElementById('comment').value;

    // prüft ob Pflichtfelder ausgefüllt sind
    if (!date || !startTime || !endTime) {
        alert('Bitte füllen Sie alle erforderlichen Felder aus.');
        return;
    }

    const workedTime = calcWorkedTime(startTime, endTime, parseInt(breakTime));

    // erstellt neuen Zeiteintrag
    const entry = {
        date: date,
        weekday: new Date(date).toLocaleDateString('de-DE', { weekday: 'long' }),
        timeStarted: startTime,
        timeEnded: endTime,
        timeBreak: `${Math.floor(breakTime/60)}:${(breakTime%60).toString().padStart(2, '0')}`,
        workedTime: workedTime,
        comment: comment
    };

    await TimeManagementAPI.saveTimeEntry(entry);       // speichert den Eintrag
    addTimeEntryToTable(entry);     // fügt den Eintrag in der Tabelle ein
    document.getElementById('manualEntryForm').classList.add('hidden');     // versteckt das Formular
    resetForm();
}

// fügt Zeiteintrag in die Tabelle ein
function addTimeEntryToTable(entry) {
    // holt die Tabelle 
    const table = document.querySelector('.table-container table tbody');
    
    const row = table.insertRow(0);

    row.className = 'entry-current';    // CSS Klasse für Styling

    const cells = ['date', 'weekday', 'timeStarted', 'timeBreak', 'timeEnded', 'workedTime', 'comment'];
    cells.forEach(function(key) {       // fügt für jede Eigenschaft eine neue Zelle ein
        const cell = row.insertCell();
        cell.textContent = entry[key] || '';    // wenn kein Wert vorhanden ist -> leerer String
    });
}

async function loadExistingEntries() {
    // holt Einträge von der API
    const entries = await TimeManagementAPI.getTimeEntries();
      
    // holt Tabelle
    const table = document.querySelector('.table-container table tbody');
    table.innerHTML = '';   // leert die Tabelle
    
    // fügt jeden Eintrag in die Tabelle ein
    entries.forEach(function(entry) {
        addTimeEntryToTable(entry);
    });
}

function calcWorkedTime(startTime, endTime, breakMinutes) {
    // zerlegt die Zeitstrings in Stunden und Minuten
    // .map(Number) wandelt die Strings in Zahlen um
    const [startHours, startMinutes] = startTime.split(':').map(Number);
    const [endHours, endMinutes] = endTime.split(':').map(Number);
    
    // Gesamtminuten
    let totalMinutes = (endHours * 60 + endMinutes) - (startHours * 60 + startMinutes);
    totalMinutes -= breakMinutes;
    
    // Umrechnung von Minuten zurück in Stunden
    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;
    
    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
}

// Initialisierung
const timeTracker = new TimeTracker();

document.addEventListener('DOMContentLoaded', function() {      // lädt die Seite
    // Event Listener für Start-Button
    document.getElementById('startButton').addEventListener('click', function() {
        timeTracker.startWork();
    });

    // Event Listener für Pause-Buttons
    document.querySelectorAll('.break-button').forEach(function(button) {
        const duration = parseInt(button.textContent);
        if (!isNaN(duration)) {
            button.addEventListener('click', function() {
                timeTracker.startBreak(duration);
            });
        }
    });

    // Initialisierung des manuellen Eingabeformulars
    initializeManualEntryForm();
    loadExistingEntries();
});