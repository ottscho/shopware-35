<?php
define('sAuthFile', 'sGUI');
define('sConfigPath',"../../../../");
include("../../../backend/php/check.php");
$result = new checkLogin();
$result = $result->checkUser();
if ($result!="SUCCESS"){
echo "
	<html><title>Time-Out</title><head></head>
	<script language=\"javascript\">
	parent.location.reload();
	</script>
	<body>Bitte loggen Sie sich neu ein!</body></html>
	";
	die();
}
// *****************
?>
{
	"init": {
		"title": "<?php echo $sLang["browser"]["options_shopware_filebrowser"] ?>",
		"minwidth": "1000",
		"minheight": "580",
		"width": "1000",
		"height": "580",
		"content": "",
		"loader": "iframe2",
		"url": "browser.php",
		"help":"http://www.hamann-media.de/dev/wiki/Hilfe:Inhalte#Datei-Archiv"
	}
}