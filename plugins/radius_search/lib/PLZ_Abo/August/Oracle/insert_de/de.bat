rem run it from BIN directory of Oracle home
rem userid = user/password
rem control = control file to load data
rem skip = skip=1 - skip 1 line (header with columns)

set nls_lang=american_america.CL8MSWIN1251

sqlldr userid=system/manager control=c:\us.ctl skip=1 log=c:\log.txt