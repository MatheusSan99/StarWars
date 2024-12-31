document.addEventListener("DOMContentLoaded", async function () {
    await loadFilm();
});

async function loadFilm() {
    const host = window.location.origin;
    const url = new URL(window.location.href);
    const filmId = url.searchParams.get("id");

    try {
        const response = await fetch(`${host}/api/external/film/${filmId}`);

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const film = await response.json();
        await buildFilm(film);
    } catch (error) {
        console.error("Failed to load film:", error);
    }
}

function startFilm() {
    const btn = document.getElementById("startButton");

    btn.classList.add("hidden");

    setTimeout(() => {
        btn.style.visibility = "hidden";
    }, 2000); 
    document.getElementById("main-content").style.display = "block";
    
    const filmAudio = document.getElementById("film-audio");
    filmAudio.play().catch((error) => {
        console.error("Erro ao iniciar o áudio:", error);
    });

    animateOpeningCrawl();
}

async function buildFilm(film) {
    const movieData = document.getElementById("movieData");
    document.getElementById("episode-name").innerText = await episode(film.episode_id);

    const title = document.createElement("p");
    title.id = "title";
    title.innerText = film.title;

    

    const openingCrawl = document.createElement("div");
    openingCrawl.id = "opening-crawl";

    const openingCrawlLines = film.opening_crawl.split("\n");

    openingCrawlLines.forEach((line) => {
        const paragraph = document.createElement("p");
        paragraph.innerText = line;
        openingCrawl.appendChild(paragraph);
    });

    movieData.appendChild(title);
    movieData.appendChild(openingCrawl);
}

function animateOpeningCrawl() {
    const openingCrawl = document.getElementById("opening-crawl");
    const openingCrawlHeight = openingCrawl.clientHeight;
    const openingCrawlDuration = openingCrawlHeight * 25; 
    openingCrawl.style.animation = `scrolling ${openingCrawlDuration}ms linear`;

    openingCrawl.addEventListener("animationend", () => {
        const startButton = document.getElementById("startButton");
        startButton.style.visibility = "visible";
        startButton.classList.remove("hidden");
        document.getElementById("main-content").style.display = "none";
        document.getElementById("film-audio").pause();
        document.getElementById("film-audio").currentTime = 0;
        openingCrawl.style.animation = "none";
    });
}

async function episode(episodeId) {
    const episodes = {
        1: 'Episode I - The Phantom Menace',
        2: 'Episode II - Attack of the Clones',
        3: 'Episode III - Revenge of the Sith',
        4: 'Episode IV - A New Hope',
        5: 'Episode V - The Empire Strikes Back',
        6: 'Episode VI - Return of the Jedi',
        7: 'Episode VII - The Force Awakens',
        8: 'Episode VIII - The Last Jedi',
        9: 'Episode IX - The Rise of Skywalker'
    };

    return episodes[episodeId];
}

var field = document.getElementById("field");
var f = (typeof field.getContext === 'function') ? field.getContext("2d") : null;
var stars = {};
var starIndex = 0;
var numStars = 0;
var acceleration = 1;
var starsToDraw = (field.width * field.height) / 200;

function Star() {
    this.X = field.width / 2;
    this.Y = field.height / 2;

    this.SX = Math.random() * 10 - 5;
    this.SY = Math.random() * 10 - 5;

    var start = 0;

    if (field.width > field.height)
        start = field.width;
    else
        start = field.height;

    this.X += this.SX * start / 10;
    this.Y += this.SY * start / 10;

    this.W = 1;
    this.H = 1;

    this.age = 0;
    this.dies = 500;

    starIndex++;
    stars[starIndex] = this;

    this.ID = starIndex;
    this.C = "#ffffff";
}

Star.prototype.Draw = function () {
    if (!f) {
        console.log('Could not load canvas element');
        return;
    }
    this.X += this.SX;
    this.Y += this.SY

    this.SX += this.SX / (50 / acceleration);
    this.SY += this.SY / (50 / acceleration);

    this.age++;

    if (this.age == Math.floor(50 / acceleration) | this.age == Math.floor(150 / acceleration) | this.age == Math.floor(300 / acceleration)) {
        this.W++;
        this.H++;
    }

    if (this.X + this.W < 0 | this.X > field.width |
        this.Y + this.H < 0 | this.Y > field.height) {
        delete stars[this.ID];
        numStars--;
    }

    f.fillStyle = this.C;
    f.fillRect(this.X, this.Y, this.W, this.H);
}

field.width = window.innerWidth;
field.height = window.innerHeight;

function draw() {
    if (!f) {
        console.log('Could not load canvas element');
        return;
    }

    field.width = window.innerWidth;
    field.height = window.innerHeight;

    f.fillStyle = "rgba(0, 0, 0, 0.6)";
    f.fillRect(0, 0, field.width, field.height);

    for (var i = numStars; i < starsToDraw; i++) {
        new Star();
        numStars++;
    }

    for (var star in stars) {
        stars[star].Draw();
    }
}

if (f) {
    setInterval(draw, 40); 
}

function resetStars() {
    stars = {};
    starIndex = 0;
    numStars = 0;
    acceleration = 1;
    starsToDraw = (field.width * field.height) / 200;
}

