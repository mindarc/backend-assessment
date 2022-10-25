
    mindarc/module-googlefeed

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)


## Main Functionalities
Mindarc Backend Assessment Module - Exercise 2

This module generates a google shopping feed from all available products in the store.
Format is from https://support.google.com/merchants/answer/160589?hl=en#

Note: shipping settings were omitted as this is better configured through here: https://support.google.com/merchants/answer/6069284

Update: Added controller that displays XML data as an array. Can be accessed via <magento_url>/googlefeed/feed

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Mindarc`
 - Enable the module by running `php bin/magento module:enable Mindarc_GoogleFeed`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration
 - Feed Filename'googlefeed/settings/feed_filename';
 - Feed Title 'googlefeed/settings/feed_title';
 - Feed Description'googlefeed/settings/feed_description';
## Specifications

 - Cronjob
	- mindarc_googlefeed_generatefeed



