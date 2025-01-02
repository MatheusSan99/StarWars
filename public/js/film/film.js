let filmSeconds = 0;
let canvas;
let f;
let stars;
let starIndex;
let numStars;
let acceleration;
let starsToDraw;
let film = {};

document.addEventListener("DOMContentLoaded", async function () {
  await loadFilm();
  await enableStartBtn();
  await assignVariables();
});

async function assignVariables() {
  canvas = document.getElementById("canvas");
  f = typeof canvas.getContext === "function" ? canvas.getContext("2d") : null;
  stars = {};
  starIndex = 0;
  numStars = 0;
  acceleration = 1;
  starsToDraw = (canvas.width * canvas.height) / 200;
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;
}
async function enableStartBtn() {
  const startButton = document.getElementById("startButton");
  startButton.disabled = false;
  startButton.textContent = "Iniciar Filme";
}

async function loadFilm() {
  const host = window.location.origin;
  const url = new URL(window.location.href);
  const filmId = url.searchParams.get("id");
  film = {};

  try {
    const response = await fetch(`${host}/api/external/film/${filmId}`);

    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }

    film = await response.json();
    await buildFilm();
  } catch (error) {
    console.error("Failed to load film:", error);
  }
}

function startFilm() {
  hiddenStartBtn();
  const mainContent = document.getElementById("main-content");
  const filmAudio = document.getElementById("film-audio");

  filmAudio.play().catch((error) => {
    console.error("Erro ao iniciar o áudio:", error);
  });

  if (f) {
    resetStars();
    mainContent.style.display = "block";

    const intervalId = setInterval(draw, 20);

    setTimeout(() => {
      clearInterval(intervalId);
      finishMovie();
    }, filmSeconds * 1000);
  }
}

function hiddenStartBtn() {
  const btn = document.getElementById("startButton");

  btn.classList.add("hidden");

  setTimeout(() => {
    btn.style.visibility = "hidden";
  });
}

function finishMovie() {
  const mainContent = document.getElementById("main-content");
  const startButton = document.getElementById("startButton");
  const filmAudio = document.getElementById("film-audio");

  mainContent.classList.add("fade-out");
  startButton.classList.add("fade-out");

  setTimeout(() => {
    mainContent.style.display = "none";
    startButton.style.visibility = "visible";
    startButton.classList.remove("hidden");

    startButton.classList.add("repositioned");

    filmAudio.pause();
    filmAudio.currentTime = 0;

    displayMovieInfo();
  }, 2000);
}

async function buildFilm() {
  const movieData = document.getElementById("movieData");
  const episodeTitle = await episode(film.episode_id);
  document.getElementById("episode-name").innerText = episodeTitle;

  const title = document.createElement("p");
  title.id = "title";
  title.innerText = episodeTitle;

  const openingCrawl = document.createElement("div");
  openingCrawl.id = "opening-crawl";

  const openingCrawlLines = film.opening_crawl.split("\n");

  openingCrawlLines.forEach((line) => {
    const paragraph = document.createElement("p");
    paragraph.innerText = line;
    openingCrawl.appendChild(paragraph);
    filmSeconds += 1.5;
  });

  movieData.appendChild(title);
  movieData.appendChild(openingCrawl);
}

async function episode(episodeId) {
  const episodes = {
    1: "Episode I - The Phantom Menace",
    2: "Episode II - Attack of the Clones",
    3: "Episode III - Revenge of the Sith",
    4: "Episode IV - A New Hope",
    5: "Episode V - The Empire Strikes Back",
    6: "Episode VI - Return of the Jedi",
    7: "Episode VII - The Force Awakens",
    8: "Episode VIII - The Last Jedi",
    9: "Episode IX - The Rise of Skywalker",
  };

  return episodes[episodeId];
}

function Star() {
  this.X = canvas.width / 2;
  this.Y = canvas.height / 2;

  this.SX = Math.random() * 10 - 5;
  this.SY = Math.random() * 10 - 5;

  var start = 0;

  if (canvas.width > canvas.height) start = canvas.width;
  else start = canvas.height;

  this.X += (this.SX * start) / 10;
  this.Y += (this.SY * start) / 10;

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
    console.log("Could not load canvas element");
    return;
  }
  this.X += this.SX;
  this.Y += this.SY;

  this.SX += this.SX / (50 / acceleration);
  this.SY += this.SY / (50 / acceleration);

  this.age++;

  if (
    (this.age == Math.floor(50 / acceleration)) |
    (this.age == Math.floor(150 / acceleration)) |
    (this.age == Math.floor(300 / acceleration))
  ) {
    this.W++;
    this.H++;
  }

  if (
    (this.X + this.W < 0) |
    (this.X > canvas.width) |
    (this.Y + this.H < 0) |
    (this.Y > canvas.height)
  ) {
    delete stars[this.ID];
    numStars--;
  }

  f.fillStyle = this.C;
  f.fillRect(this.X, this.Y, this.W, this.H);
};

function draw() {
  if (!f) {
    console.log("Could not load canvas element");
    return;
  }

  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;

  f.fillStyle = "rgba(0, 0, 0, 0.6)";
  f.fillRect(0, 0, canvas.width, canvas.height);

  for (var i = numStars; i < starsToDraw; i++) {
    new Star();
    numStars++;
  }

  for (var star in stars) {
    stars[star].Draw();
  }
}

function resetStars() {
  stars = {};
  starIndex = 0;
  numStars = 0;
  acceleration = 1;
  starsToDraw = (canvas.width * canvas.height) / 200;
}

function displayMovieInfo() {
  const movieInfoContainer = document.querySelector(".movie-info-container");
  const movieInfoDiv = document.querySelector(".movie-info");

  movieInfoDiv.innerHTML = `
    <h2>${film.title}</h2>
    <p><strong>Episódio:</strong> ${film.episode_id}</p>
    <p><strong>Sinopse:</strong> ${film.opening_crawl}</p>
    <p><strong>Data de Lançamento:</strong> ${film.release_date}</p>
    <p><strong>Diretor:</strong> ${film.director}</p>
    <p><strong>Produtores:</strong> ${film.producers}</p>
    <p><strong>Idade Do Filme:</strong> ${film.complete_age}</p>
    <p><strong>Idade Em Anos:</strong> ${film.age_in_years}</p>
    <p><strong>Idade Em Meses:</strong> ${film.age_in_months}</p>
    <p><strong>Idade Em Dias:</strong> ${film.age_in_days}</p>`;
    
    movieInfoContainer.style.display = "flex";
}

function resetStartButton() {
  const startButton = document.getElementById("startButton");
  startButton.classList.remove("repositioned");
  startButton.style.visibility = "hidden";
}

function restartMovie() {
  const movieInfoContainer = document.querySelector(".movie-info-container");
  movieInfoContainer.style.display = "none";

  startFilm();
}
