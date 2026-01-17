// assets/js/core.js

// ======================
// TAB & FORM UTILITIES
// ======================

function showTab(tabId, btn) {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));

    const tabEl = document.getElementById(tabId);
    if (!tabEl) {
        console.warn('showTab: no element with id', tabId);
        return;
    }
    tabEl.classList.add('active');
    if (btn && btn.classList) btn.classList.add('active');

    // Optional: Trigger custom event for tab shown
    const event = new CustomEvent('tabShown', { detail: { tabId } });
    document.dispatchEvent(event);
}

// Coding Day Logic
function updateCoding() {
    const input = document.getElementById('plateInput');
    const display = document.getElementById('codingDisplay');
    
    let val = input.value.toUpperCase();
    if (val.length === 3 && !val.includes('-')) {
        val = val + '-';
    }
    input.value = val.substring(0, 8); 

    const lastDigit = input.value.trim().slice(-1);
    const codingMap = {
        '1': 'Monday', '2': 'Monday',
        '3': 'Tuesday', '4': 'Tuesday',
        '5': 'Wednesday', '6': 'Wednesday',
        '7': 'Thursday', '8': 'Thursday',
        '9': 'Friday', '0': 'Friday'
    };

    if (input.value.length === 8) {
        display.value = codingMap[lastDigit] || 'Invalid Digit';
    } else {
        display.value = '';
    }
}

// Seating Logic
function handleTypeChange() {
    const type = document.getElementById('vType')?.value;
    const seating = document.getElementById('seating');
    if (!seating) return;

    if (type === 'Sedan') seating.value = 4;
    else if (type === 'SUV') seating.value = 7;
    else if (type === 'Van') seating.value = 10;
}

function adjustSeating(val) {
    const type = document.getElementById('vType')?.value;
    const input = document.getElementById('seating');
    if (!input) return;

    let current = parseInt(input.value || '0', 10);
    let next = current + val;

    if (type === 'Sedan' && next >= 4 && next <= 5) input.value = next;
    if (type === 'SUV' && next >= 7 && next <= 8) input.value = next;
    if (type === 'Van' && next >= 10 && next <= 15) input.value = next;
}

// Modal Functions
function closeModal(modalId) {
    document.getElementById(modalId)?.classList.remove('active');
}

window.onclick = function(event) {
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        if (event.target === modal) {
            modal.classList.remove('active');
        }
    });
};

// Photo Gallery (if used globally)
let currentPhotoApp = 1;
let currentPhotoIndex = 1;

function viewPhotoGallery(appId, photoNum) {
    currentPhotoApp = appId;
    currentPhotoIndex = photoNum || 1;
    updatePhotoCounter();
    document.getElementById('photoGalleryModal')?.classList.add('active');
}

function previousPhoto() {
    if (currentPhotoIndex > 1) {
        currentPhotoIndex--;
        updatePhotoCounter();
    }
}

function nextPhoto() {
    if (currentPhotoIndex < 10) {
        currentPhotoIndex++;
        updatePhotoCounter();
    }
}

function updatePhotoCounter() {
    document.getElementById('photoCounter')?.textContent = currentPhotoIndex + '/10';
}

// Initialize Lucide Icons
document.addEventListener('DOMContentLoaded', () => {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});