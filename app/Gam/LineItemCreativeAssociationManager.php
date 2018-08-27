<?php

namespace App\Gam;

require(__DIR__."/../../vendor/autoload.php");

use Google\AdsApi\Common\OAuth2TokenBuilder;
use Google\AdsApi\AdManager\AdManagerServices;
use Google\AdsApi\AdManager\AdManagerSession;
use Google\AdsApi\AdManager\AdManagerSessionBuilder;
use Google\AdsApi\AdManager\v201808\LineItemCreativeAssociation;
use Google\AdsApi\AdManager\v201808\LineItemCreativeAssociationService;
use Google\AdsApi\AdManager\v201808\Size;
use Google\AdsApi\AdManager\Util\v201808\StatementBuilder;


class LineItemCreativeAssociationManager extends GamManager
{
	protected $lineItem;
	protected $creativeList;
	protected $sizeOverrides;

	public function setLineItem($lineItem)
	{
		$this->lineItem = $lineItem;
		return $this;
	}

	public function setCreativeList($creativeList)
	{
		$this->creativeList = $creativeList;
		return $this;
	}

	public function setSizeOverride($sizes)
	{
		$this->sizeOverrides = $sizes;
		return $this;
	}

	public function setUpLica()
	{
        $licasToBeCreated = [];
        $licasToBeUpdated = [];
        //We first get all Licas per Line Items
        $existingLicas = $this->GetLicasForLineItem();
        foreach($this->creativeList as $creative)
        {
            if(in_array($creative['creativeId'], $existingLicas))
            {
                array_push($licasToBeUpdated,$creative['creativeId']);
            }
            else
            {
                array_push($licasToBeCreated, $creative['creativeId']);
            }
        }
        if(!empty($licasToBeUpdated)){ $this->UpdateLicas($licasToBeUpdated);}
        if(!empty($licasToBeCreated)){ $this->CreateLicas($licasToBeCreated);}
	}

    private function UpdateLicas($licasToBeUpdated)
    {
        $licaService = $this->gamServices->get($this->session, LineItemCreativeAssociationService::class);
        $results = $licaService->updateLineItemCreativeAssociations($this->createLicaObject($licasToBeUpdated));
        /*
        foreach ($results as $i => $lica) {
            printf(
                "%d) LICA with line item ID %d, creative ID %d, and status '%s' was "
                . "updated.\n",
                $i,
                $lica->getLineItemId(),
                $lica->getCreativeId(),
                $lica->getStatus()
            );
        }
        */
    }

    private function CreateLicas($licasToBeCreated)
    {
        $licaService = $this->gamServices->get($this->session, LineItemCreativeAssociationService::class);
        $results = $licaService->createLineItemCreativeAssociations($this->createLicaObject($licasToBeCreated));
        /*
        foreach ($results as $i => $lica) {
            printf(
                "%d) LICA with line item ID %d, creative ID %d, and status '%s' was "
                . "created.\n",
                $i,
                $lica->getLineItemId(),
                $lica->getCreativeId(),
                $lica->getStatus()
            );
        }
        */
        
    }

	private function GetLicasForLineItem()
	{
		$output = [];
        $licaService = $this->gamServices->get($this->session, LineItemCreativeAssociationService::class);
		$pageSize = StatementBuilder::SUGGESTED_PAGE_LIMIT;
		$statementBuilder = (new StatementBuilder())->where('lineItemId = :lineItemId')
            ->orderBy('lineItemId ASC, creativeId ASC')
            ->limit($pageSize)
            ->withBindVariableValue('lineItemId', $this->lineItem['lineItemId']);
        $totalResultSetSize = 0;
        do {
            $page = $licaService->getLineItemCreativeAssociationsByStatement(
                $statementBuilder->toStatement()
            );
            // Print out some information for each line item creative association.
            if ($page->getResults() !== null) {
                $totalResultSetSize = $page->getTotalResultSetSize();
                $i = $page->getStartIndex();
                foreach ($page->getResults() as $lica) {
                    array_push($output, $lica->getCreativeId());
                }     
            }
            $statementBuilder->increaseOffsetBy($pageSize);
        } while ($statementBuilder->getOffset() < $totalResultSetSize);
        return $output;
	}

	private function createLicaObject($creativeList)
	{
		$output = [];
		foreach($creativeList as $creative)
		{
			$lica = new LineItemCreativeAssociation();
        	$lica->setCreativeId($creative)
        		->setLineItemId($this->lineItem['lineItemId'])
        		->setSizes($this->setSizes());
        	array_push($output, $lica);
		}
		return $output;
	}


	private function setSizes()
    {
        $output = []; 
        foreach ($this->sizeOverrides as $element) {
            $size = new Size();
            $size->setWidth($element[0]);
            $size->setHeight($element[1]);
            $size->setIsAspectRatio(false);

            
            array_push($output, $size);
        }
        return $output;
    } 
}