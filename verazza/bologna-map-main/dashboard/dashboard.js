
// Funzione per mostrare la tab selezionata
function showTab(tabIndex) {
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach((tab, index) => {
        if (index === tabIndex) {
            tab.classList.add('active');
        } else {
            tab.classList.remove('active');
        }
    });

    const tabButtons = document.querySelectorAll('.tab');
    tabButtons.forEach((tab, index) => {
        if (index === tabIndex) {
            tab.classList.add('active');
        } else {
            tab.classList.remove('active');
        }
    });

    // Salva l'indice della tab attiva nel localStorage
    localStorage.setItem('activeTab', tabIndex);
}

function showActiveTab(){
    const activeTab = localStorage.getItem('activeTab');
    if (activeTab !== null) {
        showTab(parseInt(activeTab));
    } else {
        showTab(0); // Se nessuna tab è salvata, mostra la prima per default
    }

    // Rendi i messaggi di errore o successo invisibili dopo 5 secondi
    const message = document.querySelector('.success-message, .error-message');
    if (message) {
        setTimeout(() => {
            message.style.display = 'none';
        }, 5000); // 5000 millisecondi = 5 secondi
    }
}

document.addEventListener("DOMContentLoaded", function() {
        progressInterval = '';
        showActiveTab();

        document.getElementById('stop-import-btn').addEventListener('click', function() {
            if (confirm('Sei sicuro di voler interrompere il processo di importazione?')) {
                fetch('stop_import.php', {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message); // Notify the user that the import has been stopped

                    // Optionally hide the spinner and stop polling for progress
                    document.getElementById('loading-spinner').style.display = 'none';
                    clearInterval(progressInterval); // Stop the progress update interval
                })
                .catch(error => {
                    console.error('Errore durante l\'interruzione dell\'importazione:', error);
                });
            }
        });

        function uploadFile() {
            // Show spinner
            document.getElementById('loading-spinner').style.display = 'flex';

            // Prepare form data
            const formData = new FormData(document.getElementById('upload-form'));
            progressInterval = setInterval(updateProgress, 1000);
            fetch('../import_table.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text()) // Change to text to log it first
            .then(data => {
                console.log(data); // Log the raw response
                const jsonData = JSON.parse(data); // Then parse the JSON
                document.getElementById('successful-inserts').innerText = `Righe inserite: ${jsonData.successful_inserts}`;
                document.getElementById('skipped-rows').innerText = `Righe saltate: ${jsonData.skipped_rows}`;
                document.getElementById('total-rows').innerText = `Righe totali: ${jsonData.total_rows}`;
                document.getElementById('summary').style.display = 'block'; // Mostra il riepilogo
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('loading-spinner').style.display = 'none'; // Nascondi lo spinner in caso di errore
            });

        }

        // Function to fetch and update progress
        function updateProgress() {
            fetch('progress.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                } else {
                    console.log(`Caricamento dati... ${data.percentage}-${data.processed}-${data.total}`)
                    const progressText = document.getElementById('progress-text');
                    const percentage = data.percentage;
                    progressText.innerText = `Caricamento dati... ${percentage}%`;

                    // Optionally hide the spinner when done
                    if (percentage >= 100) {
                        document.getElementById('loading-spinner').style.display = 'none';
                        clearInterval(progressInterval); // Stop polling once complete
                    }
                }
            })
            .catch(error => console.error('Error fetching progress:', error));
        }



        window.deleteAllTables = function() {
            if (confirm("Sei sicuro di voler eliminare tutte le tabelle? Questa azione è irreversibile.")) {
                fetch('delete_all_tables.php', {
                method: 'POST'
            })
            .then(response => response.text()) // Use text() to log the raw response
            .then(data => {
                console.log(data); // Log the raw HTML response to see what the error is
                // You can handle different types of responses here, such as HTML error pages
                try {
                    const jsonData = JSON.parse(data); // Try parsing JSON if applicable
                    if (jsonData.error) {
                        alert("Errore: " + jsonData.error);
                    } else {
                        alert(jsonData.message);
                        document.getElementById('deleted-rows').innerText = `Tabelle eliminate: ${jsonData.deleted_tables}`;
                        document.getElementById('summary').style.display = 'block'; // Show summary after deletion
                    }
                } catch (err) {
                    // Handle the case where the response is not valid JSON
                    console.error('Error parsing JSON:', err);
                    alert('Errore: la risposta del server non è in formato JSON.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Errore durante la richiesta di eliminazione di tutte le tabelle.");
            });

            }
        };

        // Attach to form submission
        document.getElementById("upload-form").onsubmit = function(event) {
            event.preventDefault();
            uploadFile();
        };
    });


// Funzione di validazione della password lato client
function validatePasswordForm() {
    const newPassword = document.getElementById("new_password").value;
    const confirmNewPassword = document.getElementById("confirm_new_password").value;
    const errorDiv = document.getElementById("password-error");

    // Verifica sicurezza della password lato client
    const regexLower = /[a-z]/;
    const regexUpper = /[A-Z]/;
    const regexNumber = /[0-9]/;
    const regexSpecial = /[\W]/;

    if (newPassword.length < 8 || !regexLower.test(newPassword) || !regexUpper.test(newPassword) || !regexNumber.test(newPassword) || !regexSpecial.test(newPassword)) {
        errorDiv.innerHTML = "La password deve contenere almeno 8 caratteri, includere una lettera maiuscola, una minuscola, un numero e un carattere speciale.";
        errorDiv.style.display = "block";
        return false;
    }

    if (newPassword !== confirmNewPassword) {
        errorDiv.innerHTML = "Le nuove password non corrispondono.";
        errorDiv.style.display = "block";
        return false;
    }

    errorDiv.style.display = "none";
    return true;
}
