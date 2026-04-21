-- ============================================================
-- ILLUME Luxury Platform — Database Schema
-- Run this in phpMyAdmin or via MySQL CLI
-- ============================================================

CREATE DATABASE IF NOT EXISTS illume_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE illume_db;

-- ============================================================
-- USERS
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('founder', 'staff', 'client') NOT NULL DEFAULT 'client',
    phone VARCHAR(30),
    whatsapp VARCHAR(30),
    avatar VARCHAR(255),
    bio TEXT,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- ============================================================
-- SERVICES
-- ============================================================
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    short_desc VARCHAR(255),
    description TEXT,
    icon VARCHAR(100),
    starting_price DECIMAL(12,2),
    currency VARCHAR(10) DEFAULT 'NGN',
    display_order INT DEFAULT 0,
    active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

-- ============================================================
-- CONSULTATIONS
-- ============================================================
CREATE TABLE IF NOT EXISTS consultations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(30),
    whatsapp VARCHAR(30),
    service_type VARCHAR(100),
    occasion VARCHAR(150),
    budget_range VARCHAR(100),
    timeline VARCHAR(100),
    message TEXT,
    status ENUM('new', 'contacted', 'converted', 'declined') NOT NULL DEFAULT 'new',
    assigned_to INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- ============================================================
-- ORDERS
-- ============================================================
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_ref VARCHAR(20) NOT NULL UNIQUE,
    client_id INT NOT NULL,
    assigned_staff_id INT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    service_type VARCHAR(100),
    status ENUM('intake','design','approval','production','delivery','complete','cancelled') NOT NULL DEFAULT 'intake',
    budget DECIMAL(12,2),
    currency VARCHAR(10) DEFAULT 'NGN',
    deadline DATE NULL,
    internal_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_staff_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_client (client_id)
) ENGINE=InnoDB;

-- ============================================================
-- DESIGN SUBMISSIONS
-- ============================================================
CREATE TABLE IF NOT EXISTS design_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    uploaded_by INT NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_name VARCHAR(255),
    title VARCHAR(255),
    notes TEXT,
    approval_status ENUM('pending','approved','revision_requested','rejected') NOT NULL DEFAULT 'pending',
    client_feedback TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_order (order_id),
    INDEX idx_approval (approval_status)
) ENGINE=InnoDB;

-- ============================================================
-- ORDER TIMELINE
-- ============================================================
CREATE TABLE IF NOT EXISTS order_timeline (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    actor_id INT NULL,
    action VARCHAR(100) NOT NULL,
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (actor_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_order (order_id)
) ENGINE=InnoDB;

-- ============================================================
-- PORTFOLIO ITEMS
-- ============================================================
CREATE TABLE IF NOT EXISTS portfolio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    image_path VARCHAR(500),
    description TEXT,
    featured TINYINT(1) DEFAULT 0,
    display_order INT DEFAULT 0,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- SEED DATA — SERVICES
-- ============================================================
INSERT INTO services (name, slug, short_desc, description, icon, starting_price, display_order) VALUES
('Bespoke Couture', 'bespoke-couture', 'Fully custom garments crafted to your exact vision and measurements', 'From initial concept to final fitting, every stitch is a conversation between your vision and our craft. We create one-of-a-kind pieces that tell your story.', 'sparkles', 350000, 1),
('Bridal & Special Occasion', 'bridal-special-occasion', 'Ethereal designs for the moments that matter most', 'Your most significant moments deserve extraordinary garments. We create bridal wear and occasion pieces imbued with meaning, structure, and beauty.', 'heart', 500000, 2),
('Ready-to-Wear Collection', 'ready-to-wear', 'Curated pieces from our seasonal collections', 'Wearable luxury for the discerning modern woman. Each piece balances aesthetic intention with everyday function.', 'hanger', 85000, 3),
('Fashion Consulting', 'fashion-consulting', 'Personal style direction and wardrobe curation', 'Elevate your personal brand through strategic style. We provide comprehensive wardrobe audits, personal shopping, and style roadmaps.', 'compass', 75000, 4),
('Editorial & Brand Styling', 'editorial-brand-styling', 'Professional wardrobe direction for shoots and campaigns', 'We translate brand narratives into compelling visual stories. From concept boards to on-set direction, we ensure every frame is intentional.', 'camera', 150000, 5),
('Production Consulting', 'production-consulting', 'End-to-end guidance for fashion brands and designers', 'We share our production knowledge with emerging designers and brands. From pattern making to factory sourcing, we guide your entire production pipeline.', 'settings', 200000, 6);

-- ============================================================
-- SEED DATA — FOUNDER ACCOUNT
-- Password: password
-- ============================================================
INSERT INTO users (name, email, password_hash, role, phone, whatsapp, status) VALUES
('ILLUME Founder', 'founder@illume.ng', '$2y$12$clZ8B39jF/H4xS0B4jA/9Oe.KIDP/kLz2u6hH/O5G0/v8Y7Z2lB9.', 'founder', '+2348000000000', '+2348000000000', 'active');

-- ============================================================
-- SEED DATA — DEMO STAFF
-- Password: password
-- ============================================================
INSERT INTO users (name, email, password_hash, role, phone, whatsapp, status) VALUES
('Adaeze Okonkwo', 'adaeze@illume.ng', '$2y$12$clZ8B39jF/H4xS0B4jA/9Oe.KIDP/kLz2u6hH/O5G0/v8Y7Z2lB9.', 'staff', '+2348011111111', '+2348011111111', 'active');

-- ============================================================
-- SEED DATA — DEMO CLIENT
-- Password: password
-- ============================================================
INSERT INTO users (name, email, password_hash, role, phone, whatsapp, status) VALUES
('Chioma Eze', 'chioma@example.com', '$2y$12$clZ8B39jF/H4xS0B4jA/9Oe.KIDP/kLz2u6hH/O5G0/v8Y7Z2lB9.', 'client', '+2348022222222', '+2348022222222', 'active');
