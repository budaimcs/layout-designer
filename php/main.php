<?php

echo("Starting server from " . getcwd() . "...\n");

chdir( dirname( dirname( __FILE__ ) ) );

require_once("config.php");
require_once("vendor/ez_sql/ez_sql_core.php");
require_once("vendor/ez_sql/ez_sql_mysql.php");
require_once("vendor/2627_db.php");

require_once("control/store.php");
require_once("control/static.php");
require_once("control/api.php");


//Create event base
$base = new EventBase();

//Create HTTP server
// $ctx = new EventSslContext(EventSslContext::SSLv3_SERVER_METHOD, array() );
$http = new EventHttp($base);

$store = new Store( array(
	
));

$api = new Api( $store, array(

));

$router = new Router( $http, array(
	array( "%^/api/%", array( $api, "handle_api_call"), array() ),
	array( "%^/static/%", "serveStaic", array() ),
	array( "%%", "serveStaic", array( "path" => "index.html") ),
));

//Start event handler
$base->dispatch();
?>