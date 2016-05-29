<?php
require_once("autoload.php");

try 
{
    $account = new \PSN\Auth\Auth("test@psn.com", "password");
} 
catch (\PSN\Auth\PSNAuthException $e)
{
    header("Content-Type: application/json");
    die($e->GetError());
}

$user = new \PSN\Users\User($account->GetAccessToken());

echo "<h1>Welcome, " . $user->Me()->profile->onlineId . "!</h1>";