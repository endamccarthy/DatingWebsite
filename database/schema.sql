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
  dateOfBirth DATE NOT NULL,
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
  prefHeightMin INT NULL DEFAULT 120,
  prefHeightMax INT NULL DEFAULT 230
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
  ('Knitting'),
  ('Camping'),
  ('Milking'),
  ('Tractor pulling'),
  ('Whittling'),
  ('Basket weaving'),
  ('Swimming'),
  ('Walks in the park'),
  ('Music'),
  ('Coding'),
  ('Fishing'),
  ('Snorkelling'),
  ('Horse riding'),
  ('Formula One'),
  ('Cycling'),
  ('Pilates'),
  ('Mindfulness'),
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
  ('Michelle', 'Crowe', 'michelle@email.com', 'password'),
  ('Trisha', 'Duggan', 'trisha@email.com', 'password'),
  ('Roisin', 'Suez', '12345678@student.ul.ie', 'password12'),
  ('Oonagh', 'Brennans', '12345679@student.ul.ie', 'password13'),
  ('Elisa', 'Brode', '12345680@student.ul.ie', 'password14'),
  ('Rachel', 'Cassidy', '12345681@student.ul.ie', 'password15'),
  ('Primrose', 'Conway', '12345682@student.ul.ie', 'password16'),
  ('Shannon', 'Cusack', '12345683@student.ul.ie', 'password17'),
  ('Rebecca', 'Davitt', '12345684@student.ul.ie', 'password18'),
  ('Bridgette', 'Deasy', '12345685@student.ul.ie', 'password19'),
  ('Carrie', 'Dillon', '12345686@student.ul.ie', 'password20'),
  ('Heather', 'Doherty', '12345687@student.ul.ie', 'password21'),
  ('Diana', 'Duncan', '12345688@student.ul.ie', 'password22'),
  ('Agnes', 'Foley', '12345689@student.ul.ie', 'password23'),
  ('Paula', 'French', '12345690@student.ul.ie', 'password24'),
  ('Julie', 'Gooch', '12345691@student.ul.ie', 'password25'),
  ('Noelle', 'Guida', '12345692@student.ul.ie', 'password26'),
  ('Andrea', 'Halpin', '12345693@student.ul.ie', 'password27'),
  ('Elaine', 'Hanafin', '12345694@student.ul.ie', 'password28'),
  ('Mary', 'Cork', '12345695@student.ul.ie', 'password29'),
  ('Fiona', 'Kerry', '12345696@student.ul.ie', 'password30'),
  ('Lourda', 'Kavanagh', '12345697@student.ul.ie', 'password31'),
  ('Lorraine', 'Kearse', '12345698@student.ul.ie', 'password32'),
  ('Linda', 'Brien', '12345699@student.ul.ie', 'password33'),
  ('Anderinna', 'Lucey', '12345700@student.ul.ie', 'password34'),
  ('Elizabeth', 'Lohan', '12345701@student.ul.ie', 'password35'),
  ('Jean', 'Jones', '12345702@student.ul.ie', 'password36'),
  ('Patrick', 'Mc Elligott', '12345703@student.ul.ie', 'password37'),
  ('Derek', 'Mc Govern', '12345704@student.ul.ie', 'password38'),
  ('Patrick', 'Monaghan', '12345705@student.ul.ie', 'password39'),
  ('Donal', 'Noonan', '12345706@student.ul.ie', 'password40'),
  ('John', 'Brien', '12345707@student.ul.ie', 'password41'),
  ('Martin', 'Callaghan', '12345708@student.ul.ie', 'password42'),
  ('James', 'Connor', '12345709@student.ul.ie', 'password43'),
  ('William', 'Connor', '12345710@student.ul.ie', 'password44'),
  ('Gerard', 'Flaherty', '12345711@student.ul.ie', 'password45'),
  ('Christopher', 'Keeffe', '12345712@student.ul.ie', 'password46'),
  ('Brian', 'Keeffe', '12345713@student.ul.ie', 'password47'),
  ('Brendan', 'Smyth', '12345714@student.ul.ie', 'password48'),
  ('John', 'Oliver', '12345715@student.ul.ie', 'password49'),
  ('Seamus', 'Loughlin', '12345716@student.ul.ie', 'password50'),
  ('Ingo', 'Mahoney', '12345717@student.ul.ie', 'password51'),
  ('Carlos', 'Ryan', '12345718@student.ul.ie', 'password52'),
  ('Samuel', 'Ryan', '12345719@student.ul.ie', 'password53'),
  ('James', 'Ryan', '12345720@student.ul.ie', 'password54'),
  ('Andrea', 'Snyder', '12345721@student.ul.ie', 'password55'),
  ('Michael', 'Roche', '12345722@student.ul.ie', 'password56'),
  ('Jonathan', 'Matthews', '12345723@student.ul.ie', 'password57'),
  ('Michael', 'Wade', '12345724@student.ul.ie', 'password58'),
  ('Finbarr', 'Hughes', '12345725@student.ul.ie', 'password59'),
  ('Darragh', 'Shannon', '12345726@student.ul.ie', 'password60'),
  ('Richard', 'Young', '12345727@student.ul.ie', 'password61'),
  ('test2', 'user', 'test2@email.com', '$2y$10$Pbnnd7yvpbt/FDWpLSirVuEW9rgq7VlpULrNCN44G/LsJWzkGdQr2');

