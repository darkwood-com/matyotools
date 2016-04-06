MatyoTools
==========

About me...
-----------

My website [http://www.darkwood.fr/](http://www.darkwood.fr/)

More of my personnal projects can be found at [http://apps.darkwood.fr/](http://apps.darkwood.fr/)

Tools for programming
---------------------

*Some util tools that i developped for my developpement purpose*

* Bash
  - **applescript** : Applescript commands
  - **ffmpeg** : Scripts that use https://www.ffmpeg.org/ for batch video conversion
  - **git-sftp** : Allows to push or pull git repository to a remote git directory stored on sftp server
  - **vagrant** : Scripts for [vagrant](http://www.vagrantup.com/) development usage

* Javascript
  - **Chickenfoot** : Based on [Chickenfoot](http://groups.csail.mit.edu/uid/chickenfoot/), it allow me to avoid boring task for recursive click when browsing on Firefox (only). Chickenfoot automaticaly click for you and save time!

* Node
  - **MatyoTools** : I store all of my every-day commands into matyotools.js script.

* Perl
  - **Youtube-dl** : Script for downloading videos from the internet to the disk

* PHP
  - **Basecamp** : This allow use  [basecamp api](http://developer.37signals.com/basecamp/) in command line mode rather than spending more daily time on the web interface.

  - **Beautifier** : Extends [PHP_Beautifier](http://pear.php.net/package/PHP_Beautifier/) to format and beautify code.

  - **CS-Fixer** : Extends [PHP-CS-Fixer](http://cs.sensiolabs.org/) to format and beautify code.

  - **Harvest** : This allow use  [harverst api](http://www.getharvest.com/api) to fill and send approval of weeks, rather than spending time to record daily tasks.

  - **HeartBreaker** : Grab Heartstone card database and stats. Then make stats on player card collection to find the best deck ratio to win more in game.

  - **Hipchat** : This allow send [hipchat](https://www.hipchat.com) messages from command line.

  - **SearchReplace** : This tool allows search and replace from input to output (file or stream). You can override the classes to customise your replacement rules.

* Python
  - **Youtube-dl** : Script for downloading videos from the internet to the disk

* Vagrant

Server ready (Wheezy box provisioned with LNPP : Linux, Nginx, Percona and PHP using Puppet) for testing.

Put in /etc/hosts :

```
192.168.56.101 phpinfo.matyotools.dev
192.168.56.101 phpmyadmin.matyotools.dev
192.168.56.101 searchreplace.matyotools.dev
192.168.56.101 harvest.matyotools.dev
```
