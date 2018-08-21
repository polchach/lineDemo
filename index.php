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
if(sizeof($array_message)==3){
	if($array_message[0] == 'itts'){
		
		$name = $line -> createAgentProfile($userId,$array_message[1]);
		if($name==''){
				$arrPostData['messages'][0]['type'] = "text";
				$arrPostData['messages'][0]['text'] = "สวัสดีค่ะคุณยังไม่ได้เพิ่มเป็นเพื่อนค่ะ\nกรุณาเพิ่มเป็นเพื่อนด้วยนะคะ";
		}else{
				$arrPostData['messages'][0]['type'] = "text";
				$arrPostData['messages'][0]['text'] = "สวัสดีค่ะคุณ " .$name. "\nกรุณารอสักครู่ ระบบกำลังตรวจสอบสิทธิ์";
			$result = $line -> checkAuth($array_message);
			if($result['userId']==''){
					$arrPostData['messages'][0]['type'] = "text";
					$arrPostData['messages'][0]['text'] = "คุณไม่มีสิทธิ์ใช้งานค่ะ\nกรุณาตรวจข้อมูลอีกครั้งนะคะ";
			}else{
					$arrPostData['messages'][0]['type'] = "text";
					$arrPostData['messages'][0]['text'] = "คุณ " .$name. " คะ\nระบบพร้อมใช้งานค่ะ";

			}
		
		}
	}
}else{
  $results = $line -> getUserProfile($userId);
  
  if(sizeof($results)< 2){
		$arrPostData['messages'][0]['type'] = "text";
		$arrPostData['messages'][0]['text'] = "สวัสดีค่ะคุณยังไม่ได้เพิ่มเป็นเพื่อนค่ะ\nกรุณาเพิ่มเป็นเพื่อนด้วยนะคะ";
  }else{
		$name = $results['displayName'];
		$pic = $results['pictureUrl'];
		
		switch($arrJson['events'][0]['message']['type']){
		  case 'text':
			$msg = explode('/',$arrJson['events'][0]['message']['text']);
			if(sizeof($msg) == 5){
				if($msg[0]=='ดูดวง'){
					$messages[] = array(
									"type"	=> "text",
									"text"	=> "คุณ ".$name." คะ...\nผู้ที่เกิดใน วัน/เดือน/ปี เวลา ตามข้อมูล\nจะเป็นผู้ที่มีความรักอันบริสุทธิ์ ซึ่งหมายความว่า ..."
								);
					$arrPostData['messages'][0]['type'] = "text";
					$arrPostData['messages'][0]['text']	= "คุณ ".$name." คะ...\nผู้ที่เกิดใน วัน/เดือน/ปี เวลา ตามข้อมูล\nจะเป็นผู้ที่มีความรักอันบริสุทธิ์ ซึ่งหมายความว่า ...";
				}else{
					
				}
				
			}else if(sizeof($msg) == 4){
				
					$messages[] = array(
									"type"	=> "text",
									"text"	=> "คุณ ".$name." คะ...\nผู้ที่เกิดใน วัน/เดือน/ปี เวลา ตามข้อมูล\nจะเป็นผู้ที่มีความรักอันบริสุทธิ์ ซึ่งหมายความว่า ..."
								);
					$arrPostData['messages'][0]['type'] = "text";
					$arrPostData['messages'][0]['text']	= "คุณ ".$name." คะ...\nผู้ที่เกิดใน วัน/เดือน/ปี เวลา ตามข้อมูล\nจะเป็นผู้ที่มีความรักอันบริสุทธิ์ ซึ่งหมายความว่า ...";
			}else{
				switch($arrJson['events'][0]['message']['text']){
					case 'ดูดวง':
						$messages[] = array(
									"type"	=> "text",
									"text"	=> "คุณ ".$name." คะ...\nกรุณาพิมพิ์ วัน/เดือน/ปี/เวลา เกิด...\nตัวอย่าง 10/01/2518/09:15:00"
								);
						$arrPostData['messages'][0]['type'] = "text";
						$arrPostData['messages'][0]['text']	= "คุณ ".$name." คะ...\nกรุณาพิมพิ์ วัน/เดือน/ปี/เวลา เกิด...\nตัวอย่าง 10/01/2518/09:15:00";
						break;
					default:
						$messages[] = array(
									"type"	=> "text",
									"text"	=> "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้ทางเรารับใช้หรือคะ...?"
								);
						$arrPostData['messages'][0]['type'] = "text";
						$arrPostData['messages'][0]['text']	=  "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้ทางเรารับใช้หรือคะ...?";
						break;
					}
					
			}
			
			
		  break;
		  
		  case 'image':
			$messages[] = array(
								"type"	=> "text",
								"text"	=> "คุณ ".$name." ส่งรูปภาพ"
							);
			$messages[] = array(
									"type"	=> "text",
									"text"	=> "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้ทางเรารับใช้หรือคะ...?"
								);
						$arrPostData['messages'][0]['type'] = "text";
						$arrPostData['messages'][0]['text']	=  "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้ทางเรารับใช้หรือคะ...?";
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
									"text"	=> "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้ทางเรารับใช้หรือคะ...?"
								);
						$arrPostData['messages'][0]['type'] = "text";
						$arrPostData['messages'][0]['text']	=  "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้ทางเรารับใช้หรือคะ...?";
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
									"text"	=> "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้ทางเรารับใช้หรือคะ...?"
								);
						$arrPostData['messages'][0]['type'] = "text";
						$arrPostData['messages'][0]['text']	=  "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้ทางเรารับใช้หรือคะ...?";
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
									"text"	=> "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้ทางเรารับใช้หรือคะ...?"
								);
						$arrPostData['messages'][0]['type'] = "text";
						$arrPostData['messages'][0]['text']	=  "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้ทางเรารับใช้หรือคะ...?";
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
									"text"	=> "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้ทางเรารับใช้หรือคะ...?"
								);
						$arrPostData['messages'][0]['type'] = "text";
						$arrPostData['messages'][0]['text']	=  "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้ทางเรารับใช้หรือคะ...?";
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
									"text"	=> "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้ทางเรารับใช้หรือคะ...?"
								);
						$arrPostData['messages'][0]['type'] = "text";
						$arrPostData['messages'][0]['text']	=  "สวัสดีค่ะคุณ ".$name."...\nมีอะไรให้ทางเรารับใช้หรือคะ...?";				
			/*$source = array(
								"type"		=> "room",
								"roomId"	=> $arrJson['events'][0]['source']['roomId']
							);
			$arrPostData['messages'][0]['type'] = "text";
			$arrPostData['messages'][0]['text']	=    "คุณ ".$name." ส่งข้อความที่...\nRoomId: " .$arrJson['events'][0]['source']['roomId'];
			$arrPostData['type'] 		= "follow";
			$arrPostData['timestamp'] 	= $arrJson['events'][0]['timestamp']; 
			$arrPostData['source'][0]['type'] 		= "user";
			$arrPostData['source'][0]['userId'] 	= "U93a99a19a48ec6a47a06145847dc43b0";
		
			$arrPostData['type'] 		= "join"; 
			$arrPostData['source'][0]['type'] 		= "room";
			$arrPostData['source'][0]['roomId'] 	= $arrJson['events'][0]['source']['roomId'];*/
		
			
		  break;
	  }
  }
  
  
  
  
}
$arrTo[] = 	"U3ff18f16e94a80b192dd89eaaa8b7846";
$arrTo[] = 	"Uef43a26cff64ac0a608c9acf9d0f70cd";
$arrTo[] = 	"R4f8f29841c882322654fceb16459e06e";
$arrTo[] = 	"Rcb9eca4ffaa7798fde9eed394f9a3321";			
$arrPushData = array("to"=>"U3ff18f16e94a80b192dd89eaaa8b7846","messages"=>$messages);
//$line->Multicast_Message($arrPushData);
$line->Push_Message($arrPushData);
$line->Reply_Message($arrPostData);
 
?>