For working of GeoIP module the file 'database.tar.gz' must be unzip under var/ folder of the Magento project
and install GeoIP2 PHP API run in your project root:
       
        php composer.phar require geoip2/geoip2:~2.0
        
        
For testing the functionality, you can simulate an IP in Store->Configuration->general->GeoIP.
  1. Enable simulation mode
  2. Set an IP. For instance: US IP, 72.229.28.185
  3. Clear config cache