In order to use the database dump data properly.

The database should be created with utf8_unicode_ci. This will allow
all unicode characters to be transmitted correctly to the web page.


In the directory /var/www on the server:
** This is for the vagrant server - running on OracleVirtualBox **
Create a path zfs/data/sessions
sudo chmod -R www-data:www-data /var/www/zfs
cd /vagrant
# zftest is installed here as /vagrant/zftest
cd zftest
ln -s /var/www/zfs/data .
# this will allow the session data file to be owned by www-data 
#   without which it will not work


Sessions have been turned on.

In the directory (htdocs) or document home, there should be a directory
zfs/data/sessions that should be owned by the user which runs the 
webserver (On Linux it is www-data).

The bottom line in that in zftest/data it should find the contents of zfs/data.

If this is being installed on a windows box:
Create a directory data/sessions (instead of the path suggested above!) in the 
zftest directory. The zftest directory should live in htdocs and if using apache, 
the .htaccess file provided needs to be installed (done by default). 

For the time being we will be using the path:
<host ip address>/zftest/public. Later we will make this the default path and 
<hostname>/anypath will take you directly to the application and it will not be 
possible to see any files below ../public.

Jay Kelkar 07/25 22:15


--------------------------------------------------------------
Leave this section at the end - Always :)

Open items:

Q: Calculation of sub-section scores that depend on elements
   Somewhere there is mention of calculating the scores only at the time of 
   changing state to either 'COMPLETE' or 'APPROVED'.
   
   In any case, it is more expensive to calculate the scores on the fly, 
   as often elements go across screens and we need to have all the 
   answers before a score can be determined.