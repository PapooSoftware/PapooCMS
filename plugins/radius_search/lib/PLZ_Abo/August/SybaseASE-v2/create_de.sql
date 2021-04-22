sp_dboption database_name, 
"select into/bulkcopy", true
go

use myDBname
go

checkpoint
go

commit transaction
go

CREATE TABLE de

(
  land		varchar(2),
  plz		varchar(4),
  ort           varchar(48),
  kreis         varchar(48),
  landratsamt	varchar(48),	
  bundesland    varchar(48),
  postfach	int,
  latitude      numeric(13, 9),
  longitude     numeric(13, 9)
)

go

-- any index must exist for bulk insert
create index de_idx on de (plz)
go


