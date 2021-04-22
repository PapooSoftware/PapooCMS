LOAD DATA
INFILE 'c:\de_complete.txt'
append
INTO TABLE de
FIELDS TERMINATED BY ';'
(
  land,
  plz,
  ort,
  kreis,
  bundesland,
  latitude,
  longitude
)
