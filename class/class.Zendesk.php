<?php
require_once('config.php');
class Zendesk {
	var $cdr; 
	var $ast;
    function __construct() {
		$this->cdr = $GLOBALS['concdr'];
		$this->ast = $GLOBALS['conast'];
	}
	function setIVR($call){
		$sql= "INSERT INTO kaidee_tickets SET ticketid='',calldate = NOW(),src='".$call[0]."',dst='".$call[1]."',callid='".$call[2]."',ivr_menu='".$call[3]."',callstate='".$call[4]."'
					ON DUPLICATE KEY UPDATE calldate = NOW(),src='".$call[0]."',dst='".$call[1]."',ivr_menu='".$call[3]."',callstate='".$call[4]."'";
		$this->cdr->query($sql);
	}
	function setTicketID($phone,$ticketid){
		$sql= "UPDATE kaidee_tickets SET ticketid='".$ticketid."',callstate='2' WHERE src='".$phone."' AND callstate='1'";
		$this->cdr->query($sql);
	}
	function setEndCall($phone){
		$sql= "UPDATE kaidee_tickets SET callstate='0' WHERE src='".$phone."'";
		$this->cdr->query($sql);
	}
	function getDataTicket($phone){
		$sql= "SELECT * FROM kaidee_tickets WHERE src='".$phone."' AND callstate='1' LIMIT 1";
		$results = $this->cdr->query($sql);
		return $results;
	}
	function getToken($exten){
		$sql = "SELECT
			kaidee_users.extension,
			kaidee_users.user_id,
			kaidee_users.default_group_id,
			kaidee_tokens.token,
			kaidee_users.`name`,
			kaidee_users.email,
			kaidee_users.phone
			FROM
			kaidee_users
			INNER JOIN kaidee_tokens ON kaidee_users.user_id = kaidee_tokens.user_id
			WHERE kaidee_users.extension=".$exten;
		$results = $this->cdr->query($sql);
		$result = $results->fetch_assoc();
		return $result;
	}
	function getAuth($exten){
		$sql = "SELECT
			kaidee_users.extension,
			kaidee_users.user_id,
			kaidee_users.default_group_id,
			kaidee_tokens.token,
			kaidee_users.`name`,
			kaidee_users.email,
			kaidee_users.phone
			FROM
			kaidee_users
			INNER JOIN kaidee_tokens ON kaidee_users.user_id = kaidee_tokens.user_id
			WHERE kaidee_users.extension=".$exten;
		$results = $this->cdr->query($sql);
		$result = $results->fetch_assoc();
		return $result;
	}
	function getIvrMenu($phone){
		$sql= "SELECT ivr_menu FROM kaidee_tickets WHERE src='".$phone."' AND callstate='1' LIMIT 1";
		$results = $this->cdr->query($sql);
		$result = $results->fetch_assoc();
		return $result['ivr_menu'];
	}
	function sreachCustomer($phone){
		$cmd = 'curl -g "https://olxcoth.zendesk.com/api/v2/search.json" \
		  -G --data-urlencode "query=type:user phone:'.$phone.'" \
		  -v -u arada.i@kaidee.com:K@1dee.com';
		$result = shell_exec($cmd);
		echo json_encode($result);
		$arr = json_decode($result,true);
		$res = $arr['results'];
		foreach ($res as $r) {
			extract($r);
			$cust[] = array(
						"custid"	=> $id,
						"locale_id"	=> $locale_id,
						"name" 		=> $name,
						"phone" 	=> $phone,
						"email" 	=> $email
					);	

		}
		return $cust;
	}
	
