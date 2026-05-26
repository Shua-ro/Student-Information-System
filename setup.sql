
CREATE DATABASE IF NOT EXISTS StudentSystem;
USE StudentSystem;

CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id VARCHAR(20) NOT NULL UNIQUE,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  course ENUM('DCPET','BSIT','BSCE','DCET','DIT') NOT NULL,
  year_level ENUM('1st Year','2nd Year','3rd Year','4th Year') NOT NULL,
  section VARCHAR(10) NOT NULL,
  status ENUM('Active','Inactive') DEFAULT 'Active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample Filipino student data
INSERT INTO students (student_id, first_name, last_name, email, course, year_level, section, status) VALUES
('2024-00001', 'Juan', 'Dela Cruz', 'juan.delacruz@example.com', 'DCPET', '3rd Year', '1', 'Active', ),
('2024-00002', 'Maria', 'Santos', 'maria.santos@example.com', 'BSIT', '2nd Year', '2', 'Active'),
('2024-00003', 'Jose', 'Rizal', 'jose.rizal@example.com', 'BSCE', '4th Year', '3', 'Active'),
('2024-00004', 'Ana', 'Gonzales', 'ana.gonzales@example.com', 'DCET', '1st Year', '1', 'Active'),
('2024-00005', 'Pedro', 'Marcos', 'pedro.marcos@example.com', 'DIT', '3rd Year', '1', 'Inactive'),
('2024-00006', 'Sofia', 'Reyes', 'sofia.reyes@example.com', 'DCPET', '2nd Year', '2', 'Active'),
('2024-00007', 'Miguel', 'Lopez', 'miguel.lopez@example.com', 'BSIT', '4th Year', '3', 'Active'),
('2024-00008', 'Isabella', 'Fernandez', 'isabella.fernandez@example.com', 'BSCE', '1st Year', '1', 'Active'),
('2024-00009', 'Antonio', 'Villanueva', 'antonio.villanueva@example.com', 'DCET', '2nd Year', '1', 'Inactive'),
('2024-00010', 'Luisa', 'Torres', 'luisa.torres@example.com', 'DIT', '3rd Year', '1', 'Active'),
('2024-00011', 'Carlos', 'Mendoza', 'carlos.mendoza@example.com', 'DCPET', '1st Year', '1', 'Active'),
('2024-00012', 'Katrina', 'Navarro', 'katrina.navarro@example.com', 'BSIT', '3rd Year', '3', 'Active'),
('2024-00013', 'Ramon', 'Gutierrez', 'ramon.gutierrez@example.com', 'BSCE', '2nd Year', '3', 'Inactive'),
('2024-00014', 'Angela', 'Ramirez', 'angela.ramirez@example.com', 'DCET', '4th Year', '2', 'Active'),
('2024-00015', 'Fernando', 'Cruz', 'fernando.cruz@example.com', 'DIT', '1st Year', '1', 'Active');