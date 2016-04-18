<?php
require_once("autoload.php");


$account = new \PSN\Auth\Auth("test@psn.com", "password");

echo "<h1>Welcome, " . $account->GetInfo()->profile->onlineId . "!</h1>";

$friend = new \PSN\Friends\Friend($account->GetAccessToken());

var_dump($friend->GetInfo('Friend'));

$message = new \PSN\Message\Messaging($account->GetAccessToken());

for ($i = 0; $i < 100; $i++) {
	$message->Message('Friend', 'Test Message ' . ($i + 1));
	echo $i . '<br />';
}