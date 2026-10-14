const form = document.getElementById("registrationForm");

function setError(inputId, errorId, message) {
  const input = document.getElementById(inputId);
  document.getElementById(errorId).textContent = message;
  input.classList.toggle("invalid", Boolean(message));
}

form.addEventListener("submit", (event) => {
  event.preventDefault();
  let valid = true;
  const name = document.getElementById("name").value.trim();
  const roll = document.getElementById("rollNumber").value.trim();
  const email = document.getElementById("email").value.trim();
  const course = document.getElementById("course").value;
  const fileInput = document.getElementById("document");
  const password = document.getElementById("password").value;
  const confirmPassword = document.getElementById("confirmPassword").value;
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const allowedTypes = ["application/pdf", "image/jpeg", "image/png"];
  const maxBytes = 2 * 1024 * 1024;

  const checks = [
    ["name", "nameError", name ? "" : "Name is required."],
    ["rollNumber", "rollError", roll ? "" : "Roll number is required."],
    ["email", "emailError", emailPattern.test(email) ? "" : "Enter a valid email address."],
    ["course", "courseError", course ? "" : "Please select a course."],
    ["password", "passwordError", password.length >= 8 ? "" : "Password must contain at least 8 characters."],
    ["confirmPassword", "confirmError", password === confirmPassword && confirmPassword ? "" : "Passwords must match."]
  ];

  checks.forEach(([inputId, errorId, message]) => {
    setError(inputId, errorId, message);
    if (message) valid = false;
  });

  const file = fileInput.files[0];
  let fileMessage = "";
  if (!file) fileMessage = "Please choose a file.";
  else if (!allowedTypes.includes(file.type)) fileMessage = "Only PDF, JPG, or PNG files are allowed.";
  else if (file.size > maxBytes) fileMessage = "File must be 2 MB or smaller.";
  setError("document", "fileError", fileMessage);
  if (fileMessage) valid = false;

  const status = document.getElementById("formMessage");
  if (valid) {
    status.textContent = "Validation successful! This demo does not save data to a server.";
    status.className = "success";
    form.reset();
  } else {
    status.textContent = "Please correct the highlighted fields.";
    status.className = "failure";
  }
});
