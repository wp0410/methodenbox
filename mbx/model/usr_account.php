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
include_once 'app_result.php';

class UserAccount implements JsonSerializable
{
    public $usr_fst_name;
    public $usr_lst_name;
    public $usr_email;
    public $usr_register_date;
    public $usr_confirm_date;
    public $usr_login_date;
    
    private $usr_id;
    private $usr_pwd;
    private $usr_salt;
    private $usr_fail_count;
    private $usr_status;
    private $usr_role;
    private $usr_challenge;
	private $usr_permissions;
    
    private $db_conn;

    public function __construct($db_cn)
    {
        $this->db_conn = $db_cn;
        
        $this->usr_id = -1;
        $this->usr_fst_name = $this->usr_lst_name = $this->usr_email = '';
        $this->usr_register_date = $this->usr_confirm_date = $this->usr_login_date = '';
        $this->usr_pwd = $this->usr_salt = $this->usr_challenge = '';
        $this->usr_status = $this->usr_role = $this->usr_fail_count = 0;
		$this->usr_permissions = '';
    }
    
    public function jsonSerialize()
    {
        return array(
            'usr_id'            => $this->usr_id,
            'usr_fst_name'      => $this->usr_fstname,
            'usr_lst_name'      => $this->usr_lstname,
            'usr_email'         => $this->usr_email,
            'usr_register_date' => $this->usr_register_date,
            'usr_confirm_date'  => $this->usr_confirm_date,
            'usr_login_date'    => $this->usr_login_date,
            'usr_login_invalid' => $this->usr_fail_count,
            'usr_status'        => $this->usr_status,
            'usr_role'          => $this->usr_role,
			'usr_permissions'   => $this->usr_permissions
        );
    }
    
    public function getId()
    {
        return $this->usr_id;
    }
    
    public function getChallenge()
    {
        return $this->usr_challenge;
    }
    
    public function getRole()
    {
        return $this->usr_role;
    }
	
	public function getPermissions()
	{
		return $this->usr_permissions;
	}
    
    private function hashPassword($password, $salt)
    {
        $pwd = hash('sha256', $password . $salt);
        for ($cnt = 0; $cnt < 50000; $cnt++)
        {
            $pwd = hash('sha256', $pwd . $salt);
        }

        return $pwd;
    }

