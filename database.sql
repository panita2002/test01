CREATE DATABASE editor_db;

USE editor_db;

CREATE TABLE editor_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
