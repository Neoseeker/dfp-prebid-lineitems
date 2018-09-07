<?php

namespace App\Scripts;

class HeaderBiddingScript
{
	protected $traffikerId;
	protected $advertiserId;
	protected $orderId;
	protected $keyId;


	static function createAdUnits($params)
	{
		foreach($params['ssp'] as $ssp)
		{
			$param = array(
				"orderName" => $params['namePrefix']." - ".ucfirst($ssp),
				"advertiserName" => $params['namePrefix']." - ".ucfirst($ssp),
				"priceGranularity" => $params["priceGranularity"],
				"sizes" =>$params["sizes"],
				"priceKeyName"=>substr("hb_pb_$ssp",0,20),
				"adidKeyName"=>substr("hb_adid_$ssp",0,20),
				"sizeKeyName"=>substr("hb_size_$ssp",0,20),
				"currency"=>$params['currency'],
				"updateLineItem"=>$params['updateLineItem'],
				"updateLica"=>$params['updateLica'],
				"ssp"=>$ssp
			);
			$script = new SSPScript($param);

			$script->createAdUnits();
		}
	}

	static function createGlobalAdunits($params)
	{
		$params = array(
			"orderName" => $params['namePrefix'],
			"advertiserName" => $params['namePrefix'],
			"priceGranularity" => $params["priceGranularity"],
			"sizes" =>$params["sizes"],
			"priceKeyName"=>substr("hb_pb",0,20),
			"adidKeyName"=>substr("hb_adid",0,20),
			"sizeKeyName"=>substr("hb_size",0,20),
			"currency"=>$params['currency'],
			"updateLineItem"=>$params['updateLineItem'],
			"updateLica"=>$params['updateLica'],
			"ssp"=>""
		);
		$script = new SSPScript($params);

		$script->createAdUnits();

	}
}