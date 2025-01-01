let filmSeconds = 0;
let canvas;
let f;
let stars;
let starIndex;
let numStars;
let acceleration;
let starsToDraw;

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
    const mainContent = document.getElementById("main-content");
    const filmAudio = document.getElementById("film-audio");
  
    btn.classList.add("hidden");
  
    setTimeout(() => {
      btn.style.visibility = "hidden";
    }, 2000);
  
    filmAudio.play().catch((error) => {
      console.error("Erro ao iniciar o áudio:", error);
    });
  
    if (f) {
      resetStars();
      mainContent.style.display = "block";
  
      const intervalId = setInterval(draw, 20);
  
      setTimeout(() => { 
          clearInterval(intervalId); 
  
          mainContent.classList.add("fade-out");
          btn.classList.add("fade-out");
  
          setTimeout(() => {
            mainContent.style.display = "none"; 
            btn.style.visibility = "visible";
            btn.classList.remove("hidden");
            filmAudio.pause();
            filmAudio.currentTime = 0;
          }, 2000); 
      }, filmSeconds * 1000); 
    }
  }
  
async function buildFilm(film) {
  const movieData = document.getElementById("movieData");
  document.getElementById("episode-name").innerText = await episode(
    film.episode_id
  );

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
