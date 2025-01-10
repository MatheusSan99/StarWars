<?php

require './../../vendor/autoload.php';

$curl = curl_init();
$tableID = 'tabelagtf';
$tablePrimaryKey = 'pv';
$GLOBALS['domain'] = 'https://isc.softexpert.com/';
$GLOBALS['dataset'] = 'apigateway/v1/dataset-integration/matheusteste';
$GLOBALS['authorization'] = 'eyJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MzYyNTU3MDAsImV4cCI6MTg5NDAyMjEwMCwiaWRsb2dpbiI6Im1hdGhldXMuc29saXZlaXJhIn0.B70gvuMLiQRVf3oA2k3ZZnaQn5Rl-38Fe57q7IcEDvQ';
$essentialFields = ['pv', 'imagem', 'nomepeca', 'datat0', 'cliente', 'transformador'];
$responseData = [];
$start_date = $_POST['periodo_inicio'] ?? '';
$end_date = $_POST['periodo_fim'] ?? '';
$selected_clients = $_POST['clientes'] ?? [];

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

class MYPDF extends \TCPDF
{
    // Método para o cabeçalho
    public function Header()
    {
        // Caminho da imagem do cabeçalho
        $image_file = __DIR__ . '/../../public/img/backgrounds/background-characters.jpg';
        
        // Verifica se a imagem existe antes de tentar carregá-la
        if (file_exists($image_file)) {
            $this->Image($image_file, 10, 10, 40, 15, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        } else {
            error_log("Imagem de cabeçalho não encontrada: $image_file");
        }
        
        // Define a fonte para o título no cabeçalho
        $this->SetFont('helvetica', 'B', 20);
        $this->SetXY(55, 10); // Ajusta a posição após a imagem
        $this->Cell(0, 15, 'Relatório de Moldes', 0, false, 'L', 0, '', 0, false, 'M', 'M');
    }

    // Método para o rodapé
    public function Footer()
    {
        // Posiciona 15 mm do final da página
        $this->SetY(-15);
        // Define a fonte para o rodapé
        $this->SetFont('helvetica', 'I', 8);
        // Exibe o número da página
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

function exportToPDF($data)
{
    $pdf = new MYPDF();

    // Configurações do PDF
    $pdf->SetCreator('Gestão de Moldes');
    $pdf->SetAuthor('Sistema');
    $pdf->SetTitle('Relatório de Moldes');
    $pdf->SetMargins(15, 27, 15); // Margens
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(true, 25);

    // Adiciona a página
    $pdf->AddPage();

    // Conteúdo principal do PDF
    $pdf->SetFont('helvetica', '', 12);
    $html = '<style>
                table {
                    border-collapse: collapse;
                    width: 100%;
                }
                th {
                    background-color: #007bff;
                    color: white;
                    text-align: center;
                }
                td {
                    text-align: center;
                }
                table, th, td {
                    border: 1px solid #ddd;
                }
            </style>';
    $html .= '<table>
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
            <td>' . htmlspecialchars($row['pv'] ?? '') . '</td>
            <td>';
        if (!empty($row['imagem'])) {
            $html .= '<img src="' . htmlspecialchars($row['imagem']) . '" width="50" height="50" />';
        }
        $html .= '</td>
            <td>' . htmlspecialchars($row['nomepeca'] ?? '') . '</td>
            <td>' . htmlspecialchars($row['datat0'] ?? '') . '</td>
            <td>' . htmlspecialchars($row['cliente'] ?? '') . '</td>
            <td>' . htmlspecialchars($row['transformador'] ?? '') . '</td>
        </tr>';
    }

    $html .= '</tbody></table>';

    $pdf->writeHTML($html, true, false, true, false, '');

    // Gera o PDF
    $pdf->Output('relatorio_moldes.pdf', 'D');
    exit;
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

function filterDataFromFrontend($fieldValue, $start_date = null, $end_date = null, $selected_clients = [])
{
    return array_filter($fieldValue, function ($item) use ($start_date, $end_date, $selected_clients) {
        $data_item = isset($item['datat0']) ? new DateTime($item['datat0']) : null;
        $start = $start_date ? new DateTime($start_date) : null;
        $end = $end_date ? new DateTime($end_date) : null;
        if ($start && $data_item < $start) return false;
        if ($end && $data_item > $end) return false;
        if (!empty($selected_clients) && !in_array($item['cliente'], $selected_clients)) return false;
        return true;
    });
}

try {
    $jsonResultsList = getDatasetResult();
    $fieldValues = [];
    foreach ($jsonResultsList as $jsonResult) {
        $fieldValues[$jsonResult[$tablePrimaryKey]] = getTableRecord($tableID, $tablePrimaryKey, $jsonResult);
    }
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
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" . <?=uniqid()?>>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" . <?=uniqid()?>>
    <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" . <?=uniqid()?>>
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

        #table thead {
            background-color: #007bff;
            color: white;
        }

        body {
            background: linear-gradient(to bottom, #f8f9fa, #e9ecef);
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 900px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
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
    </style>
</head>
<body>
    <div class="container">
        <h2 class="my-4 text-center">Gestão de Moldes</h2>
        <div class="card">
        <div class="card">
    <div class="card-body">
        <form method="post">
            <div class="row">
                <!-- Filtros ocupando 33% cada -->
                <div class="col-md-4">
                    <label for="periodo_inicio">Período de Início</label>
                    <input type="date" id="periodo_inicio" name="periodo_inicio" class="form-control datepicker"
                        value="<?= htmlspecialchars($start_date) ?>">
                </div>
                <div class="col-md-4">
                    <label for="periodo_fim">Período de Fim</label>
                    <input type="date" id="periodo_fim" name="periodo_fim" class="form-control datepicker"
                        value="<?= htmlspecialchars($end_date) ?>">
                </div>
                <div class="col-md-4">
                    <label for="clientes">Clientes</label>
                    <select id="clientes" name="clientes[]" class="form-control" multiple>
                        <?php
                        $clients = array_unique(array_column($fieldValues, 'cliente'));
                        foreach ($clients as $client): ?>
                            <option value="<?= htmlspecialchars($client) ?>" <?= in_array($client, $selected_clients) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($client) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-4">
                    <button type="submit" name="exibir_relatorio" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Exibir Relatório
                    </button>
                </div>
                <div class="col-md-4">
                    <button type="button" name="resetar_filtros" class="btn btn-danger w-100" onclick="reset_filters();">
                        <i class="fas fa-sync-alt"></i> Resetar Filtros
                    </button>
                </div>
                <div class="col-md-4">
                    <button type="submit" name="exportar_pdf" class="btn btn-danger w-100">
                        <i class="fas fa-file-pdf"></i> Exportar para PDF
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
            <?php if (!empty($filtered_data)): ?>
                <div class="table-responsive mt-4">
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
                            <?php foreach ($filtered_data as $data): ?>
                                <tr>
                                    <?php foreach ($essentialFields as $field): ?>
                                        <?php if ($field == 'imagem'): ?>
                                            <td>
                                                <?php if (!empty($data[$field])): ?>
                                                    <img src="<?= htmlspecialchars($data[$field]) ?>" class="img-fluid" style="max-width: 120px; max-height: 80px;">
                                                <?php endif; ?>
                                            </td>
                                        <?php else: ?>
                                            <td><?= htmlspecialchars($data[$field] ?? '') ?></td>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="mt-4 text-center">Nenhum dado encontrado com os filtros aplicados.</p>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
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
    </script>
</body>
</html>