CREATE TABLE  IF NOT EXISTS RideNeeded (
    UserName          varchar(20)     NOT NULL,
    Start   	     DATETIME NOT NULL,
    End   	     DATETIME NOT NULL,
    PRIMARY KEY (UserName, Start, End)
);

