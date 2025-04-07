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
    station_id INT NULL,
    phone VARCHAR(20),
    date_of_hire DATE NOT NULL,
    email VARCHAR(255) UNIQUE,
    officer_pic VARCHAR(225),
    FOREIGN KEY (station_id) REFERENCES POLICE_STATION(station_id) ON DELETE SET NULL
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
    victim_pic VARCHAR(225)
);

-- SUSPECT Table
CREATE TABLE SUSPECT (
    suspect_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(255),
    phone VARCHAR(20),
    date_of_birth DATE,
    gender VARCHAR(20),
    suspect_pic VARCHAR(225)
);

-- CRIME Table
CREATE TABLE CRIME (
    crime_id INT PRIMARY KEY AUTO_INCREMENT,
    crime_type VARCHAR(255) NOT NULL,
    crime_date DATETIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    description TEXT,
    victim_id INT NULL,
    suspect_id INT NULL,
    officer_id INT NULL,
    status VARCHAR(50) NOT NULL,
    case_number VARCHAR(50) UNIQUE NOT NULL,
    FOREIGN KEY (victim_id) REFERENCES VICTIM(victim_id) ON DELETE SET NULL,
    FOREIGN KEY (suspect_id) REFERENCES SUSPECT(suspect_id) ON DELETE SET NULL,
    FOREIGN KEY (officer_id) REFERENCES OFFICER(officer_id) ON DELETE SET NULL
);

-- LOGIN_INFO Table
CREATE TABLE LOGIN_INFO (
    login_id INT PRIMARY KEY AUTO_INCREMENT,
    officer_id INT UNIQUE,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    last_login DATETIME,
    account_status VARCHAR(50),
    FOREIGN KEY (officer_id) REFERENCES OFFICER(officer_id) ON DELETE SET NULL
);

-- CASE_LOGS Table
CREATE TABLE CASE_LOGS (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    crime_id INT NULL,
    log_date DATETIME NOT NULL,
    log_entry TEXT NOT NULL,
    officer_id INT NULL,
    FOREIGN KEY (crime_id) REFERENCES CRIME(crime_id) ON DELETE SET NULL,
    FOREIGN KEY (officer_id) REFERENCES OFFICER(officer_id) ON DELETE SET NULL
);

-- CRIME_RECORDS Table
CREATE TABLE CRIME_RECORDS (
    record_id INT PRIMARY KEY AUTO_INCREMENT,
    crime_id INT NULL,
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
    crime_id INT NULL,
    witness_pic VARCHAR(225),
    FOREIGN KEY (crime_id) REFERENCES CRIME(crime_id) ON DELETE SET NULL
);

-- EVIDENCE Table
CREATE TABLE EVIDENCE (
    evidence_id INT PRIMARY KEY AUTO_INCREMENT,
    crime_id INT NULL,
    description TEXT NOT NULL,
    location_found VARCHAR(255),
    date_collected DATETIME NOT NULL,
    FOREIGN KEY (crime_id) REFERENCES CRIME(crime_id) ON DELETE SET NULL
);













USE crime_system_working;

-- POLICE_STATION
INSERT INTO POLICE_STATION (station_name, address, phone, precinct, district, email)
VALUES
('Central Police Station', '123 Main St', '9876543210', 'CENTRAL001', 'Downtown', 'central@police.gov'),
('East Side Station', '456 East Ave', '9123456780', 'EAST002', 'Eastside', 'east@police.gov'),
('West End Station', '789 West Blvd', '9988776655', 'WEST003', 'Westend', 'west@police.gov'),
('North Point Station', '321 North Rd', '9012345678', 'NORTH004', 'Northpoint', 'north@police.gov'),
('South Hills Station', '654 South Hill', '9871234567', 'SOUTH005', 'Southhills', 'south@police.gov');

-- OFFICER
INSERT INTO OFFICER (name, badge_number, station_id, phone, date_of_hire, email, officer_pic)
VALUES
('Raj Kumar', 'BN001', 1, '9876543211', '2015-06-10', 'raj.kumar@police.gov', 'raj.jpg'),
('Anita Verma', 'BN002', 2, '9123456790', '2017-03-15', 'anita.verma@police.gov', 'anita.jpg'),
('David John', 'BN003', 3, '9988776654', '2018-09-01', 'david.john@police.gov', 'david.jpg'),
('Meena Das', 'BN004', 4, '9012345689', '2016-11-20', 'meena.das@police.gov', 'meena.jpg'),
('Sanjay Rao', 'BN005', 5, '9871234568', '2014-04-05', 'sanjay.rao@police.gov', 'sanjay.jpg');

-- VICTIM
INSERT INTO VICTIM (name, address, phone, email, date_of_birth, gender, victim_pic)
VALUES
('Rahul Sharma', '12 Park Lane', '8881112233', 'rahul@gmail.com', '1990-04-15', 'Male', 'rahul.jpg'),
('Priya Singh', '45 MG Road', '7772223344', 'priya@gmail.com', '1992-06-20', 'Female', 'priya.jpg'),
('Arun Nair', '78 Patel Street', '6663334455', 'arun@gmail.com', '1985-11-25', 'Male', 'arun.jpg'),
('Neha Joshi', '56 Lotus Ave', '9994445566', 'neha@gmail.com', '1995-09-10', 'Female', 'neha.jpg'),
('Vinod Menon', '34 Ring Road', '5556667778', 'vinod@gmail.com', '1988-01-05', 'Male', 'vinod.jpg');

