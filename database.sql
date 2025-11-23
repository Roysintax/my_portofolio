-- Database: my_portofolio

-- Membuat database
CREATE DATABASE IF NOT EXISTS my_portofolio;

-- Menggunakan database yang baru dibuat
USE my_portofolio;

-- Tabel untuk halaman Dashboard
CREATE TABLE IF NOT EXISTS dashboard (
    id INT AUTO_INCREMENT PRIMARY KEY,
    carrousel_image VARCHAR(255),
    carrousel_teks TEXT,
    photo_profil VARCHAR(255)
);

-- Tabel untuk halaman Project
CREATE TABLE IF NOT EXISTS project_page (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_image VARCHAR(255),
    tool_logo VARCHAR(255),
    description TEXT,
    project_link_github VARCHAR(255)
);

-- Tabel untuk halaman Design
CREATE TABLE IF NOT EXISTS design_page (
    id INT AUTO_INCREMENT PRIMARY KEY,
    design_image VARCHAR(255),
    design_link VARCHAR(255)
);

-- Tabel untuk halaman Education and Certification
CREATE TABLE IF NOT EXISTS education_and_certification_page (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100),
    name_education_history VARCHAR(255),
    image_activity VARCHAR(255),
    name_certificate VARCHAR(255),
    image_certificate VARCHAR(255),
    link_certificate VARCHAR(255)
);

-- Tabel untuk halaman Work Experience
CREATE TABLE IF NOT EXISTS work_experience_page (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_activity_work_carrousel VARCHAR(255),
    name_company VARCHAR(255),
    date_work DATE,
    activity_description TEXT
);

-- Tabel untuk Admin
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_username VARCHAR(100) UNIQUE,
    kelas VARCHAR(50),
    umur INT,
    password VARCHAR(255)
);
