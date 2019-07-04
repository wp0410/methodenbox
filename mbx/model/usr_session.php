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
include_once 'aux_parameter.php';
include_once 'aux_text.php';
include_once 'app_result.php';
include_once 'usr_permission.php';

class UserSession implements JsonSerializable
{
    public  $ses_usr_id;
    public  $ses_usr_email;
	public  $ses_usr_full_name;

    private $ses_id;
	
	private $ses_start_time;
    private $ses_end_time;
    private $ses_last_change;
    private $ses_salt;
	private $ses_permissions; // TYPE UserPermission
	
    private $db_conn;

    public function __construct($db_cn)
    {
        $this->db_conn = $db_cn;
        
        $this->ses_id = $this->ses_usr_id = -1;
        $this->ses_start_time = $this->ses_end_time = $this->ses_last_change = $this->ses_usr_email = $this->ses_salt = '';
		$this->ses_permissions = new UserPermission($db_cn, -1);
    }

    public function jsonSerialize()
    {
        return array(
            'ses_id'            => $this->ses_id,
            'ses_start_time'    => $this->ses_start_time,
            'ses_end_time'      => $this->ses_end_time,
            'ses_last_change'   => $this->ses_last_change,
            'ses_usr_id'        => $this->ses_usr_id,
			'ses_usr_email'     => $this->ses_usr_email,
			'ses_usr_full_name' => $this->ses_usr_full_name,
            'ses_salt'          => $this->ses_salt,
			'ses_permissions'   => 'VOID' //json_encode($this->ses_permissions)
        );
    }
    
    public function getId(): string
    {
        return $this->ses_id;
    }
    
    public function getUsrId(): int
    {
        return $this->ses_usr_id;
    }

	public function getSessionDescriptor(): array
	{
		return array('sid' => $this->getId(), 'uid' => $this->getUsrId(), 'hash' => $this->getSessionHash());
	}
    
    public function startSession($usr_id, $usr_perm)
    {
        $res = '';
        
        $sql_stmt = 'delete from ta_usr_session where ses_usr_id = ?;';
        $stm_se4 = $this->db_conn->prepare($sql_stmt);
        $stm_se4->bind_param('i', $usr_id);
        $stm_se4->execute();
        $stm_se4->close();
        
        $this->ses_start_time = Helpers::dateTimeString(time());
        $this->ses_end_time = Helpers::dateTimeString(time() + GlobalParameter::$applicationConfig['userSessionLifetimeSec']);
        $this->ses_last_change = $this->ses_start_time;
        $this->ses_usr_id = $usr_id;
        $this->ses_salt = Helpers::randomString(16);
		$this->ses_permissions = new UserPermission($this->db_conn, $usr_id, $usr_perm);
		$perm_string = $this->ses_permissions->getPermissionsString();

        $sql_stmt =
            'insert into ta_usr_session( ses_start_time, ses_end_time, ses_last_change, ses_usr_id, ses_usr_email, ses_usr_full_name, ses_salt, ses_permissions ) ' .
            'values ( ?, ?, ?, ?, ?, ?, ?, ? );';
        $stm_se1 = $this->db_conn->prepare($sql_stmt);
        $stm_se1->bind_param(
			'sssissss', 
			$this->ses_start_time, $this->ses_end_time, $this->ses_last_change, 
			$this->ses_usr_id, $this->ses_usr_email, $this->ses_usr_full_name, $this->ses_salt, $perm_string);
        if (! $stm_se1->execute())
        {
            $res = new AppResult(501);
        }
        else
        {
            $this->ses_id = $stm_se1->insert_id;
            $res = new AppResult(0);
        }
        $stm_se1->close();
        
        return $res;
    }
    
    private function loadSession($ses_id)
    {
		$perm_string = '';
        $sql_stmt = 
			"SELECT ses_id, ses_start_time, ses_end_time, ses_last_change, ses_usr_id, ses_usr_email, ses_usr_full_name, 
					ses_salt, ses_permissions
			 FROM   ta_usr_session
			 WHERE  ses_id = ?";
        $stm_se5 = $this->db_conn->prepare($sql_stmt);
        $stm_se5->bind_param('i', $ses_id);
        if ($stm_se5->execute())
        {
            $stm_se5->bind_result(
                $this->ses_id, $this->ses_start_time, $this->ses_end_time, $this->ses_last_change, $this->ses_usr_id, 
				$this->ses_usr_email, $this->ses_usr_full_name, $this->ses_salt, $perm_string);
        
            $sql_res = $stm_se5->fetch();
            $stm_se5->close();
        }
        
        if (! $sql_res)
        {
            $this->ses_id = -1;
            $this->ses_usr_id = -1;
            
            return new AppResult(502);
        }
		
		$this->ses_permissions = new UserPermission($this->db_conn, $this->ses_usr_id, $perm_string);
        return new AppResult(0);
    }
    
    public function getSessionHash()
    {
        $serialized = json_encode($this);
        $result = hash('sha256', $serialized);
        
        for($cnt = 0; $cnt < 2342; $cnt++)
        {
            $result = hash('sha256', $serialized . $result);
        }
        
        return $result;
    }
    
    private function isSessionValid($ses_usr_id, $hash)
    {
        return
            ($this->ses_id > 0) &&
            ($this->ses_usr_id == $ses_usr_id) &&
            (Helpers::dateTimeString(time()) <= $this->ses_end_time) &&
            ($hash == $this->getSessionHash());
    }
    
    private function extendSession()
    {
        $this->ses_end_time = Helpers::dateTimeString(time() + GlobalParameter::$applicationConfig['userSessionLifetimeSec']);
        $this->ses_last_change = Helpers::dateTimeString(time());
        $this->ses_salt = Helpers::randomString(16);
        
        $sql_stmt = "UPDATE ta_usr_session SET ses_end_time = ?, ses_last_change = ?, ses_salt = ? WHERE ses_id = ?";
        
        $stm_se2 = $this->db_conn->prepare($sql_stmt);
        $stm_se2->bind_param('sssi', $this->ses_end_time, $this->ses_last_change, $this->ses_salt, $this->ses_id);
        $stm_se2->execute();
        $stm_se2->close();
        
        return new AppResult(0);
    }
    
    public function closeSession()
    {
        if ($this->ses_id > 0)
        {
            $sql_stmt = 'DELETE FROM ta_usr_session WHERE ses_id = ?';
            $stm_se3 = $this->db_conn->prepare($sql_stmt);
            $stm_se3->bind_param('i', $this->ses_id);
            $stm_se3->execute();
            $stm_se3->close();
        }
        
        return new AppResult(0);
    }
    
    public function validateSession($session_user, $do_extend = true)
    {
        $res = $this->loadSession($session_user['sid']);
        if (! $res->isOK())
        {
            return $res;
        }
        if ($this->isSessionValid($session_user['uid'], $session_user['hash']))
        {
            if ($do_extend)
            {
                $res =  $this->extendSession();
            }
            else
            {
                $res = new AppResult(0);
            }
        }
        else
        {
            $this->ses_id = -1;
            $this->ses_usr_id = -1;
            $res = new AppResult(502); 
        }
        
        return $res;
    }
    
    public function isAuthenticated()
    {
        return ($this->ses_id != -1) && ($this->ses_usr_id != -1);
    }
    
	public function checkPermission($permission_tag)
	{
		if (empty($this->ses_permissions))
		{
			return false;
		}
		
		return $this->ses_permissions->checkPermission($permission_tag);
	}
}
?>