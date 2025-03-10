function showLogoutPopup(event) {
    event.preventDefault();
    document.getElementById('logout-popup').style.display = 'flex';
    return false;
}

function confirmLogout() {
    document.getElementById('logout-form').submit();
}

function closePopup() {
    document.getElementById('logout-popup').style.display = 'none';
}

function closePopupOnClickOutside(event) {
    if (event.target === document.getElementById('logout-popup')) {
        closePopup();
    }
}