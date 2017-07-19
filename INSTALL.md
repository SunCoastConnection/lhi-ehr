# Installation Instructions
Last Updated: March 28, 2017

### Table of Contents

**[Overview of Directories](#overview-of-directories)**

**[Unpacking](#unpacking)**

**[Setup](#setup)**

**[Setting up Access Control](#setting-up-access-control)**

**[Upgrading](#upgrading)**

**[FAQ](#faq)**


##  Overview of Directories

NOTE: Most recent documentation can be found on the online documentation at [LibreHealth](http://librehealth.io/).

contrib: Contains many user-contributed encounter forms and utilities
custom: Contains scripts and other text files commonly customized
Documentation: Contains useful documentation
interface: Contains User Interface scripts and configuration
library: Contains scripts commonly included in other scripts
sql: Contains initial database images
gacl: Contains embedded php-GACL (access controls)

##  Unpacking

The LibreHealthEHR release archive should be named as follows:

`libreehr-<version>-release.tar.gz  -or-  libreehr-<version>-release.zip`

To extract the archive, use either of the following commands from the command line:

`bash# tar -pxvzf libreehr-<version>-release.tar.gz`
`bash# unzip libreehr-<version>-release.tar.gz`

Be sure to use the `-p` flag when using tar, as certain permissions must be preserved.

LibreHealthEHR will be extracted into a directory named `librehealthehr`.

Alternatively a Debian package may be available as a file named `libreehr-<version>.deb`.
 This may work with some other Debian-like Linux distributions such as Ubuntu.

##  Setup

To run LibreHealthEHR, MariaDB (prefered) or MySQL, and Apache or another PHP-capable webserver must be configured.

If you don't already have it, download and install [Apache](www.apache.org), [MariaDB](https://mariadb.org) (prefered) or [MySQL](www.mysql.com), and [PHP.](www.php.net)

**Note:**

1. PHP versions 7.1+ are not supported.

2. MySQL versions 5.7+ have strict mode enabled by default and must be disabled. Instructions on how to disable it are given in the FAQs section.

3. LibreHealthEHR requires a number of webserver and PHP features which may not be enabled on your system.  These include:
  * PHP Index support (ensure that index.php is in your Index path in httpd.conf)
  * Session variables
  * PHP libcurl support (optional for operation, mandatory for billing)
  
4. If installing on Linux, make sure these dependencies are met:
```
apache2
mysql-server (or if using mariadb, then use 'mariadb-server' instead)
libapache2-mod-php
libdate-calc-perl
libdbd-mysql-perl
libdbi-perl
libhtml-parser-perl
libtiff-tools
libwww-mechanize-perl
libxml-parser-perl
php
php-mysql
php-cli
php-gd
php-xsl
php-curl
php-mcrypt
php-soap
php-json
php-gettext
imagemagick
php-mbstring
php-zip
php-ldap
```


Copy the LibreHealthEHR folder into the root folder of the webserver. On Mandrake Linux, for example, use the command:
  `bash# mv libreehr /var/www/html/`

Make sure the webserver is running, and point a web-browser to `setup.php` located within the libreehr web folder.  If you installed LibreHealthEHR in the root web directory, the URL would read: `http://localhost/libreehr/setup.php`.
The setup script will step you through the configuration of the LibreHealthEHR.

The first screen of the setup script will ensure that the webserver user (in linux, often is `apache`, `www-data`, or `nobody`) has write privileges on certain files and directories. 
The files include `libreehr/sites/default/sqlconf.php` and `libreehr/interface/modules/zend_modules/config/application.config.php`.

In linux, these can be set by `chmod a+w filename` command to grant global write permissions to the file. The directories include: 
```
libreehr/gacl/admin/templates_c
libreehr/sites/default/edi
libreehr/sites/default/era
libreehr/sites/default/documents
libreehr/interface/main/calendar/modules/PostCalendar/pntemplates/compiled
libreehr/interface/main/calendar/modules/PostCalendar/pntemplates/cache. 
```

**Note:** In linux, if the webserver user name is `apache`, then the command `chown -R apache:apache directory_name` will grant global write permissions to the directories, and we recommend making these changes permanent. Should the page display errors related to file or directory writing priviledges you may click the 'Check Again' button to try again (after fixing permissions).


#### Step 1
You need to tell setup whether it needs to create the database on its own, or if you have already created the database.  MySQL root priveleges will be required to create a database.

#### Step 2
You will be presented with a number of fields which specify the MySQL server details and the `libreehr` directory paths.

The `Server Host` field specifies the location of the MySQL server.  If you run MySQL and Apache/PHP on the same server, then leave this as `localhost`. If MySQL and Apache/PHP are on separate servers, then enter the IP address (or host name) of the server running MySQL.

The `Server Port` field specifies the port to use when connecting to the MySQL server over TCP/IP.  This should be left as `3306` unless you changed it in your  MySQL configuration.

The `Database Name` field is the database where LibreHealthEHR will reside.  If you selected to have the database created for you, this database will be created, along with the user specified in `Login Name`.  If this database exists, setup will not be able to create it, and will return an error.  If you selected that you have already created the database, then setup will use the information you provide to connect to the MySQL server.  Note that setup will not accept a password that is not at least one (1) character in length.

The `Login Name` field is the MySQL user that will access the LibreHealthEHR database. If you selected to have the database created for you, this user will be created.  If you selected that you have already created the database, then setup will use the information you provide to connect to the MySQL server.

The `Password` field is the password of the user entered into the above `Login Name` field.  If you selected to have the database created for you, this user and password  will be created.  If you selected that you have already created the database, then setup will use the information you provide to connect to the MySQL server.

The `Name for Root Account` field will only appear if setup is creating the database.  It is the name of the MySQL root account. For localhost, it is usually ok to leave it `root`.

The `Root Pass` field will likewise only appear if setup is creating the database.  It is the password of your existing root user, and is used to acquire the privileges to create the new database and user.

The `User Hostname` field will also only appear if setup is creating the database.  It is the hostname of the Apache/PHP server from which the user, i.e,  `Login Name` is permitted to connect to the MySQL database.  If you are setting up MySQL and Apache/PHP on the same computer, then you can use `localhost`. 

The `UTF-8 Collation` field is the collation setting for mysql. If the language you are planning to use in LibreHealthEHR is in the menu, then you can select it. Otherwise, just select `General`. Choosing`None` is not recommended and will force latin1 encoding.

The `Initial User` is the username of the first user, which is what they will use to login.  Limit this to one word only.

The `Initial User Password` is the password of the user entered into the above `Initial User` field.

The `Initial User's First Name` is the value to be used as their first name.  This information may be changed in the user administration page.

The `Initial User's Last Name` is the value to be used as their last name.  This information may be changed in the user administration page.

The `Initial Group` is the first group, basically name of the practice, that will be created.  A user may belong to multiple groups, which again, can be altered on the user administration page. It is suggested that no more than one group per office be used.


#### Step 3
This is where setup will configure LibreHealthEHR.  It will first create the database and connect to it to create the initial tables.  It will then write the mysql database configuration to the `libreehr/sites/default/sqlconf.php` file. 

Should anything fail during Step 3, you may have to remove the existing database or tables before you can try again. If no errors occur, you will see a `Continue` button at the bottom.


#### Step 4
This step will install and configure the embedded phpGACL access controls.  It will first write configuration settings to files.  It will then configure the database.  It will then give the `Initial User` administrator access. 

Should anything fail during Step 4, you may have to remove the existing database or tables before you can try again. If no errors occur, you will see a `Continue` button at the bottom.


#### Step 5
You will be given instructions on configuring the PHP.  We suggest you print these instructions for future reference.  Instructions are given to edit the `php.ini` configuration file.  If possible, the location of your `php.ini` file will be displayed in green. 

If your `php.ini` file location is not displayed, then you will need to search for it.  The location of the `php.ini` file is dependent on the operating system.  In linux, `php.ini` is generally found inside the `/etc/php/` directory.  In Windows, the `XAMPP` package places the `php.ini` file in the `xampp\apache\bin\` directory. 

To ensure proper functioning of LibreHealthEHR you must make sure that settings in the `php.ini` file include:
```
max_execution_time = 600
max_input_time = 600
max_input_vars = 5000
memory_limit = 1024M
post_max_size = 32M
upload_max_filesize = 16M
session.gc_maxlifetime = 14400
short_open_tag = On
display_errors = Off
register_globals = Off
``` 

In order to take full advantage of the patient documents capability you must make sure that settings in `php.ini` file include `file_uploads = On`, that `upload_max_filesize` is appropriate for your use, and that `upload_tmp_dir` is set to a correct value that will work on your system.

Restart apache service. Instructions on doing that are given in the FAQ section.


#### Step 6
You will be given instructions on configuring the Apache web server.  We suggest you print these instructions for future reference. Instructions are given to secure the`libreehrwebroot/sites/*/documents`, `libreehrwebroot/sites/*/edi` and `libreehrwebroot/sites/*/era` directories, which contain patient information. This can be done be either placing pertinent `.htaccess` files in these directories or by editing the apache configuration file. 

The location of the apache configuration file is dependent on the operating system.  In linux, you can type `httpd -V` or `apache2ctle  -V` on the commandline;  the location to the configuration file will be the `HTTPD_ROOT` variable plus the `SERVER_CONFIG_FILE` variable. In Windows, the` XAMPP` package places the configuration file at `xampp\apache\conf\httpd.conf`. 

To configure Zend and to secure the `/documents`, `/edi` and `/era` directories you can paste following to the end of the apache configuration file (ensure you put full path to directories):
```xml
<Directory "libreehrwebroot">
AllowOverride FileInfo
</Directory>
<Directory "libreehrwebroot/sites">
AllowOverride None
</Directory>
<Directory "libreehrwebroot/sites/*/documents">
order deny,allow
Deny from all
</Directory>
<Directory "libreehrwebroot/sites/*/edi">
order deny,allow
Deny from all
</Directory>
<Directory "libreehrwebroot/sites/*/era">
order deny,allow
Deny from all
</Directory>
```

For proper access to all pages of the website, enable the `mod_rewrite` module by issuing `a2enmod rewrite` on a terminal.

The final screen includes some additional instructions and important information. We suggest you print these instructions for future reference.

Once the system has been configured properly, you may login.  Connect to the webserver where the files are stored with your web browser.  Login to the system using the username that you picked (default is `admin`), and the password.  From there, select the `Administration` option, and customize the system to your needs.  Add users and groups as is needed. For information on using LibreHealthEHR, consult the User Documentation located in the `Documentation` folder, the documentation at [LibreHealth](http://librehealth.io/).

Reading `libreehr/sites/default/config.php` is a good idea.

To create custom encounter forms online documentation at [LibreHealth](http://librehealth.io/). Many forms exist in `interface/forms` and may be used as examples.

Other configuration settings are stored under `includes/config.php`. Everything should work out of the installation without touching those, but if  you want fax integration you will need to adjust some parameters in that file.

General-purpose fax support requires customization within LibreHealthEHR at Administration->Globals and custom/faxcover.txt; it also requires the following utilities:

* faxstat and sendfax from the HylaFAX client package
* mogrify from the ImageMagick package
* tiff2pdf, tiffcp and tiffsplit from the libtiff-tools package
* enscript


##   Setting Up Access Control

phpGACL access controls are installed and configured automatically during LibreHealthEHR setup.  It can be administered within LibreHealthEHR in the admin->acl menu.  This is very powerful access control software. 

Learn more about phpGACL [here](http://phpgacl.sourceforge.net/), recommend reading the phpGACL manual, the `/libreehr/Documentation/README.phpgacl` file, and the online documentation at [LibreHealth](http://librehealth.io/) . Also recommend reading the comments in `/libreehr/library/acl.inc`.

##  Upgrading

Be sure to back up your LibreHealthEHR installation and database before upgrading!

Upgrading LibreHealthEHR is currently done by replacing the old `libreehr` directory with a newer version. And, ensure you copy your settings from the following old `libreehr` files to the new configuration files (we do not recommend simply copying the entire files):

```
libreehr/sites/default/config.php
libreehr/sites/default/sqlconf.php
```

In this `sqlconf.php` file, set  `$config = 1;` (found near bottom of file within bunch of slashes)

The following directories should be copied from the old version to the new version:
```
libreehr/sites/default/documents
libreehr/sites/default/era
libreehr/sites/default/edi
libreehr/sites/default/letter_templates
```

If there are other files that you have customized, then you will also need to treat those carefully.

## FAQ

**How can I install Apache, MySQL, and PHP on Windows?**

An easy way would be to install the [XAMPP Package](https://www.apachefriends.org/index.html). Make sure to copy the LibreHealthEHR files to the `htdocs` folder.


**Q. How can I install Apache, MySQL, and PHP on Linux?**

Follow the instructions [here](https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-on-ubuntu-16-04)


**Q. I'm getting `table doesn't exist` error!**

This is because MySQL versions 5.7 and above have `strict mode` on default which needs to be disabled. This can be done by editing the MySQL configuration file and appending `sql_mode = ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION` at the end (if it is already present, modify it).

In Linux, this file is located in `/etc/mysql/mysql.conf.d/mysqld.cnf`. In Windows, it is usually located in `C:\ProgramData\MySQL\MySQL Server 5.7\my.ini`


**Q. How do I restart the apache service?**

Restart apache service by using `sudo apache2ctl restart` on a terminal for Linux.
For Windows, restart apache service by using the XAMPP control interface, located in system tray (if running) or from `xampp-control.exe` in `C:\xampp`. You can restart apache by navigating to `C:\xampp\apache\bin` using CMD and executing `httpd -k restart`.

**Q. I need help! How do I reach you?**

Feel free to drop by [LibreHealth Chat](https://chat.librehealth.io) or the [LibreHealth Forums](https://forums.librehealth.io/)!
