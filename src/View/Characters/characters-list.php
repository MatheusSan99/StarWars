<?php
require_once __DIR__ . './../template/ini-html.php';
?>

<header>
  <link rel="stylesheet" href="<?php echo PROJECT_PUBLIC; ?>/css/characters/characters-list.css">
</header>

<div class="carousel-container">
</div>

<button class="carousel-button carousel-button-left" onclick="scrollCarousel(-1)">❮</button>
<button class="carousel-button carousel-button-right" onclick="scrollCarousel(1)">❯</button>
<script src="<?php echo PROJECT_PUBLIC; ?>/js/characters/characters-list.js"></script>
<?php require_once __DIR__ . './../template/end-html.php'; ?>
