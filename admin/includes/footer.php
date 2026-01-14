<?php
// Any final PHP logic or cleanup code would go here if needed.
?>
        </main> </div> <div id="paymentModal" class="modal-overlay">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal('paymentModal')">&times;</span>
            <h3>Payment Proof</h3>
            <div id="paymentModalContent" style="background: #f0f0f0; height: 300px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 20px 0; color: #999;">
                Loading...
            </div>
        </div>
    </div>

    <div id="licenseModal" class="modal-overlay">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal('licenseModal')">&times;</span>
            <h3>Drivers License</h3>
            <div id="licenseModalContent" style="background: #f0f0f0; height: 350px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 20px 0; color: #999;">
                Loading...
            </div>
        </div>
    </div>

    <div id="rentalHistoryModal" class="modal-overlay">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal('rentalHistoryModal')">&times;</span>
            <h3>Rental History</h3>
            <div id="rentalHistoryContent" style="margin-top: 20px;">
                </div>
        </div>
    </div>

    <script src="<?php echo ASSETS_URL; ?>js/script.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
    
</body>
</html>