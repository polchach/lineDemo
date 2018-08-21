<?php
if (!@include_once(getenv('FREEPBX_CONF') ? getenv('FREEPBX_CONF') : '/etc/freepbx.conf')) {
      include_once('/etc/asterisk/freepbx.conf');
}
require_once('class.pbxDB.php');

class pbxman {
	
//================================= PBX Control ==================================================================
	function callactive() 
    { 
		global $astman;
		$foo = $astman->Command("core show channels concise");
		foreach(explode("\n", $foo['data']) as $line)
		if (preg_match("/Up/i", $line) || preg_match("/!Dial!/i", $line)) 
		{
				$pieces = explode("!", $line);
				$regex = "~".preg_quote($pieces[12],"~")."!(.*?)!(.*?)!(.*?)!(.*?)!(.*?)!(.*?)!(.*?)!(.*?)!(.*?)!(.*?)!(.*?)!(.*?)!~";
				preg_match($regex,$foo['data'],$to);
					$data = array(
							'Event' => 'Active',
							'Channel' => $pieces[0],
							'Exten'=> $pieces[2],
							'srcChannel' => $pieces[12],
							'dstChannel' => $to[12],
							'State' => $pieces[4],
							'src'=>$pieces[7],
							'dst'=> $to[7],
						);
					return $data;

		}
    }
	function survey($prefix,$number,$exten) 
	{
		global $astman;
		$data = array(
					"Channel" => "Local/$prefix$number@outbound-allroutes",
					"CallerID" => $number,
					"Context" => "from-internal",
					"Exten" => $exten,
					"Priority" => "1");
		$astman->Originate($data);
	}
	function dial($prefix,$number,$exten) 
	{
		global $astman;
		$data = array(
					"Channel" => "SIP/$exten",
					"CallerID" => $number,
					"Context" => "outbound-allroutes",
					"Exten" => $number,
					"Priority" => "1");
		$astman->Originate($data);
	}
	
	function rout2exten($channel,$exten) 
    {
		global $astman;
		return $astman->Redirect($channel,'' , $exten, 'ext-local', '1');
    }
	function rout2outside($channel,$exten) 
    {
		global $astman;
		return $astman->Redirect($channel, '', $exten, 'outbound-allroutes', '1');
    }
	function hangup($channel) 
    {
		global $astman;
		return $astman->Hangup($channel);
    }
	function ExtensionStatus($exten) 
    { 
		global $astman;
		$result = $astman->ExtensionState($exten, 'from-internal');
		switch($result['Status']){
										case -1:
										$status="UNKNOWN";
										break;
										
										case 0:
										$status="IDLE";
										break;
										
										case 1:
										$status="INUSE";
										break;
										
										case 2:
										$status="BUSY";
										break;
										
										case 4:
										$status="UNAVAILABLE";
										break;
										
										case 8:
										$status="RINGING";
										break;
										
										case 16:
										$status="ONHOLD";
										break;
									}
			$data = array(
					"Event" => "ExtensionStatus",
					"Exten" => $result['Exten'],
					"Status" => $status
					);
			echo json_encode($data);
    }

	function ExtensionStatusAll() 
    {
		$db = new pbxDB();
		global $astman;
		$data = array();
		$sql = "select id from devices";
		$results = $db->_select_ast($sql);
		while ($row = $results->fetch_assoc()) {
			$result = $astman->ExtensionState($row['id'], 'from-internal');
				switch($result['Status']){
										case -1:
										$status="UNKNOWN";
										break;
										
										case 0:
										$status="IDLE";
										break;
										
										case 1:
										$status="INUSE";
										break;
										
										case 2:
										$status="BUSY";
										break;
										
										case 4:
										$status="UNAVAILABLE";
										break;
										
										case 8:
										$status="RINGING";
										break;
										
										case 16:
										$status="ONHOLD";
										break;
									}
			$exten = array(
					"Event" => "ExtensionStatus",
					"Exten" => $result['Exten'],
					"Status" => $status
					);
			array_push($data,$exten);
		}
		echo json_encode($data);
    }
	function downloadlog($callid) 
    {
		$db = new pbxDB();
		$id =1;
		if($callid != ''){
			$query = "SELECT * FROM cdr WHERE uniqueid LIKE '".$callid."%'";
		
			$results = $db->_select_cdr($query);
			while ($row = $results->fetch_assoc()) {
				$data[] = array(
					'id' => $id++,
					'calldate' => $row['calldate'],
					'callid' => $row['uniqueid'],
					'src' => $row['src'],
					'dst' => $row['dst'],
					'channel' => $row['channel'],
					'dstchannel' => $row['dstchannel'],
					'duration' => $row['duration'],
					'billsec' => $row['billsec'],
					'lastapp' => $row['lastapp'],
					'disposition' => $row['disposition'],
					'pathfile' => $row['recordingfile']
				);
			}
			echo json_encode($data);
		}
	
    }
	function downloadfile($callid) 
    {
		$db = new pbxDB();
		if($callid != ''){
			$query = "SELECT recordingfile FROM cdr WHERE uniqueid = '$callid'";

			$results = $db->_select_cdr($query);
			$row = $results->fetch_assoc();
			$file = $row['recordingfile'];
		
			if (file_exists($file)) {
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="'.basename($file).'"');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($file));
				readfile($file);
				exit;
			}
		}
		
    }
	function gsm2wav($callid)
    {
		$db = new pbxDB();
		$outputFolder = "/var/www/html/ws.pbx/tmp";
		chmod($outputFolder, 0777);
		$query = "SELECT recordingfile FROM cdr WHERE uniqueid = '$callid'";

		$results = $db->_select_cdr($query);
		$row = $results->fetch_assoc();
		$localfile = $row['recordingfile'];
		chmod($localfile, 0777);
		$type= strrchr($localfile,".");
		if($type == '.gsm'){
			$outputFile = $outputFolder."/" .$callid. ".wav";
				$cmd = "sox $localfile -r 8000 -c 1 -e signed-integer $outputFile";
				shell_exec($cmd);
				//$command = "mpg123 -w ".$outputFile.".wav $outputMP3";
				//shell_exec($command) 
				//header("Content-Type: audio/wav");
				//echo file_get_contents($outputFile);
				return($outputFile);
		}else if($type == '.wav'){
			//header("Content-Type: audio/wav");
			//echo file_get_contents($localfile);
			return($localfile);
		}	
		
	}
	function gsm2wav_test($callid)
    {
		$outputFolder = "/var/www/html/ws.pbx/tmp";
		
		
		$localfile = "/var/spool/asterisk/monitor/2018/04/01/exten-147-2003-20180401-215209-1522594322.150.gsm";
		chmod($localfile, 0777);
		$type= strrchr($localfile,".");
		if($type == '.gsm'){
			
			$outputFile = $outputFolder."/" .$callid. ".wav";
			chmod($outputFile, 0777);
				$cmd = "sox ".$localfile." -r 44100 -a ".$outputFile;
				shell_exec($cmd);
				//header("Content-Type: audio/wav");
				//echo file_get_contents($outputFile);
		}else if($type == '.wav'){
			//header("Content-Type: audio/wav");
			//echo file_get_contents($localfile); 
		}	
		
	}
	function playsound($callid)
    {
		$outputFolder = "/var/www/html/ws.pbx/tmp";
		$localfile = $outputFolder."/".$callid.".wav";
		header("Content-Type: audio/wav");
		echo file_get_contents($localfile); 
		
	}
}
?>
