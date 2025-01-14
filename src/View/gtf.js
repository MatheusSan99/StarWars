let currentPage = 1;
let totalPages = 1;
const recordsPerPage = 5;

document.addEventListener("DOMContentLoaded", async function () {
  var datepickers = document.querySelectorAll(".datepicker");
  datepickers.forEach(function (datepicker) {
    datepicker.type = "date";
  });
  await loadData();
});

function loadClients(clients) {
  const uniqueClients = [...new Set(clients)];

  let select = document.getElementById("clientes");
  select.innerHTML = "";
  uniqueClients.forEach((client) => {
    let option = document.createElement("option");
    option.value = client;
    option.textContent = client;
    select.appendChild(option);
  });
}

async function loadData() {
  const form = document.getElementById("filters-form");
  const formData = new FormData(form);
  const queryString = new URLSearchParams(formData).toString();
  const cache = localStorage.getItem("filtered_data");
  const cacheTimestamp = localStorage.getItem("cache_timestamp");
  const currentTime = new Date().getTime();

  if (
    cache &&
    cacheTimestamp &&
    currentTime - cacheTimestamp < 4 * 60 * 60 * 1000
  ) {
    const cachedData = JSON.parse(cache);
    const filteredData = Array.isArray(cachedData)
      ? cachedData
      : Object.values(cachedData);

    totalPages = Math.ceil(filteredData.length / recordsPerPage);
    await displayTableData(filteredData);
    loadClients(filteredData.map((item) => item.cliente));
    return;
  }

  try {
    toggleLoaderStatus(true);
    const response = await fetch("", {
      method: "POST",
      body: queryString,
    });

    if (!response.ok) {
      throw new Error(`Erro ao buscar dados: ${response.statusText}`);
    }

    const responseData = await response.json();

    if (responseData.error) {
      displayMsg(responseData.error, responseData.code || 400);
      return;
    }

    const filteredData = Array.isArray(responseData)
      ? responseData
      : Object.values(responseData);

    totalPages = Math.ceil(filteredData.length / recordsPerPage);
    localStorage.setItem("filtered_data", JSON.stringify(filteredData));
    localStorage.setItem("cache_timestamp", currentTime);
    await displayTableData(filteredData);
    loadClients(filteredData.map((item) => item.cliente));
  } catch (error) {
    console.error("Erro ao carregar os dados:", error);
  } finally {
    toggleLoaderStatus(false);
  }
}

async function displayTableData(filteredData) {
  const container = document.getElementById("table-container");
  if (!filteredData || filteredData.length === 0) {
    container.innerHTML =
      '<p class="text-center">Nenhum dado encontrado com os filtros aplicados.</p>';
    updatePaginationControls();
    toggleLoaderStatus(false);
    return;
  }
  const startIndex = (currentPage - 1) * recordsPerPage;
  const endIndex = startIndex + recordsPerPage;
  const pageData = filteredData.slice(startIndex, endIndex);
  container.innerHTML = await generateHtmlTable(pageData);
  updatePaginationControls();
}

async function generateHtmlTable(
    pageData,
    preContentHeader = "",
    preContentHeaderShortened = ""
  ) {
    let tableHTML = "";
  
    pageData.forEach((item, index) => {
      if (index === 0) {
        tableHTML += preContentHeader;
      }
      else if (index % 8 === 0) {
        tableHTML += preContentHeaderShortened;
      }
  
      if (index % 8 === 0) {
        tableHTML += `
          <div class="table-responsive">
          <table class="table table-striped table-hover text-center">
              <thead class="table-dark">
                  <tr>
                      <th>PV</th>
                      <th>IMAGEM</th>
                      <th>NOME DA PEÇA</th>
                      <th>DATA T0</th>
                      <th>CLIENTE</th>
                      <th>TRANSFORMADOR</th>
                  </tr>
              </thead>
              <tbody>
        `;
      }
  
      tableHTML += `
        <tr>
          <td>${item.pv}</td>
          <td><img src="${item.imagem}" class="img-fluid" style="max-width: 120px; max-height: 80px;"></td>
          <td>${item.nomepeca}</td>
          <td>${item.datat0}</td>
          <td>${item.cliente}</td>
          <td>${item.transformador}</td>
        </tr>
      `;
  
      if ((index + 1) % 8 === 0 || index === pageData.length - 1) {
        tableHTML += "</tbody></table></div>";
      }
    });
  
    return tableHTML;
  }
  

function updatePaginationControls() {
  document.getElementById(
    "page-info"
  ).textContent = `Página ${currentPage} de ${totalPages}`;
  document.getElementById("prev-page").disabled = currentPage === 1;
  document.getElementById("next-page").disabled = currentPage === totalPages;
}

async function changePage(direction) {
  currentPage += direction;
  currentPage = Math.max(1, Math.min(currentPage, totalPages));
  await loadData();
}

