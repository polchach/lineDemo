<?php
require_once('config.php');
class pbxDB {

    var $cdr; 
	var $ast;
    function __construct() {
		$this->cdr = $GLOBALS['concdr'];
		$this->ast = $GLOBALS['conast'];
	}
	function set_pbx_events($data) 
    {
		foreach ($data as $result) {
			extract($result);
			$sql= "INSERT INTO pbx_events SET time_event = NOW(),time_active=NOW(),agentId='".$agentId."',ext_src='".$ext_src."',phone_status='".$phone_status."',agent_status='".$agent_status."',line_count='".$line_count."'
					ON DUPLICATE KEY UPDATE time_event = NOW(),time_active=NOW(),ext_src='".$ext_src."',phone_status='".$phone_status."',agent_status='".$agent_status."',line_count='".$line_count."'";
			$this->cdr->query($sql);
		}
    }
//================================= Zendesk ==================================================================
	
	function set_ticket($ticketid,$data) 
    {
		foreach ($data as $result) {
			$sql= "INSERT INTO kaidee_tickets SET ticketid='".$ticketid."',calldate = NOW(),src='".$result['src']."',dst='".$result['dst']."',callid='".$result['callid']."',callstate='".$result['callstate']."'
					ON DUPLICATE KEY UPDATE  calldate = NOW(),src='".$result['src']."',dst='".$result['dst']."',callstate='".$result['callstate']."'";
			$this->cdr->query($sql);
		}
    }
	function del_ticket($callid) 
    {
		$sql= "DELETE FROM kaidee_tickets WHERE callid='".$callid."'";
			$this->cdr->query($sql);
    }
//================================= Zendesk ==================================================================
	
	function set_line_profile($data) 
    {
		foreach ($data as $result) {
			extract($result);
			$sql= "INSERT INTO line_profile SET displayName ='".$displayName."',userId='".$userId."',pictureUrl='".$pictureUrl."',phone='".$phone."',email='".$email."'
					ON DUPLICATE KEY UPDATE displayName ='".$displayName."',pictureUrl='".$pictureUrl."',phone='".$phone."',email='".$email."'";
			$this->cdr->query($sql);
		}
    }
	function set_line_mdr($data) 
    {
		foreach ($data as $result) {
			extract($result);
			$sql= "INSERT INTO line_mdr SET uniqueid='".$uniqueid."',m_date = NOW(),m_src='".$m_src."',m_dst='".$m_dst."',m_type='".$m_type."',m_message='".$m_message."',m_duration='".$m_duration."'
					ON DUPLICATE KEY UPDATE  m_date = NOW(),m_src='".$m_src."',m_dst='".$m_dst."',m_type='".$m_type."',m_message='".$m_message."',m_duration='".$m_duration."'";
			$this->cdr->query($sql);
		}
    }
	function del_line($callid) 
    {
		$sql= "DELETE FROM kaidee_tickets WHERE callid='".$callid."'";
			$this->cdr->query($sql);
    }
//================================= Activity ==================================================================
	
	function _select_cdr($sql) 
    {
		$results=$this->cdr->query($sql);
		return $results;
		
    }
	function _insert_cdr($sql) 
    {
		$this->cdr->query($sql);
    }
	function _update_cdr($sql) 
    {
		$this->cdr->query($sql);
    }
	function _delete_cdr($sql) 
    {
		$this->cdr->query($sql);
    }

	
	function _select_ast($sql) 
    {

		$results=$this->ast->query($sql);
		return $results;
		
    }
	function _insert_ast($sql) 
    {
		$this->ast->query($sql);
    }
	function _update_ast($sql) 
    {
		$this->ast->query($sql);
    }
	function _delete_ast($sql) 
    {
		$this->ast->query($sql);
    }
	
}
?>