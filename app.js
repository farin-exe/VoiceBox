document.addEventListener('DOMContentLoaded', function () {
    // Mobile navigation
    const menuToggle = document.getElementById('menuToggle');
    const mainNav = document.getElementById('mainNav');

    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function () {
            mainNav.classList.toggle('open');
        });
    }

    // Login/Register tabs
    const tabs = document.querySelectorAll('.tab');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));

            tab.classList.add('active');
            const target = document.getElementById(tab.dataset.tab);
            if (target) target.classList.add('active');
        });
    });

    // Password show/hide controls
    document.querySelectorAll('.password-toggle').forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            const field = toggle.closest('.password-field');
            const input = field ? field.querySelector('input') : null;
            if (!input) return;

            const showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';
            toggle.classList.toggle('is-visible', !showing);
            toggle.setAttribute('aria-pressed', String(!showing));
            toggle.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
        });
    });

    // Student registration confirm-password validation
    const registerForm = document.getElementById('registerPanel');
    if (registerForm) {
        registerForm.addEventListener('submit', function (event) {
            const password = registerForm.querySelector('[name="password"]');
            const confirmPassword = registerForm.querySelector('[name="confirm_password"]');

            if (password && confirmPassword && password.value !== confirmPassword.value) {
                event.preventDefault();
                confirmPassword.setCustomValidity('Passwords do not match.');
                confirmPassword.reportValidity();
                confirmPassword.focus();
            } else if (confirmPassword) {
                confirmPassword.setCustomValidity('');
            }
        });

        const confirmPassword = registerForm.querySelector('[name="confirm_password"]');
        const password = registerForm.querySelector('[name="password"]');

        if (confirmPassword && password) {
            confirmPassword.addEventListener('input', function () {
                confirmPassword.setCustomValidity(
                    confirmPassword.value && confirmPassword.value !== password.value
                        ? 'Passwords do not match.'
                        : ''
                );
            });
            password.addEventListener('input', function () {
                if (confirmPassword.value) {
                    confirmPassword.setCustomValidity(
                        confirmPassword.value !== password.value
                            ? 'Passwords do not match.'
                            : ''
                    );
                }
            });
        }
    }

    // Confirmation dialogs for delete/withdraw actions
    document.querySelectorAll('[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    // Complaint character counter
    const description = document.getElementById('description');
    const descriptionCount = document.getElementById('descriptionCount');

    if (description && descriptionCount) {
        const updateCount = () => {
            descriptionCount.textContent = description.value.length;
        };
        description.addEventListener('input', updateCount);
        updateCount();
    }

    // Client-side complaint validation
    const complaintForm = document.getElementById('complaintForm');

    if (complaintForm) {
        complaintForm.addEventListener('submit', function (event) {
            const category = complaintForm.querySelector('[name="category_id"]').value;
            const subject = complaintForm.querySelector('[name="subject"]').value.trim();
            const text = complaintForm.querySelector('[name="description"]').value.trim();

            if (!category || !subject || !text) {
                event.preventDefault();
                alert('Please complete all complaint fields.');
                return;
            }

            if (text.length < 15) {
                event.preventDefault();
                alert('Please provide at least 15 characters in the description.');
            }
        });
    }

    // Generic table search
    function setupSearch(inputId, tableId) {
        const input = document.getElementById(inputId);
        const table = document.getElementById(tableId);

        if (!input || !table) return;

        input.addEventListener('input', function () {
            const query = input.value.toLowerCase();

            table.querySelectorAll('tbody tr').forEach(function (row) {
                row.style.display = row.textContent.toLowerCase().includes(query)
                    ? ''
                    : 'none';
            });
        });
    }

    setupSearch('complaintSearch', 'complaintsTable');
    setupSearch('adminSearch', 'adminTable');
});