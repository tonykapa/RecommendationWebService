# Web Service Module for Prestashop 1.7+

This module is calling a web service which returns Product ID's from items on your shop based on the user's purchase history using user id in your website.

The product id's are used to retrieve the products details from the database and show them in the homepage of the site in a carousel.

When a purchase is completed, a POST request is sent to the Web Service with the details of the purchase.

Module is using PHP,Symfony,Smarty,glider.js(you must include it and initialize to your host yourself) and curl for transfering data.

## Installation

Just like any other Prestashop Module.

## License
Copyright © 2022 

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the “Software”), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
