@echo off
title Node Socket Server
if not defined MYCOLOR (set MYCOLOR=0A)
color %MYCOLOR%

start "" /B node socket-server\index.js

echo.
echo Node Server is running...
echo Press any key to stop the server and close this window.
pause >nul

taskkill /F /IM node.exe >nul 2>&1
exit