document.addEventListener("DOMContentLoaded", async function () {
  await loadCatalog();
  const searchInput = document.getElementById("search-movie");

  searchInput.addEventListener("keyup", searchMovieFromCatalog);
});

async function loadCatalog() {
  const host = window.location.origin;

  try {
    const response = await fetch(`${host}/api/external/catalog`);

    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }

    const catalog = await response.json();
    await buildCatalogTable(catalog);
  } catch (error) {
    console.error("Failed to load catalog:", error);
  }
}

async function buildCatalogTable(catalog) {
  const table = document.getElementById("table-catalog");
  const thead = document.getElementById("thead-catalog");
  const tbody = document.getElementById("tbody-catalog");

  if (catalog.films.length === 0) {
    displayNoFilmsMessage(tbody);
    return;
  }

  const headers = [
    "Visualizar Filme",
    "Capa",
    "Título",
    "Diretor",
    "Data de Lançamento",
    "Produtores",
    "Personagens",
    "Ações",
  ];
  buildTableHeaders(headers, thead);

  catalog.films.forEach((film) => {
    const row = buildFilmRow(film);
    tbody.appendChild(row);
  });

  table.appendChild(thead);
  table.appendChild(tbody);
}

function displayNoFilmsMessage(tbody) {
  tbody.innerHTML = "<p>Poxa, nenhum filme foi encontrado no momento.</p>";
}

function buildTableHeaders(headers, thead) {
  const theadRow = document.createElement("tr");

  headers.forEach((header) => {
    const th = document.createElement("th");
    th.textContent = header;

    if (
      ["Título", "Diretor", "Data de Lançamento", "Produtores"].includes(header)
    ) {
      addSortingFeature(th, theadRow);
    }

    theadRow.appendChild(th);
  });

  thead.appendChild(theadRow);
}

function addSortingFeature(th, theadRow) {
  const sortIcon = document.createElement("i");
  sortIcon.classList.add("fas", "fa-sort", "ml-2");
  th.appendChild(sortIcon);

  th.setAttribute("data-sort-order", "desc");

  th.addEventListener("click", () => {
    const columnIndex = Array.from(theadRow.children).indexOf(th);

    const currentOrder = th.getAttribute("data-sort-order");
    const isAscending = currentOrder === "asc";
    const newOrder = isAscending ? "desc" : "asc";
    th.setAttribute("data-sort-order", newOrder);

    sortIcon.classList.toggle("fa-sort-up", isAscending);
    sortIcon.classList.toggle("fa-sort-down", !isAscending);
    sortIcon.classList.remove("fa-sort", false); 

    sortTable(columnIndex, newOrder);
  });
}

function buildFilmRow(film) {
  const tr = document.createElement("tr");

  tr.appendChild(
    createTableCell(
      createActionButton(`film?id=${film.id}`, "Ver Filme", [
        "btn",
        "btn-info",
        "btn-round",
        "btn-sm",
      ])
    )
  );
  
  tr.appendChild(createFilmCoverCell(film));
  tr.appendChild(createTableCell(film.title));
  tr.appendChild(createTableCell(film.director));
  tr.appendChild(createTableCell(film.release_date));
  tr.appendChild(createTableCell(film.producers));
  tr.appendChild(
    createTableCell(
      createActionButton(`caracthers-list?id=${film.id}`, "Visualizar", [
        "btn",
        "btn-info",
        "btn-round",
        "btn-sm",
      ])
    )
  );

  const actionsTd = document.createElement("td");
  actionsTd.appendChild(
    createActionButton(`/edit-film?id=${film.id}`, "Editar", [
      "btn",
      "btn-info",
      "btn-round",
      "btn-sm",
    ])
  );
  actionsTd.appendChild(
    createActionButton(`/remove-film?id=${film.id}`, "Excluir", [
      "btn",
      "btn-danger",
      "btn-round",
      "btn-sm",
    ])
  );
  tr.appendChild(actionsTd);

  return tr;
}

function createTableCell(content) {
  const td = document.createElement("td");

  if (typeof content === "string") {
    td.textContent = content;
    return td;
  }

  td.appendChild(content);
  return td;
}

function createActionButton(href, text, classes) {
  const button = document.createElement("a");
  button.href = href;
  button.textContent = text;
  classes.forEach((cls) => button.classList.add(cls));
  return button;
}

async function searchMovieFromCatalog() {
  const searchInput = document.getElementById("search-movie");
  const searchValue = searchInput.value;
  const tbody = document.getElementById("tbody-catalog");
  const rows = tbody.getElementsByTagName("tr");

  for (let i = 0; i < rows.length; i++) {
    const title = rows[i].getElementsByTagName("td")[1];

    if (title) {
      const textValue = title.textContent || title.innerText;

      if (textValue.toLowerCase().indexOf(searchValue.toLowerCase()) > -1) {
        rows[i].style.display = "";
      } else {
        rows[i].style.display = "none";
      }
    }
  }
}

function sortTable(columnIndex) {
  const table = document.getElementById("table-catalog");
  const tbody = table.querySelector("tbody");
  const rows = Array.from(tbody.querySelectorAll("tr"));
  const isAscending = table.getAttribute("data-sort-order") === "asc";

  rows.sort((a, b) => {
    const aText = a.children[columnIndex].textContent.trim().toLowerCase();
    const bText = b.children[columnIndex].textContent.trim().toLowerCase();

    if (aText < bText) return isAscending ? -1 : 1;
    if (aText > bText) return isAscending ? 1 : -1;
    return 0;
  });

  table.setAttribute("data-sort-order", isAscending ? "desc" : "asc");

  rows.forEach((row) => tbody.appendChild(row));
}

function createFilmCoverCell(film) {
  const td = document.createElement("td");
  const img = document.createElement("img");

  img.src = film.cover;
  img.alt = film.title;
  img.width = 150; 
  img.height = 120; 

  img.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease'; 

  img.addEventListener('mouseover', () => {
    img.style.transform = 'scale(1.2)';  
    img.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.3)'; 
  });

  img.addEventListener('mouseout', () => {
    img.style.transform = 'scale(1)';  
    img.style.boxShadow = 'none';  
  });

  td.appendChild(img);
  return td;
}