INSERT INTO profile(userID, description, gender, dateOfBirth, countyID, photo, smokes, height) VALUES
  (1, '', 'male', '1990-01-02', 2, '../../images/profile-photos/user1-photo.jpg', 'non-smoker', 176),
  (2, '', 'male', '1990-04-12', 6, '../../images/profile-photos/user2-photo.jpg', 'non-smoker', 176),
  (3, "My name is Una and I'm from Armagh!", 'female', '1980-06-20', 2, '../../images/profile-photos/user3photo.jpg', 'non-smoker', 160),
  (4, '', 'male', '1994-04-15', 14, '../../images/profile-photos/user4-photo.jpg', 'smoker', 169),
  (5, '', 'female', '1994-11-07', 12, '../../images/profile-photos/user5-photo.jpg', 'non-smoker', 167),
  (6, '', 'male', '1985-04-15', 14, '../../images/profile-photos/user6-photo.jpg', 'smoker', 184),
  (7, '', 'female', '1982-12-07', 3, '../../images/profile-photos/user7-photo.jpg', 'non-smoker', 169),
  (8, '', 'female', '1995-04-15', 7, '../../images/profile-photos/user8-photo.jpg', 'non-smoker', 154),
  (9, '', 'female', '1983-06-12', 3, '../../images/profile-photos/user9-photo.jpg', 'smoker', 170),
  (10, '', 'female', '1978-07-05', 28, '../../images/profile-photos/user10-photo.jpg', 'non-smoker', 149),
  (11, '', 'female', '1996-09-10', 25, '../../images/profile-photos/user11-photo.jpg', 'non-smoker', 163),
  (12, "Everything in moderation, especially moderation. Have not been sprayed by a skunk in over ten years. I'll hold elevator doors open for people as long as they show some urgency. Otherwise, I'll push the door close button while maintaining eye contact with a straight face.", 'female', '1977-01-05', 14, '../../images/profile-photos/user12-photo.jpg', 'non-smoker', 160),
  (13, "Everything in moderation, especially moderation. Have not been sprayed by a skunk in over ten years. I'll hold elevator doors open for people as long as they show some urgency. Otherwise, I'll push the door close button while maintaining eye contact with a straight face.", 'female', '1964-02-23', 9, '../../images/profile-photos/user13-photo.jpg', 'non-smoker', 209),
  (14, "I appreciate comfortable seating, love hearing about what makes other people obsessed or passionate, and spend more time in slippers than I should.", 'female', '1975-08-04', 4, '../../images/profile-photos/user14-photo.jpg', 'non-smoker', 189),
  (15, "I don't want a partner in crime, I commit all my crimes on my own. I would never drag you into that.", 'female', '1978-03-17', 13, '../../images/profile-photos/user15-photo.jpg', 'non-smoker', 195),
  (16, "I floss. That's how responsible I am.", 'female', '1976-02-23', 7, '../../images/profile-photos/user16-photo.jpg', 'non-smoker', 158),
  (17, "I have stories to share, think dad jokes are funny, and enjoy a good meal with a nice young gentleman.", 'female', '1979-09-16', 14, '../../images/profile-photos/user17-photo.jpg', 'non-smoker', 175),
  (18, "I know my way around an Excel spreadsheet. I'm not afraid to put that out there. I also am a huge college football fan, an amature chef, and owner of one lucky dog named Bolero. (He's named after the tie.) If you scratch his ears, he'll be your best friend for life. I'm not much different really. We're pretty simple creatures.", 'female', '1979-05-12', 18, '../../images/profile-photos/user18-photo.jpg', 'non-smoker', 153),
  (19, "I love exploring ambitious ideas, whether it's at a festival, a tech conference, a retreat, or even at a dinner party with friends. I'm a data scientist who works in public policy. This year I've work on a bill on Congress, going sky diving, and had some amazing experiences. If you're into it, tell me a recent idea that intrigued you.", 'female', '1975-09-17', 22, '../../images/profile-photos/user19-photo.jpg', 'non-smoker', 201),
  (20, "I love New Yorker articles, staring out the window and wondering about other people's lives on long car rides, and that first sip of beer you have after a long week on a Friday night. (Preferably it's an IPA, but I like all kinds of craft brews.) Interested in meeting people who are interesting.", 'female', '1972-01-02', 25, '../../images/profile-photos/user20-photo.jpg', 'non-smoker', 158),
  (21, "I think I'm the only guy on here who doesn't like hiking. I really don't though. I like the inside. It's so nice and warm and I can order a drink and someone just brings it to me. I mean, wow, that's really nice. Why go outside when you can have all this.", 'female', '1975-07-29', 19, '../../images/profile-photos/user21-photo.jpg', 'non-smoker', 174),
  (22, "I would never put baby in a corner. Never.", 'female', '1975-04-24', 21, '../../images/profile-photos/user22-photo.jpg', 'non-smoker', 189),
  (23, "I'm into meditation, self-inquiry, getting lost on dirt roads and trail runs, and can't wait to meet you.", 'female', '1978-02-24', 20, '../../images/profile-photos/user23-photo.jpg', 'non-smoker', 206),
  (24, "If you love dogs and sports, I'm down for whatever you are.", 'female', '1974-09-25', 26, '../../images/profile-photos/user24-photo.jpg', 'non-smoker', 208),
  (25, "Let's eat!", 'female', '1956-03-13', 20, '../../images/profile-photos/user25-photo.jpg', 'non-smoker', 205),
  (26, "Like my shirt? It's made out of boyfriend material.", 'female', '1963-06-05', 24, '../../images/profile-photos/user26-photo.jpg', 'non-smoker', 162),
  (27, "Looking for someone who likes making fun of bad movies, checking out local bands, sleeping in on Sundays, and laughing at themselves. Hoping you can show me a thing or two about what you're into too.", 'female', '1965-04-09', 18, '../../images/profile-photos/user27-photo.jpg', 'non-smoker', 161),
  (28, "Not sure what to say? Let someone else say it for you. If you're not a great writer or at a loss, use a favorite song lyric or a quote from a TV show, song, or book. It still shows off your personality, just in a different way.", 'female', '1975-03-18', 4, '../../images/profile-photos/user28-photo.jpg', 'non-smoker', 189),
  (29, "Peanut butter > jelly (Though, they still go together pretty well. Maybe we will too.)", 'female', '1972-12-12', 4, '../../images/profile-photos/user29-photo.jpg', 'non-smoker', 175),
  (30, "Skiing > snowboarding", 'female', '1979-08-07', 3, '../../images/profile-photos/user30-photo.jpg', 'non-smoker', 192),
  (31, "Some people call me the space cowboy, some people call me the gangster of love. Nobody calls me Maurice though, which is too bad because I'm all about the lovey dovey all the time.", 'female', '1978-01-22', 3, '../../images/profile-photos/user31-photo.jpg', 'non-smoker', 193),
  (32, "Sunday fundays > lazy Sundays", 'female', '1962-02-04', 26, '../../images/profile-photos/user32-photo.jpg', 'non-smoker', 163),
  (33, "Tacos, Bicycles, Cats, Chilling, Tattoos, Tacos, Nonsense, New Things, You. Did I say tacos? Tacos.", 'female', '1959-07-16', 15, '../../images/profile-photos/user33-photo.jpg', 'non-smoker', 157),
  (34, "The answer is 42. What do you think the question is?", 'female', '1955-12-06', 9, '../../images/profile-photos/user34-photo.jpg', 'non-smoker', 160),
  (35, "They said, 'You don't have a bio.' 'And I said, 'I've been focusing on chemistry.", 'female', '1979-07-04', 14, '../../images/profile-photos/user35-photo.jpg', 'non-smoker', 174),
  (36, "Writing a profile can be annoying at first, but use a few of these tricks, see what works for you, and go for it. Bottom line is that people want to get to know you, and your profile is one tool you have to show them who you are. Tell a joke, quote a favorite author, cite that obscure music lyric almost nobody has heard or, or list out your favorite things. The people who are your kind of people will be into it, and you'll have a lot of fun enjoying yourself and meeting new people.", 'female', '1975-05-01', 9, '../../images/profile-photos/user36-photo.jpg', 'smoker', 170),
  (37, "I don't want a partner in crime", 'male', '1964-06-20', 24, '../../images/profile-photos/user37-photo.jpg', 'smoker', 153),
  (38, "I floss. That's how responsible I am.", 'male', '1976-03-07', 27, '../../images/profile-photos/user38-photo.jpg', 'smoker', 209),
  (39, "I commit all my crimes on my own. I would never drag you into that.", 'male', '1972-01-22', 10, '../../images/profile-photos/user39photo.jpg', 'smoker', 169),
  (40, "I'm a superhero and in my spare time a god", 'male', '1976-07-07', 21, '../../images/profile-photos/user40-photo.jpg', 'smoker', 156),
  (41, "What can I say? I just love ducks", 'male', '1975-02-10', 8, '../../images/profile-photos/user41-photo.jpg', 'smoker', 188),
  (42, "Cool water is always better than cold", 'male', '1974-02-08', 7, '../../images/profile-photos/user42-photo.jpg', 'smoker', 161),
  (43, "Sometimes I can't see the wood from the trees", 'male', '1960-11-29', 24, '../../images/profile-photos/user43-photo.jpg', 'smoker', 181),
  (44, "In times of desperation I always look to Dibert for sensible advice", 'male', '1947-10-27', 6, '../../images/profile-photos/user44-photo.jpg', 'smoker', 184),
  (45, "Corporations", 'male', '1976-10-25', 10, '../../images/profile-photos/user45-photo.jpg', 'smoker', 194),
  (46, "Lambing season is marvellous", 'male', '1967-07-26', 10, '../../images/profile-photos/user46-photo.jpg', 'smoker', 183),
  (47, "I like swimming and looking for someone to join me", 'male', '1980-07-21', 20, '../../images/profile-photos/user47-photo.jpg', 'smoker', 162),
  (48, "I would never put baby in a corner. Never.", 'male', '1977-07-23', 24, '../../images/profile-photos/user48-photo.jpg', 'smoker', 155),
  (49, "Forget safety. Live where you fear to live. Destroy your reputation. Be notorious.", 'male', '1978-04-03', 22, '../../images/profile-photos/user49-photo.jpg', 'smoker', 210),
  (50, "Are you kind of spiritual? Do you have more of a dark side? Maybe you're a homebody or maybe you're obsessed with your job. Whatever you are, be you. The person you're looking for will be into it and putting it all out there right away will save you some time.", 'male', '1977-11-13', 4, '../../images/profile-photos/user50-photo.jpg', 'smoker', 169),
  (51, "I love exploring ambitious ideas, whether it's at a festival, a tech conference, a retreat, or even at a dinner party with friends. I'm a data scientist who works in public policy. This year I've work on a bill on Congress, going sky diving, and had some amazing experiences. If you're into it, tell me a recent idea that intrigued you.", 'male', '1974-08-14', 7, '../../images/profile-photos/user51-photo.jpg', 'smoker', 183),
  (52, "I think I'm the only guy on here who doesn't like hiking. I really don't though. I like the inside. It's so nice and warm and I can order a drink and someone just brings it to me. I mean, wow, that's really nice. Why go outside when you can have all this.", 'male', '1965-06-03', 5, '../../images/profile-photos/user52-photo.jpg', 'smoker', 179),
  (53, "I'm into meditation, self-inquiry, getting lost on dirt roads and trail runs, and can't wait to meet you.", 'male', '1975-02-26', 26, '../../images/profile-photos/user53-photo.jpg', 'smoker', 167),
  (54, "I can be forthright and direct when I need to be. Not in a bar fight kind of way, but I do know how to get what I want.", 'male', '1970-03-03', 9, '../../images/profile-photos/user54-photo.jpg', 'smoker', 193),
  (55, "I'm not wearing any pants. But then, we leave the gym changing room, and they go, 'Oh cool, that scruffy-looking guy has some pretty great hair.", 'male', '1978-03-03', 28, '../../images/profile-photos/user55-photo.jpg', 'smoker', 195),
  (56, "My brother and sister, and the adorable kids they let me spoil.", 'male', '1971-05-05', 14, '../../images/profile-photos/user56-photo.jpg', 'smoker', 202),
  (57, "My grandfather. He lived until his 90s and was loved by everyone he met. He gave me my curious nature and taught me to always think of others first. He and my grandmother were married for over 60 years before he died and he always said she was the most precious and important part of his life. The way he treated her with respect, kindness and gratitude has really shaped the way I conduct myself in all my relationships, particularly with women.", 'male', '1974-06-14', 15, '../../images/profile-photos/user57-photo.jpg', 'smoker', 184),
  (58, "Taking care of others, helping people, and making people smile. Yes, I know that sounds cheesy but I try really hard to brighten the day of everyone I meet, even if it's just by asking a cashier how they are or holding the door open for someone. I try to always think about that quote by the Dalai Lama: 'Be kind whenever possible. It is always possible.", 'male', '1968-09-06', 21, '../../images/profile-photos/user58-photo.jpg', 'smoker', 167),
  (59, "The aforementioned haircut. Hairstylist of said haircut. Shampoo. Conditioner. Hat (we all have off days).", 'male', '1976-12-01', 4, '../../images/profile-photos/user59-photo.jpg', 'smoker', 191),
  (60, "Wait, I feel like we've covered this one already. Other than my lustrous mane, I'm most passionate about fish and chip night at my local, ruining'Frozen'for my niece, and my workout playlist.", 'male', '1971-11-29', 6, '../../images/profile-photos/user60-photo.jpg', 'smoker', 205),
  (61, "When I'm not rescuing kittens from trees and singing show tunes to old ladies (I'm a selfless guy), I'm catching up with friends, listening to symphonies (yes, I promise), and pretending to be Jamie Oliver.", 'male', '1944-01-01', 17, '../../images/profile-photos/user61-photo.jpg', 'smoker', 172),
  (62, '', 'female', '1990-01-02', 2, '../../images/profile-photos/user62-photo.jpg', 'non-smoker', 176);