    public function createUserAccount($usr_fst_name, $usr_lst_name, $usr_email, $usr_pwd, $usr_role_name)
    {
        $this->usr_fst_name = $usr_fst_name;
        $this->usr_lst_name = $usr_lst_name;
        $this->usr_email    = $usr_email;

        // Hash the password using a random salt
        $this->usr_salt = Helpers::randomString(16);
        $this->usr_pwd = $this->hashPassword($usr_pwd, $this->usr_salt);
        
        $this->usr_register_date = Helpers::dateTimeString(time());
        $this->usr_confirm_date = '';
        $this->usr_login_date = '';
        $this->usr_fail_count = 0;
        $this->usr_status = 0;
        $this->usr_challenge = Helpers::randomString(32);
        
        if ($usr_role_name == 'ADMIN')
        {
            $this->usr_role = 1;
        }
        else
        {
            $this->usr_role = 0;
        }
        
        // Check for duplicates
        $usr_exists = 0;
        // $sql_stmt = 'select count(1) as usr_exists from ta_usr_account where usr_email = ? or (usr_fst_name = ? and usr_lst_name = ?);';
        $sql_stmt = 'select count(1) as usr_exists from ta_usr_account where usr_email = ?;';
        $stm_u1 = $this->db_conn->prepare($sql_stmt);
        // $stm_u1->bind_param('sss', $this->usr_email, $this->usr_fst_name, $this->usr_lst_name);
        $stm_u1->bind_param('s', $this->usr_email);
        if ($stm_u1->execute())
        {
            $stm_u1->bind_result($usr_exists);
            $stm_u1->fetch();
        }
        $stm_u1->free_result();
        $stm_u1->close();
        
        if ($usr_exists != 0)
        {
            return new AppResult(401);
        }
        
        $sql_stmt = 'insert into ta_usr_account( usr_fst_name, usr_lst_name, usr_email, usr_pwd, usr_salt, usr_register_date, usr_role, usr_challenge ) ' .
                    'values( ?, ?, ?, ?, ?, ?, ?, ? );';
        $stm_u2 = $this->db_conn->prepare($sql_stmt);
        $stm_u2->bind_param('ssssssis', 
                    $this->usr_fst_name, $this->usr_lst_name, $this->usr_email, 
                    $this->usr_pwd, $this->usr_salt, $this->usr_register_date, 
                    $this->usr_role, $this->usr_challenge);
        if (! $stm_u2->execute())
        {
            $stm_u2->close();
            return new AppResult(402);
        }
        $this->usr_id = $stm_u2->insert_id;
        $stm_u2->close();
		
		if ($usr_role_name == 'CLIENT')
		{
			return new AppResult(0);
		}
		
		$all_permissions = array('MTH.NEW', 'MTH.ADM'); // Permissions for role "UPLOAD"
		if ($usr_role_name == 'ADMIN')
		{
		    $all_permissions[] = 'ADM.USR'; // User Administration (PLACE HOLDER!)
		}
		
		$sql_stmt = 'insert into ta_usr_permissions( per_usr_id, per_permission ) values ( ?, ? );';
		$stm_u9 = $this->db_conn->prepare($sql_stmt);
		foreach ($all_permissions as $permission)
		{
		    $stm_u9->bind_param('is', $this->usr_id, $permission);
		    if (! $stm_u9->execute())
		    {
		        $stm_u9->close();
		        return new AppResult(402);
		    }
		}
		$stm_u9->close();
        
        return new AppResult(0);
    }
    
    public function modifyUserAccount($new_pwd, $usr_role_name = null)
    {
        // Hash the password using a random salt
        $this->usr_salt = Helpers::randomString(16);
        $this->usr_pwd = $this->hashPassword($new_pwd, $this->usr_salt);
        
        $this->usr_confirm_date = '';
        $this->usr_login_date = '';
        $this->usr_fail_count = 0;
        $this->usr_status = 0;

        if ($usr_role_name != null)
        {
            if ($usr_role_name == 'ADM')
            {
                $this->usr_role = 1;
            }
            else
            {
                $this->usr_role = 0;
            }
        }
        $this->usr_challenge = Helpers::randomString(32);
        
        $sql_stmt = 'update ta_usr_account set usr_pwd = ?, usr_salt = ?, usr_role = ?, usr_challenge = ? where usr_id = ?;';
        $stm_u5 = $this->db_conn->prepare($sql_stmt);
        $stm_u5->bind_param('ssisi', $this->usr_pwd, $this->usr_salt, $this->usr_role, $this->usr_challenge, $this->usr_id);
        if (! $stm_u5->execute())
        {
            $stm_u5->close();
            return new AppResult(402);
        }
        $stm_u5->close();
        
        return new AppResult(0);
    }
    
    public function deleteUserAccount()
    {
        $sql_stmt = 'delete from ta_usr_permissions where per_usr_id=?;';
        $stm_uad1 = $this->db_conn->prepare($sql_stmt);
        $stm_uad1->bind_param('i', $this->usr_id);
        $stm_uad1->execute();
        $stm_uad1->close();
        
        $sql_stmt = 'delete from ta_usr_account where usr_id=?;';
        $stm_uad2 = $this->db_conn->prepare($sql_stmt);
        $stm_uad2->bind_param('i', $this->usr_id);
        $stm_uad2->execute();
        $stm_uad2->close();
        
        return new AppResult(0);
    }
	
