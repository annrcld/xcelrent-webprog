// public/assets/js/core.js

// Global variables
let fp; // Global variable for flatpickr
let currentPicking = ''; // Global variable to track which field is being edited
let carDatabase = []; // Will be populated from API

// Load car data from API
async function loadCarData() {
    try {
        const response = await fetch('../api/cars.php');
        if (response.ok) {
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                carDatabase = await response.json();
            } else {
                console.error('Response is not JSON:', await response.text());
                // Fallback to hardcoded data if response is not JSON
                carDatabase = [
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
            }
        } else {
            console.error('Failed to load car data:', response.statusText);
            // Fallback to hardcoded data if API fails
            carDatabase = [
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
        }
    } catch (error) {
        console.error('Error loading car data:', error);
        // Fallback to hardcoded data if API fails
        carDatabase = [
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
    }
}

// Initialize Flatpickr
function initializeFlatpickr() {
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
}

// Initialize Scroll Animations
function initializeScrollAnimations() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('appear');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.2 });

    document.querySelectorAll('.testimonial-card').forEach(card => observer.observe(card));
}

// Initialize Custom Selects
function initializeCustomSelects() {
    document.querySelectorAll('.custom-select').forEach(setupCustomSelect);
}

// Initialize all core functionality
document.addEventListener("DOMContentLoaded", async function() {
    // Load car data from API
    await loadCarData();

    // Initialize Flatpickr
    initializeFlatpickr();

    // Initialize Scroll Animations
    initializeScrollAnimations();

    // Initialize Custom Selects
    initializeCustomSelects();
});