/* --- DATA & CONFIG --- */
const carDatabase = [
    { 
        id: 1, 
        name: "Toyota Vios 2026", 
        type: "Automatic", 
        seats: 5, 
        fuel: "Gasoline",
        price: 1500, 
        isPopular: true,
        img: "https://imgcdn.zigwheels.ph/large/gallery/exterior/30/3013/toyota-vios-2022-front-side-view-695271.jpg" 
    },
    { 
        id: 2, 
        name: "Isuzu Sportivo X 2014", 
        type: "Automatic", 
        seats: 7, 
        fuel: "Diesel",
        price: "1,799", 
        isPopular: true,
        img: "https://imgcdn.zigwheels.ph/medium/gallery/exterior/13/89/isuzu-crosswind-front-angle-low-view-949250.jpg" 
    },
    { 
        id: 3, 
        name: "Toyota Innova 2026", 
        type: "Automatic", 
        seats: 7, 
        fuel: "Diesel",
        price: 3500, 
        isPopular: false,
        img: "https://imgcdn.zigwheels.ph/medium/gallery/exterior/30/1108/toyota-innova-64464.jpg"
    }
];

let fp; // GLOBAL VARIABLE for flatpickr
let currentPicking = ''; // Global variable to track which field is being edited

/* --- INITIALIZATION --- */
document.addEventListener("DOMContentLoaded", function() {
    // Initialize Flatpickr and assign to the global 'fp' variable
    fp = flatpickr("#inlineCalendar", {
        inline: true,
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        minDate: "today",
        defaultHour: 10,
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length > 0) {
                const displayDate = instance.formatDate(selectedDates[0], "M j • h:i K");
                document.getElementById('headerDate').innerText = displayDate;
            }
        },
        onReady: function(selectedDates, dateStr, instance) {
            const now = new Date();
            now.setHours(10, 0, 0, 0); 
            document.getElementById('headerDate').innerText = instance.formatDate(now, "M j • h:i K");
        }
    });

    // Initialize Scroll Animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('appear');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.2 });

    document.querySelectorAll('.testimonial-card').forEach(card => observer.observe(card));
    
    // Initialize Custom Selects
    document.querySelectorAll('.custom-select').forEach(setupCustomSelect);
});

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

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    // Use Flex for operatorModal to trigger the centering CSS
    modal.style.display = (modalId === 'operatorModal' || modalId === 'dateModal') ? 'flex' : 'block';
}

/* --- THE FIX: DATE CONFIRMATION --- */
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

