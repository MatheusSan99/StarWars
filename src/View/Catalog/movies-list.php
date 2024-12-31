<?php

require_once __DIR__ . './../template/ini-html.php';

/** @var CatalogDTO $catalog */
?>

<body class="login-page sidebar-collapse">
  <div class="page-header clear-filter" filter-color="orange">
    <main class="container mt-5">
      <div class="card card-body text-black">
        <h2 class="title text-center"><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?></h2>
        <input type="hidden" name="operation" id="operation" value="news-list">
        <p class="card-category text-center">Visualiza os filmes</p>
        <div class="table-responsive">
          <table class="table">
            <thead class="text-primary">
              <tr>
                <th>Visualizar Filme</th>
                <th>Título</th>
                <th>Diretor</th>
                <th>Data de Lançamento</th>
                <th>Produtores</th>
                <th>Personagens</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($catalog->getFilms() as $film): ?>
                <tr>
                  <td>
                  <a href="film/<?= $film->getId(); ?>" class="btn btn-info btn-round btn-sm">Ver Filme</a>
                  </a>
                  </td>
                    <td><?= htmlspecialchars($film->getTitle(), ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($film->getDirector(), ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($film->getReleaseDate(), ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($film->getProducers(), ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                      <a href="caracthers-list?id=<?= $film->getId(); ?>" class="btn btn-info btn-round btn-sm">Visualizar</a>
                    </td>
                    <td>
                      <a href="/edit-film?id=<?= $film->getId(); ?>" class="btn btn-info btn-round btn-sm">Editar</a>
                      <a href="/remove-film?id=<?= $film->getId(); ?>" class="btn btn-danger btn-round btn-sm">Excluir</a>
                    </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

<?php require_once __DIR__ . './../template/end-html.php'; ?>