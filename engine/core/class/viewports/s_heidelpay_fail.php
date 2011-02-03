<?php
include_once("s_login.php");

class sViewportHeidelpay_fail{
  var $sSYSTEM;
	var $sViewportLogin;

	function sViewportHeidelpay_fail(&$sSYSTEM,&$sViewportLogin){
		if (!is_object($sViewportLogin)){
			$this->sViewportLogin = new sViewportLogin($sSYSTEM,$this);
			$this->sViewportLogin->sSYSTEM = $sSYSTEM;
		}else {
			$this->sViewportLogin = $sViewportLogin;
		}
	}

	function sRender(){

    // Check users permission
		if (!$this->sSYSTEM->sMODULES['sAdmin']->sCheckUser()){
			$this->sSYSTEM->_GET["sViewport"] = "login";
			$this->sSYSTEM->_POST["sTarget"] = "sale";
			return $this->sViewportLogin->sRender();
		}else {
			$userData = $this->sSYSTEM->sMODULES['sAdmin']->sGetUserData();
			$variables["sUserData"] = $userData;
		}

		$this->sSYSTEM->_GET["sViewport"] = "heidelpay_fail";

    $templates = array(
		"sContainer"=>"/payment/heidelpay_fail.tpl",
		"sContainerRight"=>""
		);

    $variables = array();
		return array("templates"=>$templates,"variables"=>$variables);

	}
}
?>