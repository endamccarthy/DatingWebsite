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
  password VARCHAR(256) NOT NULL,
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
  prefAgeMin INT NULL DEFAULT 18,
  prefAgeMax INT NULL DEFAULT 100,
  prefCountyID INT NULL,
  prefInterestID INT NULL,
  prefSmokes ENUM("smoker", "non-smoker") NULL,
  prefHeightMin INT NULL DEFAULT 100,
  prefHeightMax INT NULL DEFAULT 250
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
  ('Clare'),
  ('Cork'),
  ('Derry'),
  ('Donegal'),
  ('Down'),
  ('Dublin'),
  ('Fermanagh'),
  ('Galway'),
  ('Kerry'),
  ('Kildare'),
  ('Kilkenny'),
  ('Laois'),
  ('Leitrim'),
  ('Limerick'),
  ('Longford'),
  ('Louth'),
  ('Mayo'),
  ('Meath'),
  ('Monaghan'),
  ('Offaly'),
  ('Roscommon'),
  ('Sligo'),
  ('Tipperary'),
  ('Tyrone'),
  ('Waterford'),
  ('Westmeath'),
  ('Wexford'),
  ('Wicklow');

/* the unhashed password for the test user is 'password' */
INSERT INTO user(firstName, lastName, email, password) VALUES
  ('test', 'user', 'test@email.com', '$2y$10$Pbnnd7yvpbt/FDWpLSirVuEW9rgq7VlpULrNCN44G/LsJWzkGdQr2'),
  ('John', 'Smith', 'john@email.com', 'password'),
  ('Una', 'Maher', 'una@email.com', 'password'),
  ('Sean', 'Breen', 'sean@email.com', 'password'),
  ('Kate', 'Dunne', 'kate@email.com', 'password'),
  ('Jack', 'Murphy', 'jack@email.com', 'password'),
  ('Mary', 'Carey', 'mary@email.com', 'password'),
  ('Aine', 'Ryan', 'aine@email.com', 'password'),
  ('Shauna', 'Fitzgerald', 'shauna@email.com', 'password'),
  ('Sinead', 'Crowe', 'sinead@email.com', 'password'),
  ('Trisha', 'Duggan', 'trisha@email.com', 'password');

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
  (6, 4),
  (7, 1),
  (7, 5),
  (8, 2),
  (8, 5),
  (9, 1),
  (9, 3),
  (10, 5),
  (10, 4),
  (11, 2),
  (11, 5);

INSERT INTO profile(userID, description, gender, dateOfBirth, countyID, photo, smokes, height) VALUES
  (1, NULL, 'male', '1990-01-02', 2, 'images/photo1.jpg', 'non-smoker', 176),
  (2, NULL, 'male', '1990-04-12', 6, 'images/photo2.jpg', 'non-smoker', 176),
  (3, "My name is Una and I'm from Armagh!", 'female', '1980-06-20', 2, 'images/photo3.jpg', 'non-smoker', 160),
  (5, NULL, 'female', '1994-11-07', 12, 'images/photo5.jpg', 'non-smoker', 167),
  (6, NULL, 'male', '1985-04-15', 14, 'images/photo6.jpg', 'smoker', 184),
  (7, NULL, 'female', '1982-12-07', 3, 'images/photo7.jpg', 'non-smoker', 169),
  (8, NULL, 'female', '1995-04-15', 7, 'images/photo8.jpg', 'non-smoker', 154),
  (9, NULL, 'female', '1983-06-12', 3, 'images/photo9.jpg', 'smoker', 170),
  (10, NULL, 'female', '1978-07-05', 28, 'images/photo10.jpg', 'non-smoker', 149),
  (11, NULL, 'female', '1996-09-10', 25, 'images/photo11.jpg', 'non-smoker', 163);

INSERT INTO preferences(userID, prefGender, prefAgeMin, prefSmokes, prefHeightMax) VALUES
  (1, 'female', 21, 'non-smoker', 168);

INSERT INTO preferences(userID, prefGender) VALUES
  (2, 'female'),
  (3, 'male'),
  (4, 'female'),
  (5, 'male'),
  (6, 'female'),
  (7, 'male'),
  (8, 'male'),
  (9, 'male'),
  (10, 'male'),
  (11, 'male');

INSERT INTO pending(pendingUserOne, pendingUserTwo) VALUES
  (1, 3),
  (2, 3),
  (3, 6),
  (7, 1);

INSERT INTO matches(matchesUserOne, matchesUserTwo) VALUES
  (5, 1),
  (4, 5),
  (6, 7);

INSERT INTO rejections(rejectionsUserOne, rejectionsUserTwo) VALUES
  (1, 10),
  (7, 2),
  (6, 5);

INSERT INTO events(eventCountyID, eventName, eventDate) VALUES
  (3, 'An Event', '2020-04-10');
