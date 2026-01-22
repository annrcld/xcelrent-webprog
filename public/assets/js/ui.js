// public/assets/js/ui.js

/* --- SCROLLING --- */
function scrollToSection(sectionId) {
    const element = document.getElementById(sectionId);
    if (element) {
        const navHeight = document.querySelector('nav').offsetHeight;
        const elementPosition = element.getBoundingClientRect().top + window.pageYOffset;
        window.scrollTo({
            top: elementPosition - navHeight,
            behavior: 'smooth'
        });
    }
}

/* --- MODAL FUNCTIONS --- */
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    // Special handling for operator modal - check login status
    if (modalId === 'operatorModal') {
        // Check if user is logged in by seeing if user menu is displayed
        const userMenu = document.getElementById('userMenu');
        const isLoggedIn = userMenu && (userMenu.style.display === 'flex' || userMenu.style.display === 'block');

        if (!isLoggedIn) {
            // User is not logged in, show login message
            document.getElementById('loginCheckMessage').style.display = 'block';
            document.getElementById('operatorRequirements').style.display = 'none';
        } else {
            // User is logged in, show operator requirements
            document.getElementById('loginCheckMessage').style.display = 'none';
            document.getElementById('operatorRequirements').style.display = 'block';
        }
    }

    // Use Flex for modals to trigger the centering CSS
    modal.style.display = (modalId === 'operatorModal' || modalId === 'dateModal' || modalId === 'signupModal' || modalId === 'loginModal' || modalId === 'otpModal') ? 'flex' : 'block';
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = "none";
    }
}

/* --- CUSTOM SELECT LOGIC --- */
function setupCustomSelect(selectContainer) {
    const trigger = selectContainer.querySelector('.select-trigger');
    const options = selectContainer.querySelectorAll('.option');
    const hiddenInput = selectContainer.querySelector('input[type="hidden"]');
    const triggerText = trigger.querySelector('span');

    trigger.addEventListener('click', (e) => {
        document.querySelectorAll('.custom-select').forEach(s => {
            if (s !== selectContainer) s.classList.remove('active');
        });
        selectContainer.classList.toggle('active');
        e.stopPropagation();
    });

    options.forEach(option => {
        option.addEventListener('click', () => {
            triggerText.textContent = option.textContent;
            hiddenInput.value = option.dataset.value;
            options.forEach(opt => opt.classList.remove('selected'));
            option.classList.add('selected');
            selectContainer.classList.remove('active');
        });
    });
}

// Toggle mobile menu
function toggleMobileMenu() {
    const mobileMenu = document.getElementById('mobileMenu');
    mobileMenu.classList.toggle('active');
}

// Global click to close dropdowns and modals
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = "none";
    }
    if (!event.target.closest('.custom-select')) {
        document.querySelectorAll('.custom-select').forEach(s => s.classList.remove('active'));
    }

    // Close mobile menu if clicking outside of it
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    if (mobileMenu && mobileMenu.classList.contains('active') &&
        !event.target.closest('#mobileMenu') && !event.target.closest('.mobile-menu-btn')) {
        mobileMenu.classList.remove('active');
    }
}

// Add event listener for mobile menu button
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', toggleMobileMenu);
    }
});

/* --- DATE MODAL LOGIC --- */
function openDateModal(type) {
    // 1. Set the global variable so confirmDate() knows which field to update
    currentPicking = type;

    // 2. Update the modal title for better UX
    const title = document.getElementById('dateModalTitle');
    title.innerText = (type === 'pickup') ? 'Select Pickup Date' : 'Select Return Date';

    // 3. Show the modal
    openModal('dateModal');

    // 4. Reset flatpickr to today's date so the user starts fresh
    if (fp) {
        fp.setDate(new Date());
    }
}

/* --- DATE CONFIRMATION --- */
function confirmDate() {
    // Now 'fp' is accessible because we defined it globally
    const selectedDate = fp.selectedDates[0];

    if (selectedDate) {
        const formatted = fp.formatDate(selectedDate, "M j, h:i K");

        // Update the display text in the search bar
        const display = document.getElementById(currentPicking + 'Display');
        display.innerText = formatted;
        display.classList.add('selected-date');

        // Update hidden input for form submission
        const hiddenInput = document.getElementById(currentPicking + 'DateValue');
        if (hiddenInput) hiddenInput.value = selectedDate.toISOString();

        closeModal('dateModal');
    } else {
        alert("Please select a date and time first.");
    }
}

/* --- NAVIGATION ROUTING --- */
function showPage(pageId) {
    // 1. Hide all main sections
    const pages = document.querySelectorAll('.page-section');
    pages.forEach(page => page.style.display = 'none');

    // 2. Show the target page
    const targetPage = document.getElementById(pageId);
    if (targetPage) {
        targetPage.style.display = 'block';
    }

    // 3. Special handling: If going home, ensure search results are hidden until searched again
    if (pageId === 'home-page') {
        document.getElementById('resultsContainer').style.display = 'none';
    }

    // 4. Always scroll to top on page change
    window.scrollTo({ top: 0, behavior: 'smooth' });

    // 5. Close dropdown if it was open
    const menu = document.getElementById('dropdownMenu');
    if (menu) menu.style.display = 'none';
}