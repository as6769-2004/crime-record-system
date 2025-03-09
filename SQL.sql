CREATE SCHEMA crime_system;
USE crime_system;

-- Police Station table (lowercase name)
CREATE TABLE policestation (
    Station_ID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100) NOT NULL,
    Code VARCHAR(20) UNIQUE NOT NULL,
    Address VARCHAR(255),
    City VARCHAR(50),
    State VARCHAR(50),
    Pincode VARCHAR(10),
    ContactNo VARCHAR(20),
    Email VARCHAR(100),
    Latitude DECIMAL(9, 6),
    Longitude DECIMAL(9, 6),
    Jurisdiction VARCHAR(255),
    OfficerInCharge VARCHAR(100),
    EstablishmentDate DATE,
    Website VARCHAR(255),
    Description TEXT
);

-- Example data (replace with your actual data)
INSERT INTO policestation (Name, Code, Address, City, State, Pincode, ContactNo, Email, Latitude, Longitude, Jurisdiction, OfficerInCharge, EstablishmentDate, Website, Description) VALUES
('Kattankulathur Police Station', 'KT001', '123 Bharathi Salai', 'Kattankulathur', 'Tamil Nadu', '603205', '+91-44-27452111', 'ktpolice@example.com', 12.8275, 80.0469, 'Kattankulathur and surrounding areas', 'Inspector K. Kumar', '1980-01-15', 'www.ktpolice.tn.gov.in', 'This is the main police station for Kattankulathur.'),
('Guindy Police Station', 'GU002', '456 Anna Salai', 'Guindy', 'Tamil Nadu', '600032', '+91-44-22358910', 'guindypolice@example.com', 13.0078, 80.2272, 'Guindy and surrounding areas', 'Inspector A. Reddy', '1975-05-20', NULL, 'Handles cases in the Guindy industrial area.'),
('T.Nagar Police Station', 'TN003', '789 Pondy Bazaar', 'T.Nagar', 'Tamil Nadu', '600017', '+91-44-24347654', 'tnagarpolice@example.com', 13.0450, 80.2446, 'T.Nagar and surrounding areas', 'Inspector S. Iyer', '1990-11-01', NULL, 'Busy police station in the heart of T.Nagar.');

-- Officer table
CREATE TABLE Officer (
    Officer_ID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100),
    Station_ID INT,
    DOB DATE,
    ContactNo VARCHAR(20),
    Email VARCHAR(100),
    Post VARCHAR(100),
    FOREIGN KEY (Station_ID) REFERENCES policestation(Station_ID)
);

-- CrimeRecord table
CREATE TABLE CrimeRecord (
    Crime_ID INT PRIMARY KEY AUTO_INCREMENT,
    Crime_Type VARCHAR(100),
    Description TEXT,
    Date DATE,
    Location VARCHAR(255),
    Officer_ID INT,
    FOREIGN KEY (Officer_ID) REFERENCES Officer(Officer_ID)
);

-- Victim table
CREATE TABLE Victim (
    Victim_ID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100),
    Age INT,
    Gender VARCHAR(10),
    Crime_ID INT,
    FOREIGN KEY (Crime_ID) REFERENCES CrimeRecord(Crime_ID)
);

-- Suspect table
CREATE TABLE Suspect (
    Suspect_ID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100),
    Gender VARCHAR(10),
    Address VARCHAR(255),
    Crime_ID INT,
    FOREIGN KEY (Crime_ID) REFERENCES CrimeRecord(Crime_ID)
);

-- Witness table
CREATE TABLE Witness (
    Witness_ID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100),
    Address VARCHAR(255),
    Statement TEXT,
    Crime_ID INT,
    FOREIGN KEY (Crime_ID) REFERENCES CrimeRecord(Crime_ID)
);

-- CaseLog table
CREATE TABLE CaseLog (
    Log_ID INT PRIMARY KEY AUTO_INCREMENT,
    Log_Date DATE,
    Action_Taken TEXT,
    Remarks TEXT,
    Crime_ID INT,
    FOREIGN KEY (Crime_ID) REFERENCES CrimeRecord(Crime_ID)
);

-- Login Info table (Corrected Station_Name to Station_ID and table reference)
CREATE TABLE LoginInfo (
    Login_ID INT AUTO_INCREMENT PRIMARY KEY,
    Station_ID INT NOT NULL,
    User_Name VARCHAR(100) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Created_At TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (Station_ID) REFERENCES policestation(Station_ID) ON DELETE CASCADE
);

-- Insert Example Login for "Central Police Station" (Corrected)
INSERT INTO LoginInfo (Station_ID, User_Name, Password)
VALUES
(1, 'KT001', 'Central@123');