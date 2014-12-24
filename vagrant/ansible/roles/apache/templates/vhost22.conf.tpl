# Default Apache virtualhost template

<VirtualHost *:80>
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
</VirtualHost>
