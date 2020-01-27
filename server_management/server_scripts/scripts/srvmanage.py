#!/usr/bin/python3
""" Script to manage virtual hosted websites with apache, php-fpm and mysql """

import sys
if sys.version_info < (3, 6): raise SystemExit("Requires python 3.6 or greater")

import os, re, crypt, secrets, yaml, random, string, json
from typing import Tuple
import subprocess as sub
import pymysql as mysql

# Script can only be run by the root user
if os.geteuid() != 0:
    print('Must be root')
    quit()

# Load configuration
with open('/root/srvmanage.yaml', 'r') as fle:
    config = yaml.load(fle.read())

srvname         = config['srvname'] 
apache_confdir  = config['apache_confdir']
php_confdir     = config['php_confdir']
fpm_sock_prefix = config['fpm_sock_prefix']
webroot         = config['webroot']
reporoot        = config['reporoot']

db_host         = config['db_host']
db_user         = config['db_user']
db_pass         = config['db_pass']

sqlite_file     = config['sqlite_file']


#+++++++++++++++++++++++++++++++++++++
def rand_passwd():
    return secrets.token_urlsafe(nbytes=20)

####################################################################
# Keep track of the sites that currently exist on the server
# in a database table
####################################################################
def db_connect():
    return mysql.connect (host = db_host, user = db_user, passwd = db_pass,
                          charset='utf8mb4', cursorclass=mysql.cursors.DictCursor)


#+++++++++++++++++++++++++++++++++++++
def db_get_site(name: str):
    conection = db_connect()
    cursor = conection.cursor()    
    cursor.execute ("use sites")
    cursor.execute("SELECT * FROM sites WHERE name='"+name+"'")
    data = cursor.fetchone()
    cursor.close()
    return data


#+++++++++++++++++++++++++++++++++++++
def db_get_sites():
    conection = db_connect()
    cursor = conection.cursor()    
    cursor.execute ("use sites")
    cursor.execute("SELECT * FROM sites")
    data = cursor.fetchall()
    cursor.close()
    return data

#+++++++++++++++++++++++++++++++++++++
def db_store_new_site(name: str, status: int, sshpass: str, sqlpass: str):
    conection = db_connect()
    cursor = conection.cursor()    
    cursor.execute ("use sites")
    cursor.execute(f"""
        insert into sites (
            name,
            status,
            sshpass,
            sqlpass
        ) values ( 
            '{name}',
            '{str(status)}',
            '{sshpass}',
            '{sqlpass}'
        ) """)

    conection.commit()
    cursor.close()


#+++++++++++++++++++++++++++++++++++++
def db_set_site_status(name: str, status: int):
    conection = db_connect()
    cursor = conection.cursor()    
    cursor.execute ("use sites")
    cursor.execute(f"update sites set status = '{str(status)}' where name='{name}'")
    conection.commit()
    cursor.close()


#+++++++++++++++++++++++++++++++++++++
def db_delete_site(name: str):
    conection = db_connect()
    cursor = conection.cursor()    
    cursor.execute ("use sites")
    cursor.execute(f"delete from sites where name='{name}'")
    conection.commit()
    cursor.close()


####################################################################
# Management of system user accounts
####################################################################
def create_system_user(sitename: str) -> str:
    password = rand_passwd()

    crypt_pass = crypt.crypt(password,"22")
    os.system(f"useradd -s /bin/bash -M --home-dir {webroot}/{sitename} -p {crypt_pass} {sitename}")

    return password


#+++++++++++++++++++++++++++++++++++++
def delete_system_user(sitename: str) -> None:
    os.system(f"gpasswd -d www-data {sitename}") # remove group from www-data
    os.system(f"userdel  {sitename}")


