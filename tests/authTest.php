<?php
try {
    $account = new \PSN\Auth('email@psn.com', 'password');
} catch (\PSN\AuthException $e) {
    header('Content-Type: application/json');
    print $e->GetError();
    exit;
}

$tokens = $account->GetTokens();

$user = new \PSN\User($tokens);

printf('<p>Hello, %s!</p>', $user->me()->profile->onlineId);
