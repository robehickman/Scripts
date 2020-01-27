#!/usr/bin/python3
# Backup databases

import sys
if sys.version_info < (3, 6): raise SystemExit("Requires python 3.6 or greater")

import os, yaml
import pymysql as mysql
import sqlite3 as lite

print("-- Backing up databases -- ")

# Load configuration
with open('/root/srvmanage.yaml', 'r') as fle:
    config = yaml.load(fle.read())

db_host         = config['db_host']
db_user         = config['db_user']
db_pass         = config['db_pass']

cnx = mysql.connect (host = db_host, user = db_user, passwd = db_pass)
cursor = cnx.cursor()

cursor.execute ("show databases")
data = cursor.fetchall()

data = [i for i in data if i[0] not in ['information_schema', 'mysql', 'performance_schema']]

for row in data:
    os.system("mysqldump -u "+db_user+" -p"+db_pass+" -h localhost "+
         "--lock-tables=false --single-transaction  "+row[0]+" > /root/mysql_backup/"+row[0]+"_backup.sql");


