import './bootstrap';

window.showLogoutPopup = function (event) {
    event.preventDefault();
    document.getElementById('logout-popup').style.display = 'flex';
    return false;
}

window.confirmLogout = function () {
    document.getElementById('logout-form').submit();
}

window.closePopup = function () {
    document.getElementById('logout-popup').style.display = 'none';
}

window.closePopupOnClickOutside = function (event) {
    if (event.target === document.getElementById('logout-popup')) {
        closePopup();
    }
}

window.closeErrorPopup = function () {
    const popup = document.getElementById('error-popup');
    popup.style.animation = 'fadeOut 0.3s ease-out forwards';
}

window.closeSuccessPopup = function () {
    const popup = document.getElementById('success-popup');
    popup.style.animation = 'fadeOut 0.3s ease-out forwards';
}

window.closeStatusPopup = function () {
    const popup = document.getElementById('status-popup');
    popup.style.animation = 'fadeOut 0.3s ease-out forwards';
}

window.showPasswordField = function () {
    let div = document.getElementById('password_fields');
    let img = document.getElementById('password_fields_img');
    if (div.style.display === 'none' || div.style.display === '') {
        div.style.display = 'flex';
        img.style.transform = 'rotate(90deg)';
    } else {
        div.style.display = 'none';
        img.style.transform = 'rotate(0deg)';
    }
}

function initializeFileUpload() {
    const fileUploadElement = document.getElementById("file-upload");
    const fileNameElement = document.getElementById("file-name");

    if (fileUploadElement && fileNameElement) {
        fileUploadElement.addEventListener("change", function () {
            let fileName = this.files.length > 0 ? this.files[0].name : "Aucun fichier choisi";
            fileNameElement.textContent = fileName;
        });
    }
}

setTimeout(() => {
    const popup = document.getElementById('error-popup');
    if (popup) {
        popup.style.animation = 'fadeOut 0.3s ease-out forwards';
    }
    const successPopup = document.getElementById('success-popup');
    if (successPopup) {
        successPopup.style.animation = 'fadeOut 0.3s ease-out forwards';
    }
    const statusPopup = document.getElementById('status-popup');
    if (statusPopup) {
        statusPopup.style.animation = 'fadeOut 0.3s ease-out forwards';
    }
}, 5000);

document.addEventListener("DOMContentLoaded", function () {
    initializeFileUpload();
});

window.toggleLanguageMenu = function () {
    document.getElementById('language-selector').classList.toggle('open');
}