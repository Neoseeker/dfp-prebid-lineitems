<?php

require (__DIR__."/../scriptLoader.php");

use App\Scripts\HeaderBiddingScript;

$entry = array(
	"namePrefix" => "Insideall - Prebid", // order and advertiser name prefix
	"priceGranularity" => "low", // can be 'low', 'med', 'high', 'auto','dense', 'test'
	"currency"=>"EUR",
	"sizes" => [[300, 250], [728, 90], [976, 91], [468, 60]]
);

if (file_exists(__DIR__."/../../Settings.php"))
{
	$entry = include('Settings.php');
}

$script = new HeaderBiddingScript;

$script->createGlobalAdUnits($entry);


