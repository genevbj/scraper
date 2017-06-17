#!/bin/bash

XML_CACHE_DIR=/home/scraper/scraper/cache/olx.uz/xml
WORK_DIR=/home/scraper/scraper/export
EXPORT_DIR=/home/scraper/www/export


LOG=/home/scraper/logs/export.log

EXPIRE=1

f=30059a060a76b26c75c9c2a4a60eb65f76db79ef
ef=export.xml
efw=export.work.xml
eft=export.tmpl.xml
x=merge.xslt



case "$1" in

    "domerge")

	xmlstarlet tr $WORK_DIR/$x -p fileName="'$2'" "$WORK_DIR/$efw"  > "$WORK_DIR/$ef"
	mv -f $WORK_DIR/$ef  $WORK_DIR/$efw

	;;

    "rebuild") 

	echo `date +%Y-%m-%d\ %H:%M:%S` Rebuild export.xml >> $LOG


	cp -f $WORK_DIR/$eft $WORK_DIR/$efw
	find "$XML_CACHE_DIR/" -type f -print0 | xargs -0 -I "_fN_" "$WORK_DIR/mergexml.sh" domerge "_fN_"
	cp -f $WORK_DIR/$efw $EXPORT_DIR/$ef

	echo `date +%Y-%m-%d\ %H:%M:%S` Rebuild is done >> $LOG

	;;

    "update") 

	echo `date +%Y-%m-%d\ %H:%M:%S` Update export.xml >> $LOG
	
	if [[ ! -f $EXPORT_DIR/$ef ]] ; then
	    cp -f $WORK_DIR/$eft $WORK_DIR/$efw
	else
	    cp -f $WORK_DIR/$ef $EXPORT_DIR/$efw
	fi
	find "$XML_CACHE_DIR/" -type f -mtime -$EXPIRE -print0 | xargs -0 -I "_fN_" "$WORK_DIR/mergexml.sh" domerge "_fN_"
	cp -f $WORK_DIR/$efw $EXPORT_DIR/$ef

	echo `date +%Y-%m-%d\ %H:%M:%S` Update is done >> $LOG

	;;

esac