####################################################################
# Management of virtual hosts
####################################################################
def create_apache_vhost(sitename: str) -> None:

    vhost = f"""
<VirtualHost 178.79.156.15:80>
    ServerName {sitename}.{srvname}
    DocumentRoot {webroot}/{sitename}/docs

    ErrorLog  ${{APACHE_LOG_DIR}}/{sitename}_error.log
    CustomLog ${{APACHE_LOG_DIR}}/{sitename}_access.log combined

    ProxyPassMatch ^/(.*\.php(/.*)?)$ unix:{fpm_sock_prefix}-{sitename}.sock|fcgi://127.0.0.1:9000{webroot}/{sitename}/docs

    <Directory {webroot}/{sitename}/docs">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
    """.strip()

    with open(f"{apache_confdir}/{sitename}.conf",'w') as cfile:
        cfile.write(vhost)

    # Create directories for web root
    os.mkdir(f"{webroot}/{sitename}")
    os.mkdir(f"{webroot}/{sitename}/docs")

    cfile = open(f"{webroot}/{sitename}/docs/index.html",'w')
    cfile.write('It works!')
    cfile.close()

    #fix permissions
    os.system(f"chown -Rf {sitename}:{sitename} {webroot}/{sitename}") 
    os.system(f"chmod -Rf 750 {webroot}/{sitename}") 
    os.system(f"usermod -a -G {sitename} www-data") 

#+++++++++++++++++++++++++++++++++++++
def create_php_fpm_config(sitename: str) -> None:

    php_conf = f"""
[{sitename}]
user   = {sitename}
group  = {sitename}
listen = {fpm_sock_prefix}-{sitename}.sock
listen.owner = www-data
listen.group = www-data
 
pm = dynamic
pm.max_children = 2
pm.start_servers = 1
pm.min_spare_servers = 1
pm.max_spare_servers = 1
 
security.limit_extensions = .php
    """.strip()

    with open(f"{php_confdir}/{sitename}.conf",'w') as cfile:
        cfile.write(php_conf)


#+++++++++++++++++++++++++++++++++++++
def create_git_repo_for_website(sitename: str) -> None:
    #set up bare git repo for access
    os.mkdir(f"{reporoot}/{sitename}")
    os.chdir(f"{reporoot}/{sitename}")
    os.system('git init --bare')

    # Initialise web root as a git repo create directories and set up git repo
    os.chdir(f"{webroot}/{sitename}")
    os.system('git init')
    os.system('git add .')
    os.system('git commit -m "Initial commit"')
    os.system(f"git remote add origin {reporoot}/{sitename}")
    os.system('git push --all origin')

#create hook script to update webroot
    hook = f"""
#!/bin/sh
cd {webroot}/{sitename}
echo 'Updating website'
env -i git reset --hard
env -i git pull origin master"""

    with open(f"{reporoot}/{sitename}/hooks/post-receive",'w') as cfile:
        cfile.write(hook)

    os.system(f"chmod +x {reporoot}/{sitename}/hooks/post-receive") 

    #copy ssh
    os.system(f"mkdir /srv/http/{sitename}/.ssh") 
    os.system(f"cp /root/ssh_keys/* /srv/http/{sitename}/.ssh/") 

    #fix permissions
    os.system(f"chown -Rf {sitename} {reporoot}/{sitename}") 
    os.system(f"chmod -Rf 700 {reporoot}/{sitename}") 


#+++++++++++++++++++++++++++++++++++++
def delete_vhost(sitename: str) -> None:
    os.system(f"rm -Rf {webroot}/{sitename}")
    os.system(f"rm -Rf {reporoot}/{sitename}")
    os.system(f"rm -Rf {apache_confdir}/{sitename}.conf")
    os.system(f"rm -Rf {php_confdir}/{sitename}.conf")


####################################################################
# Management of mysql databases
####################################################################
def create_mysql_database(sitename: str) -> Tuple[str, str]:
    db_password = rand_passwd()
    db_name     = 'm_' + sitename

    connection = db_connect()
    cursor = connection.cursor()

    cursor.execute (f"CREATE USER '{sitename}'@'localhost' IDENTIFIED BY '{db_password}'")
    cursor.execute (f"CREATE DATABASE `m_%s`" % sitename)
    cursor.execute (f"""GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,ALTER,INDEX,DROP
        ON `m_%s`.* TO '%s'@'localhost'""" % (sitename, sitename))
    connection.commit()

    return db_name, db_password

