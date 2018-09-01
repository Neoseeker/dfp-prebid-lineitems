<?php

require (__DIR__."/../scriptLoader.php");

use App\Scripts\HeaderBiddingScript;

$entry = array(
	"namePrefix" => "Insideall - Prebid", // order and advertiser name prefix
	"ssp" => ['smartadserver'], // Needs to be bidder code defined in prebid documentation, ie appnexus, rubicon, improvedigital, smartadserver
	"priceGranularity" => "test", // can be 'low', 'med', 'high', 'auto','dense', 'test'
	"currency"=>"EUR",
	"sizes" => [[300, 250], [728, 90], [976, 91], [468, 60], [160, 600]]
);

if (file_exists(__DIR__."/../../Settings.php"))
{
	$entry = include(__DIR__."/../../Settings.php");
}

$script = new HeaderBiddingScript;

$script->createAdUnits($entry);


