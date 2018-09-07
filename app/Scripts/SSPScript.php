<?php

namespace App\Scripts;

class SSPScript extends \App\Gam\GamManager
{	
	const MAX_LINE_ITEMS_PER_ORDER = 450;

	protected $orderName;
	protected $advertiserName;
	protected $priceGranularity;
	protected $sizes;
	protected $priceKeyName;
	protected $adidKeyName;
	protected $sizeKeyName;
	protected $ssp;
	protected $currency;
	protected $updateLineItem;

	//
	protected $traffickerId;
	protected $advertiserId;
	protected $orderId;
	protected $priceKeyId;
	protected $adidKeyId;
	protected $sizeKeyId;
	protected $valuesList;
	protected $gamValuesList;
	protected $creativesList;
	protected $rootAdUnitId;

	public function __construct($params)
	{
		foreach ($params as $key => $value) {
			$this->$key = $value;
		}
	}

	public function createAdUnits()
	{
		$allValuesList = Buckets::createBuckets($this->priceGranularity);

		$order_num = floor(sizeof($allValuesList) / self::MAX_LINE_ITEMS_PER_ORDER) + 1;

		$advertiserNameBase = $this->advertiserName;
		$orderNameBase = $this->orderName;

		for ($i = 0; $i < $order_num; $i++) {
			$this->valuesList = array_slice($allValuesList, $i * self::MAX_LINE_ITEMS_PER_ORDER, self::MAX_LINE_ITEMS_PER_ORDER);
			
			if ($order_num > 1) {
				$this->advertiserName = $advertiserNameBase." - ".($i+1);
				$this->orderName = $advertiserNameBase." - ".($i+1);
			}
			
			$this->setupAdUnits();
		}
	}
	
	private function setupAdUnits()
	{
		//Get the Trafficker Id
		$this->traffickerId  = (new \App\Gam\UserManager)->getUserId();
		echo "TraffickerId: ".$this->traffickerId."\n";

		

		//Get the Advertising Company Id
		$this->advertiserId = (new \App\Gam\CompanyManager)->setUpCompany($this->advertiserName);
		echo "AdvertiserName : ".$this->advertiserName."\tAdvertiserId: ".$this->advertiserId."\n";

		//Get the OrderId
		$this->orderId = (new \App\Gam\OrderManager)->setUpOrder($this->orderName, $this->advertiserId, $this->traffickerId);
		echo "OrderName : ".$this->orderName."\tOrderId: ".$this->orderId."\n";

		//Create and get KeyIds 
		$this->priceKeyId = (new \App\Gam\KeyManager)->setUpCustomTargetingKey($this->priceKeyName);
		echo "PriceKeyName : ".$this->priceKeyName."\tPriceKeyId: ".$this->priceKeyId."\n";
		$this->adidKeyId = (new \App\Gam\KeyManager)->setUpCustomTargetingKey($this->adidKeyName);
		echo "AdidKeyName : ".$this->adidKeyName."\tAdidKeyId: ".$this->adidKeyId."\n";
		$this->sizeKeyId = (new \App\Gam\KeyManager)->setUpCustomTargetingKey($this->sizeKeyName);
		echo "SizeKeyName : ".$this->sizeKeyName."\tSizeKeyId: ".$this->sizeKeyId."\n";


		//Create and get Values
		$valuesManager = new \App\Gam\ValueManager;
		$valuesManager->setKeyId($this->priceKeyId);
		$this->gamValuesList = $valuesManager->convertValuesListToGAMValuesList($this->valuesList);
		echo "Values List Created\n";

		$creativeManager = new \App\Gam\CreativeManager;
		$creativeManager->setSsp($this->ssp)
			->setAdvertiserId($this->advertiserId);
		$this->creativesList = $creativeManager->setUpCreatives();

		echo "\n\n".json_encode($this->creativesList)."\n\n";
		$this->rootAdUnitId = (new \App\Gam\RootAdUnitManager)->setRootAdUnit();
		echo "rootAdUnitId: ".$this->rootAdUnitId."\n";

		$lineItemManager = new \App\Gam\LineItemManager;
		$lineItemManager->setOrderId($this->orderId)
			->setSizes($this->sizes)
			->setSsp($this->ssp)
			->setCurrency($this->currency)
			->setKeyId($this->priceKeyId)
			->setRootAdUnitId($this->rootAdUnitId);

		$existingLineItems = $lineItemManager->getAllOrderLineItems();

		$i = 0;

		foreach($this->gamValuesList as $gamValue)
		{
			$lineItemManager->setValueId($gamValue['valueId'])
				->setBucket($gamValue['valueName'])
				->setLineItemName();
				
			$lineItem = [];
			if (array_key_exists($lineItemManager->getLineItemName(), $existingLineItems)) {
				$lineItem = $existingLineItems[$lineItemManager->getLineItemName()];
			}

			if (empty($lineItem)) {
				$lineItem = $lineItemManager->setUpLineItem(true);
			} else {
				echo "\n\nLine Item ".$lineItemManager->getLineItemName()." has existed.\n\n";			
				if ($this->updateLineItem) {
					$lineItem = $lineItemManager->setUpLineItem(false);
				}
			}

			$licaManager = new \App\Gam\LineItemCreativeAssociationManager;
			$licaManager->setLineItem($lineItem)
				->setCreativeList($this->creativesList)
				->setSizeOverride($this->sizes)
				->setUpLica($this->updateLica);

			$i ++;
			echo "\n\nLine Item ".$lineItemManager->getLineItemName()." created/updated.\n";			
			echo round(($i/count($this->gamValuesList))*100, 1)."% done\n\n";
		}

		(new \App\Gam\OrderManager)->approveOrder($this->orderId);

	}


}