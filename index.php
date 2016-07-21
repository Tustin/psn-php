<?php
require_once("autoload.php");
try 
{
    $account = new \PSN\Auth("email@psn.com", "password");
} 
catch (\PSN\PSNAuthException $e)
{
    header("Content-Type: application/json");
    die($e->GetError());
}

$tokens = $account->GetTokens();

$user = new \PSN\User($tokens);

echo "<h1>Welcome, " . $user->Me()->profile->onlineId . "!</h1>";
