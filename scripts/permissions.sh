#!/bin/bash

now=$(date +%s)
cd /var/www/html/ && zip -r "tdms_"$now".bk.zip" tdms/

cd /var/www/html/tdms && chmod -R 777 storage/
