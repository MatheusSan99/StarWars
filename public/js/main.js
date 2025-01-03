document.addEventListener("DOMContentLoaded", (event) => {
  const errorAlert = document.getElementById("errorAlert");
  const successAlert = document.getElementById("successAlert");
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
});

function addAlertMsg(type, msg) {
  const alert = document.createElement("div");
  alert.className = `alert alert-${type} show`;
  alert.setAttribute("role", "alert");
  alert.innerHTML = msg;

  alert.style.position = 'fixed';
  alert.style.top = '20px'; 
  alert.style.right = '20px';
  alert.style.zIndex = '9999';

  alert.style.transition = 'all 0.5s ease';

  document.body.appendChild(alert);

  setTimeout(() => {
    alert.classList.remove("show");
    alert.classList.add("fade");
    setTimeout(() => alert.remove(), 500);
  }, 8000);
}

function openModalImage(imageBase64) {
  var modal = document.getElementById("imagemModal");
  var modalImage = modal.querySelector(".modal-body img");
  modalImage.src = "data:image/jpeg;base64," + imageBase64;
  $(modal).modal("show");
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

function enableLoadingGif() {
  if (document.getElementById("loadingContainer")) {
    return;
  }

  const overlay = document.createElement("div");
  overlay.id = "overlay";
  overlay.style.position = 'fixed';
  overlay.style.top = '0';
  overlay.style.left = '0';
  overlay.style.width = '100%';
  overlay.style.height = '100%';
  overlay.style.backgroundColor = 'rgb(0 0 0 / 100%)'; 
  overlay.style.zIndex = '9998';  
  overlay.style.pointerEvents = 'none'; 
  document.body.appendChild(overlay);

  const loadingContainer = document.createElement("div");
  loadingContainer.id = "loadingContainer";
  loadingContainer.style.position = 'fixed';
  loadingContainer.style.top = '50%';
  loadingContainer.style.left = '50%';
  loadingContainer.style.transform = 'translate(-50%, -50%)';
  loadingContainer.style.zIndex = '9999';
  loadingContainer.style.display = 'flex';
  loadingContainer.style.flexDirection = 'column';
  loadingContainer.style.alignItems = 'center';
  loadingContainer.style.justifyContent = 'center';
  loadingContainer.style.textAlign = 'center';

  const webp = document.createElement("img");
  webp.src = loadingGifPath;  
  webp.width = "480";
  webp.height = "274";
  webp.style.borderRadius = '15px';  
  webp.style.transition = 'all 0.5s ease';
  webp.id = "loadingGif";  
  loadingContainer.appendChild(webp);

  document.body.appendChild(loadingContainer);

  document.body.style.opacity = '0.9';
  document.body.style.pointerEvents = 'none';
}

function disableLoadingGif() {
  const overlay = document.getElementById("overlay");
  if (overlay) {
    overlay.remove();
  }

  const loadingContainer = document.getElementById("loadingContainer");
  if (loadingContainer) {
    loadingContainer.remove();
  }

  document.body.style.opacity = '1';
  document.body.style.pointerEvents = 'auto'; 
}