-- CSV file must be w/o header columns

USE myDBname
go

input into de
FROM 'c:\de_complete.txt'
FORMAT ascii
ESCAPES OFF
DELIMITED BY ';'
go