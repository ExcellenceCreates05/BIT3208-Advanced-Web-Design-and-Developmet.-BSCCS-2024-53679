// 1. Grab elements from the DOM
const loginForm = document.getElementById('loginForm');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const emailError = document.getElementById('emailError');
const passwordStrength = document.getElementById('passwordStrength');

// 2. Real-time Password Strength Checker
passwordInput.addEventListener('input', function() {
    const val = passwordInput.value;
    let strengthScore = 0;

    // The Password Rules (Each adds a point)
    if (val.length >= 8) strengthScore += 1;
    if (/[A-Z]/.test(val) && /[a-z]/.test(val)) strengthScore += 1; 
    if (/[0-9]/.test(val)) strengthScore += 1; 
    if (/[^A-Za-z0-9]/.test(val)) strengthScore += 1; 

    // DOM Manipulation based on the score
    if (val.length === 0) {
        passwordStrength.textContent = "";
    } else if (strengthScore <= 1) {
        passwordStrength.textContent = "Weak Password";
        passwordStrength.style.color = "red";
    } else if (strengthScore === 2 || strengthScore === 3) {
        passwordStrength.textContent = "Moderate Password.";
        passwordStrength.style.color = "orange";
    } else if (strengthScore === 4) {
        passwordStrength.textContent = "Strong Passwordd";
        passwordStrength.style.color = "green";
    }
    if (!isValid) {
        event.preventDefault(); 
        alert("Enter Valid Password: Minimum 8 characters, mixed case, numbers, and special characters.");
    }
    else {
        alert("Valid Password");
}
});



loginForm.addEventListener('submit', function(event) {
    let isValid = true;

    
    const emailRegex = /^[a-zA-Z]+@[a-zA-Z0-9-]+\.com$/;
    
    if (!emailRegex.test(emailInput.value)) {
        emailError.textContent = "Must start with letters(no symbols), contain @, and end with .com";
        emailError.style.display = "block"; 
        isValid = false;
    } else {
        emailError.style.display = "none";  
    }

    if (passwordInput.value.length < 8) {
        isValid = false;
    }

    // Prevent server submission if validation fails
    if (!isValid) {
        event.preventDefault(); 
        alert("Security Alert:Resolve the highlighted errors before attempting to log in.");
    }
    else {
        alert("Login successful! Welcome to the Decorum Bookshop Inventory Management System.");
    }
});