async function resetCache() {
  const resetCache = document.getElementById("reset-cache");
  resetCache.textContent = "Aguarde...";
  localStorage.removeItem("filtered_data");
  localStorage.removeItem("cache_timestamp");
  await loadData();
  resetCache.textContent = "Limpar Cache";
  displayMsg(
    "Cache limpo com sucesso, os dados atualizados foram carregados.",
    200
  );
}

async function applyFilters() {
    toggleLoaderStatus(true);
    const cache = localStorage.getItem("filtered_data");
    if (cache) {
        const filteredDataWithFilters = await filterData(JSON.parse(cache));
        totalPages = Math.ceil(filteredDataWithFilters.length / recordsPerPage);
        await displayTableData(filteredDataWithFilters);
    }
    setTimeout(() => {
        toggleLoaderStatus(false);
    }, 150); 
}

async function filterData(filtered_data) {
  const start_date = document.getElementById("start_date").value;
  const end_date = document.getElementById("end_date").value;
  const clientes = Array.from(
    document.getElementById("clientes").selectedOptions
  ).map((option) => option.value);

  const filteredDataWithFilters = filtered_data.filter((item) => {
    if (start_date && item.datat0 < start_date) {
      return false;
    }

    if (end_date && item.datat0 > end_date) {
      return false;
    }

    if (clientes.length > 0 && !clientes.includes(item.cliente)) {
      return false;
    }
    return true;
  });

  return filteredDataWithFilters;
}

async function reset_filters() {
  document.getElementById("filters-form").reset();
  await loadData();
}

async function exportToPDF() {
    let allData = JSON.parse(localStorage.getItem("filtered_data"));
    allData = await filterData(allData);
    if (!allData || allData.length === 0) {
      displayMsg("Nenhum dado encontrado para exportar.", 400);
      return;
    }
  
    const filename =
      "Relatorio-Gestao-Moldes-" +
      new Date().toLocaleString().replace(/:/g, "-").replace(/\//g, "-") +
      ".pdf";
    const pdfTitle = "Relatório de Gestão de Moldes";
    const additionalInfo = "Data: " + new Date().toLocaleDateString() + "<br>" + "Certificado: " + "Matheus";
    const companyLogoURL = document.querySelector("img").src;
  
    const container = await generateHtmlTable(
        allData,
        `<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div style="flex: 1;">
                <img src="${companyLogoURL}" alt="Logo" style="height: 80px; max-width: 150px;">
            </div>
            <div style="flex: 2; text-align: center;">
                <h1 style="margin: 0; font-size: 24px;">${pdfTitle}</h1>
            </div>
            <div style="flex: 1; text-align: right;">
                <p style="margin: 0; font-size: 14px;">${additionalInfo}</p>
            </div>
        </div>`,
        `<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 40px;  margin-bottom: 5px;">
            <div style="flex: 1; text-align: right;">
                <p style="margin: 0; font-size: 14px;">${additionalInfo}</p>
            </div>
        </div>`
      );
  
    const options = {
      margin: [20, 10, 20, 10],
      filename: filename,
      image: { type: "jpeg", quality: 0.98 },
      html2canvas: { scale: 2 },
      jsPDF: { unit: "mm", format: "a4", orientation: "portrait" },
      enableHTML: true,
      pagebreak: { before: '.page-break', avoid: ['table', 'img'] },
    };
  
    html2pdf()
      .set(options)
      .from(container)
      .toPdf()
      .get("pdf")
      .then(function (pdf) {
        const totalPages = pdf.internal.getNumberOfPages();
        const pageWidth = pdf.internal.pageSize.getWidth();
        const pageHeight = pdf.internal.pageSize.getHeight();
  
        for (let i = 1; i <= totalPages; i++) {
          pdf.setPage(i);
          pdf.setFontSize(10);
          pdf.text(
            `Página ${i} de ${totalPages}`,
            pageWidth / 2,
            pageHeight - 10,
            { align: "center" }
          );
  
          if (i === 1) {
            const contentStartY = pageWidth + 150;
            pdf.text(" ", contentStartY, contentStartY);
          }
        }
      })
      .save(filename);
  }

function displayMsg(message, code) {
  const alert = document.getElementById("alert");
  const alertMessage = document.getElementById("alert-message");
  alert.className = "";
  alert.style.display = "flex";
  alertMessage.textContent = message;
  alert.classList.remove("alert", "alert-success", "alert-danger");

  if (code === 200) {
    alert.classList.add("alert", "alert-success");
  } else {
    alert.classList.add("alert", "alert-danger");
  }

  setTimeout(() => {
    alert.style.display = "none";
  }, 5000);
}

function toggleLoaderStatus(status) {
  const loader = document.getElementById("loader");
  const overlay = document.getElementById("overlay");
  loader.style.display = status ? "flex" : "none";
  overlay.style.display = status ? "block" : "none";
  document.body.style.pointerEvents = status ? "none" : "auto";
}
