<?php

define('AUTH_KEY', "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"); # authentication key here

$id = $_GET["id"];

if (!isset($id)) {
	exit("No id set!");
}
header("Content-type: application/json");
date_default_timezone_set('Europe/Stockholm');
$id_chalmers = "9021014001960000";
$id_tvargata = "9021014001970000";
$id_gbgC = "9021014008000000";



$requestData = array(
	"authKey" => AUTH_KEY,
	"id" => $id,
	"date" => date("Y-m-d"), 
	"format" => "json"
);
$locationRequest = array(
	"input" => "Göteborg",
	"authKey" => AUTH_KEY, 
	"format" => "json"
);
//$locationID = "http://api.vasttrafik.se/bin/rest.exe/location.name?" . http_build_query($locationRequest);
$base_url = "http://api.vasttrafik.se/bin/rest.exe/departureBoard?" . http_build_query($requestData);
echo file_get_contents($base_url);
?>