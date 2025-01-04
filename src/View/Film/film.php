<?php
require_once __DIR__ . './../template/ini-html.php';
?>
<header>
    <audio id="film-audio" src="<?php echo PROJECT_PUBLIC; ?>/audio/star-wars-theme.mp4" preload="auto"></audio>
    <link rel="stylesheet" href="<?php echo PROJECT_PUBLIC; ?>/css/film/film.css">
</header>
<div class="login-page sidebar-collapse">
    <div class="page-header clear-filter" filter-color="orange">
        <button id="startButton" class="start-button" onclick="startFilm();" disabled>Aguarde...</button>

        <main id="main-content" style="display: none;">
            <canvas id="canvas"></canvas>
            <div id="crawl">
                <p id="movieData">
                    <span id="episode-name"></span>
                </p>
            </div>
            <div id="overlay"></div>
        </main>

        <div class="movie-info-container" style="display: none;">
            <div class="movie-info-card">
                <div class="movie-info"></div>
            </div>
            <button id="restartButton" onclick="restartMovie()">Reiniciar Filme</button>
        </div>
    </div>
    <script src="<?php echo PROJECT_PUBLIC; ?>/js/film/film.js"></script>
    <?php require_once __DIR__ . './../template/end-html.php'; ?>
</div>