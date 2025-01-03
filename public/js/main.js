document.addEventListener("DOMContentLoaded", (event) => {
  const errorAlert = document.getElementById("errorAlert");
  const successAlert = document.getElementById("successAlert");
  const url = window.location.href;
  if (errorAlert) {
    setTimeout(() => {
      errorAlert.classList.remove("show");
      errorAlert.classList.add("fade");
      setTimeout(() => errorAlert.remove(), 500);
    }, 4000);
  }

  if (successAlert) {
    setTimeout(() => {
      successAlert.classList.remove("show");
      successAlert.classList.add("fade");
      setTimeout(() => successAlert.remove(), 500);
    }, 4000);
  }

  if (url) {
      if (url.includes("pages/create-account")) {
      document.forms["register-form"].addEventListener("submit", (event) => {
        if (!validateRegisterForm()) {
          event.preventDefault();
        }
      });
    }

    if (url.includes("pages/edit-film")) {
      $(function () {
        $(".pop").on("click", function () {
          $(".imagepreview").attr("src", $(this).find("img").attr("src"));
          $("#imagemodal").modal("show");
        });
      });
    }
  }
});

function addAlertMsg(type, msg) {
  const alert = document.createElement("div");
  alert.className = `alert alert-${type} show`;
  alert.setAttribute("role", "alert");
  alert.innerHTML = msg;

  document.body.appendChild(alert);

  setTimeout(() => {
    alert.classList.remove("show");
    alert.classList.add("fade");
    setTimeout(() => alert.remove(), 500);
  }, 8000);
}

function validateRegisterForm() {
  const username = document.forms["registerForm"]["username"].value;
  const email = document.forms["registerForm"]["email"].value;
  const password = document.forms["registerForm"]["password"].value;
  const confirmPassword =
    document.forms["registerForm"]["confirmPassword"].value;

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

function openModalImage(imageBase64) {
  var modal = document.getElementById("imagemModal");
  var modalImage = modal.querySelector(".modal-body img");
  modalImage.src = "data:image/jpeg;base64," + imageBase64;
  $(modal).modal("show");
}

async function login() {
  const email = document.getElementById("email").value;
  const password = document.getElementById("password").value;
  const host = window.location.origin;

  if (email === "" || password === "") {
    addAlertMsg("danger", "Todos os campos são obrigatórios");
    return;
  }

  const data = new URLSearchParams();
  data.append("email", email);
  data.append("password", password);

  const response = await fetch(`${host}/api/internal/login`, {
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

  window.location.href = "/pages/catalog";
}

async function logout() {
  const host = window.location.origin;

  const response = await fetch(`${host}/api/internal/logout`, {
    method: "POST",
  });

  const result = await response.json();

  if (result.error) {
    addAlertMsg("danger", result.error);
    return;
  }

  window.location.href = "/";
}

async function register() {
  const username = document.getElementById("username").value;
  const email = document.getElementById("email").value;
  const password = document.getElementById("password").value;
  const confirmPassword = document.getElementById("confirmPassword").value;
  const host = window.location.origin;

  if (username === "" || email === "" || password === "" || confirmPassword === "") {
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
