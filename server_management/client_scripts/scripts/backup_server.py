#!/usr/bin/python3

import sys
if sys.version_info < (3, 6): raise SystemExit("Requires python 3.6 or greater")

import os, fcntl
from pathlib import Path

path = Path(sys.argv[1])
os.system("cd " + str(path))

#Lock to prevent two simultaneous sync attempts, as we are calling with chron
lockfile = open(str(path / 'lock'), 'w')
fcntl.flock(fd, LOCK_EX | LOCK_NB)

# Run the backup
print("-- Backup Mysql --")
os.system("ssh root@robehickman.com 'python /root/sql_backup.py'")
os.system("rsync -a root@robehickman.com:/root/db_backup/ ./db_backup --delete")

print("-- Backup Apache --")
os.system("rsync -a root@robehickman.com:/etc/apache2/ ./apache_conf --delete")

print("-- Backup HTTP --")
os.system("rsync -a root@robehickman.com:/srv/http/ --exclude=*.git* ./http --delete")

print("-- Committing changes to git --")
os.system("cd db_backup")
os.system("git add .")
os.system("git commit -m 'backup'")
os.system("git gc")
os.system("cd " + str(path))

os.system("date > last_run.txt")

# Unlock
fcntl.flock(fd, LOCK_UN)

