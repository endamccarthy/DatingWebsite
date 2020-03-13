/********************************************************************/
/*
/* Delete all existing tables first to allow for overwriting
/*
/********************************************************************/

SET FOREIGN_KEY_CHECKS = 0;
drop table if exists user;
drop table if exists countyList;
drop table if exists events;
drop table if exists interestList;
drop table if exists interests;
drop table if exists matches;
drop table if exists pending;
drop table if exists preferences;
drop table if exists profile;
drop table if exists rejections;
SET FOREIGN_KEY_CHECKS = 1;


/********************************************************************/
/*
/* CREATE Tables
/*
/********************************************************************/

CREATE TABLE user (
  userID INT NOT NULL,
  firstName VARCHAR(128) NOT NULL,
  lastName VARCHAR(128) NOT NULL,
  email VARCHAR(128) NOT NULL UNIQUE,
  password VARCHAR(128) NOT NULL,
  dateJoined DATETIME NULL DEFAULT CURRENT_TIMESTAMP, 
  accessLevel ENUM("regular", "premium", "admin") NULL DEFAULT "regular",
  status ENUM("active", "banned", "suspended") NULL DEFAULT "active",
  notifications INT NULL DEFAULT 0
);

CREATE TABLE interestList (
  interestID INT NOT NULL,
  interestName VARCHAR(128) NOT NULL UNIQUE
);

CREATE TABLE interests (
  userID INT NOT NULL,
  interestID INT NOT NULL
);

CREATE TABLE countyList (
  countyID INT NOT NULL,
  countyName VARCHAR(128) NOT NULL UNIQUE
);

CREATE TABLE profile (
  userID INT NOT NULL,
  description TEXT(1000) NULL,
  gender ENUM("male", "female") NOT NULL,
  dateOfBirth DATETIME NOT NULL,
  countyID INT NOT NULL,
  photo VARCHAR(128) NOT NULL,
  smokes ENUM("smoker", "non-smoker") NOT NULL,
  height INT NOT NULL
);

CREATE TABLE preferences (
  userID INT NOT NULL,
  prefGender ENUM("male", "female") NOT NULL,
  prefAgeRange ENUM("<30", "30-40", "41-50", ">50") NULL,
  prefCountyID INT NULL,
  prefInterestID INT NULL,
  prefSmokes ENUM("smoker", "non-smoker") NULL,
  prefHeightRange ENUM("<170", "170-180", "<181-190", ">190") NULL
);

CREATE TABLE pending (
  pendingUserOne INT NOT NULL,
  pendingUserTwo INT NOT NULL
);

CREATE TABLE matches (
  matchesUserOne INT NOT NULL,
  matchesUserTwo INT NOT NULL
);

CREATE TABLE rejections (
  rejectionsUserOne INT NOT NULL,
  rejectionsUserTwo INT NOT NULL
);

