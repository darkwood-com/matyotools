MatyoTools
==========

About me...
-----------

My website [http://www.darkwood.fr/](http://www.darkwood.fr/)

More of my personnal projects can be found at [http://www.darkwood.fr/pagesperso/](http://www.darkwood.fr/pagesperso/)

Tools for programming
---------------------

*Some util tools that i developped for my developpement purpose*

* Bash
  - **git-sftp** : Allows to push or pull git repository to a remote git directory stored on sftp server

* Javascript
  - **Chickenfoot** : Based on [Chickenfoot](http://groups.csail.mit.edu/uid/chickenfoot/), it allow me to avoid boring task for recursive click when browsing on Firefox (only). Chickenfoot automaticaly click for you and save time!

* Node
  - **MatyoTools** : I store all of my every-day commands into matyotools.js script.

* Perl
  - **Storage** : Variant scripts for sync storage over Drobox and Ftp with Encfs

  - **Youtube-dl** : Script for downloading videos from the internet to the disk

* PHP
  - **Basecamp** : This allow use  [basecamp api](http://developer.37signals.com/basecamp/) in command line mode rather than spending more daily time on the web interface.

  - **Beautifier** : Extends [PHP_Beautifier](http://pear.php.net/package/PHP_Beautifier/) to format and beautify code.

  - **CS-Fixer** : Extends [PHP-CS-Fixer](http://cs.sensiolabs.org/) to format and beautify code.

  - **Harvest** : This allow use  [harverst api](http://www.getharvest.com/api) to fill and send approval of weeks, rather than spending time to record daily tasks.

  - **SearchReplace** : This tool allows search and replace from input to output (file or stream). You can override the classes to customise your replacement rules.

* Vagrant

Server ready (Wheezy box provisioned with LNPP : Linux, Nginx, Percona and PHP using Puppet) for testing.

Put in /etc/hosts :

```
44.44.44.44 phpinfo.matyotools.dev
44.44.44.44 phpmyadmin.matyotools.dev
44.44.44.44 searchreplace.matyotools.dev
```
