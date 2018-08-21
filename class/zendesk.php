#!/usr/bin/php -q
<?php
require_once('class.WSS.php');

$ws = new ws(array
(
	'host' => '127.0.0.1',
	'port' => 8181,
	'path' => ''
));
require 'class.Zendesk.php';
$zen = new Zendesk();

$act = $argv[1];
$subject = $argv[2];
$value = $argv[3];
$data = $argv[4];
$arr_data = explode(':',$data);
switch($act){
	
	case 'ringing':
	{
		if(sizeof($arr_data)== 4){
			$ivrmenu = $zen->getIvrMenu($arr_data[1]);
			if(strlen($arr_data[0]) < 7){
				$result=$zen->createTicket($ivrmenu,$subject,$value,$arr_data);
			
				if($result['ticketid'] != ''){
					$ws->send($result['exten'].','.$result['phone'].','.$result['callid'].','.$result['ticketid']);
				}
			}
			
		}
		
	}
	break;
	case 'selectmenu':
	{
		if(sizeof($arr_data)== 5){
			$zen->setIVR($arr_data);
			$ws->send($arr_data[0].','.$arr_data[1].','.$arr_data[2].','.$arr_data[3]);
		}
	}
	break;
	case 'hangup':
	{
		$zen->setEndCall($arr_data[1]);
	}
	break;
	
}

?>