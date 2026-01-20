    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="/project_xcelrent/public/assets/js/core.js"></script>
    <script src="/project_xcelrent/public/assets/js/ui.js"></script>
    <script src="/project_xcelrent/public/assets/js/auth.js"></script>
    <script src="/project_xcelrent/public/assets/js/booking.js"></script>
    <script src="/project_xcelrent/public/assets/js/search.js"></script>

    <script>
        // Check login status when page loads and update UI accordingly
        document.addEventListener('DOMContentLoaded', function() {
            // Check if user is logged in via session
            fetch('/project_xcelrent/public/api/check_session.php')
            .then(response => response.json())
            .then(data => {
                const loginCheckMessage = document.getElementById('loginCheckMessage');
                const operatorRequirements = document.getElementById('operatorRequirements');

                if (data.isLoggedIn) {
                    // Show operator requirements in modal if it's open
                    if (loginCheckMessage) loginCheckMessage.style.display = 'none';
                    if (operatorRequirements) operatorRequirements.style.display = 'block';
                } else {
                    // Show login message in operator modal if it's open
                    if (loginCheckMessage) loginCheckMessage.style.display = 'block';
                    if (operatorRequirements) operatorRequirements.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error checking session:', error);
                // Default to showing login message in operator modal if there's an error
                const loginCheckMessage = document.getElementById('loginCheckMessage');
                const operatorRequirements = document.getElementById('operatorRequirements');

                if (loginCheckMessage) loginCheckMessage.style.display = 'block';
                if (operatorRequirements) operatorRequirements.style.display = 'none';
            });
        });
    </script>
</body>
</html>