rem -U<username>
rem -P<password>
rem -S<server>
rem -F 2 - firstrow=2
bcp mydbname..de in c:\de_complete.txt -F 2 -c -t ";" -r "\n" -Usa -P"" -SWEBSERVER -Jcp1251
