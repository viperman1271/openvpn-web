# OpenVPN-WEB
  
Current version : 2.0.3

Web portal for OpenVPN gateway. Provides some reporting information and basic data visualization for usage of the OpenVPN server.

# Dependencies
* MySQL
* OpenVPN (already configured)

# Pre-Installation Configuration
## MySQL
The MySQL configuration must be manually edited. There is a default configuration file available config/mysql.default.php. Your locally copy should be renamed to mysql.php after all the information has been filled in.

## OpenVPN
Just as MySQL, there are a few variables that must be modified. The default configuration file is config/openvpn.default.php. The local copy should be renamed to openvpn.php with all the correct configuration information.

# Installation
Copy all of the files to the server, ensure correct execution rights are given to the files. Then launch the install.php inside your browser. This can also be accessed via the Admin/Install option from the navigation bar on the left.

The installation only configures the MySQL Database and the cron job for updating the MySQL Database

# Screenshot
![alt text](https://github.com/viperman1271/openvpn-web/blob/master/documentation/Screenshot.jpg "Screenshot")
Home page, depecting the number of connected users and the total upload and download for the day
