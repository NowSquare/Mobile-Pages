# Mobile content builder for QR codes

## Requirements
You need a domain or subdomain. The script does not work in a subdirectory (e.g. localhost/mobile-pages or example.com/mobile-pages).

 - Apache web server
   - PHP 7.1.x / 7.3.x
   - SQLite PHP Extension
   - BCMath PHP Extension
   - Ctype PHP Extension
   - JSON PHP Extension
   - Mbstring PHP Extension
   - OpenSSL PHP Extension
   - PDO PHP Extension
   - Tokenizer PHP Extension
   - XML PHP Extension

## Install
Upload all files in the `public` directory to your webroot. Open the url where you uploaded the script to in your browser and you will see an installation form.

## Troubleshooting
If you get a 500 error after installaton, you can re-install by deleting the `.env` file in the webroot. The installation page will now show again.