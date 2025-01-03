document.addEventListener("DOMContentLoaded", (event) => {
  const registerForm = document.getElementById("register-form");

  registerForm.addEventListener("submit", (event) => {
    if (!validateRegisterForm()) {
      event.preventDefault();
    }
  });
});

function validateRegisterForm() {
  const username = document.getElementById("name").value;
  const email = document.getElementById("email").value;
  const password = document.getElementById("password").value;
  const confirmPassword = document.getElementById("confirm_password").value;

  if (
    username === "" ||
    email === "" ||
    password === "" ||
    confirmPassword === ""
  ) {
    addAlertMsg("danger", "Todos os campos são obrigatórios");
    return false;
  }

  if (password !== confirmPassword) {
    addAlertMsg("danger", "Senhas não conferem");
    return false;
  }

  return true;
}

async function register() {
  const username = document.getElementById("name").value;
  const email = document.getElementById("email").value;
  const password = document.getElementById("password").value;
  const confirmPassword = document.getElementById("confirmPassword").value;
  const host = window.location.origin;

  if (
    username === "" ||
    email === "" ||
    password === "" ||
    confirmPassword === ""
  ) {
    addAlertMsg("danger", "Todos os campos são obrigatórios");
    return;
  }

  if (password !== confirmPassword) {
    addAlertMsg("danger", "Senhas não conferem");
    return;
  }

  const data = new URLSearchParams();
  data.append("username", username);
  data.append("email", email);
  data.append("password", password);

  const response = await fetch(`${host}/api/internal/create-account`, {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: data.toString(),
  });

  const result = await response.json();

  if (result.error) {
    addAlertMsg("danger", result.error);
    return;
  }

  addAlertMsg("success", "Usuário cadastrado com sucesso");
  window.location.href = "/";
}
