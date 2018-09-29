<?php
require_once "../vendor/autoload.php";

use PlayStation\Client;

$client = new Client(["verify" => false, "proxy" => "127.0.0.1:8888"]);

$client->login(getenv("PSN_PHP_TOKEN"));

$me = $client->user();

echo sprintf("%s\n", $me->onlineId());
echo sprintf("\tAbout me: %s\n", $me->aboutMe());
echo sprintf("\tAvatar: %s\n", $me->avatarUrl());
echo sprintf("\tFriends with? %d\n", $me->friend());
echo sprintf("\tClose friends with? %d\n", $me->closeFriend());
echo sprintf("\tVerified? %d\n", $me->verified());

$you = $client->user("Hakoom");

echo sprintf("%s\n", $you->onlineId());
echo sprintf("\tAbout me: %s\n", $you->aboutMe());
echo sprintf("\tAvatar: %s\n", $you->avatarUrl());
echo sprintf("\tFriends with? %d\n", $you->friend());
echo sprintf("\tClose friends with? %d\n", $you->closeFriend());
echo sprintf("\tVerified? %d\n", $you->verified());