	private function findUserIdByEmail($usr_email): int
	{
		$usr_id = -1;
		
        $sql_stmt =
            'select usr_id ' .
            'from   ta_usr_account ' .
            'where  usr_email = ?;';
        $stm_u7 = $this->db_conn->prepare($sql_stmt);
        $stm_u7->bind_param('s', $usr_email);
        if ($stm_u7->execute())
        {
			$tmp_usr_id = -1;
            $stm_u7->bind_result($tmp_usr_id);
            if ($stm_u7->fetch() === true)
			{
				$usr_id = $tmp_usr_id;
			}
            $stm_u7->close();
        }
		
		return $usr_id;
	}
    
    public function checkByEmail($usr_email)
    {
		return ($this->findUserIdByEmail($usr_email) > 0);
    }
    
    public function loadByEmail($usr_email)
    {
        $usr_id = $this->findUserIdByEmail($usr_email);
		if ($usr_id > 0)
		{
			return $this->loadById($usr_id);
		}
		else
		{
			return new AppResult(403);         
		}
    }

    public function loadById($usr_id)
    {
        $sql_stmt = 
            'select usr_id, usr_fst_name, usr_lst_name, usr_email, usr_pwd, usr_salt, usr_register_date, usr_confirm_date, usr_login_date, usr_fail_count, ' .
            '       usr_status, usr_role, usr_challenge ' .
            'from   ta_usr_account ' .
            'where  usr_id = ?;';
        $stm_u3 = $this->db_conn->prepare($sql_stmt);
        $stm_u3->bind_param('i', $usr_id);
        if ($stm_u3->execute())
        {
            $stm_u3->bind_result(
				$this->usr_id, $this->usr_fst_name, $this->usr_lst_name, $this->usr_email, $this->usr_pwd, $this->usr_salt, $this->usr_register_date,
                $this->usr_confirm_date, $this->usr_login_date, $this->usr_fail_count, $this->usr_status, $this->usr_role, $this->usr_challenge);
                                 
            $sql_res = $stm_u3->fetch();
            $stm_u3->free_result();
            $stm_u3->close();
            if (! $sql_res)
            {
                return new AppResult(403);
            }
        }
        else
        {
            return new AppResult(404);
        }
		
		$sql_stmt = 'select per_permission from ta_usr_permissions where per_usr_id = ?;';
		$stm_u8 = $this->db_conn->prepare($sql_stmt);
		$stm_u8->bind_param('i', $usr_id);
		if ($stm_u8->execute())
		{
			$this->usr_permissions = '';
			$tmp_perm = '';
			$stm_u8->bind_result($tmp_perm);
			while($stm_u8->fetch() === true)
			{
				$this->usr_permissions = $this->usr_permissions . ':' . $tmp_perm;
			}
			$stm_u8->close();
		}
        
        return new AppResult(0);
    }
    
    private function verifyPassword($password)
    {
        $usr_pwd = $this->hashPassword($password, $this->usr_salt);
        return  ($usr_pwd === $this->usr_pwd);
    }
    
    private function updateStatus()
    {
        $sql_stmt =
            'update ta_usr_account ' .
            '   set usr_register_date = ?, ' .
            '       usr_confirm_date  = ?, ' .
            '       usr_login_date    = ?, ' .
            '       usr_fail_count    = ?, ' .
            '       usr_status        = ? ' .
            'where  usr_id = ?;';
        $stm_u4 = $this->db_conn->prepare($sql_stmt);
        $stm_u4->bind_param('sssiii', $this->usr_register_date, $this->usr_confirm_date, $this->usr_login_date, $this->usr_fail_count, $this->usr_status, $this->usr_id);
        $stm_u4->execute();
        $stm_u4->close();
    }
    
    public function lockUserAccount()
    {
        $this->usr_status = 2;
        $this->updateStatus();
    }
    
    public function unlockUserAccount()
    {
        $this->usr_status = 1;
        $this->updateStatus();
    }

