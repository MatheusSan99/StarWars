<?php require_once __DIR__ . './../template/ini-html.php'; ?>
<!DOCTYPE html>

<body class="login-page sidebar-collapse">
  <div class="page-header clear-filter" filter-color="orange">
    <div class="page-header-image"></div>
    <div class="content">
      <div class="container">
        <div class="col-md-4 ml-auto mr-auto">
          <div class="card card-login card-plain">
            <form class="form" method="post">
                <input type="hidden" name="operation" id="operation" value="login">
              <div class="card-body">
                <div class="input-group no-border input-lg">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="now-ui-icons users_circle-08"></i>
                    </span>
                  </div>
                  <input type="email" name="email" class="form-control" placeholder="Insira o e-mail">
                </div> 
                <div class="input-group no-border input-lg">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="now-ui-icons ui-1_lock-circle-open"></i>
                    </span>
                  </div>
                  <input type="password" name="password" placeholder="Digite sua senha" class="form-control" />
                </div>
              </div>
              <div class="card-footer text-center">
                <button class="btn btn-primary btn-round btn-lg btn-block" type="submit" value="Entrar">Logar</button>
                <div class="pull-mid">
                  <h6>
                    <a href="/create-account" class="link">Criar Conta</a>
                  </h6>
                </div>
            </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php require_once __DIR__ . './../template/end-html.php'; ?>