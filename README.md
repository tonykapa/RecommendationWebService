# Recommendation Web Service Module for Prestashop 1.7+

This module is calling a recommendation web service which returns Product ID's from items on your shop based on the user's purchase history using user id in your website.

The product id's are used to retrieve the products details from the database and show them in the homepage of the site in a carousel.

When a purchase is completed, a GET request is sent to the Web Service with the details of the purchase.

Module is using Native Presenter curl for transfering data. 

Also you can hook this module anywhere because it uses the widget technology.

## Installation

Just like any other Prestashop Module. After installation go to Positions in admin panel and hook the module where you want it!
