<?php

namespace App\Gam;

require(__DIR__."/../../vendor/autoload.php");

use Google\AdsApi\Common\OAuth2TokenBuilder;
use Google\AdsApi\AdManager\AdManagerServices;
use Google\AdsApi\AdManager\AdManagerSession;
use Google\AdsApi\AdManager\AdManagerSessionBuilder;
use Google\AdsApi\AdManager\v201808\Order;
use Google\AdsApi\AdManager\v201808\OrderService;
use Google\AdsApi\AdManager\v201808\Company;
use Google\AdsApi\AdManager\v201808\CompanyService;
use Google\AdsApi\AdManager\v201808\CompanyType;

class  GamManager
{
	protected $gamServices;
	protected $session;
	
	public function __construct()
	{
		$oAuth2Credential = (new OAuth2TokenBuilder())
			->fromFile()
			->build();
		        
		$this->session = (new AdManagerSessionBuilder())
			->fromFile()
			->withOAuth2Credential($oAuth2Credential)
			->build();

		$this->gamServices = new AdManagerServices();
	}

	public function getGamServices()
	{
		return $this->gamServices;
	}

	public function getSession()
	{
		return $this->session;
	}

}