INSERT INTO preferences(userID, prefGender) VALUES
  (1, 'female'),
  (2, 'female'),
  (3, 'male'),
  (4, 'female'),
  (5, 'male'),
  (6, 'female'),
  (7, 'male'),
  (8, 'male'),
  (9, 'male'),
  (10, 'male'),
  (11, 'male'),
  (12, 'male'),
  (13, 'male'),
  (14, 'male'),
  (15, 'male'),
  (16, 'male'),
  (17, 'male'),
  (18, 'male'),
  (19, 'male'),
  (20, 'male'),
  (21, 'male'),
  (22, 'male'),
  (23, 'male'),
  (24, 'male'),
  (25, 'male'),
  (26, 'male'),
  (27, 'male'),
  (28, 'male'),
  (29, 'male'),
  (30, 'male'),
  (31, 'male'),
  (32, 'male'),
  (33, 'male'),
  (34, 'male'),
  (35, 'male'),
  (36, 'male'),
  (37, 'female'),
  (38, 'female'),
  (39, 'female'),
  (40, 'female'),
  (41, 'female'),
  (42, 'female'),
  (43, 'female'),
  (44, 'female'),
  (45, 'female'),
  (46, 'female'),
  (47, 'female'),
  (48, 'female'),
  (49, 'female'),
  (50, 'female'),
  (51, 'female'),
  (52, 'female'),
  (53, 'female'),
  (54, 'female'),
  (55, 'female'),
  (56, 'female'),
  (57, 'female'),
  (58, 'female'),
  (59, 'female'),
  (60, 'female'),
  (61, 'female'),
  (62, 'male');

