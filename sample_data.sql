-- ===================== SAMPLE INSERTS =====================

-- Insert Indian police stations
INSERT INTO POLICE_STATION (station_name, address, phone, precinct, district, email)
VALUES
('Mumbai Central Police Station', 'Dr. D. B. Marg, Mumbai', '02223002300', 'PS001', 'South Mumbai', 'centralps@mumbaipolice.gov.in'),
('Connaught Place Police Station', 'Connaught Circus, Delhi', '01123434333', 'PS002', 'New Delhi', 'cpstation@delhipolice.gov.in');

-- Insert officers
INSERT INTO OFFICER (name, badge_number, r_rank, station_id, phone, date_of_hire, email, status)
VALUES
('Rajesh Sharma', 'MH101', 'Inspector', 1, '9876543210', '2015-06-15', 'rajesh.sharma@mumbaipolice.gov.in', 'active'),
('Neha Singh', 'DL202', 'Sub-Inspector', 2, '8765432109', '2018-04-10', 'neha.singh@delhipolice.gov.in', 'active');

-- Update head officer in police stations (optional but ideal)
UPDATE POLICE_STATION SET head_officer_id = 1 WHERE station_id = 1;
UPDATE POLICE_STATION SET head_officer_id = 2 WHERE station_id = 2;

-- Insert sample victim
INSERT INTO VICTIM (name, address, phone, email, date_of_birth, gender)
VALUES 
('Anjali Mehta', 'Andheri East, Mumbai', '9988776655', 'anjali.mehta@gmail.com', '1990-03-25', 'female');

-- Insert sample suspect
INSERT INTO SUSPECT (name, address, phone, date_of_birth, gender, known_offender)
VALUES 
('Ravi Kumar', 'Borivali West, Mumbai', '9876543201', '1985-11-11', 'male', TRUE);

-- Insert a sample crime
INSERT INTO CRIME (crime_type, crime_date, location, description, officer_id, case_number)
VALUES 
('Theft', '2024-04-10 19:00:00', 'Dadar Station', 'Mobile theft reported on platform 3', 1, 'CN001');

-- Link the victim to the crime
INSERT INTO crime_victims (crime_id, victim_id)
VALUES (1, 1);

-- Link the suspect to the crime
INSERT INTO crime_suspects (crime_id, suspect_id)
VALUES (1, 1);

-- (Optional) Insert a witness and link if needed
-- INSERT INTO WITNESS (name, address, phone, email, date_of_birth, gender)
-- VALUES ('Sunil Verma', 'Dadar West, Mumbai', '9876512345', 'sunil.verma@example.com', '1988-09-10', 'male');
-- INSERT INTO crime_witnesses (crime_id, witness_id)
-- VALUES (1, 1);

INSERT INTO WITNESS (name, address, phone, email, date_of_birth, gender, witness_pic, created_by) VALUES
('John Doe', '1234 Maple Drive, Springfield', '+1 555-1234', 'johndoe@example.com', '1987-05-25', 'male', 'john_doe.jpg', 1),
('Jane Smith', '2345 Birch Street, Springfield', '+1 555-5678', 'janesmith@example.com', '1992-03-14', 'female', 'jane_smith.jpg', 2),
('Michael Johnson', '3456 Cedar Lane, Springfield', '+1 555-9876', 'michaelj@example.com', '1980-09-30', 'male', 'michael_johnson.jpg', 1);

INSERT INTO SUSPECT (name, address, phone, date_of_birth, gender, suspect_pic, known_offender, created_by) VALUES
('David Miller', '1234 Elm Street, Springfield', '+1 555-1234', '1985-07-12', 'male', 'david_miller.jpg', TRUE, 1),
('Alice Cooper', '5678 Oak Avenue, Springfield', '+1 555-5678', '1990-02-15', 'female', 'alice_cooper.jpg', FALSE, 2),
('Brian Taylor', '9101 Pine Road, Springfield', '+1 555-9876', '1978-11-22', 'male', 'brian_taylor.jpg', TRUE, 1);
