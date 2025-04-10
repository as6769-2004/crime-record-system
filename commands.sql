DELIMITER $$

CREATE TRIGGER trg_after_crime_insert
AFTER INSERT ON CRIME
FOR EACH ROW
BEGIN
    INSERT INTO CASE_LOGS (crime_id, log_date, log_entry, officer_id)
    VALUES (
        NEW.crime_id,
        NOW(),
        CONCAT('New crime registered: ', NEW.crime_type),
        NEW.officer_id
    );
END$$

DELIMITER ;




-- SHOW TRIGGERS FROM crime_system_working;



-- Cursors 

DELIMITER $$

CREATE PROCEDURE log_active_crimes()
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE c_id INT;
    DECLARE o_id INT;

    -- Cursor to fetch all active crime cases
    DECLARE cur CURSOR FOR 
        SELECT crime_id, officer_id FROM CRIME WHERE status = 'Active';

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    OPEN cur;

    read_loop: LOOP
        FETCH cur INTO c_id, o_id;

        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Insert log into CASE_LOGS
        INSERT INTO CASE_LOGS (crime_id, log_date, log_entry, officer_id)
        VALUES (
            c_id,
            NOW(),
            CONCAT('Routine log: Crime ID ', c_id, ' is still active.'),
            o_id
        );

    END LOOP;

    CLOSE cur;
END$$

DELIMITER ;




SELECT
    ps.station_name AS "Station Name",
    o.name AS "Officer Name"
FROM OFFICER o
RIGHT JOIN POLICE_STATION ps ON o.station_id = ps.station_id;







SELECT
    ps.station_name AS "Station Name",
    o.name AS "Officer Name"
FROM OFFICER o
RIGHT JOIN POLICE_STATION ps ON o.station_id = ps.station_id;




SELECT
    o.name AS "Officer Name",
    ps.station_name AS "Station Name"
FROM OFFICER o
INNER JOIN POLICE_STATION ps ON o.station_id = ps.station_id;




SELECT
    o.name AS "Officer Name",
    ps.station_name AS "Station Name"
FROM OFFICER o
FULL OUTER JOIN POLICE_STATION ps ON o.station_id = ps.station_id;



CREATE VIEW Active_Crimes AS
SELECT
    case_number,
    crime_type,
    crime_date,
    location
FROM CRIME
WHERE status IN ('Open', 'Investigating');

SHOW FULL TABLES IN crime_system_working WHERE TABLE_TYPE LIKE 'VIEW';