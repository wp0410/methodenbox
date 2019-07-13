<?php
//---------------------------------------------------------------------------------------
//  Copyright (c) 2018, 2019 Walter Pachlinger (walter.pachlinger@gmail.com)
//    
//  Licensed under the Apache License, Version 2.0 (the "License"); you may not use this 
//  file except in compliance with the License. You may obtain a copy of the License at
//      http://www.apache.org/licenses/LICENSE-2.0
//  Unless required by applicable law or agreed to in writing, software distributed under 
//  the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF 
//  ANY KIND, either express or implied. See the License for the specific language 
//  governing permissions and limitations under the License.
//----------------------------------------------------------------------------------------
include_once 'aux_helpers.php';

interface ILogEntry
{
	public function getUserId(): string;
	public function getLogTimestamp(): string;
	public function getRemoteIp(): string;
	public function getRemoteHost(): string;
}

class InvalidLoginAttemptEntry implements ILogEntry, JsonSerializable
{
	public $usr_email;
	public $usr_password;
	public $rem_ip_address;
	public $rem_host_name;
	public $err_code;
	public $err_text;
	
	private $log_timestamp;
	
	public function __construct()
	{
		$this->usr_email = $this->usr_password = $this->rem_ip_address = $this->rem_host_name = $this->err_text = '';
		$this->err_code = 999;
	}
	
	public function getUserId(): string
	{
		if (empty($this->usr_email))
		{
			$this->usr_email = 'UNKNOWN';
		}
		return $this->usr_email;
	}
	
	public function getLogTimestamp(): string
	{
		if (empty($this->log_timestamp))
		{
			$this->log_timestamp = Helpers::dateTimeString(time());
		}
		return $this->log_timestamp;
	}
	
	public function getRemoteIp(): string
	{
		if (empty($this->rem_ip_address))
		{
			$this->rem_ip_address = 'UNKNOWN';
		}
		return $this->rem_ip_address;
	}
	
	public function getRemoteHost(): string
	{
		if (empty($this->rem_host_name))
		{
			if (($this->rem_ip_address == '127.0.0.1') || ($this->rem_ip_address == '::1'))
			{
				$this->rem_host_name = 'localhost';
			}
			else
			{
				$this->rem_host_name = 'UNKNOWN';
			}
		}
		
		return $this->rem_host_name;
	}
	
    public function jsonSerialize()
    {
        return array(
			'log_timestamp'  => $this->log_timestamp,
			'usr_email'      => $this->getUserId(),
			'usr_password'   => $this->usr_password,
			'err_code'       => $this->err_code,
			'err_text'       => $this->err_text,
			'rem_ip_address' => $this->getRemoteIp(),
			'rem_host_name'  => $this->getRemoteHost()
        );
    }
}

class SecurityLog
{
	private $db_conn;
	
	public function __construct($db_cn)
	{
		$this->db_conn = $db_cn;
	}
	
	public function storeLogEntry($entry)
	{
		$log_usr_id = $entry->getUserId();
		$log_timestamp = $entry->getLogTimestamp();
		$log_rem_ip = $entry->getRemoteIp();
		$log_rem_host = $entry->getRemoteHost();
		$log_detail = json_encode($entry);
		
		$sql_stmt = 'INSERT INTO ta_log_security( log_timestamp, log_client_id, log_remote_ip, log_remote_host, log_detail ) VALUES( ?, ?, ?, ?, ? )';
		$stm_slg1 = $this->db_conn->prepare($sql_stmt);
		$stm_slg1->bind_param('sssss', $log_timestamp, $log_usr_id, $log_rem_ip, $log_rem_host, $log_detail);
		$stm_slg1->execute();
		$stm_slg1->close();
	}
}
?>