#+++++++++++++++++++++++++++++++++++++
def delete_mysql_database(sitename: str) -> None:
    connection = db_connect()
    cursor = connection.cursor()

    try: cursor.execute (f"DROP DATABASE `m_{sitename}`")
    except mysql.err.InternalError: pass

    try: cursor.execute (f"DROP USER '{sitename}'@'localhost'")
    except mysql.err.InternalError: pass

    connection.commit()


####################################################################
# Apache virtual host management
####################################################################
def enable_apache_vhost(sitename):
    os.system('a2ensite '+sitename)

#+++++++++++++++++++++++++++++++++++++
def disable_apache_vhost(sitename):
    os.system('a2dissite '+sitename)

#+++++++++++++++++++++++++++++++++++++
def restart_apache2():
    os.system('service apache2 restart')

####################################################################
# PHP=fpm management
####################################################################
def restart_php_fpm():
    os.system('service php7.4-fpm restart')



####################################################################
# Handle command line interface
####################################################################
def read_site_name() -> str:
    if len(sys.argv) < 3: raise SystemExit('Please specify site name.')

    sitename = sys.argv[2]

    if not (len(sitename) < 14 and re.match('^[a-zA-Z0-9]+$', sitename) != None):
        raise SystemExit("Site name is invalid.")

    return sitename


#--------------------------------
if len(sys.argv) == 1 or sys.argv[1] == '-h' or sys.argv[1] == '--help':
    print("""
usage: srvmanage.py [option] [sitename]

options:
create  - Create a website
delete  - delete a website
enable  - Enable a website
disable - Disable a website
list    - get a list of sites and credentials, either
          in flat format or json (-j)

Site name must be less than 16 characters and contain only
alphanumeric characters.
""")
    raise SystemExit()

#--------------------------------
# UI Switch
#--------------------------------
if sys.argv[1] == 'create':
    sitename = read_site_name()

    if db_get_site(sitename) is not None: raise SystemExit('Site allready exists')

    print('creating site...')

    password = create_system_user(sitename)
    create_apache_vhost(sitename)
    create_php_fpm_config(sitename)
    create_git_repo_for_website(sitename)

    db_name, db_password = create_mysql_database(sitename)

    db_store_new_site(sitename, 1, password, db_password)

    enable_apache_vhost(sitename)
    restart_apache2()
    restart_php_fpm()

    print()
    print("-------------------------------------")
    print("ssh user       " + sitename)
    print("ssh password   " + password)
    print() 
    print("db username    " + sitename)
    print("db_password    " + db_password)
    print("db_name        " + db_name)
    print("-------------------------------------")


#++++++++++++++++++++++++++++++++++
elif sys.argv[1] == 'delete':
    print('deleteing site...')

    sitename = read_site_name()
    disable_apache_vhost(sitename)
    delete_vhost(sitename)
    delete_mysql_database(sitename)
    delete_system_user(sitename)

    db_delete_site(sitename)

    restart_apache2()
    restart_php_fpm()

#++++++++++++++++++++++++++++++++++
elif sys.argv[1] == 'enable':
    print('enabling site...')

    sitename = read_site_name()
    enable_apache_vhost(sitename)
    restart_apache2()

    db_set_site_status(sitename, 1)

#++++++++++++++++++++++++++++++++++
elif sys.argv[1] == 'disable':
    print('disabling site...')

    sitename = read_site_name()
    disable_apache_vhost(sitename)
    restart_apache2()

    db_set_site_status(sitename, 0)

#++++++++++++++++++++++++++++++++++
elif sys.argv[1] == 'list':

    sites = db_get_sites()

    if len(sys.argv) > 2 and sys.argv[2] == '-j':
        print(json.dumps(sites))

    else:
        for site in sites:
            print('Name:   ' + site['name'])
            print('Status: ' + str(site['status']))
            print('DB:     ' + str(site['sqlpass']))
            print('SSH:    ' + str(site['sshpass']))
            print()

else:
    print('Unknown command')

