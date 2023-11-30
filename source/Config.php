<?php


$envPath = realpath(dirname(__FILE__) . '/env.ini');
$env = parse_ini_file($envPath);

/**
 * SITE CONFIG
 */
define("SITE", [
  "name" => "Authen em MVC com PHP",
  "desc" => "Uma aplicação de autenticação de login em MVC com PHP baseado no curso do youtube www.youtube/upinside/",
  "domain" => "localhost:8091",
  "locale" => "pt-br",
  "root" => "http://localhost:8091/projetos/authen"
]);

/**
 * SITE MINIFY
 */
if ($_SERVER["SERVER_NAME"] === "localhost") {
  require __DIR__ . "/Minify.php";
}

/**
 * DATABASE CONNECT
 */
define("DATA_LAYER_CONFIG", [
  "driver" => $env["db_driver"],
  "host" => $env["db_host"],
  "port" => $env["db_port"],
  "dbname" => $env["db_name"],
  "username" => $env["db_username"],
  "passwd" => $env["db_password"],
  "options" => [
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    PDO::ATTR_CASE => PDO::CASE_NATURAL
  ]
]);

/**
 * SOCIAL MEDIA CONFIG
 */
define("SOCIAL", [
  "facebook_page" => $env["s_facebook_page"],
  "facebook_author" => $env["s_facebook_author"],
  "facebook_appId" => $env["s_facebook_appId"],
  "twitter_creator" => $env["s_twitter_creator"],
  "twitter_site" => $env["s_twitter_site"]
]);

/**
 * MAIL CONNECT
 */
define("MAIL", [
  "host" => $env["mail_host"],
  "port" => $env["mail_port"],
  "user" => $env["mail_user"],
  "passwd" => $env["mail_password"],
  "from_name" => $env["mail_from_name"],
  "from_email" => $env["mail_from_email"]
]);

/**
 * SOCIAL LOGIN
 */
define("FACEBOOK_LOGIN", [
  "clientId" => $env["f_clientId"],
  "clientSecret" => $env["f_clientSecret"],
  "redirectUri" => SITE["root"] . $env["f_redirectUri"],
  "graphApiVersion" => $env["f_graphApiVersion"]
]);

define("GOOGLE_LOGIN", [
  "clientId" => $env["g_clientId"],
  "clientSecret" => $env["g_clientSecret"],
  "redirectUri" => SITE["root"] . $env["g_redirectUri"]
]);
