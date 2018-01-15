INSERT INTO Collections (
   CID
  ,Available
  ,Name
  ,City
  ,State
  ,ZipCode
  ,Rating
  ,Description
  ,Ordered
  ,Abbreviation
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);

INSERT INTO landmarks (
   LID
  ,CID
  ,LandmarkName
  ,Latitude
  ,Longitude
  ,LandmarkDescription
  ,QRCode
  ,PicID
  ,BadgeID
  ,OrderNum
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);

INSERT INTO CollectionImages (CID, PicID) VALUES (?, ?);

INSERT INTO CollectionOwner (
   UID
  ,CID
) VALUES (?, ?);

INSERT INTO awards (
   AwardName
  ,CID
  ,AwardDescription
  ,RedeemCode
) VALUES (?, ?, ?, ?);
INSERT INTO landmarkimages (
   LID
  ,CID
  ,PicID
) VALUES (?, ?, ?);
