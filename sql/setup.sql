-- Active: 1739718093099@@127.0.0.1@3306
DROP DATABASE avature;

CREATE DATABASE IF NOT EXISTS avature;
USE avature;

-- Crear la tabla job_skill
CREATE TABLE IF NOT EXISTS job_skill (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE
);

-- Crear la tabla job_offers
CREATE TABLE IF NOT EXISTS job_offers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    salary INT NOT NULL,
    country VARCHAR(100) NOT NULL,
    description VARCHAR(255)
);

-- Tabla intermedia para la relación muchos a muchos entre job_offers y job_skill
CREATE TABLE IF NOT EXISTS job_offer_skills (
    job_offer_id INT,
    skill_id INT,
    PRIMARY KEY (job_offer_id, skill_id),
    FOREIGN KEY (job_offer_id) REFERENCES job_offers(id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES job_skill(id) ON DELETE CASCADE
);

-- Insertar datos en job_skill
INSERT IGNORE INTO job_skill (name) VALUES 
('PHP'), ('Laravel'), ('MySQL'), ('Selenium'), ('JUnit'), ('TestRail'), 
('Figma'), ('Adobe XD'), ('Sketch'), ('Scrum'), ('Kanban'), ('JIRA'), 
('Docker'), ('Kubernetes'), ('AWS'), ('PostgreSQL'), ('MongoDB'), 
('Data Analysis'), ('Reporting'), ('SQL'), ('Ethical Hacking'), 
('Penetration Testing'), ('ISO 27001'), ('Help Desk'), ('Network Configuration'), 
('Troubleshooting'), ('TensorFlow'), ('PyTorch'), ('Machine Learning');

-- Insertar datos en job_offers
INSERT INTO job_offers (title, salary, country, description) VALUES 
('Software Engineer', 40000, 'Argentina', 'Develops and maintains software.'),
('QA Engineer', 50000, 'USA', 'Ensures software quality.'),
('UX/UI Designer', 45000, 'Canada', 'Designs user experiences and interfaces.'),
('Project Manager', 70000, 'UK', 'Manages projects and work teams.'),
('DevOps Specialist', 75000, 'Germany', 'Optimizes integration and continuous delivery.'),
('Database Administrator', 60000, 'Spain', 'Manages and secures databases.'),
('Business Analyst', 55000, 'France', 'Analyzes business requirements and processes.'),
('Cybersecurity Specialist', 80000, 'Australia', 'Protects systems from digital threats.'),
('IT Support Technician', 35000, 'Mexico', 'Provides technical support to users.'),
('AI Engineer', 90000, 'Japan', 'Develops artificial intelligence solutions.');



-- Insertar datos en job_offer_skills
INSERT INTO job_offer_skills (job_offer_id, skill_id)
VALUES 
(1, 1), (1, 2), (1, 3), -- Software Engineer
(2, 4), (2, 5), (2, 6), -- QA Engineer
(3, 7), (3, 8), (3, 9), -- UX/UI Designer
(4, 10), (4, 11), (4, 12), -- Project Manager
(5, 13), (5, 14), (5, 15), -- DevOps Specialist
(6, 16), (6, 17), (6, 3), -- Database Administrator
(7, 18), (7, 19), (7, 20), -- Business Analyst
(8, 21), (8, 22), (8, 23), -- Cybersecurity Specialist
(9, 24), (9, 25), (9, 26), -- IT Support Technician
(10, 27), (10, 28), (10, 29); -- AI Engineer