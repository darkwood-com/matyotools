Symfony Standard Edition
========================

Install
-------

### /etc/hosts
10.1.1.52 heartbreaker
10.1.1.52 heartbreaker.dev
10.1.1.52 php-memcached-admin.heartbreaker.dev

### phpMemcachedAdmin

mkdir phpMemcachedAdmin
cd phpMemcachedAdmin
wget http://phpmemcacheadmin.googlecode.com/files/phpMemcachedAdmin-1.2.2-r262.tar.gz
tar -xvzf phpMemcachedAdmin-1.2.2-r262.tar.gz
chmod +rx *
chmod 0777 Config/Memcache.php
chmod 0777 Temp/

### vagrant up ###

scp -i puphpet/files/dot/ssh/id_rsa ~/.ssh/id_rsa vagrant@heartbreaker:~/.ssh/id_rsa
scp -i puphpet/files/dot/ssh/id_rsa ~/.ssh/id_rsa.pub vagrant@heartbreaker:~/.ssh/id_rsa.pub
