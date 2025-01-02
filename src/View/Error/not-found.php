<?php require_once __DIR__ . './../template/ini-html.php'; ?>
<!DOCTYPE html>

<body class="error-page sidebar-collapse">
  <div class="page-header clear-filter" filter-color="orange">
    <div class="page-header-image" style="background-image: url('/assets/img/404-background.jpg');"></div>
    <div class="content">
      <div class="container">
        <div class="col-md-6 ml-auto mr-auto text-center">
          <div class="card card-plain">
            <div class="card-body">
              <h1 class="display-1 text-danger">404</h1>
              <h3 class="text-uppercase">Página Não Encontrada</h3>
              <p class="lead">Desculpe, a página que você está tentando acessar não existe ou foi movida.</p>
            </div>
            <div class="card-footer text-center">
              <a href="/" class="btn btn-primary btn-round btn-lg btn-block">Voltar para a Página Inicial</a>
              <div class="pull-mid">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php require_once __DIR__ . './../template/end-html.php'; ?>
