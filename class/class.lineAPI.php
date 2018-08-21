<?php

class lineAPI {
	var $accToken; 
	var $apiUrl_push;
	var $apiUrl_multicast;
	var $apiUrl_reply;
	var $apiUrl_profile;
 
    function __construct() {
		$this->accToken 		= "uQpBChF8z+1KiQkkGKwti/nbfc1HrvsUPIVfHt4v/t0Aq6do97HKoQPAnW2080uDYP+4mqtwT7qnG/fa1SgNEZNlTSDa7Ur6aBWMxC5WnSTMhb8Th/jc2B1u6Y0Rimvijmk4RpUz/Hw/NrI3t3zaUwdB04t89/1O/w1cDnyilFU=";
		$this->apiUrl_push 		= "https://api.line.me/v2/bot/message/push";
		$this->apiUrl_multicast = "https://api.line.me/v2/bot/message/multicast";
		$this->apiUrl_reply 	= "https://api.line.me/v2/bot/message/reply";
		$this->apiUrl_profile 	= "https://api.line.me/v2/bot/profile";
	}

	function Reply_Message($arrPostData){
		$strUrl = $this->apiUrl_reply;
 
		$arrHeader = array();
		$arrHeader[] = "Content-Type: application/json";
		$arrHeader[] = "Authorization: Bearer {$this->accToken}";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$strUrl);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeader);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrPostData));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		curl_close ($ch);
	}
	function Push_Message($arrPostData){
		$strUrl = $this->apiUrl_push;
 
		$arrHeader = array();
		$arrHeader[] = "Content-Type: application/json";
		$arrHeader[] = "Authorization: Bearer {$this->accToken}";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$strUrl);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeader);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrPostData));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		curl_close ($ch);
	}
	function Multicast_Message($arrPostData){
		$strUrl = $this->apiUrl_multicast;
 
		$arrHeader = array();
		$arrHeader[] = "Content-Type: application/json";
		$arrHeader[] = "Authorization: Bearer {$this->accToken}";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$strUrl);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeader);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrPostData));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		curl_close ($ch);
	}
	function Get_Content($messageId){
		$strUrl = "https://api.line.me/v2/bot/message/{$messageId}/content";
 
		$arrHeader = array();
		$arrHeader[] = "Content-Type: application/json";
		$arrHeader[] = "Authorization: Bearer {$this->accToken}";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$strUrl);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeader);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		curl_close ($ch);
	}
	function keepToDB($arrPostData){
		$strUrl = "http://www.thailandsmartai.com/GW/push/index.php?data=".base64_encode(json_encode($arrPostData));
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$strUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_exec($ch);
		curl_close ($ch);

	}
	function checkAuth($arrPostData){
		$strUrl = "http://www.thailandsmartai.com/GW/auth/index.php?data=".base64_encode(json_encode($arrPostData));
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$strUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		curl_close ($ch);
		return json_decode($result,true);

	}
	
	function getUserProfile($userId){
		$strUrl = $this->apiUrl_profile ."/{$userId}";
 
		$arrHeader = array();
		$arrHeader[] = "Content-Type: application/json";
		$arrHeader[] = "Authorization: Bearer {$this->accToken}";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$strUrl);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeader);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
	
		$data = json_decode($result,true);
		curl_close ($ch);
		if(sizeof($data) == 4){
			$users = array(
							"userId"		=> $data['userId'],
							"displayName"	=> $data['displayName'],
							"pictureUrl"	=> $data['pictureUrl'],
							"statusMessage"	=> $data['statusMessage'],
							"phone" 	=> "",
							"email" 	=> "",
							"role" 		=> "EndUser",
							"status" 	=> "1"
						);	
		}else if(sizeof($data) == 3){
			$users = array(
							"userId"		=> $data['userId'],
							"displayName"	=> $data['displayName'],
							"pictureUrl"	=> $data['pictureUrl'],
							"statusMessage"	=> "",
							"phone" 	=> "",
							"email" 	=> "",
							"role" 		=> "EndUser",
							"status" 	=> "1"
						);	

		}
		$arrPostData = array(
				"users" => $users
			);
		$this->keepToDB($arrPostData);
		
		return $users;
	}
	
	function createAgentProfile($userId,$role){
		$strUrl = $this->apiUrl_profile ."/{$userId}";
 
		$arrHeader = array();
		$arrHeader[] = "Content-Type: application/json";
		$arrHeader[] = "Authorization: Bearer {$this->accToken}";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$strUrl);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeader);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
	
		$data = json_decode($result,true);
		curl_close ($ch);
		if(sizeof($data) == 4){
			$users = array(
							"userId"		=> $data['userId'],
							"displayName"	=> $data['displayName'],
							"pictureUrl"	=> $data['pictureUrl'],
							"statusMessage"	=> $data['statusMessage'],
							"phone" 	=> "",
							"email" 	=> "",
							"role" 		=> $role,
							"status" 	=> "1"
						);	
		}else if(sizeof($data) == 3){
			$users = array(
							"userId"		=> $data['userId'],
							"displayName"	=> $data['displayName'],
							"pictureUrl"	=> $data['pictureUrl'],
							"statusMessage"	=> "",
							"phone" 	=> "",
							"email" 	=> "",
							"role" 		=> $role,
							"status" 	=> "1"
						);	

		}else{
			$users = array(
							"userId"		=> $data['userId'],
							"displayName"	=> $data['displayName'],
							"pictureUrl"	=> $data['pictureUrl'],
							"statusMessage"	=> "",
							"phone" 	=> "",
							"email" 	=> "",
							"role" 		=> $role,
							"status" 	=> "1"
						);	
		}
		$arrPostData = array(
				"agents" => $users
			);
		$this->keepToDB($arrPostData);
		
		return $arrPostData['agents']['displayName'];
	}
}
?>