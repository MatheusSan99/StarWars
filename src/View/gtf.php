<?php
require 'vendor/autoload.php';

$curl = curl_init();
$tableID = 'tabelagtf';
$actualPage = 1;
$totalPages = 1;
$GLOBALS['domain'] = 'https://isc.softexpert.com/';
$GLOBALS['authorization'] = 'eyJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MzYyNTU3MDAsImV4cCI6MTg5NDAyMjEwMCwiaWRsb2dpbiI6Im1hdGhldXMuc29saXZlaXJhIn0.B70gvuMLiQRVf3oA2k3ZZnaQn5Rl-38Fe57q7IcEDvQ';
$essentialFields = ['PV', 'NOMEPECA', 'DATAT0', 'CLIENTE', 'TRANSFORMADOR', 'imagem'];
$responseData = [];
$start_date = $_POST['periodo_inicio'] ?? '';
$end_date = $_POST['periodo_fim'] ?? '';
$selected_clients = $_POST['clientes'] ?? [];

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

function exportToPDF($data)
{
    $pdf = new \TCPDF();

    $pdf->SetCreator('Gestão de Moldes');
    $pdf->SetAuthor('Sistema');
    $pdf->SetTitle('Relatório de Moldes');
    $pdf->SetHeaderData('', 0, 'Relatório de Moldes', 'Exportado em: ' . date('d/m/Y'));
    $pdf->setHeaderFont(['helvetica', '', 10]);
    $pdf->setFooterFont(['helvetica', '', 8]);
    $pdf->SetMargins(15, 27, 15);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(true, 25);

    $pdf->AddPage();

    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Relatório de Moldes', 0, 1, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('helvetica', '', 12);

    $html = '<table border="1" cellspacing="3" cellpadding="4">
        <thead>
            <tr>
                <th>PV</th>
                <th>Imagem</th>
                <th>Nome da Peça</th>
                <th>Data T0</th>
                <th>Cliente</th>
                <th>Transformador</th>
            </tr>
        </thead>
        <tbody>';

    foreach ($data as $row) {
        $html .= '<tr>
            <td>' . htmlspecialchars($row['PV'] ?? '') . '</td>
            <td>';
        if (!empty($row['imagem'])) {
            $html .= '<img src="' . htmlspecialchars($row['imagem']) . '" width="50" height="50" />';
        }
        $html .= '</td>
            <td>' . htmlspecialchars($row['NOMEPECA'] ?? '') . '</td>
            <td>' . htmlspecialchars($row['DATAT0'] ?? '') . '</td>
            <td>' . htmlspecialchars($row['CLIENTE'] ?? '') . '</td>
            <td>' . htmlspecialchars($row['TRANSFORMADOR'] ?? '') . '</td>
        </tr>';
    }

    $html .= '</tbody></table>';

    $pdf->writeHTML($html, true, false, true, false, '');

    $pdf->Output('relatorio_moldes.pdf', 'D');
    exit;
}

function getImageContent($fileHash)
{
    $imageData = makeCurlRequest('apigateway/v1/file/' . $fileHash, ['Authorization: ' . $GLOBALS['authorization']]);
    return 'data:image/png;base64,' . base64_encode($imageData);
}

function getTableRecord($tableID, $actualPage)
{
    $headers = [
        'Content-Type: text/xml; charset=utf-8',
        'SOAPAction: urn:form#getTableRecord',
        'Authorization: ' . $GLOBALS['authorization']
    ];

    $postFields = '<?xml version="1.0" encoding="utf-8"?>
    <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
      <soap:Body>
        <tns:getTableRecord xmlns="urn:form">
          <tns:TableID>' . $tableID . '</tns:TableID>
          <tns:Pagination>' . $actualPage . '</tns:Pagination>
        </tns:getTableRecord>
      </soap:Body>
    </soap:Envelope>';

    try {
        $xmlString = makeCurlRequest('apigateway/se/ws/fm_ws.php', $headers, $postFields, 'POST');
    } catch (Exception $e) {
        displayError('Erro ao buscar os dados da tabela: ' . $tableID);
    }

    $xmlString = makeCurlRequest('apigateway/se/ws/fm_ws.php', $headers, $postFields, 'POST');
    $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);
    if ($xml === false) {
        displayError('Erro ao processar os dados da tabela: ' . $tableID);
    }
    return $xml;
}

