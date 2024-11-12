// Switcher for toggling between login and signup forms
const switchers = [...document.querySelectorAll('.switcher')];

switchers.forEach(item => {
    item.addEventListener('click', function() {
        switchers.forEach(item => item.parentElement.classList.remove('is-active'));
        this.parentElement.classList.add('is-active');
    });
});

function validatePasswords(event) {
    var password = document.getElementById("signup-password").value;
    var confirmPassword = document.getElementById("signup-password-confirm").value;

    if (password !== confirmPassword) {
        alert("Passwords do not match. Please try again.");
        event.preventDefault();
        return false;
    }
    return true;
}