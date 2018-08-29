<?php

namespace App\Gam;

require(__DIR__."/../../vendor/autoload.php");

use Google\AdsApi\Common\OAuth2TokenBuilder;
use Google\AdsApi\AdManager\AdManagerServices;
use Google\AdsApi\AdManager\AdManagerSession;
use Google\AdsApi\AdManager\AdManagerSessionBuilder;
use Google\AdsApi\AdManager\v201808\Company;
use Google\AdsApi\AdManager\v201808\CompanyService;
use Google\AdsApi\AdManager\v201808\CompanyType;
use Google\AdsApi\AdManager\Util\v201808\StatementBuilder;

class CompanyManager extends GamManager
{
	
	public function setUpCompany($companyName)
	{	
		if(empty(($foo = $this->getCompany($companyName))))
		{
			$foo = $this->createCompany($companyName);
		}
		return $foo[0]['companyId'];
	}

	public function createCompany($companyName)
	{
		$output = [];

		$companyService = $this->gamServices->get($this->session, CompanyService::class);
		$company = new Company();
	    $company->setName($companyName);
	    $company->setType(CompanyType::ADVERTISER);
	    // Create the company on the server.
	    $data = $companyService->createCompanies([$company]);
	    // Print out some information for each created company.
	    foreach ($data as $i => $company) {
	        $foo = array(
	            "companyId"=>$company->getId(),
	            "companyName"=>$company->getName()
	        );
	        array_push($output, $foo);
	    }
	    return $output;
	}

	public function getAllCompanies()
	{
		$output = [];
		$companyService = $this->gamServices->get($this->session, CompanyService::class);
		$statementBuilder = (new StatementBuilder())->orderBy('id ASC');
		$data = $companyService->getCompaniesByStatement($statementBuilder->toStatement());
		if ($data->getResults() !== null)
		{
			foreach ($data->getResults() as $company) {
				$foo = array(
					"companyId"=>$company->getId(),
					"companyName"=>$company->getName()
				);
				array_push($output, $foo);
			}
		}
		return $output;
	}

	public function getCompany($companyName)
	{
		$output = [];
		$companyService = $this->gamServices->get($this->session, CompanyService::class);
		$statementBuilder = (new StatementBuilder())
			->orderBy('id ASC')
			->where('name = :name')
			->WithBindVariableValue('name', $companyName);
		$data = $companyService->getCompaniesByStatement($statementBuilder->toStatement());
		if ($data->getResults() !== null)
		{
			foreach ($data->getResults() as $company) {
				$foo = array(
					"companyId"=>$company->getId(),
					"companyName"=>$company->getName()
				);
				array_push($output, $foo);
			}
		}
		return $output;
	}
}