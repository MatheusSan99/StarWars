<?php

// require './../../vendor/autoload.php';
require 'vendor/autoload.php';

$curl = curl_init();
$tableID = 'tabelagtf';
$tablePrimaryKey = 'pv';
$GLOBALS['domain'] = 'https://isc.softexpert.com/';
$GLOBALS['dataset'] = 'apigateway/v1/dataset-integration/matheusteste';
$GLOBALS['authorization'] = 'eyJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MzYyNTU3MDAsImV4cCI6MTg5NDAyMjEwMCwiaWRsb2dpbiI6Im1hdGhldXMuc29saXZlaXJhIn0.B70gvuMLiQRVf3oA2k3ZZnaQn5Rl-38Fe57q7IcEDvQ';
$essentialFields = ['pv', 'imagem', 'nomepeca', 'datat0', 'cliente', 'transformador'];
$responseData = [];

function getDatasetResult()
{
    $jsonResultsList = '';
    try {
        $jsonResultsList = makeCurlRequest($GLOBALS['dataset'], [
            'Authorization: ' . $GLOBALS['authorization']
        ]);
    } catch (Exception $e) {
        displayError('Erro ao buscar os dados: ' . $e->getMessage(), $e->getCode());
    }
    return json_decode($jsonResultsList, true);
}

function displayError($message, $code = 400)
{
    echo json_encode(['error' => $message, 'code' => $code]);
    exit;
}

function makeCurlRequest($endpoint, $headers = [], $postFields = null, $method = 'GET')
{
    try {
        $curl = curl_init();

        if ($curl === false) {
            displayError('Ocorreu um erro ao tentar buscar os dados. Por favor, tente novamente mais tarde.', 500);
        }

        $options = [
            CURLOPT_URL => $GLOBALS['domain'] . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => $headers,
        ];

        if ($method == 'POST' && $postFields !== null) {
            $options[CURLOPT_CUSTOMREQUEST] = 'POST';
            $options[CURLOPT_POSTFIELDS] = $postFields;
        }

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);

        if ($response === false) {
            curl_close($curl);
            displayError('Ocorreu um erro ao tentar buscar os dados. Por favor, tente novamente mais tarde.', 500);
        }

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode < 200 || $httpCode >= 300) {
            displayError('Ocorreu um erro ao tentar buscar os dados. Por favor, tente novamente mais tarde.', 500);
        }

        return $response;
    } catch (Exception $e) {
        displayError('Ocorreu um erro ao tentar buscar os dados. Por favor, tente novamente mais tarde.', 500);
    }
}

function getImageContent($fileHash)
{
    $imageData = makeCurlRequest('apigateway/v1/file/' . $fileHash, ['Authorization: ' . $GLOBALS['authorization']]);
    return 'data:image/png;base64,' . base64_encode($imageData);
}

function getTableRecord($tableID, $tablePrimaryKey, $jsonResponse)
{
    $postFields = '<?xml version="1.0" encoding="utf-8"?>
    <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <tns:getTableRecord xmlns="urn:form">
            <tns:TableID>' . $tableID . '</tns:TableID>
            <tns:Pagination>1</tns:Pagination>
            <tns:TableFieldList>
                <tns:TableField>
                    <tns:TableFieldID>' . $tablePrimaryKey . '</tns:TableFieldID>
                    <tns:TableFieldValue>' . $jsonResponse[$tablePrimaryKey] . '</tns:TableFieldValue>
                </tns:TableField>
            </tns:TableFieldList>
            </tns:getTableRecord>
        </soap:Body>
    </soap:Envelope>';

    $headers = [
        'Content-Type: text/xml; charset=utf-8',
        'SOAPAction: urn:form#getTableRecord',
        'Authorization: ' . $GLOBALS['authorization']
    ];

    try {
        $xmlString = makeCurlRequest('apigateway/se/ws/fm_ws.php', $headers, $postFields, 'POST');
        $xml = new SimpleXMLElement($xmlString);
        $xml->registerXPathNamespace('SOAP-ENV', 'http://schemas.xmlsoap.org/soap/envelope/');
        $xml->registerXPathNamespace('form', 'urn:form');
        $fileHashNodes = $xml->xpath('//form:FileFieldList/form:FileField/form:FileHash');
        $imageHash = (string)$fileHashNodes[0];
        $jsonResponse['imagem'] = getImageContent($imageHash);
        return $jsonResponse;
    } catch (Exception $e) {
        displayError('Erro ao buscar os dados da tabela: ' . $tableID, $e->getCode());
    }
}

