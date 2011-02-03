<?php
define('sAuthFile', 'sGUI');
define('sConfigPath',"../../../");
include("../../backend/php/check.php");
include("../../core/class/sTicketSystem.php");
include("json.php");
$result = new checkLogin();
$result = $result->checkUser();
if ($result!="SUCCESS"){
	die("FAIL");
}
// Create sTicket instance
$sTicketSystem = new sTicketSystem();

$userID = intval($_REQUEST['userID']);
$ticketID = intval($_REQUEST['ticketID']);

$json = new Services_JSON();
echo $json->encode($sTicketSystem->getTicketHistoryStore($ticketID, $userID));
?>