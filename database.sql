-- Use the existing database
USE train_booking;

-- Create 'users' table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



-- Create 'tickets' table
CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    from_station VARCHAR(100) NOT NULL,
    to_station VARCHAR(100) NOT NULL,
    travel_date DATE NOT NULL,
    travel_time TIME NOT NULL,
    travel_class VARCHAR(50) NOT NULL,
    seats INT NOT NULL,
    payment_status ENUM('Pending', 'Paid', 'Cancelled') NOT NULL DEFAULT 'Pending',
    payment_method VARCHAR(50) NULL,      
    transaction_id VARCHAR(100) NULL,     
    amount_paid DECIMAL(10, 2) NULL,     
    booked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


-- Create 'contact' table
CREATE TABLE contact (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create 'admin' table
CREATE TABLE admin (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    PRIMARY KEY (id)
) ENGINE=InnoDB;




-- Dummy Admin
INSERT INTO admin (email, password) VALUES ('admin@example.com', MD5('admin123'));


