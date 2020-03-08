/********************************************************************/
/*
/* CREATE Tables
/*
/********************************************************************/

CREATE TABLE user (userID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                   firstName VARCHAR(128) NOT NULL,
                   lastName VARCHAR(128) NOT NULL,
                   email VARCHAR(64) NOT NULL UNIQUE,
                   password VARCHAR(128) NOT NULL,
                   dateJoined DATETIME DEFAULT CURRENT_TIMESTAMP);