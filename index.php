<?php
require_once("autoload.php");

$account = new \PSN\Auth\Auth("example@email.com", "password");

echo "<h1>Welcome, " . $account->GetInfo()->profile->onlineId . "!</h1>";