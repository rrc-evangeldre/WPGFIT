function fetchUserRoles() {
    const selectUser = document.getElementById('select-user');
    const selectedOption = selectUser.options[selectUser.selectedIndex];
    const rolesString = selectedOption.getAttribute('data-roles') || '';
    const roles = rolesString.split(', ').map(role => role.trim());

    // Reset all checkboxes
    document.querySelectorAll('.checkbox-group input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
        checkbox.disabled = false;
    });

    // "Member" is always checked and disabled
    const memberCheckbox = document.getElementById('role-member');
    if (memberCheckbox) {
        memberCheckbox.checked = true;
        memberCheckbox.disabled = true;
    }

    // Check the roles that apply
    roles.forEach(role => {
        const checkbox = document.getElementById(`role-${role.toLowerCase()}`);
        if (checkbox) {
            checkbox.checked = true;
        }
    });
}

function handleUserSelection() {
    const selectUser = document.getElementById('select-user');
    const selectedOption = selectUser.options[selectUser.selectedIndex];
    const rolesString = selectedOption.getAttribute('data-roles') || '';
    const isAdmin = selectedOption.getAttribute('data-is-admin') === 'true';

    const roles = rolesString.split(', ').map(role => role.trim());
    const checkboxes = document.querySelectorAll('.checkbox-group input[type="checkbox"]');
    const editButton = document.querySelector('.btn-edit-user');
    const deleteButton = document.querySelector('.btn-delete-user');

    // Reset all checkboxes and enable by default
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
        checkbox.disabled = false;
    });

    // "Member" is always checked and disabled
    const memberCheckbox = document.getElementById('role-member');
    if (memberCheckbox) {
        memberCheckbox.checked = true;
        memberCheckbox.disabled = true;
    }

    // Checks all checkbox roles that the selected user has
    roles.forEach(role => {
        const checkbox = document.getElementById(`role-${role.toLowerCase()}`);
        if (checkbox) {
            checkbox.checked = true;
        }
    });

    // Admins can't edit or delete other Admins' roles or account
    if (isAdmin) {
        checkboxes.forEach(checkbox => {
            checkbox.disabled = true;
        });
        editButton.disabled = true;
        deleteButton.disabled = true;
    } else {
        editButton.disabled = false;
        deleteButton.disabled = false;
    }
}
