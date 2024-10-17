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

// Recupera la tab attiva dal localStorage al caricamento della pagina
document.addEventListener('DOMContentLoaded', function() {
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
