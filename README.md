# MagpieCMS2.0
Team Members: James Smith, Blake Impecoven, Evan Gordon

Support Members: Ethan Tuning

Client: Ginelle Hustrulid

# Basic Environment Setup
1. Download and install XAMPP from https://www.apachefriends.org/index.html
2. Change directory to:
Windows: C:\xampp\apache\conf\extra and open httpd-vhosts.conf
Linux: /opt/lampp/etc/httpd.conf 
        open that file in the text editor of your choice
        follow the instructions: https://ourcodeworld.com/articles/read/302/how-to-setup-a-virtual-host-locally-with-xampp-in-ubuntu
        TLDR: uncomment this line: #Include etc/extra/httpd-vhosts.conf
        edit: /opt/lampp/etc/extra/httpd-vhosts.conf
        

3.Replace its contents with:

    <VirtualHost *:80>
        DocumentRoot "your-repository-directory\public_html"
        ServerName home.dev
        <Directory "your-repository-directory\public_html">
            Allow from all
            Require all granted
            AllowOverride All
        </Directory>
    </VirtualHost>
    
4. Navigate to C:\Windows\System32\drivers\etc\hosts and add '127.0.0.1 home.dev' to the bottom.
5. start Apache through the XAMPP Control Panel then in a browser search home.dev (the MagpieHunt homepage should pop up)

If you need any help with these instructions or setting up PHPStorm with XDebug, contact James Smith
