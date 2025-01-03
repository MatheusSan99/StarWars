<?php require_once __DIR__ . './../template/ini-html.php'; ?>
<head>
    <title>Documentação da API</title>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/3.52.0/swagger-ui.css" >
   
</head>
<body>
    <div id="swagger-ui"></div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/3.52.0/swagger-ui-bundle.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/3.52.0/swagger-ui-standalone-preset.js"></script>
  
    <script src="<?php echo PROJECT_PUBLIC; ?>/js/doc/api-doc.js"></script>
</body>
<?php require_once __DIR__ . './../template/end-html.php'; ?>
