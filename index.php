<?php
require_once("autoload.php");

$account = new \PSN\Auth\Auth("test@psn.com", "password");

$user = new \PSN\Users\User($account->GetAccessToken());

echo "<h1>Welcome, " . $user->Me()->profile->onlineId . "!</h1>";