document.addEventListener("DOMContentLoaded", function() {
    const contactForm = document.getElementById("contactForm");
    const messageInput = document.getElementById("message");
    const messageError = document.getElementById("messageError");
    
    contactForm.addEventListener("submit", function(event) {
        
        let isValid = true; 
        
        messageError.textContent = ""; 

        if (messageInput.value.trim() === "") {
            messageError.textContent = "Message cannot be empty. Please write something.";
            isValid = false; 
        }

        if (!isValid) {
            event.preventDefault(); 
        }
        
    });
});