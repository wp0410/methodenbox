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

class UserPermission implements JsonSerializable
{
	private $db_conn;
	private $usr_id;
	private $permissions;
	
	public function __construct($db_cn, $usr_id, $perm_string = null)
    {
        $this->db_conn = $db_cn;
		$this->usr_id = $usr_id;
		
		if (empty($perm_string))
		{
			$this->permissions = null;
		}
		else
		{
			$this->permissions = explode(';', $perm_string);
		}
	}

    public function jsonSerialize()
    {
        return array(
			'usr_id'      => $this->usr_id,
			'permissions' => $this->getPermissionsString()
		);
	}
	
	public function getPermissions(): array
	{
		if (empty($this->permissions))
		{
			if ($this->usr_id > 0)
			{
				$this->permissions = $this->loadUsrPermissions($this->usr_id);
			}
			else
			{
				$this->permissions = $this->loadUnauthPermissions();
			}
		}
		
		return $this->permissions;
	}
	
	public function getAssignedRoles(): array
	{
		$roles = array();
		
		$perm_list = $this->getPermissions();
		foreach($perm_list as $perm)
		{
			$detail = explode('/', $perm);
			if (! in_array($detail[0], $roles))
			{
				$roles[] = $detail[0];
			}
		}
		
		return $roles;
	}
	
	public function getPermissionsString(): string
	{
		$perm_string = '';
		$perm_list = $this->getPermissions();
		
		foreach($perm_list as $perm)
		{
			$perm_string = $perm_string . $perm . ';';
		}
		
		return $perm_string;
	}
	
	public function checkPermission($perm_to_check)
	{
		$perm_string = $this->getPermissionsString();
		
		return ! (strpos($perm_string, $perm_to_check) === false);
	}
	
	public function hasRole($role_to_check)
	{
		$perm_string = $this->getPermissionsString();
		
		return ! (strpos($perm_string, $role_to_check . '/') === false);
	}
	
	public function assignRole($role_to_assign)
	{
		if ($this->usr_id > 0)
		{
			$this->unassignRoleFromUser();
			$this->assignRoleToUser($role_to_assign);
			$this->permissions = $this->loadUsrPermissions($this->usr_id);
		}
	}
	
	public function cleanupOnDropUser()
	{
		if ($this->usr_id > 0)
		{
			$this->unassignRoleFromUser();
		}
	}
	
	private function loadUsrPermissions($usr_id): array
	{
		$perm = array();
		
		$sql_stmt = 
			"SELECT acrl.rl_role_name, perm.perm_name
			 FROM   ta_usr_account_role AS acrl
					INNER JOIN ta_usr_role AS role ON role.role_name = acrl.rl_role_name
					INNER JOIN ta_usr_role_permission AS rlpm ON rlpm.role_name = acrl.rl_role_name
					INNER JOIN ta_usr_permission AS perm ON perm.perm_name = rlpm.perm_name
			 WHERE  acrl.rl_usr_id = ?
			   AND  perm.perm_authenticated = 1";
		$stm_p2 = $this->db_conn->prepare($sql_stmt);
		$stm_p2->bind_param('i', $usr_id);
		if ($stm_p2->execute())
		{
			$role_name = $perm_name = '';
			$stm_p2->bind_result($role_name, $perm_name);
			while ($stm_p2->fetch())
			{
				$perm[] = $role_name . '/' . $perm_name;
			}
		}
		$stm_p2->close();

		return $perm;
	}
	
	private function loadUnauthPermissions(): array
	{
		$perm = array();
		
		$sql_stmt =
			"SELECT 'UNAUTHENTICATED' as role_name, perm.perm_name
			 FROM   ta_usr_permission AS perm
			 WHERE  perm.perm_unauthenticated = 1";
		$stm_p3 = $this->db_conn->prepare($sql_stmt);
		if ($stm_p3->execute())
		{
			$role_name = $perm_name = '';
			$stm_p3->bind_result($role_name, $perm_name);
			while($stm_p3->fetch())
			{
				$perm[] = $role_name . '/' . $perm_name;
			}
		}
		$stm_p3->close();
		
		return $perm;
	}
	
	private function unassignRoleFromUser($role_to_unassign = null)
	{
		if (empty($role_to_unassign))
		{
			$sql_stmt = "DELETE FROM ta_usr_account_role WHERE rl_usr_id = ?";
			$stm_p4 = $this->db_conn->prepare($sql_stmt);
			$stm_p4->bind_param('i', $this->usr_id);
		}			
		else
		{
			$sql_stmt = "DELETE FROM ta_usr_account_role WHERE rl_usr_id = ? AND rl_role_name = ?";
			$stm_p4 = $this->db_conn->prepare($sql_stmt);
			$stm_p4->bind_param('is', $this->usr_id, $role_to_unassign);
		}
		$stm_p4->execute();
		$stm_p4->close();
	}
	
	private function assignRoleToUser($role_to_assign)
	{
		$sql_stmt = "INSERT INTO ta_usr_account_role( rl_usr_id, rl_role_name ) VALUES ( ?, ? )";
		$stm_p5 = $this->db_conn->prepare($sql_stmt);
		$stm_p5->bind_param('is', $this->usr_id, $role_to_assign);
		$stm_p5->execute();
		$stm_p5->close();
	}
}

class UserPermissionSchema
{
	private $db_conn;
	
	public function __construct($db_cn)
    {
        $this->db_conn = $db_cn;
	}
	
	public function getAllRoles(): array
	{
		$role_list = array();
		
		$sql_stmt = 
			"SELECT role.role_name, role.role_description, role.role_symbol
             FROM   ta_usr_role AS role
             ORDER BY role_seq";
		$stm_p6 = $this->db_conn->prepare($sql_stmt);
		if ($stm_p6->execute())
		{
			$role_name = $role_descr = $role_sym = '';
			$stm_p6->bind_result($role_name, $role_descr, $role_sym);
			
			while($stm_p6->fetch())
			{
				$role_list[] = array('role_name' => $role_name, 'role_description' => $role_descr, 'role_symbol' => $role_sym);
			}
		}
		$stm_p6->close();
		
		return $role_list;
	}
	
	public function getRolePrivileges($for_role): array
	{
		$priv_list = array();
		
		$sql_stmt = 
			"SELECT rp.role_name, rp.perm_name, perm.perm_description
			 FROM   ta_usr_role_permission AS rp
					INNER JOIN ta_usr_permission AS perm ON perm.perm_name = rp.perm_name
			 WHERE  rp.role_name = ?
			 ORDER BY rp.role_name, perm.perm_seq";
		$stm_p7 = $this->db_conn->prepare($sql_stmt);
		$stm_p7->bind_param('s', $for_role);
		if ($stm_p7->execute())
		{
			$role_name = $perm_name = $perm_descr = '';
			$stm_p7->bind_result($role_name, $perm_name, $perm_descr);
			
			while($stm_p7->fetch())
			{
				$priv_list[] = array('role_name' => $role_name, 'perm_name' => $perm_name, 'perm_description' => $perm_descr);
			}
		}
		$stm_p7->close();
		
		return $priv_list;
	}
}
?>