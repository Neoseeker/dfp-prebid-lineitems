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
				"orderName" => (isset($params['namePrefix']) ? $params['namePrefix']." - ".ucfirst($ssp) : "Insideall - Prebid")." - ".ucfirst($ssp),
				"advertiserName" => (isset($params['namePrefix']) ? $params['namePrefix']." - ".ucfirst($ssp) : "Insideall - Prebid")." - ".ucfirst($ssp),
				"priceGranularity" => $params["priceGranularity"],
				"sizes" =>$params["sizes"],
				"priceKeyName"=>substr("hb_pb_$ssp",0,20),
				"adidKeyName"=>substr("hb_adid_$ssp",0,20),
				"sizeKeyName"=>substr("hb_size_$ssp",0,20),
				"currency"=>$params['currency'],
				"updateLineItem"=>(isset($params['updateLineItem']) ? $params['updateLineItem'] : true),
				"updateLica"=>(isset($params['updateLica']) ? $params['updateLica'] : true),
				"namePrefix"=>(isset($params['namePrefix']) ? $params['namePrefix'] : ""),
				"isSafeFrameCompatible"=>(isset($params['isSafeFrameCompatible']) ? $params['isSafeFrameCompatible'] : false),
				"snippet"=>(isset($params['snippet']) ? $params['snippet'] : ""),
				"keyValues"=>(isset($params['keyValues']) ? $params['keyValues'] : []),
				"ssp"=>$ssp
			);
			$script = new SSPScript($param);

			$script->createAdUnits();
		}
	}

	static function createGlobalAdunits($params)
	{
		$params = array(
			"orderName" => (isset($params['namePrefix']) ? $params['namePrefix'] : "Insideall - Prebid"),
			"advertiserName" => (isset($params['namePrefix']) ? $params['namePrefix'] : "Insideall - Prebid"),
			"priceGranularity" => $params["priceGranularity"],
			"sizes" =>$params["sizes"],
			"priceKeyName"=>substr("hb_pb",0,20),
			"adidKeyName"=>substr("hb_adid",0,20),
			"sizeKeyName"=>substr("hb_size",0,20),
			"currency"=>$params['currency'],
			"updateLineItem"=>(isset($params['updateLineItem']) ? $params['updateLineItem'] : true),
			"updateLica"=>(isset($params['updateLica']) ? $params['updateLica'] : true),
			"namePrefix"=>(isset($params['namePrefix']) ? $params['namePrefix'] : ""),
			"isSafeFrameCompatible"=>(isset($params['isSafeFrameCompatible']) ? $params['isSafeFrameCompatible'] : false),
			"snippet"=>(isset($params['snippet']) ? $params['snippet'] : ""),
			"keyValues"=>(isset($params['keyValues']) ? $params['keyValues'] : []),
			"ssp"=>""
		);
		$script = new SSPScript($params);

		$script->createAdUnits();

	}
}