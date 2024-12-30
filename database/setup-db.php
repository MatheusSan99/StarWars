<?php

declare(strict_types=1);

$dbPath = __DIR__ . '/database.sqlite';
$dbDir = __DIR__; 

if (!file_exists($dbDir)) {
    mkdir($dbDir, 0777, true); 
}

if (!file_exists($dbPath)) {
    touch($dbPath); 
    chmod($dbPath, 0666);
}

chmod($dbDir, 0777); 

$pdo = new PDO("sqlite:$dbPath");

$createAccountsTable = '
    CREATE TABLE IF NOT EXISTS accounts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT NOT NULL
    );
';

$createMovieTable = '
    CREATE TABLE IF NOT EXISTS movies (
        id INTEGER PRIMARY KEY,
        title TEXT NOT NULL,
        episode_id INTEGER NOT NULL,
        opening_crawl TEXT NOT NULL,
        release_date TEXT NOT NULL,
        director TEXT NOT NULL,
        producers TEXT NOT NULL,
        characters TEXT NOT NULL,
    );
';

$createCharactersTable = '
    CREATE TABLE IF NOT EXISTS characters (
        id INTEGER PRIMARY KEY,
        name TEXT NOT NULL,
        birth_year TEXT NOT NULL,
        starships TEXT NOT NULL,
        vehicles TEXT NOT NULL,
    );
';

$createStarshipsTable = '
    CREATE TABLE IF NOT EXISTS starships (
        id INTEGER PRIMARY KEY,
        name TEXT NOT NULL,
        model TEXT NOT NULL,
        starship_class TEXT NOT NULL,
        manufacturer TEXT NOT NULL,
        cost_in_credits TEXT NOT NULL,
        length TEXT NOT NULL,
        crew TEXT NOT NULL,
        passengers TEXT NOT NULL,
        max_atmosphering_speed TEXT NOT NULL,
        hyperdrive_rating TEXT NOT NULL,
        MGLT TEXT NOT NULL,
        cargo_capacity TEXT NOT NULL,
        consumables TEXT NOT NULL
    );
';

$pdo->exec($createAccountsTable);
$pdo->exec($createMovieTable);
$pdo->exec($createCharactersTable);
$pdo->exec($createStarshipsTable);

$checkUserAdmin = 'SELECT * FROM accounts WHERE email = "admin@gmail.com";';

$statement = $pdo->query($checkUserAdmin);

if (!empty($statement->fetchAll())) {
    return;
}

$password = password_hash('123456', PASSWORD_ARGON2ID);

$inserirUsuarioPadrao = 'INSERT INTO accounts (name, email, password, role) VALUES ("Administrador", "admin@gmail.com", ?, "admin");';

$statement = $pdo->prepare($inserirUsuarioPadrao);

$statement->bindValue(1, $password);

$statement->execute();