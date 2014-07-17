# Magento Extension for Universal Variable

Qubit's Universal Variable streamlines the process of passing values between OpenTag and your pages, whilst future-proofing against updates and changes. We recommend creating the relevant JavaScript object on your page prior to deploying OpenTag's container script. Doing this will assure that all values will be present on the page when the script runs and can be used by libraried OpenTag scripts. You only need to declare the object variables you use. For example, if your pages only have category and no subcategory, just declare the category. Likewise, if you feel the need to extend objects, or feel like renaming them, please do so. However, please take a note of the new variable names, as these are needed to access your scripts in your OpenTag container.

## Useful links

 * [What's Universal Variable?](http://tools.qubitproducts.com/uv/developers/) 
 * [Universal Variable is Opentag’s W3C approved data model](http://www.qubitproducts.com/tag-management/data-model)
 * [W3C digital data toolkit](http://www.w3cdigitaldatatoolkit.com/)
 

## Universal Variable Specification
Exported JavaScript object under `universal_variable` on all pages follows open standarded universal variable specification. The specificaiton is also available on GitHub:
[http://github.com/QuBitProducts/UniversalVariable](http://github.com/QuBitProducts/UniversalVariable)

## Supported Magento Versions

We've tested the followed versions. Please submit Github Issues with detailed description if you find any bugs.

 * 1.6.x, 1.7.x, 1.8.x CE
 * 1.6.x, 1.7.x, 1.8.x Enterprise

## Installation

### Magento Connect

This is the recommanded way of installing the extenion. Get your extension key on [the extension page](http://www.magentocommerce.com/magento-connect/catalog/product/view/id/13932/s/qubit-universal-variable-9450/) and install the extension in your Magento Connect extension manager.

## Development

 * [Changelog and Download Previous Versions](https://github.com/QubitProducts/UniversalVariable-Magento-Extension/blob/master/CHANGELOG.md)

## Happy Contributors

Thank you to the contributors improving our code base:

* Robert Coleman [@rjocoleman](https://github.com/rjocoleman)
* beeplogic [@rgranadino](https://github.com/rgranadino)
* Rudger [@Rud5G](https://github.com/Rud5G)
* rogy [@rogy](https://github.com/rogy)

## License

The extension is released under Apache License 2.0