function formatTableRecord($xml, $essentialFields)
{
    $namespaces = $xml->getNamespaces(true);
    $body = $xml->children($namespaces['SOAP-ENV'])->Body;
    $response = $body->children($namespaces[''])->getTableRecordResponse;
    $fieldValues = [];

    foreach ($response->RecordList->Record as $record) {
        $oid = null;
        $recordData = [];

        foreach ($record->FieldList->TableField as $field) {
            $fieldId = (string)$field->TableFieldID;
            $fieldValue = (string)$field->TableFieldValue;

            if ($fieldId === 'OID') {
                $oid = $fieldValue;
            }

            if (in_array($fieldId, $essentialFields)) {
                $recordData[$fieldId] = $fieldValue;
            }
        }

        if (empty($oid)) {
            continue;
        }

        if (isset($record->FileFieldList->FileField)) {
            foreach ($record->FileFieldList->FileField as $fileField) {
                if ((string)$fileField->FieldID === 'imagem') {
                    $recordData['imagem'] = getImageContent((string)$fileField->FileHash);
                }
            }
        }
        $fieldValues[$oid] = $recordData;
    }
    return $fieldValues;
}

function filterDataFromFrontend($fieldValue, $start_date = null, $end_date = null, $selected_clients = [])
{
    return array_filter($fieldValue, function ($item) use ($start_date, $end_date, $selected_clients) {
        $data_item = isset($item['DATAT0']) ? new DateTime($item['DATAT0']) : null;
        $start = $start_date ? new DateTime($start_date) : null;
        $end = $end_date ? new DateTime($end_date) : null;
        if ($start && $data_item < $start) return false;
        if ($end && $data_item > $end) return false;
        if (!empty($selected_clients) && !in_array($item['CLIENTE'], $selected_clients)) return false;
        return true;
    });
}

try {
    $xml = getTableRecord($tableID, $actualPage);
    $fieldValues = formatTableRecord($xml, $essentialFields);
    $filtered_data = filterDataFromFrontend($fieldValues, $start_date, $end_date, $selected_clients);
} catch (Exception $e) {
    displayError('Erro ao buscar os dados da tabela: ' . $tableID);
}
if (isset($_POST['exportar_pdf'])) {
    exportToPDF($filtered_data);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Moldes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
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

        .table thead {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="my-4 text-center">Gestão de Moldes</h2>

        <div class="card-body">
    <form method="post">
        <div class="row">
            <div class="col-md-3">
                <label>Período de Início</label>
                <input type="date" name="periodo_inicio" class="form-control datepicker" value="<?= htmlspecialchars($start_date) ?>">
            </div>
            <div class="col-md-3">
                <label>Período de Fim</label>
                <input type="date" name="periodo_fim" class="form-control datepicker" value="<?= htmlspecialchars($end_date) ?>">
            </div>
            <div class="col-md-3">
                <label>Clientes</label>
                <select name="clientes[]" class="form-control" multiple>
                    <?php
                    $clients = array_unique(array_column($fieldValues, 'CLIENTE'));
                    foreach ($clients as $client): ?>
                        <option value="<?= htmlspecialchars($client) ?>" <?= in_array($client, $selected_clients) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($client) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Botões lado a lado -->
        <div class="mt-3 d-flex gap-2">
            <button type="button" name="resetar_filtros" class="btn btn-danger">
                <i class="fas fa-sync-alt"></i> Resetar Filtros
            </button>
            <button type="submit" name="exibir_relatorio" class="btn btn-primary">
                <i class="fas fa-search"></i> Exibir Relatório
            </button>
            <button type="submit" name="exportar_pdf" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Exportar para PDF
            </button>
        </div>
    </form>
</div>

        <?php if (!empty($filtered_data)): ?>
            <div class="table-responsive mt-4">
                <table class="table table-striped table-hover text-center">
                    <thead>
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
                        <?php foreach ($filtered_data as $data): ?>
                            <tr>
                                <td><?= htmlspecialchars($data['PV'] ?? '') ?></td>
                                <td>
                                    <?php if (!empty($data['imagem'])): ?>
                                        <img src="<?= htmlspecialchars($data['imagem']) ?>" class="img-fluid" style="max-width: 150px; max-height: 150px;">
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($data['NOMEPECA'] ?? '') ?></td>
                                <td><?= htmlspecialchars($data['DATAT0'] ?? '') ?></td>
                                <td><?= htmlspecialchars($data['CLIENTE'] ?? '') ?></td>
                                <td><?= htmlspecialchars($data['TRANSFORMADOR'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="mt-4 text-center">Nenhum dado encontrado com os filtros aplicados.</p>
        <?php endif; ?>
    </div>

    <script>
        $(function() {
            $(".datepicker").datepicker({
                dateFormat: 'yy-mm-dd'
            });
        });

        function reset_filters() {
            $('input[type="date"]').val('');
            $('select').val('');
        }

        function showLoader() {
            document.getElementById('loading-spinner').style.display = 'block';
        }
    </script>
</body>

</html>