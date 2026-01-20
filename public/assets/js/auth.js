// public/assets/js/auth.js

/* --- AUTHENTICATION FUNCTIONS --- */

function handleLogin(e) {
    e.preventDefault();

    const email = document.querySelector('#loginModal input[type="email"]').value;
    const password = document.querySelector('#loginModal input[type="password"]').value;

    // Basic validation
    if (!email || !password) {
        alert("Please enter both email and password.");
        return;
    }

    // Prepare data for API call
    const loginData = {
        email: email,
        password: password
    };

    // Call the login API
    fetch('/project_xcelrent/public/api/login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(loginData)
    })
    .then(response => {
        // Check if response is OK before parsing JSON
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Login successful
            closeModal('loginModal');

            // Safely update UI elements
            const authButtons = document.getElementById('authButtons');
            const userMenu = document.getElementById('userMenu');
            const userAvatar = document.querySelector('.user-avatar');

            if (authButtons) {
                authButtons.style.display = 'none';
            }
            if (userMenu) {
                userMenu.style.display = 'flex';
            }
            if (userAvatar && data.user) {
                const firstName = data.user.first_name.charAt(0);
                const lastName = data.user.last_name.charAt(0);
                userAvatar.textContent = firstName + lastName;
            }

            // Update operator modal if it's open
            const operatorModal = document.getElementById('operatorModal');
            if (operatorModal && operatorModal.style.display !== 'none') {
                const loginCheckMessage = document.getElementById('loginCheckMessage');
                const operatorRequirements = document.getElementById('operatorRequirements');

                if (loginCheckMessage) {
                    loginCheckMessage.style.display = 'none';
                }
                if (operatorRequirements) {
                    operatorRequirements.style.display = 'block';
                }
            }
        } else {
            alert(data.message || "Login failed. Please check your credentials.");
        }
    })
    .catch(error => {
        console.error('Login error:', error);
        // Try to get more specific error information
        alert("An error occurred during login. Please try again. Error: " + error.message);
    });
}

function logout() {
    const authButtons = document.getElementById('authButtons');
    const userMenu = document.getElementById('userMenu');

    if (authButtons) {
        authButtons.style.display = 'flex';
    }
    if (userMenu) {
        userMenu.style.display = 'none';
    }

    // Reset operator modal to show login message if it's open
    const operatorModal = document.getElementById('operatorModal');
    if (operatorModal && operatorModal.style.display !== 'none') {
        const loginCheckMessage = document.getElementById('loginCheckMessage');
        const operatorRequirements = document.getElementById('operatorRequirements');

        if (loginCheckMessage) {
            loginCheckMessage.style.display = 'block';
        }
        if (operatorRequirements) {
            operatorRequirements.style.display = 'none';
        }
    }
}

function toggleDropdown() {
    const menu = document.getElementById('dropdownMenu');
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
}

function handleSignUp(event) {
    event.preventDefault();

    const firstName = document.getElementById('regFirst').value;
    const lastName = document.getElementById('regLast').value;
    const phone = document.getElementById('regPhone').value;
    const email = document.getElementById('regEmail').value;
    const pass = document.getElementById('regPass').value;

    // VALIDATION
    if (!firstName || !lastName || !email || !phone || !pass) {
        alert("All fields are required.");
        return;
    }
    if (phone.length !== 11) {
        alert("Contact number must be exactly 11 digits.");
        return;
    }
    if (pass.length < 8) {
        alert("Password must be at least 8 characters.");
        return;
    }

    // Prepare data for API call
    const userData = {
        firstName: firstName,
        lastName: lastName,
        email: email,
        phone: phone,
        password: pass
    };

    // Call the signup API
    fetch('/project_xcelrent/public/api/signup.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(userData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message and transition to OTP
            const displayEmailElement = document.getElementById('displayEmail');
            if (displayEmailElement) {
                displayEmailElement.innerText = email;
            }
            closeModal('signupModal');
            openModal('otpModal');
        } else {
            alert(data.message || "Registration failed. Please try again.");
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("An error occurred during registration. Please try again.");
    });
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

// Operator modal functions
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

        if (!vName || !vPlate || !vCategory || !vFuel || !vDriverType) {
            alert("Please fill in all vehicle information fields.");
            return;
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
    const fileIds = ['up-photos', 'up-or', 'up-deed', 'up-nbi', 'up-license'];
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