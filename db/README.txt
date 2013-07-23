In order to use the database dump data properly.

The database should be created with utf8_unicode_ci. This will allow
all unicode characters to be transmitted correctly to the web page.


In the directory /var/www on the server:
** This is for the vagrant server **
Create a path zfs/data/sessions
sudo chmod -R www-data:www-data /var/www/zfs
cd /vagrant
# zftest is installed here as /vagrant/zftest
cd zftest
ln -s /var/www/zfs/data .
# this will allow the session data file to be owned by www-data without which it will not work
