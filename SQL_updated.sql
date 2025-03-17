CREATE SCHEMA crime_system_working;
USE crime_system_working;
-- POLICE_STATION Table
CREATE TABLE POLICE_STATION (
    station_id INT PRIMARY KEY AUTO_INCREMENT,
    station_name VARCHAR(255) NOT NULL,
    address VARCHAR(255),
    phone VARCHAR(20),
    precinct VARCHAR(100) UNIQUE NOT NULL,
    district VARCHAR(100),
    email VARCHAR(255),
    head_officer_id INT
);
-- OFFICER Table
CREATE TABLE OFFICER (
    officer_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    badge_number VARCHAR(50) UNIQUE NOT NULL,
    station_id INT,
    phone VARCHAR(20),
    date_of_hire DATE NOT NULL,
    email VARCHAR(255) UNIQUE,
    officer_pic VARCHAR(225,) FOREIGN KEY (station_id) REFERENCES POLICE_STATION(station_id) ON DELETE
    SET NULL
);
-- VICTIM Table
CREATE TABLE VICTIM (
    victim_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(255),
    phone VARCHAR(20),
    email VARCHAR(255) UNIQUE,
    date_of_birth DATE,
    gender VARCHAR(20),
    victim_pic VARCHAR(225,)
);
-- SUSPECT Table
CREATE TABLE SUSPECT (
    suspect_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(255),
    phone VARCHAR(20),
    date_of_birth DATE,
    gender VARCHAR(20),
    suspect_pic VARCHAR(225,)
);
-- CRIME Table
CREATE TABLE CRIME (
    crime_id INT PRIMARY KEY AUTO_INCREMENT,
    crime_type VARCHAR(255) NOT NULL,
    crime_date DATETIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    description TEXT,
    victim_id INT,
    suspect_id INT,
    officer_id INT,
    status VARCHAR(50) NOT NULL,
    case_number VARCHAR(50) UNIQUE NOT NULL,
    FOREIGN KEY (victim_id) REFERENCES VICTIM(victim_id) ON DELETE
    SET NULL,
        FOREIGN KEY (suspect_id) REFERENCES SUSPECT(suspect_id) ON DELETE
    SET NULL,
        FOREIGN KEY (officer_id) REFERENCES OFFICER(officer_id) ON DELETE
    SET NULL
);
-- LOGIN_INFO Table
CREATE TABLE LOGIN_INFO (
    login_id INT PRIMARY KEY AUTO_INCREMENT,
    officer_id INT UNIQUE,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    last_login DATETIME,
    account_status VARCHAR(50),
    FOREIGN KEY (officer_id) REFERENCES OFFICER(officer_id) ON DELETE
    SET NULL
);
-- CASE_LOGS Table
CREATE TABLE CASE_LOGS (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    crime_id INT,
    log_date DATETIME NOT NULL,
    log_entry TEXT NOT NULL,
    officer_id INT,
    FOREIGN KEY (crime_id) REFERENCES CRIME(crime_id) ON DELETE
    SET NULL,
        FOREIGN KEY (officer_id) REFERENCES OFFICER(officer_id) ON DELETE
    SET NULL
);
-- CRIME_RECORDS Table
CREATE TABLE CRIME_RECORDS (
    record_id INT PRIMARY KEY AUTO_INCREMENT,
    crime_id INT,
    record_date DATETIME NOT NULL,
    summary TEXT,
    status VARCHAR(50),
    FOREIGN KEY (crime_id) REFERENCES CRIME(crime_id) ON DELETE SET NULL
);
-- WITNESS Table
CREATE TABLE WITNESS (
    witness_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(255),
    phone VARCHAR(20),
    email VARCHAR(255) UNIQUE,
    date_of_birth DATE,
    gender VARCHAR(20),
    crime_id INT,
    witness_pic VARCHAR(225,),
    FOREIGN KEY (crime_id) REFERENCES CRIME(crime_id) ON DELETE SET NULL
);
-- EVIDENCE Table
CREATE TABLE EVIDENCE (
    evidence_id INT PRIMARY KEY AUTO_INCREMENT,
    crime_id INT NOT NULL,
    description TEXT NOT NULL,
    location_found VARCHAR(255),
    date_collected DATETIME NOT NULL,
    FOREIGN KEY (crime_id) REFERENCES CRIME(crime_id) ON DELETE SET NULL
);