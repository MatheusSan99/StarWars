document.addEventListener("DOMContentLoaded", async function () {
    await loadCharacters();
});

const scrollCarousel = (direction) => {
    const container = document.querySelector('.carousel-container');
    const scrollAmount = direction * 300; 
    container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
};

async function cacheCharacters(filmId, characters) {
    const cacheKey = `film-${filmId}-characters`;
    const cacheData = {
        characters: characters,
        timestamp: Date.now()
    };
    localStorage.setItem(cacheKey, JSON.stringify(cacheData));
}

async function getCachedCharacters(filmId) {
    const cacheKey = `film-${filmId}-characters`;
    const cacheData = localStorage.getItem(cacheKey);
    
    if (!cacheData) {
        return null; 
    }

    const { characters, timestamp } = JSON.parse(cacheData);
    
    const cacheValidity = 1000 * 60 * 60 * 24; 
    const now = Date.now();

    if (now - timestamp > cacheValidity) {
        localStorage.removeItem(cacheKey); 
        return null;
    }

    return characters;
}

async function loadCharacters() {
    const host = window.location.origin;
    const filmId = window.location.pathname.split("/")[3];

    const cachedCharacters = await getCachedCharacters(filmId);
    
    if (cachedCharacters) {
        await buildCharactersCarousel(cachedCharacters);
        return;
    }

    try {
        const response = await fetch(`${host}/api/external/film/${filmId}/characters`);

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const data = await response.json();
        
        await cacheCharacters(filmId, data.characters);

        await buildCharactersCarousel(data.characters);
    } catch (error) {
        console.error("Failed to load characters:", error);
    }
}


async function buildCharactersCarousel(characters) {
    const carouselContainer = document.querySelector(".carousel-container");

    for (let i = 0; i < characters.length; i++) {
        const character = characters[i];

        const characterCard = document.createElement("div");
        characterCard.classList.add("character-card");

        const characterImage = document.createElement("img");
        characterImage.src = character.cover;
        characterImage.alt = "Imagem do Personagem";
        characterImage.classList.add("character-image");

        const characterName = document.createElement("h2");
        characterName.textContent = character.name;

        const characterDetails = document.createElement("div");
        characterDetails.classList.add("character-details");

        const characterDetailsList = document.createElement("ul");

        const characterDetailsItems = [
            { label: "Altura", value: character.height },
            { label: "Peso", value: character.mass },
            { label: "Cor do Cabelo", value: character.hair_color },
            { label: "Cor da Pele", value: character.skin_color },
            { label: "Cor dos Olhos", value: character.eye_color },
            { label: "Ano de Nascimento", value: character.birth_year },
            { label: "Gênero", value: character.gender },
        ];

        for (let j = 0; j < characterDetailsItems.length; j++) {
            const item = characterDetailsItems[j];
            const listItem = document.createElement("li");
            listItem.innerHTML = `<strong>${item.label}:</strong> ${item.value}`;
            characterDetailsList.appendChild(listItem);
        }

        characterDetails.appendChild(characterDetailsList);
        characterCard.appendChild(characterImage);
        characterCard.appendChild(characterName);
        characterCard.appendChild(characterDetails);
        carouselContainer.appendChild(characterCard);
    }
}
