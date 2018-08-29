<?php

namespace App\Gam;

require(__DIR__."/../../vendor/autoload.php");

use DateTime;
use DateTimeZone;
use Google\AdsApi\Common\OAuth2TokenBuilder;
use Google\AdsApi\AdManager\AdManagerServices;
use Google\AdsApi\AdManager\AdManagerSession;
use Google\AdsApi\AdManager\AdManagerSessionBuilder;

use Google\AdsApi\AdManager\v201808\NetworkService;

class RootAdUnitManager extends GamManager
{
	public function setRootAdUnit()
	{
		$networkService = $this->gamServices->get($this->session, NetworkService::class);
        $rootAdUnitId = $networkService->getCurrentNetwork()
            ->getEffectiveRootAdUnitId();
        return $rootAdUnitId; 
	}
}