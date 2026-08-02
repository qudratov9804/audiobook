@echo off
cd /d "D:\AALaravel\audiobook"
:loop
"C:\Users\admin\.config\herd\bin\php84\php.exe" artisan queue:work --queue=default --tries=3 --timeout=3600 --sleep=3
timeout /t 5 /nobreak >nul
goto loop
