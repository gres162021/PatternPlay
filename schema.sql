-- Database: patternplay_db
CREATE DATABASE IF NOT EXISTS patternplay_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE patternplay_db;

-- Table: users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: levels
CREATE TABLE IF NOT EXISTS levels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category ENUM('angka', 'huruf', 'gambar', 'kalender') NOT NULL,
    level_number INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    color VARCHAR(7),
    image_path VARCHAR(255) NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_category_level (category, level_number),
    INDEX idx_category (category),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: questions
CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    level_id INT NOT NULL,
    question_text TEXT NOT NULL,
    question_image VARCHAR(255) NULL,
    question_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (level_id) REFERENCES levels(id) ON DELETE CASCADE,
    INDEX idx_level (level_id),
    INDEX idx_order (question_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: answers
CREATE TABLE IF NOT EXISTS answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    answer_text VARCHAR(255) NOT NULL,
    answer_image VARCHAR(255) NULL,
    is_correct TINYINT(1) DEFAULT 0,
    reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    INDEX idx_question (question_id),
    INDEX idx_correct (is_correct)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: user_progress
CREATE TABLE IF NOT EXISTS user_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    level_id INT NOT NULL,
    question_id INT NOT NULL,
    answer_id INT NOT NULL,
    is_correct TINYINT(1) DEFAULT 0,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (level_id) REFERENCES levels(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    FOREIGN KEY (answer_id) REFERENCES answers(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_level (level_id),
    INDEX idx_completed (completed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin
INSERT INTO users (name, email, password, role) VALUES 
('Admin', 'admin@patternplay.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE name=name;

-- Insert sample levels
INSERT INTO levels (category, level_number, title, description, color) VALUES
('angka', 1, 'Pola Angka Level 1', 'Mengenal pola angka sederhana', '#d25a4b'),
('huruf', 1, 'Pola Huruf Level 1', 'Mengenal pola huruf sederhana', '#88b751'),
('gambar', 1, 'Pola Gambar Level 1', 'Mengenal pola gambar sederhana', '#efb739'),
('kalender', 1, 'Pola Kalender Level 1', 'Mengenal pola tanggal', '#f17cc9')
ON DUPLICATE KEY UPDATE title=title;

-- Insert sample questions
INSERT INTO questions (level_id, question_text, question_order) VALUES
(1, 'Lengkapi pola berikut: 2, 4, 6, 8, __', 1),
(1, 'Lengkapi pola berikut: 5, 10, 15, 20, __', 2)
ON DUPLICATE KEY UPDATE question_text=question_text;

-- Insert sample answers
INSERT INTO answers (question_id, answer_text, is_correct, reason) VALUES
(1, '10', 1, 'Benar! Pola ini bertambah 2 setiap langkah. 8 + 2 = 10'),
(1, '9', 0, 'Pola ini bukan bertambah 1, tapi bertambah 2. Jawaban yang benar adalah 10'),
(1, '12', 0, 'Pola ini bertambah 2, bukan 4. Jawaban yang benar adalah 10'),
(1, '7', 0, 'Pola ini naik, bukan turun. Jawaban yang benar adalah 10'),
(2, '25', 1, 'Benar! Pola ini adalah kelipatan 5. 20 + 5 = 25'),
(2, '24', 0, 'Ini bukan pola +4. Pola ini adalah kelipatan 5. Jawaban yang benar adalah 25'),
(2, '30', 0, 'Pola ini +5, bukan +10. Jawaban yang benar adalah 25'),
(2, '22', 0, 'Pola ini +5, bukan +2. Jawaban yang benar adalah 25')
ON DUPLICATE KEY UPDATE answer_text=answer_text;