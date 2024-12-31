<?php

require_once __DIR__ . './../template/ini-html.php';

/** @var FilmDTO $film */
?>

<header>
    <link rel="stylesheet" href="<?php echo PROJECT_PUBLIC; ?>/css/stars.css">
    <script src="<?php echo PROJECT_PUBLIC; ?>/js/stars.js"></script>
</header>

<body class="login-page sidebar-collapse">
  <div class="page-header clear-filter" filter-color="orange">
  <main>
        <canvas id="field"></canvas>
        <div id="crawl">
            <p>
                <span><?=$episode?></span>
                <br /><br />
                <?=$film->getTitle();?>
                <br /><br />
                
                <?php
                $opening_crawl = $film->getOpeningCrawl();

                $opening_crawl = explode("\n", $opening_crawl);

                foreach ($opening_crawl as $line) {
                    echo $line . '<br />';
                }
                ?>
            </p>
        </div>
        <div id="overlay"></div>
    </main>
  </div>

<?php require_once __DIR__ . './../template/end-html.php'; ?>