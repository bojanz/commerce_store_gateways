Commerce Store Gateways
=======================

Allows stores to have their own payment gateway configuration.

The gateway configuration is stored in a store field, and applied to the payment gateway config entity
using Drupal's configuration override system.

Setup:

0) Install the module.

1) Create your payment gateways at admin/commerce/config/payment-gateways.

2) Edit your store type (admin/commerce/config/store-types/online/edit)
   and enable "Allow each store to have its own payment gateway configuration".

Done! Now visit the "Payment gateways" tab next to the store edit form.

Requires Commerce 2.x-dev (newer than RC1).
Required Commerce issues:
https://www.drupal.org/node/2904148 (for general functioning)
https://www.drupal.org/node/2904417 (for fully supporting off-site gateways).

Todo:
1) Implement the Payment Gateways tab.

2) Hide the Payment Gateways tab if !$store->hasField('payment_gateways')

3) Add a checkbox to the admin Payment Gateway form that when checked hides the configuration.
   For example: "Allow this payment gateway to be overriden by each store."
   This way the admin can create unconfigured payment gateways, without needing to enter
   valid credentials.

4) Allow stores to enforce a single gateway per store, probably with an IEF-like widget
   on the store form instead of the Payment Gateways tab.