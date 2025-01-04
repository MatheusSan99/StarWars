<?php
define('PROJECT_PUBLIC', './../../../public');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <link rel="stylesheet" href="<?php echo PROJECT_PUBLIC; ?>/css/reset.css">
    <link rel="stylesheet" href="<?php echo PROJECT_PUBLIC; ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo PROJECT_PUBLIC; ?>/css/now-ui-kit.css?v=1.3.0" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo PROJECT_PUBLIC; ?>/css/main.css">
    <?php if (isset($title)): ?>
        <title><?php echo $title; ?></title>
    <?php else: ?>
        <title>Star Wars</title>
    <?php endif; ?>
    <link rel="shortcut icon" href="<?php echo PROJECT_PUBLIC; ?>/img/icons/dark.png" type="image/x-icon">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg bg-primary fixed-top navbar-transparent " color-on-scroll="400">
            <div class="container">
                <div class="dropdown button-dropdown">
                    <a href="#" class="dropdown-toggle" id="navbarDropdown" data-toggle="dropdown">
                        <span class="button-bar"></span>
                        <span class="button-bar"></span>
                        <span class="button-bar"></span>
                    </a>
                    <a class="logo" href="/"></a>

                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-header">GitHub</a>
                        <a class="dropdown-item" target="_blank" href="https://github.com/MatheusSan99">Matheus
                            Oliveira</a>
                    </div>
                </div>
            </div>
            <?php if (!empty($_SESSION['logged'])): ?>
                <ul class="nav">
                <li class="nav-item" id="documentation">
                    <a class="nav-link" href="/pages/documentation">Documentação</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/pages/catalog">Lista de Filmes</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" style="cursor: pointer;" onclick="logout();">Sair</a>
                    </li>
                </ul>
            <?php endif; ?>
        </nav>
    </header>
    <?php if (isset($_SESSION['error_message']) || isset($_SESSION['success_message'])): ?>
        <div class="notification-container">
            <?php if (isset($_SESSION['error_message'])): ?>
                    <div id="errorAlert" class="alert alert-danger alert-dismissible fade show custom-alert" role="alert">
                        <ul>
                            <?php foreach ($_SESSION['error_message'] as $error): ?>
                                <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['success_message'])): ?>
                <div id="successAlert" class="alert alert-success alert-dismissible fade show custom-alert" role="alert">
                    <?= htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php unset($_SESSION['error_message'], $_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>