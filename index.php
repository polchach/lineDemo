<?php
date_default_timezone_set("Asia/Bangkok");

require_once('class/class.lineAPI.php');
$line = new lineAPI();

$date_time = date("d/m/Y H:i:s");

$content = file_get_contents('php://input');
$arrJson = json_decode($content, true);
 
$array_message = explode('/',strtolower($arrJson['events'][0]['message']['text']));
$arrPostData = array();
$arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];

$userId = $arrJson['events'][0]['source']['userId'];
$messageId = $arrJson['events'][0]['message']['id'];

if(sizeof($array_message)==2){
	$role = '';
	$name = $line -> createAgentProfile($userId,$role);
	switch($array_message[1]){
		case 'new':
		if($name==''){
			$arrPostData['messages'][0]['type'] = "text";
			$arrPostData['messages'][0]['text'] = "สวัสดีค่ะคุณยังไม่ได้เพิ่มเป็นเพื่อนค่ะ\nกรุณาเพิ่มเป็นเพื่อนก่อนนะคะ";
		}else{
			$arrPostData['messages'][0]['type'] = "text";
			$arrPostData['messages'][0]['text'] = "คุณ " .$name. " คะ\nระบบพร้อมใช้งานแล้วค่ะ" 
			."\n1. วิธี login\nพิมพิ์  code/login"
			."\n2. วิธี logout\nพิมพิ์  code/logout"
			."\n3. วิธี พักชั่วขณะ (ACW)\nพิมพิ์  code/acw"
			."\n4. วิธี พักใช้เวลานาน (DND)\nพิมพิ์  code/dnd";
		}
		break;
		
		case 'login':
			$arrPostData['messages'][0]['type'] = "text";
			$arrPostData['messages'][0]['text'] = "คุณ " .$name. " คะ\nระบบพร้อมใช้งานแล้วค่ะ" 
		break;
		
		case 'logout':
			$arrPostData['messages'][0]['type'] = "text";
			$arrPostData['messages'][0]['text'] = "คุณ " .$name. " คะ\nระบบยกเลิกการใช้งานแล้วค่ะ"
		break;
		
		case 'acw':
			$arrPostData['messages'][0]['type'] = "text";
			$arrPostData['messages'][0]['text'] = "คุณ " .$name. " คะ\nระบบพักการใช้งานชั่วขณะแล้วค่ะ"
		break;
		
		case 'dnd':
			$arrPostData['messages'][0]['type'] = "text";
			$arrPostData['messages'][0]['text'] = "คุณ " .$name. " คะ\nระบบพักการใช้งานแล้วค่ะ"
		break;
	}

}
else{
    $results = $line -> getUserProfile($userId);
  
    if(sizeof($results)< 2){
		$arrPostData['messages'][0]['type'] = "text";
		$arrPostData['messages'][0]['text'] = "สวัสดีค่ะคุณยังไม่ได้เพิ่มทางเราเป็นเพื่อนค่ะ\nกรุณาเพิ่มเป็นเพื่อนก่อนนะคะ";
    }
    else{
		//$res = $line -> userState($userId);
		$res = '0';
		if($res =='0'){
			$name = $results['displayName'];
			$pic = $results['pictureUrl'];
			
			switch($arrJson['events'][0]['message']['type']){
			  case 'text':
				$event[] = array(
									"type"		=> "text",
									"sendby"	=> $name,
									"senddate"	=> $date_time,
									"comment"	=> "คุณ ".$name." ส่ง Text"
								);
				$messages[] = array(
									"type"	=> "text",
									"text"	=> $arrJson['events'][0]['message']['text']
								);
				//ค้นหาข้อมูลใน ฐานข้อมูล google แล้วนำมาตอบกลับไป ถ้าเป็น AI
				
				
			  break;
			  
			  case 'image':
				$event[] = array(
									"type"		=> "text",
									"sendby"	=> $name,
									"senddate"	=> $date_time,
									"comment"	=> "คุณ ".$name." ส่ง Image"
								);
				$messages[] = array(
									"type"					=> "image",
									"originalContentUrl"	=> $arrJson['events'][0]['message']['originalContentUrl'],
									"previewImageUrl"		=> $arrJson['events'][0]['message']['previewImageUrl']
								);
			  break;
			  
			  case 'video':
				$event[] = array(
									"type"		=> "text",
									"sendby"	=> $name,
									"senddate"	=> $date_time,
									"comment"	=> "คุณ ".$name." ส่ง Video"
								);
				$messages[] = array(
									"type"					=> "video",
									"originalContentUrl"	=> $arrJson['events'][0]['message']['originalContentUrl'],
									"previewImageUrl"		=> $arrJson['events'][0]['message']['previewImageUrl']
								);
			  break;
			  
			  case 'audio':
				$event[] = array(
									"type"		=> "text",
									"sendby"	=> $name,
									"senddate"	=> $date_time,
									"comment"	=> "คุณ ".$name." ส่ง Audio"
								);
				$messages[] = array(
									"type"					=> "audio",
									"originalContentUrl"	=> $arrJson['events'][0]['message']['originalContentUrl'],
									"duration"				=> $arrJson['events'][0]['message']['duration']
								);
			  break;
			  
			  case 'file':
				$event[] = array(
									"type"		=> "text",
									"sendby"	=> $name,
									"senddate"	=> $date_time,
									"comment"	=> "คุณ ".$name." ส่ง File"
								);
				$messages[] = array(
									"type"		=> "file",
									"fileName"	=> $arrJson['events'][0]['message']['fileName'],
									"fileSize"	=> $arrJson['events'][0]['message']['fileSize']
								);
			  break;
			  
			  case 'location':
				$event[] = array(
									"type"		=> "text",
									"sendby"	=> $name,
									"senddate"	=> $date_time,
									"comment"	=> "คุณ ".$name." ส่ง  Location"
								);
				$messages[] = array(
									"type"	=> "location",
									"title"	=> "ส่งพิกัด โดยคุณ ".$name,
									"address"	=> $arrJson['events'][0]['message']['address'],
									"latitude"	=> $arrJson['events'][0]['message']['latitude'],
									"longitude"	=> $arrJson['events'][0]['message']['longitude']
								);
			  break;
			 
			  
			  case 'sticker':
		
				$event[] = array(
									"type"		=> "text",
									"sendby"	=> $name,
									"senddate"	=> $date_time,
									"comment"	=> "คุณ ".$name." ส่ง Sticker"
								);
				$messages[] = array(
									"type"		=> "sticker",
									"packageId"	=> $arrJson['events'][0]['message']['packageId'],
									"stickerId" => $arrJson['events'][0]['message']['stickerId']
								);
			  break;
			}
			$arrTo[] = 	"U3ff18f16e94a80b192dd89eaaa8b7846";
			$arrTo[] = 	"Uef43a26cff64ac0a608c9acf9d0f70cd";
			$arrTo[] = 	"R4f8f29841c882322654fceb16459e06e";
			$arrTo[] = 	"Rcb9eca4ffaa7798fde9eed394f9a3321";			
			$arrPushData = array("to"=>"U3ff18f16e94a80b192dd89eaaa8b7846","messages"=>$messages);
			$line->Push_Message($arrPushData);
			$line->Reply_Message($arrPostData);
		}
		else{
			//ค้นหา agent ที่มี skill และพร้อม สนทนากับ ลูกค้า
			/*$results = $line->getAgentState($arrPostData);
			
			switch($arrJson['events'][0]['message']['type']){
				case 'text':
					//ถ้ามีประโยคปิดงาน ทำการ update Agent State
					$ms = $arrJson['events'][0]['message']['text'];
					if(($ms == 'ขอบคุณที่ใช้บริการค่ะ')||($ms == 'ขอบคุณที่ใช้บริการครับ')){
						$line->updateAgentState($arrPostData);
					}else{
						$messages[] = array(
												"type"	=> "text",
												"text"	=> $arrJson['events'][0]['message']['text'];
											);
					}
					
				break;
				
				case 'image':
					$messages[] = array(
										"type"	=> "text",
										"text"	=> "คุณ ".$name." ส่งรูปภาพ"
									);

					$messages[] = array(
									"type"					=> "image",
									"originalContentUrl"	=> $arrJson['events'][0]['message']['originalContentUrl'],
									"previewImageUrl"		=> $arrJson['events'][0]['message']['previewImageUrl']
								);
				break;
			  
				case 'video':
					$messages[] = array(
										"type"	=> "text",
										"text"	=> "คุณ ".$name." ส่ง  video"
									);

					$messages[] = array(
										"type"					=> "video",
										"originalContentUrl"	=> $arrJson['events'][0]['message']['originalContentUrl'],
										"previewImageUrl"		=> $arrJson['events'][0]['message']['previewImageUrl']
									);
				break;
				  
				case 'audio':
					$messages[] = array(
										"type"	=> "text",
										"text"	=> "คุณ ".$name." ส่ง ไฟล์เสียง"
									);

					$messages[] = array(
										"type"					=> "audio",
										"originalContentUrl"	=> $arrJson['events'][0]['message']['originalContentUrl'],
										"duration"				=> $arrJson['events'][0]['message']['duration']
									);
				break;
				  
				case 'file':
					$messages[] = array(
										"type"	=> "text",
										"text"	=> "คุณ ".$name." ส่งไฟล์ข้อมูล \nชื่อ ".$arrJson['events'][0]['message']['fileName']."\n ขนาด: ".$arrJson['events'][0]['message']['fileSize']
									);
					$messages[] = array(
										"type"		=> "file",
										"fileName"	=> $arrJson['events'][0]['message']['fileName'],
										"fileSize"	=> $arrJson['events'][0]['message']['fileSize']
									);

				break;
				  
				case 'location':
					$messages[] = array(
										"type"	=> "text",
										"text"	=> "คุณ ".$name." ส่งข้อมูลพิกัด \nที่อยู่ ".$arrJson['events'][0]['message']['address']
									);
					$messages[] = array(
										"type"	=> "location",
										"title"	=> "ส่งพิกัด โดยคุณ ".$name,
										"address"	=> $arrJson['events'][0]['message']['address'],
										"latitude"	=> $arrJson['events'][0]['message']['latitude'],
										"longitude"	=> $arrJson['events'][0]['message']['longitude']
									);
			
				break;
				 
				  
				case 'sticker':
					$messages[] = array(
										"type"	=> "text",
										"text"	=> "คุณ ".$name." ส่ง sticker"
									);
					$messages[] = array(
										"type"	=> "text",
										"text"	=> "UserId: " .$arrJson['events'][0]['source']['userId']
									);
					$messages[] = array(
										"type"	=> "text",
										"text"	=> "GroupId: " .$arrJson['events'][0]['source']['groupId']
									);
					$messages[] = array(
										"type"	=> "text",
										"text"	=> "RoomId: " .$arrJson['events'][0]['source']['roomId']
									);
					
					$messages[] = array(
										"type"		=> "sticker",
										"packageId"	=> $arrJson['events'][0]['message']['packageId'],
										"stickerId" => $arrJson['events'][0]['message']['stickerId']
									);

				break;
			}
			
			//$line->AgentToTalk($messages);  */
		}
	}

}

?>