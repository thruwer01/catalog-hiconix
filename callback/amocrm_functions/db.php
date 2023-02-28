<?php
declare(strict_types=1);

/*
* Variables
*/
$dbName = "u1501272_amocrm-hiconix";
$dbUser = "u1501272_amocrm";
$dbPass = "amocrmEcoclima";
/*
* End variables
*/

$db = new mysqli('localhost',$dbUser, $dbPass, $dbName);

/**
 * Get tokens from mysqli DB
 *
 * @param myslqi $db object of db
 * 
 * @return array Return array of tokens
 */ 
function getTokensFromDB(mysqli $db):array {
    $tokens = $db->query("SELECT `access_token`, `refresh_token` FROM `amocrm_tokens` WHERE `id` = '1' LIMIT 1")->fetch_assoc();
    return $tokens;
}
/**
 * Update toknes in DB
 *
 * @param myslqi $db object of db
 * @param string $access_token
 * @param string $refresh_token
 * 
 */ 
function updateTokensDB(mysqli $db, string $access_token, string $refresh_token) {
    $db->query("UPDATE `amocrm_tokens` SET `access_token` = '$access_token', `refresh_token` = '$refresh_token' WHERE `id` = 1 LIMIT 1");
}