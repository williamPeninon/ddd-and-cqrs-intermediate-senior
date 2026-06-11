CREATE TABLE IF NOT EXISTS fleets (
    id VARCHAR(64) PRIMARY KEY,
    owner_id VARCHAR(128) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS vehicles (
    plate_number VARCHAR(64) PRIMARY KEY
);

CREATE TABLE IF NOT EXISTS fleet_vehicles (
    fleet_id VARCHAR(64) NOT NULL REFERENCES fleets(id) ON DELETE CASCADE,
    vehicle_plate_number VARCHAR(64) NOT NULL REFERENCES vehicles(plate_number) ON DELETE CASCADE,
    PRIMARY KEY (fleet_id, vehicle_plate_number)
);

CREATE TABLE IF NOT EXISTS vehicle_locations (
    fleet_id VARCHAR(64) NOT NULL,
    vehicle_plate_number VARCHAR(64) NOT NULL,
    latitude DOUBLE PRECISION NOT NULL,
    longitude DOUBLE PRECISION NOT NULL,
    altitude DOUBLE PRECISION NULL,
    localized_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (fleet_id, vehicle_plate_number),
    FOREIGN KEY (fleet_id, vehicle_plate_number)
        REFERENCES fleet_vehicles(fleet_id, vehicle_plate_number)
        ON DELETE CASCADE
);