INSERT INTO interests(userID, interestID) VALUES
  (1, 1),
  (1, 2),
  (1, 4),
  (2, 1),
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
  (11, 5),
  (12, 10),
  (13, 2),
  (14, 5),
  (15, 1),
  (16, 5),
  (17, 10),
  (18, 10),
  (19, 3),
  (20, 9),
  (21, 9),
  (22, 3),
  (23, 2),
  (24, 7),
  (25, 3),
  (26, 9),
  (27, 7),
  (28, 3),
  (29, 4),
  (30, 5),
  (31, 7),
  (32, 2),
  (33, 7),
  (34, 10),
  (35, 3),
  (36, 9),
  (37, 7),
  (38, 8),
  (39, 9),
  (40, 4),
  (41, 4),
  (42, 8),
  (43, 10),
  (44, 6),
  (45, 1),
  (46, 8),
  (47, 10),
  (48, 1),
  (49, 6),
  (50, 3),
  (51, 4),
  (52, 1),
  (53, 7),
  (54, 7),
  (55, 4),
  (56, 4),
  (57, 1),
  (58, 6),
  (59, 9),
  (60, 4),
  (61, 4),
  (12, 13),
  (13, 17),
  (14, 11),
  (15, 13),
  (16, 12),
  (17, 16),
  (18, 14),
  (19, 18),
  (20, 14),
  (21, 14),
  (22, 18),
  (23, 19),
  (24, 14),
  (25, 18),
  (26, 11),
  (27, 14),
  (28, 14),
  (29, 12),
  (30, 19),
  (31, 16),
  (32, 15),
  (33, 18),
  (34, 19),
  (35, 18),
  (36, 13),
  (37, 15),
  (38, 18),
  (39, 16),
  (40, 15),
  (41, 15),
  (42, 17),
  (43, 19),
  (44, 14),
  (45, 14),
  (46, 11),
  (47, 11),
  (48, 18),
  (49, 15),
  (50, 12),
  (51, 11),
  (52, 13),
  (53, 13),
  (54, 16),
  (55, 17),
  (56, 12),
  (57, 12),
  (58, 17),
  (59, 15),
  (60, 17),
  (61, 13);

INSERT INTO pending(pendingUserOne, pendingUserTwo) VALUES
  (1, 3),
  (2, 3),
  (3, 6),
  (11, 1),
  (7, 1),
  (62, 1);

INSERT INTO matches(matchesUserOne, matchesUserTwo) VALUES
  (4, 5),
  (6, 7);

INSERT INTO rejections(rejectionsUserOne, rejectionsUserTwo) VALUES
  (7, 2),
  (6, 5);

INSERT INTO events(eventCountyID, eventName, eventDate) VALUES
  (3, 'An Event', '2020-04-10');
