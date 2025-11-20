-- Contact Manager Database Schema
-- This file creates the necessary tables for the contact management system

-- Create database
CREATE DATABASE IF NOT EXISTS contact_manager;
USE contact_manager;

-- Users table for authentication
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Contacts table for storing contact information
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_name (name),
    INDEX idx_email (email)
);

-- Insert sample user (password: john123)
INSERT INTO users (username, email, password_hash) VALUES 
('john', 'john@example.com', '$2y$10$KF9hnCdspf2pJjjrBvZ.9.UGyHMyeoxleHyrm0B7oLG5Nn2Y4brrW');

-- Insert sample contacts for demo
INSERT INTO contacts (user_id, name, email, phone, notes) VALUES 
(1, 'John Doe', 'john.doe@email.com', '+1-555-0123', 'Friend from college'),
(1, 'Jane Smith', 'jane.smith@email.com', '+1-555-0456', 'Work colleague'),
(1, 'Bob Johnson', 'bob.johnson@email.com', '+1-555-0789', 'Neighbor');

