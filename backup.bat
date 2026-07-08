@echo off
set MYSQL=D:\xampp\mysql\bin\mysqldump.exe
set USER=root
set BACKUP=D:\backup

if not exist "%BACKUP%" mkdir "%BACKUP%"

%MYSQL% -u %USER% --skip-password nach > "%BACKUP%\nach.sql"
%MYSQL% -u %USER% --skip-password nachi > "%BACKUP%\nachi.sql"
%MYSQL% -u %USER% --skip-password nachias > "%BACKUP%\nachias.sql"
%MYSQL% -u %USER% --skip-password nachias_erp_live > "%BACKUP%\nachias_erp_live.sql"
%MYSQL% -u %USER% --skip-password nachias_live > "%BACKUP%\nachias_live.sql"
%MYSQL% -u %USER% --skip-password nachias_live_db > "%BACKUP%\nachias_live_db.sql"

echo.
echo =====================================
echo Backup completed successfully!
echo Files saved in D:\backup
echo =====================================
pause