#!/usr/bin/python
#
# simple script to manage apache vhosts, hosts are managed with
# git and are kept apart with mpm_itk.
#
# Creates sites on sub domains, these can be moved to full domains if desired
# by editing the generated apache config file.

# No trailing slashes!

srvname  = 'example.com' #server domain name, sites are created as sub domain of this
confdir  = '' #i.e. /etc/apache2/sites-available
webroot  = '' #i.e. /srv/http/user1/docs 
reporoot = '' #i.e. /srv/repos/user1

db_host  = 'localhost'
db_user  = ''
db_pass  = ''

# end of configuration options
import os, sys, re
import subprocess as sub
import random
import string
import MySQLdb

# Script can only be run by the root user
if os.geteuid() != 0:
    print 'Must be root'
    quit()

con = MySQLdb.connect (host = db_host, db_user, db_pass)
cursor = con.cursor ()

####################################################################
# Create a random password
####################################################################
def rand_passwd():
    chars = string.letters + string.digits
    return ''.join([random.choice(chars) for i in range(20)])

####################################################################
# Create a system user with a random password
####################################################################
def create_user(sitename):
    password = rand_passwd()

    p = sub.Popen("perl -e 'print crypt(\"%s\", \"%s\")'" % (password,
        rand_passwd()), shell=True, stdout=sub.PIPE, stderr=sub.PIPE)
    crypt_pass, errors = p.communicate()

    os.system("useradd -s /usr/bin/git-shell -M --home-dir %s -p %s %s"
        % (webroot+'/'+sitename, crypt_pass, sitename));

    return password

####################################################################
# Destroy a system user
####################################################################
def destroy_user(sitename):
    os.system("userdel  %s" % sitename)


####################################################################
# Create a vhost
####################################################################
def create_vhost(sitename):
    vhost = """<VirtualHost *:80>
    ServerName """+sitename+'.'+srvname+"""
    """+'DocumentRoot '+webroot+'/'+sitename+"""/docs

    AssignUserId """+sitename+' '+sitename+"""

    """+'<Directory "'+webroot+'/'+sitename+"""/docs">
        Options ExecCGI FollowSymLinks
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>
</VirtualHost>"""

    cfile = open(confdir+'/'+sitename,'w')
    cfile.write(vhost)
    cfile.close()

#create directories and set up git repo
    os.mkdir(webroot+'/'+sitename)
    os.mkdir(webroot+'/'+sitename+'/docs')

    cfile = open(webroot+'/'+sitename+'/docs/index.html','w')
    cfile.write('It works!')
    cfile.close()

    os.chdir(webroot+'/'+sitename)
    os.system('git init')
    os.system('git add .')
    os.system('git commit -m "Initial commit"')

#set up bare git repo for access
    os.mkdir(reporoot+'/'+sitename)
    os.chdir(reporoot+'/'+sitename)
    os.system('git init --bare')

    os.chdir(webroot+'/'+sitename)
    os.system('git remote add origin '+ reporoot+'/'+sitename)
    os.system('git push --all origin')

#create hook script to update webroot
    hook = """#!/bin/sh
cd """+webroot+'/'+sitename+"""
echo 'Updating website'
env -i git reset --hard
env -i git pull origin master"""

    cfile = open(reporoot+'/'+sitename+'/hooks/post-receive','w')
    cfile.write(hook)
    cfile.close()

    os.system('chmod +x '+reporoot+'/'+sitename+'/hooks/post-receive') 

#fix permissions
    os.system('chown -Rf '+sitename+' '+webroot+'/'+sitename) 
    os.system('chmod -Rf 700 '+webroot+'/'+sitename) 

    os.system('chown -Rf '+sitename+' '+reporoot+'/'+sitename) 
    os.system('chmod -Rf 700 '+reporoot+'/'+sitename) 

####################################################################
# Destroy a vhost
####################################################################
def destroy_vhost(sitename):
    os.system('rm -Rf '+webroot+'/'+sitename+'*')
    os.system('rm -Rf '+reporoot+'/'+sitename+'*')
    os.system('rm -Rf '+confdir+'/'+sitename)

####################################################################
# Create a mysql database
####################################################################
def create_mysqldb(sitename):
    global cursor

    sql_password = rand_passwd()

    cursor.execute ("CREATE USER '%s'@'localhost' IDENTIFIED BY '%s'"
        % (sitename, sql_password))
    cursor.execute ("CREATE DATABASE `m_%s`" % sitename)
    cursor.execute ("""GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,ALTER,INDEX,DROP
        ON `m_%s`.* TO '%s'@'localhost'""" % (sitename, sitename))

    return sql_password

####################################################################
# Destroy a mysql database
####################################################################
def destroy_mysqldb(sitename):
    global cursor

    cursor.execute ("DROP DATABASE `m_%s`" % sitename)
    cursor.execute ("DROP USER '%s'@'localhost'" % sitename)


####################################################################
# Enable a vhost
####################################################################
def enable_vhost(sitename):
    os.system('a2ensite '+sitename)

####################################################################
# Disable a vhost
####################################################################
def disable_vhost(sitename):
    os.system('a2dissite '+sitename)

####################################################################
# Restart the web server
####################################################################
def restart():
    os.system('/etc/init.d/apache2 restart')

####################################################################
# Handle command line interface
####################################################################

if len(sys.argv) == 1:
    print """
usage: srvmanage.py [option] [sitename]

options:
create  - Create a account
destroy - Destroy a account
enable  - Enable a website
disable - Disable a website

Site name must be less than 16 characters and contain only
alphanumeric characters.
"""

elif len(sys.argv) == 2:
    print 'Please specify site name.'
elif len(sys.argv) == 3:
    sitename = sys.argv[2];


    if not (len(sitename) < 14 and re.match('^[a-zA-Z0-9]+$', sitename) != None):
        print "Site name is invalid."
        exit()

#++++++++++++++++++++++++++++++++++
    if sys.argv[1] == 'create':
        print 'creating site...'

        #add user

        password = create_user(sitename)

        create_vhost(sitename)

        db_password = create_mysqldb(sitename)

        enable_vhost(sitename)

        restart()

        print
        print "-------------------------------------"
        print "ssh user       " + sitename
        print "ssh password   " + password
        print 
        print "db username    " + sitename
        print "db_password    " + db_password
        print "db_name        m_"+sitename
        print "-------------------------------------"


#++++++++++++++++++++++++++++++++++
    elif sys.argv[1] == 'destroy':
        print 'destroying site...'

        disable_vhost(sitename)

        destroy_mysqldb(sitename)

        destroy_vhost(sitename)

        destroy_user(sitename)

        restart()

#++++++++++++++++++++++++++++++++++
    elif sys.argv[1] == 'enable':
        print 'enabling site...'
        enable_vhost(sitename)
        restart()

#++++++++++++++++++++++++++++++++++
    elif sys.argv[1] == 'disable':
        print 'disabling site...'
        disable_vhost(sitename)
        restart()
    
#++++++++++++++++++++++++++++++++++
    else:
        print 'Unknown command'

cursor.close ()
con.close ()
