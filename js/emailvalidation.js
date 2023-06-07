document.addEventListener("DOMContentLoaded", function() {
    var form = document.getElementById("myForm");
    var emailInput = document.getElementById("email");

    form.addEventListener("submit", function(event) {
      if (!validateEmail(emailInput.value)) {
        event.preventDefault(); // Prevent form submission
        alert("Invalid email address!");
      }
    });

    function validateEmail(email) {
      var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(email);
    }
  });