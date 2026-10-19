@echo off

cd /d C:\inetpub\wwwroot\ccod_v1

"C:\PHP\php.exe" artisan queue:work database