	function getUsers(){
		$cmd = 'curl -g "https://olxcoth.zendesk.com/api/v2/users.json" \
		  -v -u arada.i@kaidee.com:K@1dee.com';
		$result = shell_exec($cmd);
		echo json_encode($result);
		$arr = json_decode($result,true);
		$res = $arr['users'];
		foreach ($res as $r) {
			extract($r);
			$users[] = array(
						"user_id"	=> $id,
						"role"		=> $role,
						"default_group_id"	=> $default_group_id,
						"locale_id"	=> $locale_id,
						"name" 		=> $name,
						"phone" 	=> $phone,
						"email" 	=> $email
					);	

		}
		return $users;
	}
	/*function getToken(){
		$cmd = 'curl -g "https://olxcoth.zendesk.com/api/v2/oauth/tokens.json" \
		  -v -u arada.i@kaidee.com:K@1dee.com';
		$result = shell_exec($cmd);
		echo json_encode($result);
		$arr = json_decode($result,true);
		$res = $arr['tokens'];
		foreach ($res as $r) {
			extract($r);
			$users[] = array(
						"user_id"	=> $user_id,
						"token"		=> $token,
						"refresh_token"	=> $refresh_token,
						"expires_at"	=> $expires_at
					);	

		}
		return $users;
	}*/
	function createTicket($ivr,$subject,$value,$call) { 
	$strUrl = "https://olxcoth.zendesk.com/api/v2/tickets.json";
		$res = $this->getAuth($call[0]);
		if(sizeof($res) > 0){
			$token = $res['token'];
			$strUser = $res['email'];
			$assignee_id = $res['user_id'];
			$group_id = $res['default_group_id'];
		}else{
			$token = $res['token'];
			$strUser = "arada.i@kaidee.com";
			$strPassword = "K@1dee.com";
			$assignee_id = "361145582893";
			$group_id = "360000477693";
		}
			$arrHeader = array();
			$arrHeader[] = "Content-Type: application/json";
			if($token != ''){
				$arrHeader[] = "Authorization: Bearer ".$token;
			}
			 
			
			$cust = $this->sreachCustomer($call[1]);
			$locale_id 	= "";
			$name		= "";
			$email		= "";
			$subjects 	= "";
			$ticketid	= "";
			$link_sound	= "https://172.19.1.27/ws.pbx/pbx/cmd.php?action=playsound&callid=";
			if(sizeof($cust) > 0){
				$locale_id 	=	$cust['locale_id'];
				$name  		= 	$cust['name'];
				$email 		= 	$cust['email'];
			}
			$phone = $call[1];
			switch($ivr){
					case '1':
					$subjects = 'สอบถามการโปรโมท หรือ เรื่องประกาศ ';
					break;
					
					case '2':
					$subjects = 'สอบถามวิธีลงขาย และ ข้อสงสัยอื่น ๆ';
					break;
					
					case '3':
					$subjects = 'แจ้งปัญหาการใช้งาน';
					break;
					
					case '4':
					$subjects = 'แจ้งพบมิจฉาชีพ';
					break;
				}
			
			$comment = array(
							"html_body" => "<a href='$link_sound$value'>ฟังเสียงสนทนา</a>"
				);
			$tags = array("call");
		
			$custom_fields[] = array(
							"id" 	=> 360000262047,
							"value" => $phone
				);
			if($email != ''){
				$ticket = array(
					"subject"  		=> $subjects,
					"comment" 		=> $comment,
					"assignee_id" 	=> $assignee_id,
					"group_id" 		=> $group_id,
					"tags" 			=> $tags,
					"type"			=> "incident",
					"custom_fields" => $custom_fields,
					"requester"     => array(
											'locale_id' => $locale_id,
											'name' => $name,
											'email' => $email
										),
					"priority" 		=> "normal"
				);
			}else{
				$ticket = array(
					"subject"  		=> $subjects,
					"comment" 		=> $comment,
					"assignee_id" 	=> $assignee_id,
					"group_id" 		=> $group_id,
					"tags" 			=> $tags,
					"type"			=> "incident",
					"custom_fields" => $custom_fields,
					"requester"     => array(
											'locale_id' => 81,
											'name' => 'protollcall',
											'email' => 'ptc@protollcall.com'
										),
					"priority" 		=> "normal"
				);
				
			}
			
			$arrPostData = array(
				"ticket" => $ticket
			);

			$ch = curl_init();
			
		   
			curl_setopt($ch, CURLOPT_URL,$strUrl);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeader);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrPostData));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_TIMEOUT_MS,10000);
			curl_setopt($ch, CURLOPT_USERNAME,"$strUser");
			if($token == ''){
				curl_setopt($ch, CURLOPT_USERPWD, "$strUser:$strPassword");
			}
			$result = curl_exec($ch);
			echo json_encode($result);
			curl_close ($ch);
			$arr = json_decode($result,true);
			if(sizeof($arr) > 0){
				$ticketid= $arr['ticket']['id'];
			}
			
			$this->setTicketID($phone,$ticketid);
		
			$dataTicket=array('exten'=>$call[0],'phone'=>$call[1],'callid'=>$call[2],'ticketid'=>$ticketid);
			return $dataTicket;
		}
	
}
?>