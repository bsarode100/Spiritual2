@echo off
setlocal enabledelayedexpansion

echo ===================================================
echo   SpiritualShaadi Android APK and AAB Builder
echo ===================================================
echo.

set "JAVA_HOME=C:\Program Files\Android\Android Studio\jbr"
set "ANDROID_HOME=%LOCALAPPDATA%\Android\Sdk"
set "PATH=%JAVA_HOME%\bin;%PATH%"

set "GRADLE_HOME=%~dp0gradle-7.6"
if exist "%GRADLE_HOME%\bin\gradle.bat" (
    set "GRADLE_CMD=%GRADLE_HOME%\bin\gradle.bat"
) else (
    set "GRADLE_CMD=gradle"
)

echo [1/2] Building Release APK (for direct phone install)...
call "%GRADLE_CMD%" assembleRelease
if %ERRORLEVEL% NEQ 0 (
    echo Error building APK!
    pause
    exit /b %ERRORLEVEL%
)

echo.
echo [2/2] Building Release AAB (for Google Play Store)...
call "%GRADLE_CMD%" bundleRelease
if %ERRORLEVEL% NEQ 0 (
    echo Error building AAB!
    pause
    exit /b %ERRORLEVEL%
)

echo.
echo ===================================================
echo   Build Successful!
echo   APK: app\build\outputs\apk\release\app-release.apk
echo   AAB: app\build\outputs\bundle\release\app-release.aab
echo ===================================================
echo.
pause
