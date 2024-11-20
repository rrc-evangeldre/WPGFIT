 // JavaScript to enforce a limit of 3 checked categories

const generalCheckbox = document.getElementById('general');
const otherCheckboxes = document.querySelectorAll('input[type="checkbox"]:not(#general)');
const allCheckboxes = document.querySelectorAll('input[type="checkbox"]');

let isGeneralUncheckedOnce = false;

function updateCategoryLimit() {
    const checkedCategories = document.querySelectorAll('input[type="checkbox"]:checked');
    const checkedCount = checkedCategories.length;

    // If more than 3 checkboxes are selected, uncheck the last one
    if (checkedCount > 3) {
        alert('You can only select up to 3 categories.');
        checkedCategories[checkedCategories.length - 1].checked = false;
    }
}

// Add event listeners for changes to any of the checkboxes
allCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        // Prevent selecting more than 3 categories
        updateCategoryLimit();

        // Uncheck "General" if another category is selected for the first time
        if (!isGeneralUncheckedOnce && this !== generalCheckbox && this.checked) {
            generalCheckbox.checked = false; // Uncheck the General checkbox
            isGeneralUncheckedOnce = true;  // Set the flag to prevent unchecking General again
        }
    });
});