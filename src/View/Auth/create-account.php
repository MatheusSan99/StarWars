<?php require_once __DIR__ . './../template/ini-html.php'; ?>
<!DOCTYPE html>

<body class="login-page sidebar-collapse" id="body-register">
  <div class="page-header clear-filter" filter-color="orange">
    <div class="page-header-image"></div>
    <div class="content">
      <div class="container">
        <div class="col-md-4 ml-auto mr-auto">
          <div class="card card-login card-plain">
            <form class="form" method="post" id="register-form">
              <div class="card-body">
                <div class="input-group no-border input-lg">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="now-ui-icons users_circle-08"></i>
                    </span>
                  </div>
                  <input type="text" name="name" class="form-control" placeholder="Insira seu nome">
                </div>
                <div class="input-group no-border input-lg">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="now-ui-icons ui-1_email-85"></i>
                    </span>
                  </div>
                  <input type="email" name="email" class="form-control" placeholder="Insira seu e-mail">
                </div>
                <div class="input-group no-border input-lg">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="now-ui-icons ui-1_lock-circle-open"></i>
                    </span>
                  </div>
                  <input type="password" name="password" class="form-control" placeholder="Digite sua senha">
                </div>
                <div class="input-group no-border input-lg">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="now-ui-icons ui-1_lock-circle-open"></i>
                    </span>
                  </div>
                  <input type="password" name="confirm_password" class="form-control" placeholder="Confirme sua senha">
                </div>
              </div>
              <div class="card-footer text-center">
                <button class="btn btn-primary btn-round btn-lg btn-block" type="submit" value="Registrar" onclick="register();">Registrar</button>
                <div class="pull-mid">
                  <h6>
                    <a href="/pages/login" class="link">Já tem uma conta? Faça login</a>
                  </h6>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php require_once __DIR__ . './../template/end-html.php'; ?>