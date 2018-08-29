<?php

namespace App\Gam;

require(__DIR__."/../../vendor/autoload.php");

use Google\AdsApi\Common\OAuth2TokenBuilder;
use Google\AdsApi\AdManager\AdManagerServices;
use Google\AdsApi\AdManager\AdManagerSession;
use Google\AdsApi\AdManager\AdManagerSessionBuilder;
use Google\AdsApi\AdManager\v201808\NetworkService;

class NetworkManager extends GamManager
{
	
	protected $gamServices;
	protected $session;

	public function getCurrentNetwork()
	{
		$networkService  = $this->gamServices->get($this->session, NetworkService::class);

		$network = $networkService->getCurrentNetwork();
		
		$output = array(
	  		"networkCode" => $network->getNetworkCode(),
	        "networkName" => $network->getDisplayName()
	    );
	
		return $output;
	}

	public function makeTestNetwork()
	{
		$networkService  = $this->gamServices->get($this->session, NetworkService::class);
		$network = $networkService->makeTestNetwork();

		printf(
            "Test network with network code '%s' and display name '%s' created.\n"
            . 'You may now sign in at' . " https://www.google.com/dfp/main?networkCode=%s\n",
            $network->getNetworkCode(),
            $network->getDisplayName(),
            $network->getNetworkCode()
        );

	}

}


