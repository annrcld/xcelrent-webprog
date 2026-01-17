<?php
// Any PHP logic needed *before* the HTML starts goes here.
// For example, maybe processing a submitted operator application form?
// e.g., if ($_SERVER['REQUEST_METHOD'] === 'POST') { ... validate and save ... }
?>
<!-- Operator Applications -->
<section id="operators" class="tab-content active">
    <h1 class="page-title">Operator Applications</h1>
    <p style="color: #666; margin-bottom: 20px;">For reviewing and approving external partners.</p>

    <div class="panel" id="operatorApplicationsContainer">
        <form action="<?php echo API_URL; ?>add_operator.php" method="POST" enctype="multipart/form-data" class="entry-form">
            <div class="form-section">
                <h3>Operator / Company Info</h3>
                <div class="form-row">
                    <input type="text" name="company_name" placeholder="Company Name" required>
                    <input type="text" name="contact_name" placeholder="Contact Person" required>
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-row" style="margin-top:10px;">
                    <input type="text" name="phone" placeholder="Phone" required>
                    <select name="operator_type">
                        <option value="owner">Owner</option>
                        <option value="partner">Partner</option>
                    </select>
                </div>
            </div>

            <div class="form-section">
                <h3>Documents</h3>
                <div class="form-row">
                    <label>Business Permit / ID</label>
                    <input type="file" name="operator_doc" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>

            <button type="submit" class="btn btn-red">Submit Application</button>
        </form>
    </div>
</section>

<!-- At the bottom -->
<script src="assets/js/core.js"></script>
<script src="assets/js/manage_operators.js"></script>