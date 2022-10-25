   
      mindarc/module-geoip

   - [Main Functionalities](#markdown-header-main-functionalities)
   - [Installation](#markdown-header-installation)
   - [Configuration](#markdown-header-configuration)
   - [Specifications](#markdown-header-specifications)
   - [Attributes](#markdown-header-attributes)


   ## Main Functionalities
   Mindarc Backend Assessment Module

   This module determines user location based on Geo IP from https://ip-api.com/

   Setup scripts automatically create the US and Global static blocks.
   Admin configuration for IP Override has been added for easier testing purposes.
   Static block and country code is displayed in the product-info-main on the Product Page.


   ## Installation
   \* = in production please use the `--keep-generated` option

   - Unzip the zip file in `app/code/Mindarc`
   - Enable the module by running `php bin/magento module:enable Mindarc_GeoIp`
   - Apply database updates by running `php bin/magento setup:upgrade`\*
   - Flush the cache by running `php bin/magento cache:flush`

   ## Configuration

   - API URL (geoip/settings/api_url)
   - IP Override (geoip/settings/ip_override)

