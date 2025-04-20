CREATE SCHEMA IF NOT EXISTS crime_system_working;
USE crime_system_working;
-- ===================== POLICE_STATION =====================
CREATE TABLE POLICE_STATION (
    station_id INT AUTO_INCREMENT PRIMARY KEY,
    station_name VARCHAR(255) NOT NULL,
    address TEXT,
    phone VARCHAR(20),
    precinct VARCHAR(100) NOT NULL UNIQUE,
    district VARCHAR(100),
    email VARCHAR(255) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    head_officer_id INT
);
-- ===================== OFFICER =====================
CREATE TABLE OFFICER (
    officer_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    badge_number VARCHAR(50) NOT NULL UNIQUE,
    r_rank VARCHAR(100),
    station_id INT,
    phone VARCHAR(20),
    date_of_hire DATE NOT NULL,
    email VARCHAR(255) UNIQUE,
    officer_pic VARCHAR(255),
    status ENUM('active', 'suspended', 'retired') DEFAULT 'active',
    CHECK (email LIKE '%@%'),
    FOREIGN KEY (station_id) REFERENCES POLICE_STATION(station_id) ON DELETE
    SET NULL
);
-- Add foreign key for head_officer_id after OFFICER is created
ALTER TABLE POLICE_STATION
ADD CONSTRAINT fk_head_officer FOREIGN KEY (head_officer_id) REFERENCES OFFICER(officer_id) ON DELETE
SET NULL;
-- ===================== VICTIM =====================
CREATE TABLE VICTIM (
    victim_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(255) UNIQUE,
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other'),
    victim_pic VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- ===================== SUSPECT =====================
CREATE TABLE SUSPECT (
    suspect_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address TEXT,
    phone VARCHAR(20),
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other'),
    suspect_pic VARCHAR(255),
    known_offender BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- ===================== CRIME =====================
CREATE TABLE CRIME (
    crime_id INT AUTO_INCREMENT PRIMARY KEY,
    crime_type VARCHAR(255) NOT NULL,
    crime_date DATETIME NOT NULL,
    location TEXT NOT NULL,
    description TEXT,
    victim_id INT NULL,
    suspect_id INT NULL,
    officer_id INT NULL,
    status ENUM('open', 'under investigation', 'closed') DEFAULT 'open',
    case_number VARCHAR(50) UNIQUE NOT NULL,
    reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- ===================== LOGIN_INFO =====================
CREATE TABLE LOGIN_INFO (
    login_id INT AUTO_INCREMENT PRIMARY KEY,
    officer_id INT UNIQUE,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    last_login DATETIME,
    account_status ENUM('active', 'inactive', 'locked') DEFAULT 'active',
    FOREIGN KEY (officer_id) REFERENCES OFFICER(officer_id) ON DELETE CASCADE
);
-- ===================== CASE_LOGS =====================
CREATE TABLE CASE_LOGS (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    crime_id INT,
    log_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    log_entry TEXT NOT NULL,
    officer_id INT,
    FOREIGN KEY (crime_id) REFERENCES CRIME(crime_id) ON DELETE CASCADE,
    FOREIGN KEY (officer_id) REFERENCES OFFICER(officer_id) ON DELETE
    SET NULL
);
-- ===================== CRIME_RECORDS =====================
CREATE TABLE CRIME_RECORDS (
    record_id INT AUTO_INCREMENT PRIMARY KEY,
    crime_id INT,
    record_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    summary TEXT,
    status VARCHAR(50),
    FOREIGN KEY (crime_id) REFERENCES CRIME(crime_id) ON DELETE CASCADE
);
-- ===================== WITNESS =====================
CREATE TABLE WITNESS (
    witness_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(255) UNIQUE,
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other'),
    crime_id INT,
    witness_pic VARCHAR(255),
    FOREIGN KEY (crime_id) REFERENCES CRIME(crime_id) ON DELETE
    SET NULL
);
-- ===================== EVIDENCE =====================
CREATE TABLE EVIDENCE (
    evidence_id INT AUTO_INCREMENT PRIMARY KEY,
    crime_id INT NOT NULL,
    description TEXT NOT NULL,
    location_found TEXT,
    date_collected DATETIME NOT NULL,
    file_url VARCHAR(255),
    FOREIGN KEY (crime_id) REFERENCES CRIME(crime_id) ON DELETE CASCADE
);
-- ===================== VIEW: ACTIVE CASES =====================
CREATE OR REPLACE VIEW active_cases AS
SELECT c.crime_id,
    c.case_number,
    c.crime_type,
    c.status,
    v.name AS victim_name,
    s.name AS suspect_name
FROM CRIME c
    LEFT JOIN VICTIM v ON c.victim_id = v.victim_id
    LEFT JOIN SUSPECT s ON c.suspect_id = s.suspect_id
WHERE c.status IN ('open', 'under investigation');