CREATE TABLE events (
  eventID INT NOT NULL,
  eventCountyID INT NOT NULL,
  eventName VARCHAR(128) NOT NULL,
  eventDate DATETIME, 
  eventWebsite VARCHAR(128) NULL DEFAULT ""
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

ALTER TABLE countyList
  CHANGE countyID countyID INT NOT NULL PRIMARY KEY AUTO_INCREMENT;

ALTER TABLE profile
  ADD PRIMARY KEY (userID),
  ADD KEY countyID (countyID);

ALTER TABLE preferences
  ADD PRIMARY KEY (userID),
  ADD KEY prefCountyID (prefCountyID),
  ADD KEY prefInterestID (prefInterestID);
  
ALTER TABLE pending
  ADD PRIMARY KEY (pendingUserOne, pendingUserTwo),
  ADD KEY pendingUserOne (pendingUserOne),
  ADD KEY pendingUserTwo (pendingUserTwo);
  
ALTER TABLE matches
  ADD PRIMARY KEY (matchesUserOne, matchesUserTwo),
  ADD KEY matchesUserOne (matchesUserOne),
  ADD KEY matchesUserTwo (matchesUserTwo);  
  
ALTER TABLE rejections
  ADD PRIMARY KEY (rejectionsUserOne, rejectionsUserTwo),
  ADD KEY rejectionsUserOne (rejectionsUserOne),
  ADD KEY rejectionsUserTwo (rejectionsUserTwo); 
  
ALTER TABLE events
  CHANGE eventID eventID INT NOT NULL PRIMARY KEY AUTO_INCREMENT;
  
/********************************************************************/
/*
/* CONSTRAINTS
/*
/********************************************************************/

ALTER TABLE interests
  ADD CONSTRAINT interests_ibfk_1 FOREIGN KEY (userID) REFERENCES user (userID),
  ADD CONSTRAINT interests_ibfk_2 FOREIGN KEY (interestID) REFERENCES interestList (interestID);

ALTER TABLE preferences
  ADD CONSTRAINT preferences_ibfk_1 FOREIGN KEY (userID) REFERENCES user (userID),
  ADD CONSTRAINT preferences_ibfk_2 FOREIGN KEY (prefCountyID) REFERENCES countyList (countyID),
  ADD CONSTRAINT preferences_ibfk_3 FOREIGN KEY (prefInterestID) REFERENCES interestList (interestID);
  
ALTER TABLE profile
  ADD CONSTRAINT profile_ibfk_1 FOREIGN KEY (userID) REFERENCES user (userID),
  ADD CONSTRAINT profile_ibfk_2 FOREIGN KEY (countyID) REFERENCES countyList (countyID);
	
ALTER TABLE pending
  ADD CONSTRAINT pending_ibfk_1 FOREIGN KEY (pendingUserOne) REFERENCES user (userID),
  ADD CONSTRAINT pending_ibfk_2 FOREIGN KEY (pendingUserTwo) REFERENCES user (userID);

ALTER TABLE matches
  ADD CONSTRAINT matches_ibfk_1 FOREIGN KEY (matchesUserOne) REFERENCES user (userID),
  ADD CONSTRAINT matches_ibfk_2 FOREIGN KEY (matchesUserTwo) REFERENCES user (userID);
  
ALTER TABLE rejections
  ADD CONSTRAINT rejections_ibfk_1 FOREIGN KEY (rejectionsUserOne) REFERENCES user (userID),
  ADD CONSTRAINT rejections_ibfk_2 FOREIGN KEY (rejectionsUserTwo) REFERENCES user (userID);

ALTER TABLE events
  ADD CONSTRAINT events_ibfk_1 FOREIGN KEY (eventCountyID) REFERENCES countyList (CountyID);

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

INSERT INTO countyList(countyName) VALUES
  ('Antrim'),
  ('Armagh'),
  ('Carlow'),
  ('Cavan'),
  ('Clare');

INSERT INTO user(firstName, lastName, email, password) VALUES
  ('John', 'Smith', 'john@email.com', 'password'),
  ('Una', 'Maher', 'una@email.com', 'password'),
  ('Sean', 'Breen', 'sean@email.com', 'password'),
  ('Kate', 'Dunne', 'kate@email.com', 'password'),
  ('Jack', 'Murphy', 'jack@email.com', 'password'),
  ('Mary', 'Treacy', 'mary@email.com', 'password');

INSERT INTO interests(userID, interestID) VALUES
  (1, 1),
  (1, 2),
  (2, 1),
  (2, 5),
  (3, 2),
  (3, 5),
  (4, 3),
  (4, 4),
  (5, 2),
  (5, 5),
  (6, 3),
  (6, 4);

INSERT INTO profile(userID, description, gender, dateOfBirth, countyID, photo, smokes, height) VALUES
  (1, NULL, 'male', '1990-04-12', 2, 'images/photo1.jpg', 'non-smoker', 176),
  (2, "My name is Una and I'm from Armagh!", 'female', '1980-06-20', 2, 'images/photo2.jpg', 'smoker', 160),
  (3, NULL, 'male', '1975-01-15', 3, 'images/photo3.jpg', 'non-smoker', 181),
  (4, NULL, 'female', '1994-11-07', 1, 'images/photo4.jpg', 'non-smoker', 167),
  (5, NULL, 'male', '1985-04-15', 4, 'images/photo5.jpg', 'non-smoker', 184),
  (6, NULL, 'female', '1982-12-07', 5, 'images/photo6.jpg', 'non-smoker', 169);

INSERT INTO preferences(userID, prefGender, prefAgeRange, prefCountyID, prefInterestID, prefSmokes, prefHeightRange) VALUES
  (1, 'female', '<30', NULL, 2, 'non-smoker', NULL),
  (2, 'male', '30-40', 4, NULL, 'smoker', '170-180'),
  (3, 'female', '41-50', NULL, 1, 'non-smoker', NULL),
  (4, 'male', '<30', 3, 5, 'non-smoker', NULL),
  (5, 'female', '41-50', NULL, 1, 'non-smoker', NULL),
  (6, 'male', '<30', 3, 5, 'non-smoker', NULL);

INSERT INTO pending(pendingUserOne, pendingUserTwo) VALUES
  (1, 2),
  (2, 5);

INSERT INTO matches(matchesUserOne, matchesUserTwo) VALUES
  (3, 4),
  (5, 6);

INSERT INTO rejections(rejectionsUserOne, rejectionsUserTwo) VALUES
  (6, 1),
  (5, 4);

INSERT INTO events(eventCountyID, eventName, eventDate) VALUES
  (3, 'An Event', '2020-04-10');
