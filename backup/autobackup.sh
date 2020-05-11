#!/bin/bash

## Enter your specifics here (be careful only to change the content in between the quotation marks):
##----------------------------------------------------------------------------------------------------------------------------------------------
DAYS_TO_KEEP='10'								## Number of days, the backup is going to be kept. Define 0 to keep the backup forever.
BACKUP_PATH_1='/media/maggie/JORINDE_2/Backups'	## Path to external drive, on which the backup is saved.
BACKUP_PATH_2='/home/maggie/Desktop/Backups'	## Path to internal drive, on which the backup is saved.
##----------------------------------------------------------------------------------------------------------------------------------------------

## Initialise variables which are needed for the database backup.
user='root'										## Username to connect with database.
password=''										## Password to connect with database.
database='facility'								## Name of database, which is going to be backuped.
socket='/opt/lampp/var/mysql/mysql.sock'		## MySQL configuration. Compare with /opt/lampp/etc/my.cnf line 29 if any problem appears

## Initialise variable with the current time stamp in format dd.mm.yyyy_hh.mm.
date=$(date +"%d.%m.%Y_%H.%M")

## Initialise variable for the filename which consists of the name of that database which is backed up and the current date.
file_name=$database'_'$date

## Perform the database backup twice. One is saved at an external drive and one at the internal drive.
mysqldump $database -u $user -p$password --socket=$socket > $BACKUP_PATH_1/$file_name.sql 
mysqldump $database -u $user -p$password --socket=$socket > $BACKUP_PATH_2/$file_name.sql 

## Check, if in internal as well as external drive a backup exists, which is older than the defined time to keep.
## If such an old backup is found, delete it.
if [ "$DAYS_TO_KEEP" -gt 0 ] ; then
	find $BACKUP_PATH_1/* -mtime +$DAYS_TO_KEEP -exec rm {} \;
	find $BACKUP_PATH_2/* -mtime +$DAYS_TO_KEEP -exec rm {} \;
fi
