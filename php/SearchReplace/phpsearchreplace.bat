@echo off

set workDir=..\..
set dirTest=%workDir%\Tests

set exe=php run.php

for /r "%dirTest%" %%i in (*_sr.php) do %exe% "%%i"

pause