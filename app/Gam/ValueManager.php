<?php

namespace App\Gam;

require(__DIR__."/../../vendor/autoload.php");

use Google\AdsApi\Common\OAuth2TokenBuilder;
use Google\AdsApi\AdManager\AdManagerServices;
use Google\AdsApi\AdManager\AdManagerSession;
use Google\AdsApi\AdManager\AdManagerSessionBuilder;
use Google\AdsApi\AdManager\v201808\CustomTargetingKey;
use Google\AdsApi\AdManager\v201808\CustomTargetingKeyType;
use Google\AdsApi\AdManager\v201808\CustomTargetingService;
use Google\AdsApi\AdManager\v201808\CustomTargetingValue;
use Google\AdsApi\AdManager\v201808\CustomTargetingValueMatchType;
use Google\AdsApi\AdManager\Util\v201808\StatementBuilder;

class ValueManager extends GamManager
{

	protected $keyId;
	protected $existingGAMValues;

	public function setKeyId($keyId)
	{
		$this->keyId = $keyId;
		return $this;
	}

	public function getExistingValues()
	{
		return $this->existingValues;
	}

	public function convertValuesListToGAMValuesList($valuesList)
	{
		//We get from GAM which keys already exists
		$output = $this->getExistingValuesFromGAM();

		//We create a table with only existing keys
		$existingValuesList = [];
		
		foreach ($output as $foo) {
			array_push($existingValuesList, $foo['valueName']);
		}

		//We create a list with values to be created
		$valuesToBeCreated = [];
		foreach ($valuesList as $element) {
			if(!in_array($element, $existingValuesList))
			{
				array_push($valuesToBeCreated, $element);
			}
		}
		if(!empty($valuesToBeCreated))
		{
			$foo = $this->createCustomTargetingValues($valuesToBeCreated);
			foreach($foo as $bar)
			{
				array_push($output, $bar);
			}
		}
		return $output;
	}

	public function createCustomTargetingValues($valuesToBeCreated)
	{
		if(!is_array($valuesToBeCreated))
		{
			echo "The input needs to be an array";
			exit;
		}

		$customTargetingService = $this->gamServices->get($this->session, CustomTargetingService::class);
		$output = [];
		$values = [];
		foreach ($valuesToBeCreated as $value)
		{	
			$foo= new CustomTargetingValue();
	        $foo->setCustomTargetingKeyId($this->keyId);
	        $foo->setDisplayName($value);
	        $foo->setName($value);
	        $foo->setMatchType(CustomTargetingValueMatchType::EXACT);
    		array_push($values,$foo);
    	}
    	$values = $customTargetingService->createCustomTargetingValues($values);
    	foreach ($values as $value) {
            $foo = array(
                "valueId" => $value->getId(),
                "valueName"=>$value->getName(),
                "valueDisplayName"=>$value->getDisplayName()
            );
            printf(
            	"A custom targeting value with ID %d, belonging to key with ID %d, "
                . "name '%s', and display name '%s' was created.\n",
                $value->getId(),
                $value->getCustomTargetingKeyId(),
                $value->getName(),
                $value->getDisplayName()
            );
            array_push($output, $foo);
        }
        return $output;
	}


	public function getExistingValuesFromGAM()
	{	
		$output = [];
		$pageSize = StatementBuilder::SUGGESTED_PAGE_LIMIT;
		$customTargetingService = $this->gamServices->get($this->session, CustomTargetingService::class);
		$statementBuilder = (new StatementBuilder())->where('customTargetingKeyId = :customTargetingKeyId')
            ->orderBy('id ASC')
            ->limit($pageSize);
      	$statementBuilder->withBindVariableValue(
            'customTargetingKeyId',
            $this->keyId
        );
        $totalResultSetSize = 0;
        do {
			$data = $customTargetingService->getCustomTargetingValuesByStatement($statementBuilder->toStatement());
			if ($data->getResults() !== null)
			{
				foreach ($data->getResults() as $value) {
				    $foo = array(
				  		"valueId" => $value->getId(),
				        "valueName" => $value->getName(),
				        "valueDisplayName" => $value->getDisplayName()
				    );
				    array_push($output, $foo);
				    $statementBuilder->increaseOffsetBy($pageSize);
				}
			}
		} while ($statementBuilder->getOffset() < $totalResultSetSize);
		return $output;
	}

}