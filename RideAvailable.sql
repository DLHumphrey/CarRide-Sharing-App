CREATE TABLE IF NOT EXISTS RideAvailable (
 UserName VARCHAR(20) NOT NULL,
 License VARCHAR(20) NOT NULL,
 Start   DATETIME,
 End     DATETIME,
 PRIMARY KEY (UserName, License, Start, End)
);
