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
echo [3/3] Copying signed release artifacts to release folder...
if not exist "release" mkdir "release"
if exist "app\build\outputs\apk\release\app-release.apk" (
    copy /y "app\build\outputs\apk\release\app-release.apk" "release\SpiritualShaadi-v1.0.4.apk"
    copy /y "app\build\outputs\apk\release\app-release.apk" "release\SpiritualShaadi.apk"
)
if exist "app\build\outputs\bundle\release\app-release.aab" (
    copy /y "app\build\outputs\bundle\release\app-release.aab" "release\SpiritualShaadi-v1.0.4.aab"
    copy /y "app\build\outputs\bundle\release\app-release.aab" "release\com.spiritualshaadi.aab"
)

echo.
echo ===================================================
echo   Build Successful!
echo   Play Store AAB: release\SpiritualShaadi-v1.0.4.aab
echo   Direct Install APK: release\SpiritualShaadi-v1.0.4.apk
echo ===================================================
echo.
pause
