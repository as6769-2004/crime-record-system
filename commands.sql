-- View for victim details per crime
CREATE OR REPLACE VIEW view_crime_victims AS
SELECT
    cv.crime_id,
    v.victim_id,
    v.name AS victim_name,
    v.address AS victim_address,
    v.phone AS victim_phone,
    v.email AS victim_email,
    v.date_of_birth AS victim_dob,
    v.gender AS victim_gender,
    v.victim_pic
FROM crime_victims cv
JOIN victim v ON cv.victim_id = v.victim_id;

-- View for suspect details per crime
CREATE OR REPLACE VIEW view_crime_suspects AS
SELECT
    cs.crime_id,
    s.suspect_id,
    s.name AS suspect_name,
    s.address AS suspect_address,
    s.phone AS suspect_phone,
    s.date_of_birth AS suspect_dob,
    s.gender AS suspect_gender,
    s.suspect_pic,
    s.known_offender
FROM crime_suspects cs
JOIN suspect s ON cs.suspect_id = s.suspect_id;

-- View for witness details per crime
CREATE OR REPLACE VIEW view_crime_witnesses AS
SELECT
    cw.crime_id,
    w.witness_id,
    w.name AS witness_name,
    w.address AS witness_address,
    w.phone AS witness_phone,
    w.email AS witness_email,
    w.date_of_birth AS witness_dob,
    w.gender AS witness_gender,
    w.witness_pic
FROM crime_witnesses cw
JOIN witness w ON cw.witness_id = w.witness_id;

-- // a view table that includes the officer name, crime type, victim details, witness details, and suspect details:

CREATE OR REPLACE VIEW view_crime_details_full AS
SELECT
    o.name AS officer_name,
    c.crime_type,
    v.name AS victim_name,
    v.address AS victim_address,
    v.phone AS victim_phone,
    w.name AS witness_name,
    w.address AS witness_address,
    w.phone AS witness_phone,
    s.name AS suspect_name,
    s.address AS suspect_address,
    s.phone AS suspect_phone
FROM crime c
JOIN officer o ON c.officer_id = o.officer_id
JOIN crime_victims cv ON c.crime_id = cv.crime_id
JOIN victim v ON cv.victim_id = v.victim_id
JOIN crime_witnesses cw ON c.crime_id = cw.crime_id
JOIN witness w ON cw.witness_id = w.witness_id
JOIN crime_suspects cs ON c.crime_id = cs.crime_id
JOIN suspect s ON cs.suspect_id = s.suspect_id;
