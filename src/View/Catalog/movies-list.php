<?php
require_once __DIR__ . './../template/ini-html.php';
?>
<header>
  <link rel="stylesheet" href="<?php echo PROJECT_PUBLIC; ?>/css/movies/movies-list.css">
</header>
  <div class="page-header clear-filter" filter-color="orange">
    <main class="container mt-5">
      <div class="card card-body text-black">
        <h2 class="title text-center"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h2>
        <input type="hidden" name="operation" id="operation" value="news-list">
        <div class="mb-3">
          <input
            type="text"
            id="search-movie"
            class="form-control"
            placeholder="Pesquisar (Nome do Filme, Diretor, Produtores, Data de Lançamento)" onkeyup="searchMovieFromCatalog()">
        </div>
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="table-catalog">
            <thead class="text-primary" id="thead-catalog">
            </thead>
            <tbody id="tbody-catalog">
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
  <script src="<?php echo PROJECT_PUBLIC; ?>/js/catalog/movies-list.js"></script>

  <?php require_once __DIR__ . './../template/end-html.php'; ?>