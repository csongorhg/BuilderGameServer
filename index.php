<?php
$time_start = microtime(true);
session_start();
$hitelesites = "OK";
include("_!config.php");
include("_kapcsolat.php");
require_once("_fuggvenyek.php");
require_once("_sql.php");
require_once("server.php");

$srv = new BuilderGameServer\server();
$srv->process();
print(http_build_query($srv->generateMessage()));
$srv->testpage();