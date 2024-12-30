document.addEventListener("DOMContentLoaded", (event) => {
  console.log("DOM fully loaded and parsed");
  const errorAlert = document.getElementById("errorAlert");
  const successAlert = document.getElementById("successAlert");
  const operation = document.getElementById("operation");
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

  if (operation) {
    if (operation.value === "register-account") {
      document.forms["register-form"].addEventListener("submit", (event) => {
        if (!validateRegisterForm()) {
          event.preventDefault();
        }
      });
    }
    manipulateNewNoticeNavBarText();

    manipulateTitleFormNews();

    if (operation.value === "edit-news") {
      $(function () {
        $(".pop").on("click", function () {
          $(".imagepreview").attr("src", $(this).find("img").attr("src"));
          $("#imagemodal").modal("show");
        });
      });
    }
  }
});

function manipulateTitleFormNews() {
  const titleNews = document.getElementById("titleNews");

  if (!titleNews) return;

  operation.value === "create-news"
    ? (titleNews.innerHTML = "Criar Notícia")
    : (titleNews.innerHTML = "Editar Notícia");
}

function manipulateNewNoticeNavBarText() {
  const newNoticeNavBar = document.getElementById("newNoticeNavBar");

  if (!newNoticeNavBar) return;

  if (operation.value === "create-news") {
    newNoticeNavBar.style.display = "none";
    return;
  }

  newNoticeNavBar.style.display = "block";
}

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
