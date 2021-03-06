Create database MagpieDB;

CREATE TABLE Collections(
  CID INTEGER PRIMARY KEY AUTO_INCREMENT,
  Available BOOL DEFAULT 1,
  Name VARCHAR(100) NOT NULL,
  City VARCHAR(100) DEFAULT "Spokane",
  State VARCHAR(100) DEFAULT "Washington",
  ZipCode INTEGER DEFAULT 99207,
  Rating VARCHAR(100) DEFAULT "E",
  Description VARCHAR(1000) NOT NULL, 
  Ordered BOOL DEFAULT 0,
  Abbreviation VARCHAR(4) NOT NULL,
  Sponsor VARCHAR(50) 
  
)ENGINE=InnoDB;

CREATE TABLE Landmarks(
  LID INTEGER PRIMARY KEY AUTO_INCREMENT,
  CID INTEGER NOT NULL,
  LandmarkName VARCHAR(100) NOT NULL,
  Latitude DOUBLE DEFAULT 0.0 NOT NULL,
  Longitude DOUBLE DEFAULT 0.0 NOT NULL,
  LandmarkDescription VARCHAR(1000) NOT NULL,
  QRCode VARCHAR(625) DEFAULT "{ EMPTY }",
  PicID INTEGER DEFAULT 0,
  BadgeID INTEGER DEFAULT 0,
  OrderNum INTEGER,
  
  FOREIGN KEY (CID) REFERENCES Collections(CID) ON DELETE CASCADE ON UPDATE CASCADE
  
)ENGINE=InnoDB;

CREATE TABLE LandmarkImages(
  LID INTEGER,
  CID INTEGER,
  PicID INTEGER AUTO_INCREMENT,
  
  PRIMARY KEY (PicID),
  
  FOREIGN KEY (LID) REFERENCES Landmarks(LID) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (CID) REFERENCES Collections(CID) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE=InnoDB;

CREATE TABLE Awards(
  AwardName VARCHAR(100),
  CID INTEGER PRIMARY KEY,
  AwardDescription VARCHAR(1000),
  RedeemCode VARCHAR(6) UNIQUE,
  
  FOREIGN KEY (CID) REFERENCES Collections(CID) ON DELETE CASCADE ON UPDATE CASCADE
  
)ENGINE=InnoDB;

CREATE TABLE CollectionImages(
  CID INTEGER,
  PicID INTEGER AUTO_INCREMENT,
  
  PRIMARY KEY (PicID),
  FOREIGN KEY (CID) REFERENCES Collections(CID) ON DELETE CASCADE ON UPDATE CASCADE
  
)ENGINE=InnoDB;

CREATE TABLE CollectionOwner(
  UID VARCHAR(10),
  CID INTEGER PRIMARY KEY,
  
  FOREIGN KEY (CID) REFERENCES Collections(CID) ON DELETE CASCADE ON UPDATE CASCADE
  
)ENGINE=InnoDB;
