@echo off

::
:: Composer Install
::
:: This will fetch the defined dependencies into the local project.
:: The dependencies are defined in "..\composer.json".
::

if "%GIT_EXEC_PATH%" == "" call::no_gitcommand_found

if "%PHPBIN%" == "" call::no_phpcommand_found

:: leave "/bin" folder, for composer to find "composer.json"
cd..

:: always run a self-update
"%PHPBIN%" "bin\composer\composer.phar" "self-update"

:: update dependencies (if lock and json are out of sync)
"%PHPBIN%" "bin\composer\composer.phar" "update"

:: then install the vendor packages
"%PHPBIN%" "bin\composer\composer.phar" "install" "--dev --prefer-dist --no-interaction"

pause

:no_phpcommand_found - displayes a hint for the user to setup env var PHPBIN
echo ---------------------------------------------------------------------------
echo  WARNING  Set environment variable PHPBIN to the location of your php.exe.
echo           Hint: "set PHPBIN=C:\PHP\php.exe"
echo ---------------------------------------------------------------------------
:: fallback to hardcoded path
set PHPBIN=d:\wpnxm-0.5.2-test5\bin\php\php.exe
goto:eof

:no_gitcommand_found - displayes a hint for the user to setup env var GIT_EXEC_PATH
echo --------------------------------------------------------------------------------
echo  WARNING  Set environment variable GIT_EXEC_PATH to the location of your git.exe
echo           Hint: "set GIT_EXEC_PATH=C:\Programs\GIT\bin"
echo --------------------------------------------------------------------------------
:: fallback to hardcoded path
set GIT_EXEC_PATH=C:\Programme\Git\bin
goto:eof
