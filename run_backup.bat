@echo off
cd /d D:\xampp\htdocs\nachias
D:\xampp\php\php.exe artisan backup:database >> storage\logs\backup_cron.log 2>&1
    