// Global click to close dropdowns and modals
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = "none";
    }
    if (!event.target.closest('.custom-select')) {
        document.querySelectorAll('.custom-select').forEach(s => s.classList.remove('active'));
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

/* --- SEARCH & AUTH WITH VALIDATION --- */
function searchCars(e) {
    e.preventDefault();

    const pickupDate = document.getElementById('pickupDateValue').value;
    const returnDate = document.getElementById('returnDateValue').value;
    
    if (!pickupDate || !returnDate) {
        const triggers = document.querySelectorAll('.date-trigger');
        triggers.forEach(trigger => {
            const inputId = trigger.nextElementSibling.id;
            if (!document.getElementById(inputId).value) {
                trigger.classList.add('input-error');
                setTimeout(() => trigger.classList.remove('input-error'), 500);
            }
        });
        return; 
    }

    const container = document.getElementById('resultsContainer');
    const loader = document.getElementById('resultsLoader');
    const content = document.getElementById('resultsContent');
    const grid = document.getElementById('carsGrid');

    container.style.display = 'block';
    loader.style.display = 'grid';
    content.style.display = 'none';
    grid.innerHTML = ""; 

    scrollToSection('resultsContainer');

    setTimeout(() => {
        loader.style.display = 'none';
        content.style.display = 'block';

        // Update the "Context Bar" text
        const pickupText = document.getElementById('pickupDisplay').innerText;
        const returnText = document.getElementById('returnDisplay').innerText;
        const contextText = document.querySelector('.results-count');
        if (contextText) contextText.innerText = `Showing available vehicles for ${pickupText} - ${returnText}`;

        carDatabase.forEach(car => {
            grid.innerHTML += `
            <div class="car-card">
                <div class="car-image">
                    <img src="${car.img}" alt="${car.name}">
                    ${car.isPopular ? '<span class="status-badge">Popular</span>' : ''}
                </div>
                <div class="car-details">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1rem;">
                        <div>
                            <h3 style="margin:0;">${car.name}</h3>
                            <small style="color:var(--text-muted)">Daily Rental</small>
                        </div>
                        <div class="price-tag">₱${car.price.toLocaleString()}<small style="font-size:0.7rem; font-weight:400;">/day</small></div>
                    </div>
                    
                    <div class="car-specs-row" style="display:flex; justify-content:space-between; padding:10px 0; border-top:1px solid var(--border-color); color:var(--text-muted); font-size:0.8rem;">
                        <span><i class="fa-solid fa-user" style="color:var(--accent-red); margin-right:4px;"></i> ${car.seats} Seats</span>
                        <span><i class="fa-solid fa-gears" style="color:var(--accent-red); margin-right:4px;"></i> ${car.type}</span>
                        <span><i class="fa-solid fa-gas-pump" style="color:var(--accent-red); margin-right:4px;"></i> ${car.fuel}</span>
                    </div>
                    
                    <button class="btn btn-primary full-width" style="margin-top:0.5rem;">Book Now</button>
                </div>
            </div>`;
        });
    }, 1200);
        // Inside your searchCars carDatabase.forEach loop:
    const popularBadge = car.isPopular ? `<span class="status-badge">Most Popular</span>` : "";

    grid.innerHTML += `
    <div class="car-card">
        <div class="car-image">
            <img src="${car.img}" alt="${car.name}">
            ${popularBadge}
        </div>
        </div>`;
}

    let currentOpStep = 1;

function moveStep(direction) {
    // --- VALIDATION RULES ---
    
    // 1. Validate Privacy Checkbox (Step 2 to 3)
    if (currentOpStep === 2 && direction === 'next') {
        if (!document.getElementById('privacyAgree').checked) {
            alert("You must agree to the Privacy Policy to continue.");
            return;
        }
    }

    // 2. Validate Vehicle Details (Step 3 to 4)
    if (currentOpStep === 3 && direction === 'next') {
        const vName = document.getElementById('vName').value.trim();
        const vPlate = document.getElementById('vPlate').value.trim();
        const vCategory = document.getElementById('vCategory').value;
        const vFuel = document.getElementById('vFuel').value;
        const vDriverType = document.getElementById('vDriverType').value;

        if (!vName || !vPlate || !vCategory || !vFuel || !v) {
            alert("Please fill in all vehicle information fields.");
            return; // Stops movement
        }
    }

    // --- MOVEMENT LOGIC ---
    if (direction === 'next') {
        currentOpStep++;
    } else if (direction === 'back') {
        currentOpStep--;
    }

    // Update Step Visibility
    document.querySelectorAll('.step-content').forEach(s => s.classList.remove('active'));
    document.getElementById(`opStep${currentOpStep}`).classList.add('active');

    // Footer & Button Updates
    const footer = document.getElementById('opFooter');
    footer.style.display = (currentOpStep === 1) ? 'none' : 'flex';

    const btnNext = document.getElementById('btnNext');
    if (currentOpStep === 4) {
        btnNext.innerText = "Submit Application";
        btnNext.onclick = handleFinalSubmit; // Reassign to final check
    } else {
        btnNext.innerText = "Next";
        btnNext.onclick = () => moveStep('next');
    }
}

function handleFinalSubmit() {
    // 3. Validate Step 4: All Files Must be Present
    const fileIds = ['up-photos', 'up-or', 'up-cr', 'up-nbi', 'up-license'];
    let allUploaded = true;

    fileIds.forEach(id => {
        const input = document.getElementById(id);
        if (!input.files || input.files.length === 0) {
            allUploaded = false;
            input.style.border = "1px solid red"; // Visual hint
        } else {
            input.style.border = "none";
        }
    });

    if (!allUploaded) {
        alert("Please upload all required documents before submitting.");
        return;
    }

    // SUCCESS STATE
    const modalContent = document.querySelector('#operatorModal .modal-content');
    modalContent.innerHTML = `
        <div style="text-align:center; padding: 3rem 1rem;">
            <i class="fa-solid fa-circle-check" style="font-size: 4rem; color: #22c55e; margin-bottom: 1.5rem;"></i>
            <h2>Application Received!</h2>
            <p style="color:var(--text-muted); margin-top:1rem;">Our team will review your requirements and contact you within 24-48 hours.</p>
            <button class="btn btn-primary" style="margin-top:2rem;" onclick="location.reload()">Back to Home</button>
        </div>
    `;
}

// --- DYNAMIC SEATER LOGIC ---
function updateSeaters() {
    const category = document.getElementById('vCategory').value;
    const seatersInput = document.getElementById('vSeaters');

    const seatingMap = {
        'Sedan': '4-5 seaters',
        'SUV': '7-8 seaters',
        'Van': '10-15 seaters'
    };

    seatersInput.value = seatingMap[category] || '';
}

// --- UPDATED MOVE STEP WITH VALIDATION ---
function moveStep(direction) {
    if (currentOpStep === 2 && direction === 'next') {
        if (!document.getElementById('privacyAgree').checked) {
            alert("You must agree to the Privacy Policy to continue.");
            return;
        }
    }

    // Step 3 Validation (Vehicle Details)
    if (currentOpStep === 3 && direction === 'next') {
        const vName = document.getElementById('vName').value.trim();
        const vPlate = document.getElementById('vPlate').value.trim();
        const vCategory = document.getElementById('vCategory').value;
        const vFuel = document.getElementById('vFuel').value;
        const vDriverType = document.getElementById('vDriverType').value;

        if (!vName || !vPlate || !vCategory || !vFuel || !vDriverType) {
            alert("Please fill in all vehicle information fields.");
            return;
        }
    }

    if (direction === 'next') currentOpStep++;
    else if (direction === 'back') currentOpStep--;

    document.querySelectorAll('.step-content').forEach(s => s.classList.remove('active'));
    document.getElementById(`opStep${currentOpStep}`).classList.add('active');

    const footer = document.getElementById('opFooter');
    footer.style.display = (currentOpStep === 1) ? 'none' : 'flex';

    const btnNext = document.getElementById('btnNext');
    if (currentOpStep === 4) {
        btnNext.innerText = "Submit Application";
        btnNext.onclick = handleFinalSubmit; 
    } else {
        btnNext.innerText = "Next";
        btnNext.onclick = () => moveStep('next');
    }
}

// Ensure the final check includes the Deed of Sale
function handleFinalSubmit() {
    const fileIds = ['up-photos', 'up-or', 'up-deed', 'up-nbi', 'up-license'];
    let allUploaded = true;

    fileIds.forEach(id => {
        const input = document.getElementById(id);
        if (!input.files || input.files.length === 0) {
            allUploaded = false;
        }
    });

    if (!allUploaded) {
        alert("Please upload all required photos and documents.");
        return;
    }

    // Success State
    const modalContent = document.querySelector('#operatorModal .modal-content');
    modalContent.innerHTML = `
        <div style="text-align:center; padding: 3rem 1rem;">
            <i class="fa-solid fa-circle-check" style="font-size: 4rem; color: #22c55e; margin-bottom: 1.5rem;"></i>
            <h2>Application Received!</h2>
            <p style="color:var(--text-muted); margin-top:1rem;">Our team will review your requirements and contact you within 24-48 hours.</p>
            <button class="btn btn-primary" style="margin-top:2rem;" onclick="location.reload()">Back to Home</button>
        </div>
    `;
}   

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

    // Update your existing closeModal function (or add it if missing)
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = "none";
        }
    }

    function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active'); // Uses the flex centering
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

    function handleSignUp(event) {
        event.preventDefault();
        
        const phone = document.getElementById('regPhone').value;
        const email = document.getElementById('regEmail').value;
        const pass = document.getElementById('regPass').value;

        // VALIDATION
        if (phone.length !== 11) {
            alert("Contact number must be exactly 11 digits.");
            return;
        }
        if (pass.length < 8) {
            alert("Password must be at least 8 characters.");
            return;
        }

        // TRANSITION TO OTP
        document.getElementById('displayEmail').innerText = email;
        closeModal('signupModal');
        openModal('otpModal');
    }

    function verifyAndFinish() {
        const otp = document.getElementById('otpInput').value;
        if (otp.length === 6) {
            alert("Success! Your account is verified.");
            closeModal('otpModal');
            // Automatically Log them in
            isLoggedIn = true;
            updateNavUI(); // Updates menu to show profile
        } else {
            alert("Please enter a valid 6-digit code.");
        }
    }

    
function handleLogin(e) {
    e.preventDefault();
    closeModal('loginModal');
    document.getElementById('authButtons').style.display = 'none';
    document.getElementById('userMenu').style.display = 'flex';
}

function logout() {
    document.getElementById('authButtons').style.display = 'flex';
    document.getElementById('userMenu').style.display = 'none';
}

function toggleDropdown() {
    const menu = document.getElementById('dropdownMenu');
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
}