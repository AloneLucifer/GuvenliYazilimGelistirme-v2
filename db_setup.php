<?php
// db_setup.php
$dbFile = 'yemektarifi.db';

if (!file_exists($dbFile)) {
    $db = new SQLite3($dbFile);

    // Create users table
    $db->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT NOT NULL,
        firstname TEXT,
        lastname TEXT,
        linkedin TEXT,
        github TEXT,
        profile_pic TEXT
    )");

    // Create roles table
    $db->exec("
    CREATE TABLE IF NOT EXISTS roles (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        role_name TEXT NOT NULL
    )");

    // Create recipes table
    $db->exec("
    CREATE TABLE IF NOT EXISTS recipes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        description TEXT NOT NULL,
        category TEXT NOT NULL,
        is_private INTEGER NOT NULL DEFAULT 0,
        created_by INTEGER,
        image TEXT,
        FOREIGN KEY (created_by) REFERENCES users(id)
    )");

    // Create comments table
    $db->exec("
    CREATE TABLE IF NOT EXISTS comments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        recipe_id INTEGER NOT NULL,
        comment TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (recipe_id) REFERENCES recipes(id)
    )");

    // Create favorites table
    $db->exec("
    CREATE TABLE IF NOT EXISTS favorites (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        recipe_id INTEGER NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (recipe_id) REFERENCES recipes(id)
    )");

    // Insert admin user
    $db->exec("
    INSERT INTO users (username, password, role) VALUES ('admin', 'admin123', 'admin')
    ");
}

?>
