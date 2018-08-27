<?php

namespace App\Gam;

require(__DIR__."/../../vendor/autoload.php");

use Google\AdsApi\Common\OAuth2TokenBuilder;
use Google\AdsApi\AdManager\AdManagerServices;
use Google\AdsApi\AdManager\AdManagerSession;
use Google\AdsApi\AdManager\AdManagerSessionBuilder;
use Google\AdsApi\AdManager\v201808\Order;
use Google\AdsApi\AdManager\v201808\OrderService;
use Google\AdsApi\AdManager\Util\v201808\StatementBuilder;
use Google\AdsApi\AdManager\v201808\ApproveOrders as ApproveOrdersAction;

class OrderManager extends GamManager
{
	public function setUpOrder($orderName, $advertiserId, $traffickerId)
	{	
		if(empty(($foo = $this->getOrder($orderName))))
		{
			$foo = $this->createOrder($orderName, $advertiserId, $traffickerId);
		}
		return $foo[0]['orderId'];
	}




	public function getAllOrders()
	{
		$output = [];
		$orderService = $this->gamServices->get($this->session, OrderService::class);

		$statementBuilder = (new StatementBuilder())->orderBy('id ASC');
		$data = $orderService->getOrdersByStatement($statementBuilder->toStatement());
		if($data->getResults() == null)
		{
			return $output;
		}
		foreach ($data->getResults() as $order) {
		    $foo = array(
		  		"orderId" => $order->getId(),
		        "orderName" => $order->getName(),
		        "orderAdvertiserId" => $order->getAdvertiserId(),
		        "salespersonId" => $order->getSalespersonId(),
		        "traffickerId" => $order->getTraffickerId()
		    );
		    array_push($output, $foo);
		}
		return $output;
	}

	public function approveOrder($orderId)
	{
		$orderService = $this->gamServices->get($this->session, OrderService::class);
		$statementBuilder = (new StatementBuilder())
			->where('id = :id')
			->withBindVariableValue('id', $orderId);
		
        // Create and perform action.
        $action = new ApproveOrdersAction();
        $result = $orderService->performOrderAction(
            $action,
            $statementBuilder->toStatement()
        );

	}

	public function getOrder($orderName)
	{
		$output = [];
		$orderService = $this->gamServices->get($this->session, OrderService::class);
		$statementBuilder = (new StatementBuilder())
			->orderBy('id ASC')
			->where('name = :name')
			->WithBindVariableValue('name', $orderName);
		$data = $orderService->getOrdersByStatement($statementBuilder->toStatement());
		if ($data->getResults() !== null)
		{
			foreach ($data->getResults() as $order) {
				$foo = array(
					"orderId"=>$order->getId(),
					"orderName"=>$order->getName()
				);
				array_push($output, $foo);
			}
		}
		return $output;
	}


	public function createOrder($orderName, $advertiserId, $traffickerId)
	{
		$output = [];
		$orderService = $this->gamServices->get($this->session, OrderService::class);
		$order = new Order();
        $order->setName($orderName);
        $order->setAdvertiserId($advertiserId);
        //$order->setSalespersonId($traffickerId);
        $order->setTraffickerId($traffickerId);
        // Create the order on the server.
        $results = $orderService->createOrders([$order]);
        foreach ($results as $order) {
			$foo = array(
				"orderId"=>$order->getId(),
				"orderName"=>$order->getName()
			);
			array_push($output, $foo);
		}
		return $output;
	}
}