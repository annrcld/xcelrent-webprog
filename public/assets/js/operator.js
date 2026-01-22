// public/assets/js/operator.js

/**
 * Handles the submission of the operator application form.
 */
async function submitOperatorApplication(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;

    try {
        // Disable button and show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';

        const response = await fetch('/project_xcelrent/public/api/submit_operator_application.php', {
            method: 'POST',
            body: formData
        });

        // Check if the response is actually JSON
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            const text = await response.text();
            console.error("Server returned non-JSON response:", text);
            throw new Error("Server error: The application could not be processed. Please check the console for details.");
        }

        const data = await response.json();

        if (!response.ok || data.error) {
            throw new Error(data.error || 'Submission failed.');
        }

        // Success
        alert(data.message || 'Application submitted successfully!');
        form.reset();
        
        // Close modal if the function exists
        if (typeof closeModal === 'function') {
            closeModal('operatorModal');
        }

    } catch (error) {
        alert(error.message);
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    }
}