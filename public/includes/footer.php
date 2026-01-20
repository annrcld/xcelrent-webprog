    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="/project_xcelrent/public/assets/js/core.js"></script>
    <script src="/project_xcelrent/public/assets/js/ui.js"></script>
    <script src="/project_xcelrent/public/assets/js/auth.js"></script>
    <script src="/project_xcelrent/public/assets/js/booking.js"></script>
    <script src="/project_xcelrent/public/assets/js/search.js"></script>

    <script>
        // Check login status when page loads and update operator modal accordingly
        document.addEventListener('DOMContentLoaded', function() {
            // Initially hide the operator requirements and show login message
            const loginCheckMessage = document.getElementById('loginCheckMessage');
            const operatorRequirements = document.getElementById('operatorRequirements');

            // Check if user menu is visible (indicating user is logged in)
            const userMenu = document.getElementById('userMenu');

            // Simple check: if user menu is displayed, user is logged in
            if (userMenu && userMenu.style.display === 'flex') {
                // User is logged in, show operator requirements
                loginCheckMessage.style.display = 'none';
                operatorRequirements.style.display = 'block';
            } else {
                // User is not logged in, show login message
                loginCheckMessage.style.display = 'block';
                operatorRequirements.style.display = 'none';
            }
        });
    </script>
</body>
</html>