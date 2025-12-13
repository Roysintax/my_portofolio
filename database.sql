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
    title VARCHAR(255),
    project_image VARCHAR(255),
    tool_logo TEXT,
    description TEXT,
    project_link_github VARCHAR(255)
);

-- Tabel untuk halaman Design
CREATE TABLE IF NOT EXISTS design_page (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    design_image TEXT,
    design_link VARCHAR(255)
);

-- Tabel untuk Education History
CREATE TABLE IF NOT EXISTS education_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name_education VARCHAR(255),
    description TEXT,
    image_activity TEXT,
    start_date DATE,
    end_date DATE,
    still_studying TINYINT(1) DEFAULT 0
);

-- Tabel untuk Certifications
CREATE TABLE IF NOT EXISTS certifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name_certificate VARCHAR(255),
    issuer VARCHAR(255),
    issue_date DATE,
    image_certificate VARCHAR(255),
    link_certificate VARCHAR(255)
);

-- Tabel untuk halaman Work Experience
CREATE TABLE IF NOT EXISTS work_experience_page (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_activity_work_carrousel TEXT,
    name_company VARCHAR(255),
    date_work_start DATE,
    date_work_end DATE,
    still_working TINYINT(1) DEFAULT 0,
    work_status ENUM('magang', 'kerja') DEFAULT 'kerja',
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

-- Tabel untuk Home Carousel (Separate Rows)
CREATE TABLE IF NOT EXISTS home_carousel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(255),
    title VARCHAR(255),
    subtitle VARCHAR(255)
);
