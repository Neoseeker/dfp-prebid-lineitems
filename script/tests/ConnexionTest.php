<?php

require (__DIR__."/../scriptLoader.php");

$traffickerId  = (new \App\Gam\UserManager)->getUserId();

if(is_numeric($traffickerId))
{
	echo "\n====Connexion OK====\n\n";
}
else 
{
	echo "\n===Connexion KO====\n\n";
}