function loadData($tableID, $tablePrimaryKey)
{
    try {
        $jsonResultsList = getDatasetResult();
        $data = [];
        foreach ($jsonResultsList as $jsonResult) {
            $data[$jsonResult[$tablePrimaryKey]] = getTableRecord($tableID, $tablePrimaryKey, $jsonResult);
        }

        echo json_encode($data);
        exit;
    } catch (Exception $e) {
        displayError('Erro ao buscar os dados da tabela: ' . $tableID, $e->getCode());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    loadData($tableID, $tablePrimaryKey);
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Gestão de Moldes</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" . <?= uniqid() ?>>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" . <?= uniqid() ?>>
    <style>
        #alert {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            padding: 15px;
            font-size: 16px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        body {
            background: linear-gradient(to bottom, #f8f9fa, #e9ecef);
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 50px;
        }

        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 100%;
            max-width: 1200px;
        }

        .form-row {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .btn {
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3 !important;
        }

        #table thead {
            background-color: #007bff;
            color: white;
        }

        .card-body label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .btn {
            min-width: 150px;
        }

        .gap-3 {
            gap: 15px !important;
        }

        .pagination {
            justify-content: center;
        }

        .dots-loader {
            display: flex;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
        }

        .dot {
            width: 15px;
            height: 15px;
            margin: 0 5px;
            background-color: #007bff;
            border-radius: 50%;
            animation: bounce 0.8s infinite ease-in-out;
        }

        .dot:nth-child(1) {
            animation-delay: 0s;
        }

        .dot:nth-child(2) {
            animation-delay: 0.3s;
        }

        .dot:nth-child(3) {
            animation-delay: 0.6s;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        #overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9998;
            display: none;
        }
    </style>
</head>

<body>
    <div id="overlay" style="display: none;"></div>
    <div class="container">
        <h2 class="my-4 text-center">Gestão de Moldes</h2>
        <div class="card">
            <div class="card-body">
                <form method="post" id="filters-form">
                    <div id="alert" role="alert" style="display: none; position: fixed; top: 20px; right: 20px; z-index: 1050; min-width: 300px; max-width: 400px;">
                        <span id="alert-message"></span>
                    </div>
                    <div class="dots-loader" id="loader" style="display: none;">
                        <div class="dot"></div>
                        <div class="dot"></div>
                        <div class="dot"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <label for="start_date">Período de Início</label>
                            <input type="date" id="start_date" name="start_date" class="form-control datepicker">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date">Período de Fim</label>
                            <input type="date" id="end_date" name="end_date" class="form-control datepicker">
                        </div>
                        <div class="col-md-4">
                            <label for="clientes">Clientes</label>
                            <select id="clientes" name="clientes[]" class="form-control" multiple>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12 col-md-3 mb-2">
                            <button type="button" class="btn btn-primary btn-block" onclick="applyFilters();">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                        </div>
                        <div class="col-12 col-md-3 mb-2">
                            <button type="button" class="btn btn-danger btn-block" onclick="reset_filters();">
                                <i class="fas fa-sync-alt"></i> Resetar Filtros
                            </button>
                        </div>
                        <div class="col-12 col-md-3 mb-2">
                            <button type="button" class="btn btn-danger btn-block" onclick="exportToPDF();">
                                <i class="fas fa-file-pdf"></i> Exportar para PDF
                            </button>
                        </div>
                        <div class="col-12 col-md-3 mb-2">
                            <button type="button" class="btn btn-warning btn-block" id="reset-cache" onclick="event.preventDefault(); resetCache();">
                                <i class="fas fa-trash-alt"></i> Limpar Cache
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div id="table-container" class="mt-4">
        </div>
        <div id="pagination-container" class="mt-4 text-center">
            <button id="prev-page" class="btn btn-secondary" onclick="changePage(-1)">Anterior</button>
            <span id="page-info">Página 1</span>
            <button id="next-page" class="btn btn-secondary" onclick="changePage(1)">Próxima</button>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentPage = 1;
        let totalPages = 1;
        const recordsPerPage = 5;

        document.addEventListener("DOMContentLoaded", async function() {
            var datepickers = document.querySelectorAll(".datepicker");
            datepickers.forEach(function(datepicker) {
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
                const filteredData = Array.isArray(cachedData) ?
                    cachedData :
                    Object.values(cachedData);

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

                const filteredData = Array.isArray(responseData) ?
                    responseData :
                    Object.values(responseData);

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
                } else if (index % 8 === 0) {
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
                image: {
                    type: "jpeg",
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2
                },
                jsPDF: {
                    unit: "mm",
                    format: "a4",
                    orientation: "portrait"
                },
                enableHTML: true,
                pagebreak: {
                    before: '.page-break',
                    avoid: ['table', 'img']
                },
            };

            html2pdf()
                .set(options)
                .from(container)
                .toPdf()
                .get("pdf")
                .then(function(pdf) {
                    const totalPages = pdf.internal.getNumberOfPages();
                    const pageWidth = pdf.internal.pageSize.getWidth();
                    const pageHeight = pdf.internal.pageSize.getHeight();

                    for (let i = 1; i <= totalPages; i++) {
                        pdf.setPage(i);
                        pdf.setFontSize(10);
                        pdf.text(
                            `Página ${i} de ${totalPages}`,
                            pageWidth / 2,
                            pageHeight - 10, {
                                align: "center"
                            }
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
    </script>
</body>
</html>