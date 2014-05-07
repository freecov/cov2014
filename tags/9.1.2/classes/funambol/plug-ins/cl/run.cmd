@echo off

setlocal

if not "%JAVA_HOME%" == "" goto CONT0
echo ERROR: Set JAVA_HOME before running this script.
goto END
:CONT0

set CLASSPATH=lib\sc-api-j2se.jar;lib\sync4j-clientframework.jar;lib\funambol-ext-6.0.1.jar

%JAVA_HOME%\bin\java com.funambol.syncclient.spds.source.Test

:END
endlocal
