@echo off
setlocal

set "APP_ROOT=C:\inetpub\wwwroot\ccod_v1"
set "PHP_EXE=C:\PHP\php.exe"
set "TASK_NAME=CCOD Server Start Teams Notification"
set "TASK_COMMAND=%PHP_EXE% %APP_ROOT%\scripts\scheduler\notify-server-start.php"

if not exist "%PHP_EXE%" (
    echo PHP executable not found: %PHP_EXE%
    exit /b 1
)

if not exist "%APP_ROOT%\scripts\scheduler\notify-server-start.php" (
    echo Startup notification script not found in %APP_ROOT%
    exit /b 1
)

schtasks /Create /TN "%TASK_NAME%" /SC ONSTART /DELAY 0002:00 /TR "%TASK_COMMAND%" /RU SYSTEM /RL HIGHEST /F

if errorlevel 1 (
    echo Failed to register the CCOD server-start notification task.
    exit /b 1
)

echo Registered: %TASK_NAME%
echo It will run two minutes after Windows starts.
endlocal
