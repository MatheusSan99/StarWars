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