#!/bin/sh

CLASSPATH=lib/sc-api-j2se.jar:lib/sync4j-clientframework.jar:lib/funambol-ext-6.0.1.jar

export CLASSPATH

$JAVA_HOME/bin/java com.funambol.syncclient.spds.source.Test
