/*******w******** 

    Name: Raphael Evangelista
    Date: December 9, 2024
    Description: These functions contain these functionalities:
                - The `.switcher` elements are buttons or links used to toggle between forms
                - Validates the password and confirm password fields during signup.

****************/
// Switcher for toggling between login and signup forms
const switchers = [...document.querySelectorAll('.switcher')];

switchers.forEach(item => {
    item.addEventListener('click', function() {
        switchers.forEach(item => item.parentElement.classList.remove('is-active'));
        this.parentElement.classList.add('is-active');
    });
});

// Checks if the user password matches the confirm password field
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