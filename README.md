# GAM (previously DFP) Prebid LineItems Setup Tool
Automatically setup and update your Line Items on Google Ad Manager (previously DFP) for [Prebid.js](http://prebid.org/)

# GAM Setup Tool for Prebid
An automated GAM line item generator for [Prebid.js](http://prebid.org/)

## Overview
When setting up Prebid, your ad ops team often has to create [hundreds of line items](http://prebid.org/adops.html) in GAM.

This tool automates setup for new header bidding partners. You define the advertiser, placements, and Prebid settings; then, it creates an order with one line item per price level, attaches creatives, and sets placement and Prebid key-value targeting.

While this tool covers typical use cases, it might not fit your needs. Check out the [limitations](#limitations) before you dive in.

_Note: Doubleclick for Publishers (DFP) was recently renamed to Google Ad Manager (GAM), so this repository may refer to GAM as DFP._

## Getting Started

### Creating Google Credentials
You will need credentials to access your GAM account programmatically. This summarizes steps from [GAM docs](https://developers.google.com/ad-manager/docs/authentication) and the GAM PHP library [auth guide](https://github.com/googleads/googleads-php-lib#setting-up-your-oauth2-credentials).
1. If you haven't yet, sign up for a [GAM account](https://admanager.google.com/).
2. Create Google developer credentials
   * Go to the [Google Developers Console Credentials page](https://console.developers.google.com/apis/credentials).
   * On the **Credentials** page, select **Create credentials**, then select **Service account key**.
   * Select **New service account**, and select JSON key type. You can leave the role blank.
   * Click **Create** to download a file containing a `.json` private key.
3. Enable API access to GAM
   * Sign into your [GAM account](https://admanager.google.com/). You must have admin rights.
   * In the **Admin** section, select **Global settings**
   * Ensure that **API access** is enabled.
   * Click the **Add a service account user** button.
     * Use the service account email for the Google developer credentials you created above.
     * Set the role to "Trafficker".
     * Click **Save**.

### Setting Up
1. Clone this repository.
2. Include the library via Composer:
`$ composer require googleads/googleads-php-lib
3. Rename key
   * Rename the Google credentials key you previously downloaded (`[something].json`) to `googleServiceAccount.json` and move it to the project root folder
4. Make a copy of [adsapi_php.ini](https://github.com/googleads/googleads-php-lib/blob/master/examples/AdManager/adsapi_php.ini) and save it into the project root folder.
5. In `adsapi_php.ini`, set the required fields:
   * `application_name` is the name of the application you used to get your Google developer credentials
   * `network_code` is your GAM network number; e.g., for `https://admanager.google.com/12398712#delivery`, the network code is `12398712`.
   * `jsonKeyFilePath`is the path to your JSON key file
   * `scopes` is "https://www.googleapis.com/auth/dfp"
   * `impersonatedEmail` is the email account of the user you want to impersonate as, if any (something like user@app.iam.gserviceaccount.com)

### Verifying Setup
Let's try it out! From the top level directory, run

`php script/tests/ConnexionTest.php`

and you should whether the connexion is OK or not

## Creating Line Items

Modify the settings in 
`/script/hb/HeaderBiddingCreation.php`, or create `/Settings.php` to config the settings
    
    <?php
    return array(
        "namePrefix" => "Insideall - Prebid", // order and advertiser name prefix
        "updateLineItem" => true, // Update existing Line Items, true or false
        "updateLica" => true, // Update existing Licas per Line Items, true or false
        "ssp" => ['smartadserver'], // Needs to be bidder code defined in prebid documentation, ie appnexus, rubicon, improvedigital, smartadserver
        "priceGranularity" => "test", // can be 'low', 'med', 'high', 'auto','dense', 'test'
        "currency"=>"EUR",
        "sizes" => [[300, 250], [728, 90], [976, 91], [468, 60], [160, 600]],
	    "isSafeFrameCompatible"=>false, // set SafeFrame compatible for creative
	    "snippet"=>"", // set Code snippet for creative
        "keyValues"=>[
            ["key"=>"hb_bidder", "operator"=>"is", "value"=>"appnexus"]
        ], // Set custom key-values
    );
    ?>
    
* SSP must be an array of ssp you want to create - please enter here the bidder code defined in prebid documentation
* Price Granularity are standards, defined on [prebid.org](http://prebid.org/prebid-mobile/adops-price-granularity.html). You can also define a custom granularity by passing an array of buckets in the following format 
    
    ```
        'priceGranularity' => [ 
            'buckets' => [
                ['min' => 0, 'max' => 5, 'increment' => 0.05, 'precision' => 2 /* optional */],
                ['min' => 5, 'max' => 10, 'increment' => 0.1, 'precision' => 2 /* optional */],
                ['min' => 10, 'max' => 20, 'increment' => 0.5, 'precision' => 2 /* optional */],
            ]
        ]
    ```
* Currency is the AdServer Currency (USD, EUR...)
* Sizes: please enter all sizes allowed on your inventory 


Then, from the root of the repository, run:

`php script/hb/HeaderBiddingCreation.php`

You should be all set! Review your order, line items, and creatives to make sure they are correct. Then, approve the order in GAM.

*Note:  an exception can appears during the line items creation process - Restart HeaderBiddingCreation.php with the same setup*

*Note: GAM might show a "Needs creatives" warning on the order for ~15 minutes after order creation. Typically, the warning is incorrect and will disappear on its own.*

## Limitations
* This tool does not support additional line item targeting beyond placement, hb_bidder, and hb_pb values. Placement targeting is currently required, and targeting by ad unit isn't supported
* This tool does not modify existing orders or line items, it only creates them. If you need to make a change to an order, it's easiest to archive the existing order and recreate it. However, once orders are created, you can easily update them (change Price Granularity, change Available Sizes)
