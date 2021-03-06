# Default Apache virtualhost template

<VirtualHost *:443>
    ServerAdmin webmaster@localhost
    DocumentRoot {{ doc_root }}
{% set servernames = servername.split() %}
{% for servername in servernames %}
{% if loop.first %}
    ServerName {{ servername }}
{% else %}
    ServerAlias {{ servername }}
{% endif %}
{% endfor %}

    <IfModule mod_rewrite.c>
        RewriteEngine On
        RewriteRule ^ - [E=SYMFONY_ENV:dev]
    </IfModule>

    <Directory {{doc_root}}>
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>

    #ProxyPass /videostream http://(Your camera IP)/videostream.cgi
    #ProxyPassReverse /videostram http://(Your camera IP)/videostream.cgi

    <Location /videostream>
        #RequestHeader set Authorization "Basic (Your base64 encoded username:password)"
    </Location>

    SSLEngine on
    SSLCertificateFile    /etc/ssl/certs/ssl-cert-snakeoil.pem
    SSLCertificateKeyFile /etc/ssl/private/ssl-cert-snakeoil.key
    <FilesMatch "\.(cgi|shtml|phtml|php)$">
        SSLOptions +StdEnvVars
    </FilesMatch>
    BrowserMatch "MSIE [17-9]" ssl-unclean-shutdown
</VirtualHost>
