<?php
require_once __DIR__ . './../template/ini-html.php';
?>
<header>
    <audio id="film-audio" src="<?php echo PROJECT_PUBLIC; ?>/audio/star-wars-theme.mp4" preload="auto"></audio>
    <script src="<?php echo PROJECT_PUBLIC; ?>/js/film/film.js"></script>
    <link rel="stylesheet" href="<?php echo PROJECT_PUBLIC; ?>/css/stars.css">
</header>

<body class="login-page sidebar-collapse">
    <div class="page-header clear-filter" filter-color="orange">
        <button id="startButton" class="start-button" onclick="startFilm();">Iniciar</button>
        
        <main id="main-content" style="display: none;">
            <canvas id="field"></canvas>
            <div id="crawl">
                <p id="movieData">
                    <span id="episode-name"></span>
                </p>
            </div>
            <div id="overlay"></div>
        </main>
    </div>

    <?php require_once __DIR__ . './../template/end-html.php'; ?>
</body>
