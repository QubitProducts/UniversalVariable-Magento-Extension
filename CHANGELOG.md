## Changelog

### 1.0.22 [:arrow_down: Download](https://github.com/QubitProducts/UniversalVariable-Magento-Extension/archive/1.0.22.zip)
* When shipping method is not provided, export as empty string instead of null. #39

### 1.0.21 [:arrow_down: Download](https://github.com/QubitProducts/UniversalVariable-Magento-Extension/archive/1.0.21.zip)
* Apply voucher discount on top of product unit_sale_price. #35


### 1.0.20 [:arrow_down: Download](https://github.com/QubitProducts/UniversalVariable-Magento-Extension/archive/1.0.20.zip)
* Added state variable in shipping and delivery address. If state/region is not available for certain countries, it will be exported as empty string to be used by tracking script.
* Refactoring and clean code, disable UV execution on admin page. (https://github.com/QubitProducts/UniversalVariable-Magento-Extension/pull/32)

### 1.0.18 [:arrow_down: Download](https://github.com/QubitProducts/UniversalVariable-Magento-Extension/archive/1.0.18.zip)
* Added `basket` on every page except confirmation page
* Added `breadcrumb` variables under `universal_variable.page.breadcrumb` whenenver this information is available
* DEPRECATION: We started deprecating `page.category`, moving to `page.type`. We current keep both to give times for frontend JavaScript migration. We recommand to use `page.type` to describe type of page from an ecommerce funnel perspective. Reference: http://tools.qubitproducts.com/uv/developers/specification/#toc_7
* DEPRECATION: `page.category` and `page.subcategory` will soon be deprecated in the next release

### 1.0.17 [:arrow_down: Download](https://github.com/QubitProducts/UniversalVariable-Magento-Extension/archive/1.0.17.zip)
* Fix tax number export in basket and transaction page 

### 1.0.16  [:arrow_down: Download](https://github.com/QubitProducts/UniversalVariable-Magento-Extension/archive/1.0.16.zip)
* Export guest email in transaction pages

### 1.0.15  [:arrow_down: Download](https://github.com/QubitProducts/UniversalVariable-Magento-Extension/archive/1.0.15.zip)
* Improve theme and frontend compatibility; Added modman to github source for ease of development.

### 1.0.14
* Corrected `line_item` quantity for non-transaction pages

### 1.0.13
* Added `voucher_discount` in transaction

### 1.0.11
* Deprecated `listing` variable on category pages, because it loops all the products without pagination; only output `listing.query` in search result that contains search queries.

### 1.0.10
* when voucher code is not used, use empty string intead of null

### 1.0.9
* output user_id and store language code correctly

### 1.0.8
* Rename page categroy from controller/view naming to correct page category inclueding home, checkout, basket, confirmation, product, category pages
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
