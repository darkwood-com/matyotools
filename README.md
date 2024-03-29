MatyoTools
==========

About me...
-----------

My website [https://darkwood.fr](https://darkwood.fr)

More of my personnal projects can be found at [https://apps.darkwood.fr/](https://apps.darkwood.fr)

Tools for programming
---------------------

*Some util tools that i developped for my developpement purpose*

* Bash

  - **[applescript](bash/applescript)** : Applescript commands
  
  - **[ffmpeg](bash/ffmpeg)** : Scripts that use https://www.ffmpeg.org/ for batch video conversion
  
  - **[git-sftp](bash/git-sftp)** : Allows to push or pull git repository to a remote git directory stored on sftp server
  
  - **[bash-profile](bash/profile)** : My custom bash profile
  
  - **[vagrant](bash/vagrant)** : Scripts for [vagrant](http://www.vagrantup.com/) development usage

* Javascript

  - **[Chickenfoot](js/Chickenfoot)** : Based on [Chickenfoot](http://groups.csail.mit.edu/uid/chickenfoot/), it allow me to avoid boring task for recursive click when browsing on Firefox (only). Chickenfoot automaticaly click for you and save time!

* Node

  - **[MatyoTools](node/MatyoTools)** : I store all of my every-day commands into matyotools.js script.

* PHP

  - **[Basecamp](php/Basecamp)** : This allow use  [basecamp api](http://developer.37signals.com/basecamp/) in command line mode rather than spending more daily time on the web interface.

  - **[Beautifier](php/Beautifier)** : Extends [PHP_Beautifier](http://pear.php.net/package/PHP_Beautifier/) to format and beautify code.

  - **[Botman](php/Botman)** : Bot chat scripting.
  
  - **[CS-Fixer](php/CS-Fixer)** : Extends [PHP-CS-Fixer](http://cs.sensiolabs.org/) to format and beautify code.

  - **[Harvest](php/Harvest)** : This allow use  [harverst api](http://www.getharvest.com/api) to fill and send approval of weeks, rather than spending time to record daily tasks.

  - **[HeartBreaker](php/HeartBreaker)** : Grab Heartstone card database and stats. Then make stats on player card collection to find the best deck ratio to win more in game.

  - **[Hipchat](php/Hipchat)** : This allow send [hipchat](https://www.hipchat.com) messages from command line.

  - **[SearchReplace](php/SearchReplace)** : This tool allows search and replace from input to output (file or stream). You can override the classes to customise your replacement rules.

  - **[SocialNetwork](php/SocialNetwork)** : Try to grab social feed (Facebook, Twitter, Intagram, ...) and make it into a dashboard (friends activity, interests, feed push).

* Python

  - **[Youtube-dl](python/youtube)** : Script for downloading videos from the internet to the disk

* Vagrant

Server ready (Wheezy box provisioned with LNPP : Linux, Nginx, Percona and PHP using Puppet) for testing.

Put in /etc/hosts :

```
192.168.56.101 phpinfo.matyotools.dev
192.168.56.101 phpmyadmin.matyotools.dev
192.168.56.101 searchreplace.matyotools.dev
192.168.56.101 harvest.matyotools.dev
```
