@echo off
title PHP Server
if not defined MYCOLOR (set MYCOLOR=0A)
color %MYCOLOR%

start "" /B php artisan serve

echo.
echo PHP Server is running...
echo Press any key to stop the server and close this window.
pause >nul

taskkill /F /IM php.exe >nul 2>&1
exit