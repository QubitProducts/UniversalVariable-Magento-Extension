# Magento Extension for Universal Variable

QuBit's Universal Variable streamlines the process of passing values between OpenTag and your pages, whilst future-proofing against updates and changes. We recommend creating the relevant JavaScript object on your page prior to deploying OpenTag's container script. Doing this will assure that all values will be present on the page when the script runs and can be used by libraried OpenTag scripts. You only need to declare the object variables you use. For example, if your pages only have category and no subcategory, just declare the category. Likewise, if you feel the need to extend objects, or feel like renaming them, please do so. However, please take a note of the new variable names, as these are needed to access your scripts in your OpenTag container.

## Universal Variable Specification
Exported JavaScript object under `universal_variable` on all pages follows open standarded universal variable specification. The specificaiton is also available on GitHub:
[http://github.com/QuBitProducts/UniversalVariable](http://github.com/QuBitProducts/UniversalVariable)

## Installation
There are two ways of installing the extension. You can install the extesnion via Magento Connect or manually install the extension by copying files to Magento system directory. Installing from Magento Connect is strongly recommanded, which means you are able to receive notification when an update is available.

### Magento Connect

This is the recommanded way of installing the extenion. Get your extension key on [the extension page](http://www.magentocommerce.com/magento-connect/catalog/product/view/id/13932/s/qubit-universal-variable-9450/) and install the extension in your Magento Connect extension manager.



## License

The extension is released under Apache License 2.0

## Changelog

### 1.0.8
* Rename page categroy from controller/view naming to correct page category inclueding home, checkout, basket, confirmation, product, categroy pages
* Added `listing` variable for category pages 

### 1.0.7
* Added total_discount for a `line_item` in basket and transaction; it's useful to calculate average product cost when complicated sales discount rule applied.

### 1.0.6
* address get product details compatibility issue; output system version in universal variable for diagnosing. 

### 1.0.5
* verbosely check shipping and billing address, some systems does not have such value 

### 1.0.4
* handle more transaction pages provided by other checkout systems

### 1.0.3
* fixed issue with failed to load Data.php file

### 1.0.2
* ignore invisible products in basket and order
* calcuate the correct order prices for configurable products

### 1.0.1
* parse price into float data type

### 1.0.0
* inital release
* allow system output universal variables
* configure OpenTag script through configuration panel