-- SUSPECT
INSERT INTO SUSPECT (name, address, phone, date_of_birth, gender, suspect_pic)
VALUES
('Ravi Teja', '87 Blue St', '9112233445', '1984-07-11', 'Male', 'ravi.jpg'),
('Kiran Bedi', '29 Red Cross', '9321456789', '1975-12-09', 'Female', 'kiran.jpg'),
('Sameer Khan', '62 Bridge Lane', '9456127890', '1990-10-05', 'Male', 'sameer.jpg'),
('Anjali Mehra', '31 Rose Garden', '9011223344', '1982-03-08', 'Female', 'anjali.jpg'),
('Rohit Desai', '73 Moon Road', '9234567890', '1993-08-14', 'Male', 'rohit.jpg');

-- CRIME
INSERT INTO CRIME (crime_id, crime_type, crime_date, location, description, victim_id, suspect_id, officer_id, status, case_number)
VALUES
(1, 'Theft', '2024-03-15 10:30:00', 'City Mall', 'Wallet stolen from victim’s bag', 1, 1, 1, 'Open', 'CASE001'),
(2, 'Assault', '2024-02-22 19:00:00', 'MG Road', 'Physical assault during argument', 2, 2, 2, 'Under Investigation', 'CASE002'),
(3, 'Burglary', '2024-01-10 02:15:00', 'Green Apartments', 'House break-in reported', 3, 3, 3, 'Solved', 'CASE003'),
(4, 'Cyber Fraud', '2024-03-01 16:00:00', 'Online', 'Phishing scam incident', 4, 4, 4, 'Open', 'CASE004'),
(5, 'Hit and Run', '2024-03-20 21:00:00', 'Ring Road', 'Car hit victim and fled', 5, 5, 5, 'Closed', 'CASE005');

-- LOGIN_INFO
INSERT INTO LOGIN_INFO (officer_id, username, password, last_login, account_status)
VALUES
(1, 'raj.kumar', 'pass123', '2025-04-06 09:30:00', 'Active'),
(2, 'anita.verma', 'pass456', '2025-04-05 14:15:00', 'Active'),
(3, 'david.john', 'pass789', '2025-04-04 12:00:00', 'Inactive'),
(4, 'meena.das', 'pass321', '2025-04-06 08:00:00', 'Active'),
(5, 'sanjay.rao', 'pass654', '2025-04-03 17:45:00', 'Suspended');

-- CASE_LOGS
INSERT INTO CASE_LOGS (crime_id, log_date, log_entry, officer_id)
VALUES
(1, '2024-03-16 11:00:00', 'Investigation initiated.', 1),
(2, '2024-02-23 10:00:00', 'Statements recorded.', 2),
(3, '2024-01-11 14:30:00', 'Evidence collected from crime scene.', 3),
(4, '2024-03-02 17:45:00', 'Digital trail traced.', 4),
(5, '2024-03-21 10:20:00', 'Witness identified the vehicle.', 5);

-- CRIME_RECORDS
INSERT INTO CRIME_RECORDS (crime_id, record_date, summary, status)
VALUES
(1, '2024-03-16 15:00:00', 'Crime scene inspected and photographed.', 'Ongoing'),
(2, '2024-02-23 12:00:00', 'Medical report of victim received.', 'Pending'),
(3, '2024-01-12 10:00:00', 'Suspect arrested and questioned.', 'Closed'),
(4, '2024-03-03 18:00:00', 'Cyber security team notified.', 'Ongoing'),
(5, '2024-03-22 11:30:00', 'Vehicle traced to nearby district.', 'Closed');

-- WITNESS
INSERT INTO WITNESS (name, address, phone, email, date_of_birth, gender, crime_id, witness_pic)
VALUES
('Amit Jha', '23 Civil Lines', '9898989898', 'amit.jha@gmail.com', '1980-02-14', 'Male', 1, 'amit.jpg'),
('Sneha Rao', '89 Hill View', '9777766666', 'sneha.rao@gmail.com', '1993-07-22', 'Female', 2, 'sneha.jpg'),
('Karthik Raj', '52 College Rd', '9666655555', 'karthik.raj@gmail.com', '1991-09-30', 'Male', 3, 'karthik.jpg'),
('Nikita Dey', '44 Lake Park', '9555544444', 'nikita.dey@gmail.com', '1988-11-11', 'Female', 4, 'nikita.jpg'),
('Imran Ali', '10 Station Road', '9444433333', 'imran.ali@gmail.com', '1985-03-01', 'Male', 5, 'imran.jpg');

-- EVIDENCE
INSERT INTO EVIDENCE (crime_id, description, location_found, date_collected)
VALUES
(1, 'Wallet with ID cards', 'City Mall Floor 2', '2024-03-15 12:00:00'),
(2, 'Blood-stained cloth', 'MG Road footpath', '2024-02-22 20:00:00'),
(3, 'Broken window glass', 'Green Apartments', '2024-01-10 03:00:00'),
(4, 'Email trail logs', 'Victim’s laptop', '2024-03-01 17:00:00'),
(5, 'Car bumper fragment', 'Ring Road crossing', '2024-03-20 21:30:00');
