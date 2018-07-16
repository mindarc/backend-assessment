Introduction
---
Thanks for taking the time to complete this backend technical assessment. We will be focusing on code quality (reusability, readability, maintainability, etc.). You will be required to setup a Magento 2 Instance to test the modules and be flexible to work on any instance without conflicts (for code reusability)

*Estimated Time: 3h each* 

Exercise 1
---
Build a module that allows you to handle GeoIP and show the country code on the product page

##### Requirements
1. Module has to utilize a GeoIP Database or External Source
2. Module must handle SSL Termination and IP Masks to pull the IP from multiple server variables
3. Module must render the country code on the product page

Exercise 2
---
Build a module that links into Fixer.io and extract the converted currency for Magento on the fly 

##### Requirements
1. Utilize rest API of fixer.io to send current value for product
2. Extract json payload from controller and render it on page
3. Must remove the current price with the converted price

###### Bonus points
* The use of the best practice coding for Magento 2
* Code readability and structure
* Use of controllers/plugins/observers
* Code optimization
