
-- =====================================================
-- BLUE MOON HOTEL MANAGEMENT SYSTEM
-- Complete Database Setup
-- Compatible with MariaDB 10.4+
-- =====================================================

-- ======our project user name and password===========
-- Role	Email	Password
-- Admin	admin@bluemoon.com	Admin@123
-- Guest	guest@bluemoon.com	Guest@123
-- Staff	staff@bluemoon.com	Staff@123
-- =====================================================

CREATE DATABASE IF NOT EXISTS blue_moon_hotel_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE blue_moon_hotel_db;


-- =====================================================
-- 1. USERS TABLE
-- =====================================================

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,

    full_name VARCHAR(100) NOT NULL,

    email VARCHAR(100) NOT NULL UNIQUE,

    password VARCHAR(255) NOT NULL,

    phone VARCHAR(15) NOT NULL,

    role ENUM('Customer', 'Staff', 'Admin')
        NOT NULL DEFAULT 'Customer',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB;


-- =====================================================
-- 2. ROOM TYPES TABLE
-- =====================================================

CREATE TABLE room_types (
    type_id INT AUTO_INCREMENT PRIMARY KEY,

    type_name VARCHAR(50) NOT NULL UNIQUE,

    description TEXT,

    price_per_night DECIMAL(10,2) NOT NULL,

    capacity INT NOT NULL,

    CONSTRAINT chk_room_price
        CHECK (price_per_night >= 0),

    CONSTRAINT chk_room_capacity
        CHECK (capacity > 0)

) ENGINE=InnoDB;


-- =====================================================
-- 3. ROOMS TABLE
-- =====================================================

CREATE TABLE rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,

    room_number VARCHAR(10) NOT NULL UNIQUE,

    type_id INT NOT NULL,

    status ENUM(
        'Available',
        'Booked',
        'Maintenance'
    ) NOT NULL DEFAULT 'Available',

    CONSTRAINT fk_room_type
        FOREIGN KEY (type_id)
        REFERENCES room_types(type_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    INDEX idx_room_type (type_id),

    INDEX idx_room_status (status)

) ENGINE=InnoDB;


-- =====================================================
-- 4. BOOKINGS TABLE
-- =====================================================

CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,

    room_id INT NOT NULL,

    check_in DATE NOT NULL,

    check_out DATE NOT NULL,

    total_amount DECIMAL(10,2) NOT NULL,

    booking_status ENUM(
        'Pending',
        'Confirmed',
        'Checked-Out',
        'Cancelled'
    ) NOT NULL DEFAULT 'Pending',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_booking_user
        FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_booking_room
        FOREIGN KEY (room_id)
        REFERENCES rooms(room_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT chk_booking_dates
        CHECK (check_out > check_in),

    CONSTRAINT chk_booking_amount
        CHECK (total_amount >= 0),

    INDEX idx_booking_user (user_id),

    INDEX idx_booking_room (room_id),

    INDEX idx_booking_dates (check_in, check_out),

    INDEX idx_booking_status (booking_status)

) ENGINE=InnoDB;


-- =====================================================
-- 5. INSERT ROOM TYPES
-- =====================================================

INSERT INTO room_types
(type_id, type_name, description, price_per_night, capacity)
VALUES

(1, 'Single Deluxe',
 'Comfortable single bedroom with modern facilities.',
 120.00, 1),

(2, 'Double Executive',
 'Spacious double bedroom with luxury amenities.',
 200.00, 2),

(3, 'Presidential Suite',
 'Premium luxury suite with private jacuzzi and balcony.',
 350.00, 4);


-- =====================================================
-- 6. INSERT ROOMS
-- =====================================================

INSERT INTO rooms
(room_id, room_number, type_id, status)
VALUES

(1, '101', 1, 'Available'),

(2, '102', 1, 'Available'),

(3, '201', 2, 'Available'),

(4, '202', 2, 'Available'),

(5, '301', 3, 'Available');


-- =====================================================
-- 7. INSERT DEFAULT USERS
-- PASSWORDS ARE STORED USING BCRYPT
-- =====================================================

INSERT INTO users
(user_id, full_name, email, password, phone, role)
VALUES

(
  1,
  'System Admin',
  'admin@bluemoon.com',
  '$2y$12$Wgq1ZPzN8v85cE/za8uEMuSDbzvn1PCzPWcmQWqdnkKXBZgWYAt0K',
  '0771112233',
  'Admin'
),

(
  2,
  'Front Desk Staff',
  'staff@bluemoon.com',
  '$2y$12$4ZkcmTterO7AS5osdU5nBuv0dVIZUAt.RX3pX9pKAs.frMBG6/dDC',
  '0774445566',
  'Staff'
),

(
  3,
  'Sample Guest',
  'guest@gmail.com',
  '$2y$12$TchwBLGn0m3QJKoyAcVsresBpzCQrK1rEjCWSJxppWW/HLuttbl0K',
  '0777778899',
  'Customer'
);


-- =====================================================
-- END OF BLUE MOON HOTEL DATABASE
-- =====================================================
