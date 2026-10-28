@echo off
cd /d D:\xampp\htdocs\nachias

echo ======================================================== >> storage\logs\backup_cron.log
echo [%date% %time%] Backup trigger started >> storage\logs\backup_cron.log

:: Wait up to 10 minutes (60 checks * 10 seconds) for MySQL to be up and responsive
set attempts=0
:CHECK_MYSQL
D:\xampp\mysql\bin\mysqladmin.exe -u root ping >nul 2>&1
if %ERRORLEVEL% equ 0 goto MYSQL_READY

set /a attempts+=1
if %attempts% geq 60 goto MYSQL_TIMEOUT

echo [%date% %time%] MySQL not ready yet. Waiting 10 seconds (attempt %attempts%/60)... >> storage\logs\backup_cron.log
timeout /t 10 /nobreak >nul
goto CHECK_MYSQL

:MYSQL_TIMEOUT
echo [%date% %time%] ERROR: MySQL did not start within 10 minutes. Backup failed. >> storage\logs\backup_cron.log
exit /b 1

:MYSQL_READY
echo [%date% %time%] MySQL is alive and running. Executing backup command... >> storage\logs\backup_cron.log
D:\xampp\php\php.exe artisan backup:database >> storage\logs\backup_cron.log 2>&1
set BACKUP_EXIT=%ERRORLEVEL%
echo [%date% %time%] Backup finished with exit code: %BACKUP_EXIT% >> storage\logs\backup_cron.log
echo ======================================================== >> storage\logs\backup_cron.log
exit /b %BACKUP_EXIT%
