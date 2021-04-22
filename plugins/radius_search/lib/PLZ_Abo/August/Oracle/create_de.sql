-- run from SQL*Plus
-- you should be connected to the Oracle database

CREATE TABLE de
(
  land		varchar2(2),
  plz		varchar2(5),
  ort           varchar2(48),
  kreis		varchar2(48),
  landratsamt	varchar(48),	
  bundesland    varchar(48),
  postfach	int,
  latitude      number(13,9),
  longitude     number(13,9)
)
/

