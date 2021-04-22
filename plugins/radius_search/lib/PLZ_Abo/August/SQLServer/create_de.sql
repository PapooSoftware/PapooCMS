USE myDBname

GO

CREATE TABLE de

(
  land		varchar(2),
  plz		varchar(5),
  ort           varchar(64),
  ortsname           varchar(48),
  ortsnamezusatz           varchar(48),
  ortsteil          varchar(48),
  kreis         varchar(48),
  landratsamt	varchar(48),
  bundesland    varchar(48),
  postfach	int,
  latitude      decimal(13, 7),
  longitude     decimal(13, 7)
)

GO


