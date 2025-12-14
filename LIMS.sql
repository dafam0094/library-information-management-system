CREATE DATABASE olims_db;
USE olims_db;

-- Users Table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'lecturer', 'student') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Laboratories Table
CREATE TABLE laboratories (
    lab_id INT AUTO_INCREMENT PRIMARY KEY,
    lab_name VARCHAR(100) NOT NULL,
    location VARCHAR(100) NOT NULL,
    capacity INT NOT NULL,
    status ENUM('available', 'occupied', 'maintenance') DEFAULT 'available'
);

-- Equipment Table
CREATE TABLE equipment (
    equipment_id INT AUTO_INCREMENT PRIMARY KEY,
    equipment_name VARCHAR(100) NOT NULL,
    lab_id INT NOT NULL,
    status ENUM('available', 'in_use', 'under_maintenance') DEFAULT 'available',
    purchase_date DATE,
    last_maintenance DATE,
    FOREIGN KEY (lab_id) REFERENCES laboratories(lab_id) ON DELETE CASCADE
);

-- Laboratory Booking Table
CREATE TABLE lab_bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    lab_id INT NOT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (lab_id) REFERENCES laboratories(lab_id) ON DELETE CASCADE
);

-- Experiment Records Table
CREATE TABLE experiments (
    experiment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    lab_id INT NOT NULL,
    experiment_title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    experiment_date DATE NOT NULL,
    results TEXT,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (lab_id) REFERENCES laboratories(lab_id) ON DELETE CASCADE
);

-- Maintenance Records Table
CREATE TABLE maintenance (
    maintenance_id INT AUTO_INCREMENT PRIMARY KEY,
    equipment_id INT NOT NULL,
    technician_name VARCHAR(100) NOT NULL,
    maintenance_date DATE NOT NULL,
    remarks TEXT,
    FOREIGN KEY (equipment_id) REFERENCES equipment(equipment_id) ON DELETE CASCADE
);

INSERT INTO users (full_name, email, password, role) 
VALUES 
('Admin User', 'admin@olims.com', SHA2('admin123', 256), 'admin'),
('Dr. Smith', 'smith@olims.com', SHA2('lecturer123', 256), 'lecturer'),
('John Doe', 'john.doe@olims.com', SHA2('student123', 256), 'student');

INSERT INTO laboratories (lab_name, location, capacity, status) 
VALUES 
('Computer Lab A', 'First Floor, CS Department', 30, 'available'),
('Computer Lab B', 'Second Floor, CS Department', 25, 'maintenance');

INSERT INTO equipment (equipment_name, lab_id, status, purchase_date, last_maintenance) 
VALUES 
('Digital Oscilloscope', 1, 'available', '2023-05-10', '2024-01-15'),
('3D Printer', 2, 'under_maintenance', '2022-12-20', '2024-02-05');

INSERT INTO lab_bookings (user_id, lab_id, booking_date, start_time, end_time, status) 
VALUES (3, 1, '2025-03-15', '10:00:00', '12:00:00', 'pending');

UPDATE lab_bookings 
SET status = 'approved' 
WHERE booking_id = 1;

INSERT INTO experiments (user_id, lab_id, experiment_title, description, experiment_date, results) 
VALUES 
(3, 1, 'AI Model Training', 'Training a machine learning model on Python', '2025-03-16', 'Successful classification with 92% accuracy');

INSERT INTO maintenance (equipment_id, technician_name, maintenance_date, remarks) 
VALUES 
(1, 'Engr. Adewale', '2025-04-01', 'Replaced damaged screen and recalibrated sensors');

SELECT lab_id, lab_name, location, capacity 
FROM laboratories 
WHERE status = 'available';

SELECT e.experiment_id, e.experiment_title, e.description, e.experiment_date, l.lab_name 
FROM experiments e
JOIN laboratories l ON e.lab_id = l.lab_id
JOIN users u ON e.user_id = u.user_id
WHERE u.email = 'john.doe@olims.com';

SELECT equipment_id, equipment_name, last_maintenance 
FROM equipment 
WHERE status = 'under_maintenance' OR last_maintenance < DATE_SUB(CURDATE(), INTERVAL 6 MONTH);
