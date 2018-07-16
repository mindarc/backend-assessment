Introduction
---
Thanks for taking the time to complete this backend technical assessment. We will be focusing on code quality (reusability, readability, maintainability, etc.). You will be required to setup a Magento 2 Instance to test the modules and be flexible to work on any instance without conflicts (for code reusability)


*Sprint 1*

*Estimated Time: 3h each* 

Exercise 1 (Ticket ID: EX-01)
---
Build a module that allows you to handle GeoIP and show the country code on the product page. 
Build an update (as a feature branch) to the GeoIP module to show a static block on the product page depending on the country your visiting from. 
Build an update (as a second feature branch) If the country is Russia or China, block them from accessing the site

##### Requirements
1. Module has to utilize a GeoIP Database or External Source
2. Module must pull the IP from multiple server variables to ensure its capturing an IP past different layers
3. Module must render the country code on the product page
4. Module Update: If you are from the US, render the US static block, If you are from anywhere else, render the Global static block
5. Module Update: Determine if the country is Russia or China and have them redirect to the error page of Magento

Exercise 2 (Ticket ID: EX-02)
---
Build a module that allows the system to generate a Google Feed (https://support.google.com/merchants/answer/160589?hl=en#) every 24 hours via CRON
Build an update (as a feature branch) to the feed that allows you to execute manually and view the results of the feed through a controller in Magento
Build an update (as a second feature branch) that converts the price on the go to USD
Create a pull request for both your branches to be released to a release branch

##### Requirements
1. Using the specification provided, generate a XML file that iterates through products in the system to populate the data for each node
2. The Feed should automatically generate every 24 hours using Magento standard CRON's
3. Module Update: Your module will require a controller that can be executed manually to view the results of the feed.
4. Module Update 2: Use fixer.io to do a curl out to retrieve the current price converted into USD and render as a new node "g:converted_price"

###### Bonus points
* The use of the best practice coding for Magento 2
* Code readability and structure
* Use of controllers/plugins/observers
* Code optimization 

###### Notes
* Feature branch is a new feature based on the master branch, ensure that the format follows TICKET_ID-description-of-task (EX-00-example-task)
* The release branch is created at the end for work to be released to production, its the last check before production release, ensure that the format follows YYYY-M-SPRINT_NO (2018-JUL-2)

###### Submission
* If you are to perform this task, you can fork this onto your Github account and work from there and send back your forked Github repository to us afterwards for assessment. This will allow us to see your branching structure and obtain the code for installation and review. We will provide the structure within our repository to get you started as if we were in the base of Magento directory and you are making updates to an "existing site"