    public function userLogin($email_addr, $password)
    {
        $res = $this->loadByEmail($email_addr);
        if (! $res->isOK())
        {
            return new AppResult(410);
        }
        
        if ($this->usr_status == 0)
        {
            // Registration is not yet confirmed
            return new AppResult(411);
        }
        if ($this->usr_status != 1)
        {
            // User account is locked
            return new AppResult(412);
        }
        
        if ($this->verifyPassword($password))
        {
            $this->usr_fail_count = 0;
            $res = new AppResult(0);
        }
        else
        {
            $this->usr_fail_count += 1;
            if ($this->usr_fail_count >= GlobalParameter::$applicationConfig['userAccountFailLimit'])
            {
                $this->usr_status = 2;
            }
            $res = new AppResult(400);
        }
        
        $this->usr_login_date = Helpers::dateTimeString(time());
        $this->updateStatus();
        
        return $res;
    }
    
    public function checkUserPassword($password)
    {
        if ($this->verifyPassword($password))
        {
            return new AppResult(0);
        }
        else 
        {
            return new AppResult(413);
        }
    }
    
    public function verifyChallenge($email_addr, $password, $challenge)
    {
        $res = $this->loadByEmail($email_addr);
        if (! $res->isOK())
        {
            return new AppResult(410);
        }
        
        if ($this->usr_status > 1)
        {
            // User account is locked
            return new AppResult(412);
        }
        
        $res = new AppResult(0);
        if ($this->verifyPassword($password))
        {
            $this->usr_fail_count = 0;
            
            if ($this->usr_status == 0)
            {
                // Verify the challenge
                if ($challenge === $this->usr_challenge)
                {
                    $this->usr_challenge = '';
                    $this->usr_confirm_date = Helpers::dateTimeString(time());
                    $this->usr_status = 1;
                }
                else
                {
                    $this->usr_fail_count += 1;
                    if ($this->usr_fail_count >= GlobalParameter::$applicationConfig['userAccountFailLimit'])
                    {
                        $this->usr_status = 3;
                    }
                    $res = new AppResult(420);
                }
            }
        }
        else
        {
            $this->usr_fail_count += 1;
            if ($this->usr_fail_count >= GlobalParameter::$applicationConfig['userAccountFailLimit'])
            {
                $this->usr_status = 2;
            }
            $res = new AppResult(400);
        }
        
        $this->usr_login_date = Helpers::dateTimeString(time());
        $this->updateStatus();
        
        return $res;
    }
    
    public function privilegesGrant($privileges)
    {
        $all_permissions = array();
        
        foreach ($privileges as $priv)
        {
            switch($priv)
            {
                case 'CLT':
                case 'NON':
                    break;
                case 'UPL':
                    $all_permissions[] = 'MTH.NEW';
                    $all_permissions[] = 'MTH.ADM';
                    break;
                case 'ADM':
                    $all_permissions[] = 'ADM.USR';
                    break;
                default:
                    break;
            }
        }
        $sql_stmt = 'insert into ta_usr_permissions( per_usr_id, per_permission ) values ( ?, ? );';
        $stm_u10 = $this->db_conn->prepare($sql_stmt);
        foreach ($all_permissions as $permission)
        {
            $stm_u10->bind_param('is', $this->usr_id, $permission);
            if (! $stm_u10->execute())
            {
                $stm_u10->close();
                return new AppResult(402);
            }
        }
        $stm_u10->close();
        
    }
    
    public function privilegesRevoke($privileges)
    {
        $all_permissions = array();
        
        foreach($privileges as $priv)
        {
            switch($priv)
            {
                case 'CLT':
                case 'NON':
                    break;
                case 'UPL':
                    $all_permissions[] = 'MTH.NEW';
                    $all_permissions[] = 'MTH.ADM';
                    break;
                case 'ADM':
                    $all_permissions[] = 'ADM.USR';
                    break;
                default:
                    break;
            }
        }
        
        $sql_stm = 'delete from ta_usr_permissions where per_usr_id=? and per_permission=?;';
        $stm_u11 = $this->db_conn->prepare($sql_stm);
        foreach($all_permissions as $permission)
        {
            $stm_u11->bind_param('is', $this->usr_id, $permission);
            if (! $stm_u11->execute())
            {
                $stm_u11->close();
                return new AppResult(402);
            }
        }
        $stm_u11->close();
    }
}
?>