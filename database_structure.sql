-- CV Management System Database Structure
-- Database: ipt_group_project

CREATE DATABASE IF NOT EXISTS ipt_group_project;
USE ipt_group_project;

-- Table: personal_information
CREATE TABLE IF NOT EXISTS personal_information (
    id INT AUTO_INCREMENT PRIMARY KEY,
    photo VARCHAR(255),
    given_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    surname VARCHAR(100) NOT NULL,
    extension VARCHAR(20),
    gender ENUM('male', 'female') NOT NULL,
    birthdate DATE NOT NULL,
    birthplace VARCHAR(200) NOT NULL,
    civil_status ENUM('single', 'married', 'divorced', 'widowed') NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    address TEXT NOT NULL,
    website VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: career_objectives
CREATE TABLE IF NOT EXISTS career_objectives (
    id INT AUTO_INCREMENT PRIMARY KEY,
    personal_info_id INT NOT NULL,
    objective TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (personal_info_id) REFERENCES personal_information(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: education
CREATE TABLE IF NOT EXISTS education (
    id INT AUTO_INCREMENT PRIMARY KEY,
    personal_info_id INT NOT NULL,
    degree VARCHAR(200) NOT NULL,
    institution VARCHAR(200) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (personal_info_id) REFERENCES personal_information(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: work_experience
CREATE TABLE IF NOT EXISTS work_experience (
    id INT AUTO_INCREMENT PRIMARY KEY,
    personal_info_id INT NOT NULL,
    job_title VARCHAR(200) NOT NULL,
    city VARCHAR(100) NOT NULL,
    employer VARCHAR(200) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (personal_info_id) REFERENCES personal_information(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: skills
CREATE TABLE IF NOT EXISTS skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    personal_info_id INT NOT NULL,
    skill_name VARCHAR(100) NOT NULL,
    level ENUM('Expert', 'Experienced', 'Skillful', 'Intermediate', 'Beginner') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (personal_info_id) REFERENCES personal_information(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: interests
CREATE TABLE IF NOT EXISTS interests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    personal_info_id INT NOT NULL,
    interests TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (personal_info_id) REFERENCES personal_information(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: reference
CREATE TABLE IF NOT EXISTS reference (
    id INT AUTO_INCREMENT PRIMARY KEY,
    personal_info_id INT NOT NULL,
    company_name VARCHAR(200) NOT NULL,
    contact_person VARCHAR(200) NOT NULL,
    phone_number VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (personal_info_id) REFERENCES personal_information(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create indexes for better search performance
CREATE INDEX idx_personal_name ON personal_information(given_name, middle_name, surname);
CREATE INDEX idx_personal_email ON personal_information(email);
CREATE INDEX idx_personal_phone ON personal_information(phone);
CREATE INDEX idx_skills_name ON skills(skill_name);
