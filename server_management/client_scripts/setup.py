from setuptools import setup

setup(
    name='client_scripts',
    version='1',
    author='Robert Hickman',
    author_email='robehickman@gmail.com',
    license='MIT',
    install_requires=[
    ],
    scripts=['scripts/backup_server.py', 'scripts/update_sites.py'],
    zip_safe=False)
