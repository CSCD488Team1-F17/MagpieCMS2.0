# MagpieCMS2.0
Team Members: James Smith, Blake Impecoven, Evan Gordon

Support Members: Ethan Tuning

Client: Ginelle Hustrulid

# Basic Environment Setup
1. Download and install XAMPP from https://www.apachefriends.org/index.html
2. Navigate to C:\xampp\apache\conf\extra and open httpd-vhosts.conf, replace its contents with:

    <VirtualHost *:80>
        DocumentRoot "your-repository-directory\public_html"
        ServerName home.dev
        <Directory "your-repository-directory\public_html">
            Allow from all
            Require all granted
            AllowOverride All
        </Directory>
    </VirtualHost>
    
3. Navigate to C:\Windows\System32\drivers\etc\hosts and add '127.0.0.1 home.dev' to the bottom.
4. start Apache through the XAMPP Control Panel then in a browser search home.dev (the MagpieHunt homepage should pop up)

If you need any help with these instructions or setting up PHPStorm with XDebug, contact James Smith