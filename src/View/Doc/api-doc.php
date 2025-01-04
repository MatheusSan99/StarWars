<?php require_once __DIR__ . './../template/ini-html.php'; ?>
<head>
    <title>Documentação da API</title>
    <link rel="stylesheet" type="text/css" href="<?php echo PROJECT_PUBLIC; ?>/css/doc/api-doc.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PROJECT_PUBLIC; ?>/css/doc/swagger-ui.css">
</head>
<div id="swagger-ui"></div>
<script src="<?php echo PROJECT_PUBLIC; ?>/js/doc/swagger-ui-bundle.js"></script>
<script src="<?php echo PROJECT_PUBLIC; ?>/js/doc/swagger-ui-standalone-preset.js"></script>
<script src="<?php echo PROJECT_PUBLIC; ?>/js/doc/api-doc.js"></script>
<?php require_once __DIR__ . './../template/end-html.php'; ?>