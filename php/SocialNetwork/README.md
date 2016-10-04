SocialNetwork
=============

A Symfony project created on October 4, 2016, 11:22 am.


configure apache (mac)
----------------------

Doc : https://coolestguidesontheplanet.com/set-virtual-hosts-apache-mac-osx-10-10-yosemite/

Allow the vhosts configuration from the Apache configuration file httpd.conf

Open the httpd.conf

    sudo nano /etc/apache2/httpd.conf

Search for ‘vhosts‘ and uncomment the include line

    Include /private/etc/apache2/extra/httpd-vhosts.conf

Also allow another module to run by uncommenting:

    LoadModule vhost_alias_module libexec/apache2/mod_vhost_alias.so

Edit the vhosts.conf file
-------------------------

Open this file to add in the vhost.

    sudo nano /etc/apache2/extra/httpd-vhosts.conf

An example in the file is given of the format required to add additional domains, just follow this to create your new virtual host:

<VirtualHost *:80>
    ServerName social.darkwood.dev
    DocumentRoot "/Users/math/Sites/darkwood/matyotools/php/SocialNetwork/web"
    ErrorLog "/private/var/log/apache2/social.darkwood.dev-error.log"
    CustomLog "/private/var/log/apache2/social.darkwood.dev-access.log" common
    ServerAdmin matyo91@gmail.com

    <Directory />
	AllowOverride All
		Options Indexes MultiViews FollowSymLinks
		Require all granted
    </Directory>
</VirtualHost>

Find Your User and Group
------------------------

In the Terminal use the id command to see your username and group

    id

You will get a bunch of user groups, you need your primary user uid and group gid names

    uid=502(math) gid=20(staff)

Change this back in /etc/apache2/httpd.conf

    #User _www
    #Group _www

    User math
    Group staff

Restart Apache
--------------

sudo apachectl restart

hosts
-----

127.0.0.1 social.darkwood.dev