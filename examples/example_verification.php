<?php
include_once('../ConstantContact.php');
session_start();

// Set variables
$api_key = $_GET['apiKey']; // API Key
$consumer_secret = $_GET['secret']; // Consumer Secret
$_SESSION['return'] = $_GET['return'];
$callback_url = 'http://'.$_SERVER['SERVER_NAME'].(($_SERVER['SERVER_PORT'] != '80') ? ':'.$_SERVER['SERVER_PORT'] : '') .$_SERVER['REQUEST_URI'];

// Instantiate ConstantContact class with the key and secret from ConstantContact.php
$CTCTOAuth = new CTCTOAuth($api_key, $consumer_secret, $callback_url);

if(!$_GET['oauth_verifier']){
    $CTCTOAuth->getRequestToken();
    $_SESSION['request_token'] = $CTCTOAuth->request_token->key;
    $_SESSION['request_secret'] = $CTCTOAuth->request_token->secret;
    header('Location: '.$CTCTOAuth->generateAuthorizeUrl());
} else {
    $returnLocation = 'http://'.$_SESSION['return'];
    unset($_SESSION['return']);

    $requestToken = new OAuthToken($_SESSION['request_token'], $_SESSION['request_secret']);

    $CTCTOAuth->request_token = $requestToken;
    $CTCTOAuth->username = $_GET['username'];
    $CTCTOAuth->getAccessToken($_GET['oauth_verifier']);

    $sessionConsumer = array(
        'key' => $CTCTOAuth->access_token->key,
        'secret' => $CTCTOAuth->access_token->secret,
        'username' => $CTCTOAuth->username
    );

    $Datastore = new CTCTDataStore();
    $Datastore->addUser($sessionConsumer);

    header('Location: '.$returnLocation);
}