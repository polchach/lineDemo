<?php
require_once('class/class.lineAPI.php');
$line = new lineAPI();

$content = file_get_contents('php://input');
$arrJson = json_decode($content, true);
 
$array_message = explode('/',strtolower($arrJson['events'][0]['message']['text']));
$arrPostData = array();
$arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];

$userId = $arrJson['events'][0]['source']['userId'];
$messageId = $arrJson['events'][0]['message']['id'];
if(sizeof($array_message)==5){
	if(($array_message[0] =='itts')&&($array_message[4] == 'login')){
		
		$name = $line -> createAgentProfile($userId,$array_message[3]);
		if($name==''){
			$arrPostData['messages'][0]['type'] = "text";
			$arrPostData['messages'][0]['text'] = "สวัสดีค่ะคุณยังไม่ได้เพิ่มเป็นเพื่อนค่ะ\nกรุณาเพิ่มเป็นเพื่อนก่อนนะคะ";
		}else{
			$arrPostData['messages'][0]['type'] = "text";
			$arrPostData['messages'][0]['text'] = "สวัสดีค่ะคุณ " .$name. "\nกรุณารอสักครู่ ระบบกำลังตรวจสอบสิทธิ์";
			$oauth = array('cocode'=>$array_message[0],'username'=>$array_message[1],'secret'=>$array_message[2],'role'=>$array_message[3],'event'=>$array_message[4]);
			$result = $line -> checkAuth($oauth);
			if($result['userId']==''){
					$arrPostData['messages'][0]['type'] = "text";
					$arrPostData['messages'][0]['text'] = "ข้อมูลของคุณใช้งานไม่ได้ค่ะ\nกรุณาตรวจข้อมูลอีกครั้งนะคะ";
			}else{
					$arrPostData['messages'][0]['type'] = "text";
					$arrPostData['messages'][0]['text'] = "คุณ " .$name. " คะ\nระบบพร้อมใช้งานแล้วค่ะ";

			}
		
		}
			$arrTo[] = 	"U3ff18f16e94a80b192dd89eaaa8b7846";
			$arrTo[] = 	"Uef43a26cff64ac0a608c9acf9d0f70cd";
			$arrTo[] = 	"R4f8f29841c882322654fceb16459e06e";
			$arrTo[] = 	"Rcb9eca4ffaa7798fde9eed394f9a3321";			
			//$arrPushData = array("to"=>"U3ff18f16e94a80b192dd89eaaa8b7846","messages"=>$messages);
			//$line->Multicast_Message($arrPushData);
			//$line->Push_Message($arrPushData);
			$line->Reply_Message($arrPostData);
	}
}
else{
    $results = $line -> getUserProfile($userId);
  
    if(sizeof($results)< 2){
		$arrPostData['messages'][0]['type'] = "text";
		$arrPostData['messages'][0]['text'] = "สวัสดีค่ะคุณยังไม่ได้เพิ่มทางเราเป็นเพื่อนค่ะ\nกรุณาเพิ่มเป็นเพื่อนก่อนนะคะ";
    }
    else{
		$result = $line -> getAgentState($userId);
		if($result['state']=='0'){
			$name = $results['displayName'];
			$pic = $results['pictureUrl'];
			
			switch($arrJson['events'][0]['message']['type']){
			  case 'text':
				$msg = explode('/',$arrJson['events'][0]['message']['text']);
				//ค้นหาข้อมูลใน ฐานข้อมูล google แล้วนำมาตอบกลับไป
				
				
			  break;
			  
			  case 'image':
				$messages[] = array(
									"type"	=> "text",
									"text"	=> "คุณ ".$name." ส่งรูปภาพ"
								);
				$messages[] = array(
										"type"	=> "text",
										"text"	=>  "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้รับใช้หรือคะ...?"
									);
							$arrPostData['messages'][0]['type'] = "text";
							$arrPostData['messages'][0]['text']	=  "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้รับใช้หรือคะ...?";
				/*$messages[] = array(
									"type"					=> "image",
									"originalContentUrl"	=> $arrJson['events'][0]['message']['originalContentUrl'],
									"previewImageUrl"		=> $arrJson['events'][0]['message']['previewImageUrl']
								);*/
			  break;
			  
			  case 'video':
				$messages[] = array(
									"type"	=> "text",
									"text"	=> "คุณ ".$name." ส่ง  video"
								);
				$messages[] = array(
										"type"	=> "text",
										"text"	=>  "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้รับใช้หรือคะ...?"
									);
							$arrPostData['messages'][0]['type'] = "text";
							$arrPostData['messages'][0]['text']	=   "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้รับใช้หรือคะ...?";
				/*$messages[] = array(
									"type"					=> "video",
									"originalContentUrl"	=> $arrJson['events'][0]['message']['originalContentUrl'],
									"previewImageUrl"		=> $arrJson['events'][0]['message']['previewImageUrl']
								);*/
			  break;
			  
			  case 'audio':
				$messages[] = array(
									"type"	=> "text",
									"text"	=> "คุณ ".$name." ส่ง ไฟล์เสียง"
								);
				$messages[] = array(
										"type"	=> "text",
										"text"	=>  "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้รับใช้หรือคะ...?"
									);
							$arrPostData['messages'][0]['type'] = "text";
							$arrPostData['messages'][0]['text']	=   "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้รับใช้หรือคะ...?";
				/*$messages[] = array(
									"type"					=> "audio",
									"originalContentUrl"	=> $arrJson['events'][0]['message']['originalContentUrl'],
									"duration"				=> $arrJson['events'][0]['message']['duration']
								);*/
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
				$messages[] = array(
										"type"	=> "text",
										"text"	=>  "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้รับใช้หรือคะ...?"
									);
							$arrPostData['messages'][0]['type'] = "text";
							$arrPostData['messages'][0]['text']	=   "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้รับใช้หรือคะ...?";
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
				$messages[] = array(
										"type"	=> "text",
										"text"	=>  "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้รับใช้หรือคะ...?"
									);
							$arrPostData['messages'][0]['type'] = "text";
							$arrPostData['messages'][0]['text']	=   "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้รับใช้หรือคะ...?";
			  break;
			 
			  
			  case 'sticker':
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
				$messages[] = array(
										"type"	=> "text",
										"text"	=>  "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้รับใช้หรือคะ...?"
									);
							$arrPostData['messages'][0]['type'] = "text";
							$arrPostData['messages'][0]['text']	=   "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้รับใช้หรือคะ...?";			
				
				
			  break;
			}
			$arrTo[] = 	"U3ff18f16e94a80b192dd89eaaa8b7846";
			$arrTo[] = 	"Uef43a26cff64ac0a608c9acf9d0f70cd";
			$arrTo[] = 	"R4f8f29841c882322654fceb16459e06e";
			$arrTo[] = 	"Rcb9eca4ffaa7798fde9eed394f9a3321";			
			$arrPushData = array("to"=>"U3ff18f16e94a80b192dd89eaaa8b7846","messages"=>$messages);
			//$line->Multicast_Message($arrPushData);
			$line->Push_Message($arrPushData);
			$line->Reply_Message($arrPostData);
		}
		else{
			//ค้นหา agent ที่มี skill และพร้อม สนทนากับ ลูกค้า
			$results = $line->getAgentState($arrPostData);
			
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
			
			$line->AgentToTalk($messages);  
		}
	}

}

 
?>