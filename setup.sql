-- SIS Database Setup
-- Run this script to initialize the database and sample data

CREATE DATABASE IF NOT EXISTS StudentSystem;
USE StudentSystem;

CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id VARCHAR(20) NOT NULL UNIQUE,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  course ENUM('BSCS','BSIT','BSCE','BSEE','BSME') NOT NULL,
  year_level ENUM('1st Year','2nd Year','3rd Year','4th Year') NOT NULL,
  section VARCHAR(10) NOT NULL,
  status ENUM('Active','Inactive') DEFAULT 'Active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample Filipino student data
INSERT INTO students (student_id, first_name, last_name, email, course, year_level, section, status) VALUES
('2024-00001', 'Juan', 'Dela Cruz', 'juan.delacruz@example.com', 'BSCS', '3rd Year', 'A', 'Active'),
('2024-00002', 'Maria', 'Santos', 'maria.santos@example.com', 'BSIT', '2nd Year', 'B', 'Active'),
('2024-00003', 'Jose', 'Rizal', 'jose.rizal@example.com', 'BSCE', '4th Year', 'C', 'Active'),
('2024-00004', 'Ana', 'Gonzales', 'ana.gonzales@example.com', 'BSME', '1st Year', 'A', 'Active'),
('2024-00005', 'Pedro', 'Marcos', 'pedro.marcos@example.com', 'BSEE', '3rd Year', 'B', 'Inactive'),
('2024-00006', 'Sofia', 'Reyes', 'sofia.reyes@example.com', 'BSCS', '2nd Year', 'A', 'Active'),
('2024-00007', 'Miguel', 'Lopez', 'miguel.lopez@example.com', 'BSIT', '4th Year', 'C', 'Active'),
('2024-00008', 'Isabella', 'Fernandez', 'isabella.fernandez@example.com', 'BSCE', '1st Year', 'B', 'Active'),
('2024-00009', 'Antonio', 'Villanueva', 'antonio.villanueva@example.com', 'BSME', '2nd Year', 'A', 'Inactive'),
('2024-00010', 'Luisa', 'Torres', 'luisa.torres@example.com', 'BSEE', '3rd Year', 'C', 'Active'),
('2024-00011', 'Carlos', 'Mendoza', 'carlos.mendoza@example.com', 'BSCS', '1st Year', 'A', 'Active'),
('2024-00012', 'Katrina', 'Navarro', 'katrina.navarro@example.com', 'BSIT', '3rd Year', 'B', 'Active'),
('2024-00013', 'Ramon', 'Gutierrez', 'ramon.gutierrez@example.com', 'BSCE', '2nd Year', 'C', 'Inactive'),
('2024-00014', 'Angela', 'Ramirez', 'angela.ramirez@example.com', 'BSME', '4th Year', 'A', 'Active'),
('2024-00015', 'Fernando', 'Cruz', 'fernando.cruz@example.com', 'BSEE', '1st Year', 'B', 'Active');