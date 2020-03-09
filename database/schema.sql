/********************************************************************/
/*
/* CREATE Tables
/*
/********************************************************************/

CREATE TABLE user (
  userID INT NOT NULL,
  firstName VARCHAR(128) NOT NULL,
  lastName VARCHAR(128) NOT NULL,
  email VARCHAR(64) NOT NULL UNIQUE,
  password VARCHAR(128) NOT NULL,
  dateJoined DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE interestList (
  interestID INT NOT NULL,
  interestName VARCHAR(128) NOT NULL UNIQUE
);

CREATE TABLE interests (
  userID INT NOT NULL,
  interestID INT NOT NULL
);


/********************************************************************/
/*
/* INDEXES
/*
/********************************************************************/

ALTER TABLE user
  CHANGE userID userID INT NOT NULL PRIMARY KEY AUTO_INCREMENT;

ALTER TABLE interestList
  CHANGE interestID interestID INT NOT NULL PRIMARY KEY AUTO_INCREMENT;

ALTER TABLE interests
  ADD PRIMARY KEY (userID, interestID),
  ADD KEY userID (userID),
  ADD KEY interestID (interestID);


/********************************************************************/
/*
/* CONSTRAINTS
/*
/********************************************************************/

ALTER TABLE interests
  ADD CONSTRAINT interests_ibfk_1 FOREIGN KEY (userID) REFERENCES user (userID),
  ADD CONSTRAINT interests_ibfk_2 FOREIGN KEY (interestID) REFERENCES interestList (interestID);


/********************************************************************/
/*
/* INSERTS 
/*
/********************************************************************/

INSERT INTO interestList(interestName) VALUES
  ('Hurling'),
  ('Soccer'),
  ('Reading'),
  ('Travelling'),
  ('Baking');