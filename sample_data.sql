
-- ===================== SAMPLE INSERTS =====================
-- Insert Indian police stations and officers
INSERT INTO POLICE_STATION (station_name, address, phone, precinct, district, email)
VALUES
('Mumbai Central Police Station', 'Dr. D. B. Marg, Mumbai', '02223002300', 'PS001', 'South Mumbai', 'centralps@mumbaipolice.gov.in'),
('Connaught Place Police Station', 'Connaught Circus, Delhi', '01123434333', 'PS002', 'New Delhi', 'cpstation@delhipolice.gov.in');

INSERT INTO OFFICER (name, badge_number, r_rank, station_id, phone, date_of_hire, email, status)
VALUES
('Rajesh Sharma', 'MH101', 'Inspector', 1, '9876543210', '2015-06-15', 'rajesh.sharma@mumbaipolice.gov.in', 'active'),
('Neha Singh', 'DL202', 'Sub-Inspector', 2, '8765432109', '2018-04-10', 'neha.singh@delhipolice.gov.in', 'active');

-- Insert sample victim, suspect, crime
INSERT INTO VICTIM (name, address, phone, email, date_of_birth, gender)
VALUES ('Anjali Mehta', 'Andheri East, Mumbai', '9988776655', 'anjali.mehta@gmail.com', '1990-03-25', 'female');

INSERT INTO SUSPECT (name, address, phone, date_of_birth, gender, known_offender)
VALUES ('Ravi Kumar', 'Borivali West, Mumbai', '9876543201', '1985-11-11', 'male', TRUE);

INSERT INTO CRIME (crime_type, crime_date, location, description, victim_id, suspect_id, officer_id, case_number)
VALUES ('Theft', '2024-04-10 19:00:00', 'Dadar Station', 'Mobile theft reported on platform 3', 1, 1, 1, 'CASE20240410');
