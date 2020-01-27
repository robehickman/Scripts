#!/usr/bin/python3

import sys
if sys.version_info < (3, 6): raise SystemExit("Requires python 3.6 or greater")

import os, os.path, stat, json, yaml
from subprocess import Popen, PIPE
from pathlib import Path

with open(str(Path.home() / '.update_sites.yaml'), 'r') as fle:
    config = yaml.load(fle.read())

path                       = Path(config['local_sites_path'])
backup_accout_ssh_username = config['backup_accout_ssh_username']
ssh_domain                 = config['ssh_domain']


# Get data about existing sites from the server
stdout, stderr = Popen(['ssh', f"{backup_accout_ssh_username}@{ssh_domain}", 'srvmanage.py list -j'],
                        stdout=PIPE).communicate()

sites = json.loads(stdout)

for site in sites:
    name = site['name']

    if os.path.exists(str(path / name)):
        print(f"site {name} exists, doing nothing")

    else:
        print(f"Setting up site  {name}")

        os.mkdir(str(path / name))
        os.mkdir(str(path / name / "remote"))
        os.mkdir(str(path / name / "git"))

        script = "#!/bin/bash\n"
        script += f"sshfs -o reconnect,ServerAliveInterval=15,ServerAliveCountMax=3 {name}@{ssh_domain}:/srv/http/{name} remote"

        with open(str(path / name / "mount.sh"), 'a') as fle: fle.write(script)

        #chmod file
        os.chmod(str(path / name / "mount.sh"), stat.S_IEXEC)

        #checkout git repo
        os.system(f"git clone {name}@{ssh_domain}:/srv/repos/web/{name} {str(path / name / 'git')}")

    #Run git pull on all vhosts
    os.system(f"git --work-tree={str(path / name / 'git')} --git-dir={str(path / name / 'git' / '.git')} pull origin master")
        
