-- Create database if it does not exist
CREATE DATABASE IF NOT EXISTS billiard_manager_db;

-- Use the newly created or existing database
USE billiard_manager_db;

-- ----------------------------------------------------
-- Table structure for `categories`
-- ----------------------------------------------------
DROP TABLE IF EXISTS categories;
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(255) NOT NULL UNIQUE
);

-- Insert some initial data into `categories`
INSERT INTO categories (category_name) VALUES 
('9-Ball'),
('Snooker'),
('8-Ball');

-- ----------------------------------------------------
-- Table structure for `billiard_clubs`
-- ----------------------------------------------------
DROP TABLE IF EXISTS billiard_clubs;
CREATE TABLE billiard_clubs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    club_name VARCHAR(255) NOT NULL,
    owner_name VARCHAR(255) NOT NULL,
    address VARCHAR(255),
    club_member_count INT DEFAULT 0,
    -- category_id is optional (NULL is fine, but 0 is easier to handle in form selects)
    category_id INT DEFAULT 0, 
    -- Adding a foreign key constraint (optional but good practice)
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Insert some initial data into `billiard_clubs`
INSERT INTO billiard_clubs (club_name, owner_name, address, club_member_count, category_id) VALUES
('The Corner Pocket', 'Alice Smith', '123 Main St', 150, 1),
('Billiards Palace', 'Bob Johnson', '456 Oak Ave', 300, 2),
('Cue Master Club', 'Charlie Brown', '789 Pine Ln', 80, 3);


-- ----------------------------------------------------
-- NEW: Table structure for `roles`
-- ----------------------------------------------------
DROP TABLE IF EXISTS roles;
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(100) NOT NULL UNIQUE
);

-- NEW: Insert initial roles
INSERT INTO roles (id, role_name) VALUES
(1, 'System Administrator'),
(2, 'Club Manager'),
(3, 'Club Member');


-- ----------------------------------------------------
-- NEW: Table structure for `users`
-- ----------------------------------------------------
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    member_since DATE,
    total_clubs_managed INT DEFAULT 0,
    role_id INT NOT NULL,
    -- Add foreign key constraint to roles table
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- NEW: Insert mock user data for testing the profile view (ID 1 will be used)
INSERT INTO users (id, username, email, member_since, total_clubs_managed, role_id) VALUES
(1, 'SuperAdmin', 'admin@example.com', '2022-10-01', 5, 1);