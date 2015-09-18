EFI-EST Web Interface

Sequence similarity networks (SSNs) are a powerful tool for analyzing relationships among sequences in protein (super)families and that these will be useful for enhancing functional discovery/annotation using strategies developed by the Enzyme Function Initiative (EFI) as well as developing hypotheses about structure‑function relationships in families and superfamilies. As a result, this web tool provides “open access” to the ability to generate SSNs. Four different options for user-initiated generation of SSNs, three with this web tool and one with Unix terminal scripts:

This is the web interface for the command line scripts located at
```https://github.com/EnzymeFunctionInitiative/EST```

## Installation
1.  Git Clone the repository
```git clone https://github.com/EnzymeFunctionInitiative/est-web.git```
2.  Add Alias in the apache config to point to the html folder
```Alias /efi-est /var/www/efi-est/html```
3.  Create Mysql database and user
4.  Import sql schema into mysql
```mysql -u root -p efi-gnt < sql/efi-est.sql```
5.  Copy conf/settings.inc.php.example to conf/settings.inc.php
6.  Edit conf/setting.inc.php with mysql information, and other information
7.  Create user that efi-est will run as
8.  Add to crontab
```*/5 * * * * efi_est /var/www/efi-est/bin/master.sh > /dev/null 2>&1```
9.  Create symlink to html/results folder to data directory. 
```ln -s /home/efi_est/results /var/www/efi-est/html/results```
10.  Give apache user read/write permissions to /uploads
11.  Give efi_est user read/write permssions to /logs




