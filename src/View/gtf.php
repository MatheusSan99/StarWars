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
        displayError('Erro ao buscar os dados: ' . $e->getMessage());
    }
    return json_decode($jsonResultsList, true);
}

function displayError($message)
{
    echo '<div style="color: white; background-color: red; padding: 10px; margin: 10px 0; border-radius: 5px;">';
    echo htmlspecialchars($message);
    echo '</div>';
    exit;
}

function makeCurlRequest($endpoint, $headers = [], $postFields = null, $method = 'GET')
{
    $curl = curl_init();

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
    curl_close($curl);

    return $response;
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
        displayError('Erro ao buscar os dados da tabela: ' . $tableID);
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
        displayError('Erro ao buscar os dados da tabela: ' . $tableID);
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
    </style>
</head>

<body>
    <div class="container">
        <h2 class="my-4 text-center">Gestão de Moldes</h2>
        <div class="card">
            <div class="card-body">
                <form method="post" id="filters-form">
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
                            <button type="button" name="exibir_relatorio" class="btn btn-primary btn-block" onclick="applyFilters();">
                                <i class="fas fa-search"></i> Exibir Relatório
                            </button>
                        </div>
                        <div class="col-12 col-md-3 mb-2">
                            <button type="button" name="resetar_filtros" class="btn btn-danger btn-block" onclick="reset_filters();">
                                <i class="fas fa-sync-alt"></i> Resetar Filtros
                            </button>
                        </div>
                        <div class="col-12 col-md-3 mb-2">
                            <button type="button" name="exportar_pdf" class="btn btn-danger btn-block" onclick="exportToPDF();">
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

        document.addEventListener('DOMContentLoaded', function() {
            var datepickers = document.querySelectorAll('.datepicker');
            datepickers.forEach(function(datepicker) {
                datepicker.type = 'date';
            });
            loadData();
        });

        function loadClients(clients) {
            const uniqueClients = [...new Set(clients)];

            let select = document.getElementById('clientes');
            select.innerHTML = '';
            uniqueClients.forEach(client => {
                let option = document.createElement('option');
                option.value = client;
                option.textContent = client;
                select.appendChild(option);
            });
        }

        function loadData() {
            const form = document.getElementById('filters-form');
            const formData = new FormData(form);
            const queryString = new URLSearchParams(formData).toString();

            const cache = localStorage.getItem('filtered_data');
            const cacheTimestamp = localStorage.getItem('cache_timestamp');
            const currentTime = new Date().getTime();

            if (cache && cacheTimestamp && (currentTime - cacheTimestamp < 24 * 60 * 60 * 1000)) {
                const filteredData = JSON.parse(cache);
                totalPages = Math.ceil(filteredData.length / recordsPerPage);
                displayTableData(filteredData);
                loadClients(filteredData.map(item => item.cliente));
                return;
            }

            let xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    try {
                        const filteredData = JSON.parse(xhr.responseText);
                        totalPages = Math.ceil(filteredData.length / recordsPerPage);
                        localStorage.setItem('filtered_data', JSON.stringify(filteredData));
                        localStorage.setItem('cache_timestamp', currentTime);
                        displayTableData(filteredData);
                        loadClients(filteredData.map(item => item.cliente));
                    } catch (e) {
                        console.error('Erro ao processar a resposta JSON:', e);
                    }
                }
            };

            xhr.send(queryString);
        }

        function displayTableData(filteredData) {
            const container = document.getElementById('table-container');
            if (!filteredData || filteredData.length === 0) {
                container.innerHTML = '<p class="text-center">Nenhum dado encontrado com os filtros aplicados.</p>';
                return;
            }

            const startIndex = (currentPage - 1) * recordsPerPage;
            const endIndex = startIndex + recordsPerPage;
            const pageData = filteredData.slice(startIndex, endIndex);

            let tableHTML = `
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

            pageData.forEach(item => {
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
            });

            tableHTML += '</tbody></table></div>';
            container.innerHTML = tableHTML;

            updatePaginationControls();
        }

        function updatePaginationControls() {
            document.getElementById('page-info').textContent = `Página ${currentPage} de ${totalPages}`;
            document.getElementById('prev-page').disabled = currentPage === 1;
            document.getElementById('next-page').disabled = currentPage === totalPages;
        }

        function changePage(direction) {
            currentPage += direction;
            currentPage = Math.max(1, Math.min(currentPage, totalPages));
            loadData();
        }

        function resetCache() {
            localStorage.removeItem('filtered_data');
            localStorage.removeItem('cache_timestamp');
            loadData();
        }

        function applyFilters() {
            const start_date = document.getElementById('start_date').value;
            const end_date = document.getElementById('end_date').value;
            const clientes = Array.from(document.getElementById('clientes').selectedOptions).map(option => option.value);
            const cache = localStorage.getItem('filtered_data');

            if (cache) {
                const filteredData = JSON.parse(cache);
                const filteredDataWithFilters = filteredData.filter(item => {
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

                totalPages = Math.ceil(filteredDataWithFilters.length / recordsPerPage);
                displayTableData(filteredDataWithFilters);
            }
        }

        function reset_filters() {
            document.getElementById('filters-form').reset();
            loadData();
        }

        function exportToPDF() {
            const filename = 'gtf - ' + new Date().toLocaleDateString() + '.pdf';
            const container = document.getElementById('table-container');
            // const companyLogoURL = './main-background.jpg';
            const companyLogoURL = document.querySelector('img').src;
            const pdfTitle = 'Relatório de Gestão de Moldes';
            const additionalInfo = 'Data: ' + new Date().toLocaleDateString();

            if (container) {
                const options = {
                    margin: [20, 10, 20, 10],
                    filename: filename,
                    image: {
                        type: 'jpeg',
                        quality: 0.98
                    },
                    html2canvas: {
                        scale: 2
                    },
                    jsPDF: {
                        unit: 'mm',
                        format: 'a4',
                        orientation: 'portrait'
                    }
                };

                var worker = html2pdf()
                    .set(options)
                    .from(container)
                    .toPdf()
                    .get('pdf')
                    .then(function(pdf) {
                        const totalPages = pdf.internal.getNumberOfPages();
                        const pageWidth = pdf.internal.pageSize.getWidth();
                        const pageHeight = pdf.internal.pageSize.getHeight();
                        const logoWidth = 40;
                        const logoHeight = 15;
                        const headerHeight = 30;
                        const contentStartY = headerHeight + 10;

                        for (let i = 1; i <= totalPages; i++) {
                            pdf.setPage(i);

                            pdf.addImage(companyLogoURL, 'PNG', 10, 10, logoWidth, logoHeight);

                            pdf.setFontSize(14);
                            pdf.setTextColor(40);
                            pdf.text(additionalInfo, pageWidth - 10, 15, {
                                align: 'right'
                            });

                            if (i === 1) {
                                pdf.setFontSize(14);
                                pdf.setTextColor(40);
                                pdf.text(pdfTitle, pageWidth / 2, 15, {
                                    align: 'center'
                                });
                            }

                            const pageNumber = `Página ${i} de ${totalPages}`;
                            pdf.setFontSize(10);
                            pdf.setTextColor(100);
                            pdf.text(pageNumber, pageWidth / 2, pageHeight - 10, {
                                align: 'center'
                            });
                        }
                    }).save(filename);
            }
        }
    </script>
</body>

</html>