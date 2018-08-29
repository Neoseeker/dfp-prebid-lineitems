<?php

namespace App\Gam;

require(__DIR__."/../../vendor/autoload.php");

use Google\AdsApi\Common\OAuth2TokenBuilder;
use Google\AdsApi\AdManager\AdManagerServices;
use Google\AdsApi\AdManager\AdManagerSession;
use Google\AdsApi\AdManager\AdManagerSessionBuilder;
use Google\AdsApi\AdManager\v201808\UserService;
use Google\AdsApi\AdManager\v201808\User;
use Google\AdsApi\AdManager\Util\v201808\StatementBuilder;

class UserManager extends GamManager
{
	protected $user;

	public function getCurrentUser()
	{
		$userService = $this->gamServices->get($this->session, UserService::class);

		$user = $userService->getCurrentUser();
        $output = array(
            "userId"=>$user->getId(),
            "userName"=>$user->getName(),
            "userMail"=>$user->getEmail(),
            "userRole"=>$user->getRoleName()
        );
        $this->user = $output;
        return $output;
	}

	public function createUser()
	{
		 $userService = $this->gamServices->get($this->session, UserService::class);
		$user = new User();
        $user->setName('Gabriel');
        $user->setEmail('gabriel@insideall.com');
        //$user->setName($userName);
        $user->setRoleId("-1");
         // Create the users on the server.
        $results = $userService->createUsers([$user]);
        // Print out some information for each created user.
        foreach ($results as $i => $user) {
            printf(
                "%d) User with ID %d and name '%s' was created.\n",
                $i,
                $user->getId(),
                $user->getName()
            );
        }
	}

	public function getUserId()
	{
		$userArray = $this->getCurrentUser();
		return $userArray['userId'];
	}

	public function getAllUsers()
	{
		$userService = $this->gamServices->get($this->session, UserService::class);
		$statementBuilder = (new StatementBuilder())->orderBy('id ASC');
		$data = $userService->getUsersByStatement(
            $statementBuilder->toStatement()
        );
		if ($data->getResults() !== null) {
            $totalResultSetSize = $data->getTotalResultSetSize();
            $i = $data->getStartIndex();
            foreach ($data->getResults() as $user) {
                printf(
                    "%d) User with ID %d and name '%s' was found.\n",
                    $i++,
                    $user->getId(),
                    $user->getName()
                );
            }
        }
	}
}