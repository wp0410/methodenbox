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
/**
 * "Methodenbox" User Identification and Authentication.
 *
 * @package        MBX/Model/UserAuth
 * @author         Walter Pachlinger <walter.pachlinger@gmail.com>
 * @copyright      2019 Walter Pachlinger (walter.pachlinger@gmail.com)
 * @license        Apache License, Version 2.0
 * @license        http://www.apache.org/licenses/LICENSE-2.0
 */

/**
 * Roles and permissions for a Methodenbox User Account.
 * 
 * @author Walter Pachlinger <walter.pachlinger@gmail.com>
 * 
 * @property-read string[] $permissions List of assigned permissions.
 * @property-read string $permissionString List of assigned permissions as delimited string.
 * @property-read string[] $assignedRoles List of assigned roles.
 */
class UserPermission implements JsonSerializable
{
    /** Handle to the MySQL database session. */
	private $db_conn;
	/** Reference to a User Account. */
	private $usr_id;
	/** List of permissions for the referenced User Account */
	private $permissions;
	
	/**
	 * Constructor (initializes a new User Permission object).
	 * 
	 * @param mysqli $db_cn MySQL database session to be used for DB operations.
	 * @param int $usr_id Internal identifier of a User Account.
	 * @param string $perm_string Assigned permissions as a delimted string, optional.
	 */
	public function __construct(mysqli $db_cn, int $usr_id, string $perm_string = null)
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
	
	/**
	 * Magic function implementing the read only properties of a User Permission.
	 * 
	 * @param string $field Name of the read only property.
	 * @throws Exception Undefined property name.
	 * @return string|string[] Value of the requested read-only property.
	 */
	public function __get(string $field)
	{
	    switch($field)
	    {
	        case 'permissions':
	            return $this->getPermissions();
	        case 'permissionString':
	            return $this->getPermissionsString();
	        case 'assignedRoles':
	            return $this->getAssignedRoles();
	        default:
	            throw new Exception('exception: undefined property "UserPermission::' . $field . '"');
	    }
	}

	/**
	 * Implementation of JsonSerializable::jsonSerialize().
	 * 
	 * @return string[] Instance mapped to an associative array.
	 */
    public function jsonSerialize()
    {
        return array(
			'usr_id'      => $this->usr_id,
			'permissions' => $this->getPermissionsString()
		);
	}
	
	/**
	 * Gets the list of assigned permissions from the object.
	 * 
	 * @return string[] List of permissions.
	 */
	public function getPermissions()
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
	
	/**
	 * Get list of roles assigned to the referenced User Account.
	 * 
	 * @return string[] List of assigned roles.
	 */
	public function getAssignedRoles()
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
	
	/**
	 * Get list of assigned permissions as a separated string.
	 * 
	 * @return string List of assigned permissions.
	 */
	public function getPermissionsString()
	{
		$perm_string = '';
		$perm_list = $this->getPermissions();
		
		foreach($perm_list as $perm)
		{
			$perm_string = $perm_string . $perm . ';';
		}
		
		return $perm_string;
	}
	
	/**
	 * Checks if a permission is present in the associated permissions.
	 * 
	 * @param string $perm_to_check Permission to check.
	 * @return boolean Permission is in list (true) or not (false).
	 */
	public function checkPermission(string $perm_to_check)
	{
		$perm_string = $this->getPermissionsString();
		
		return ! (strpos($perm_string, $perm_to_check) === false);
	}
	
	/**
	 * Checks if a role is assigned to the User Account.
	 * 
	 * @param string $role_to_check Role to check.
	 * @return boolean Role is assigned (true) or not (false).
	 */
	public function hasRole(string $role_to_check)
	{
		$perm_string = $this->getPermissionsString();
		
		return ! (strpos($perm_string, $role_to_check . '/') === false);
	}
	
	/**
	 * Assigns a role to the current permission object.
	 * 
	 * @param string $role_to_assign Name of the role to assign.
	 */
	public function assignRole(string $role_to_assign)
	{
		if ($this->usr_id > 0)
		{
			$this->unassignRoleFromUser();
			$this->assignRoleToUser($role_to_assign);
			$this->permissions = $this->loadUsrPermissions($this->usr_id);
		}
	}
	
	/**
	 * Cleans up the permission table in the database when the assoctiated User Account is dropped.
	 */
	public function cleanupOnDropUser()
	{
		if ($this->usr_id > 0)
		{
			$this->unassignRoleFromUser();
		}
	}
	
	/**
	 * Loads the list of associated permissions from the database.
	 * 
	 * @param int $usr_id Reference to a User Account.
	 * @return string[] List of associated permissions read from the database.
	 */
	private function loadUsrPermissions(int $usr_id)
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
	
	/**
	 * Load permissions for unauthenticated users.
	 * 
	 * @return string[] List of permissions for unauthenticated users.
	 */
	private function loadUnauthPermissions()
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
	
	/**
	 * Unassignes a role and its associated permissins from the referenced User Account.
	 * 
	 * @param string $role_to_unassign Name of the role to unassign.
	 */
	private function unassignRoleFromUser(string $role_to_unassign = null)
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
	
	/**
	 * Assign a role and its associated permissions to the referenced User Account.
	 * 
	 * @param string $role_to_assign Name of the role to assign.
	 */
	private function assignRoleToUser(string $role_to_assign)
	{
		$sql_stmt = "INSERT INTO ta_usr_account_role( rl_usr_id, rl_role_name ) VALUES ( ?, ? )";
		$stm_p5 = $this->db_conn->prepare($sql_stmt);
		$stm_p5->bind_param('is', $this->usr_id, $role_to_assign);
		$stm_p5->execute();
		$stm_p5->close();
	}
}

/**
 * Permission schema (roles and permissions) for the Methodenbox application.
 * 
 * @author Walter Pachlinger <walter.pachlinger@gmail.com>
 *
 */
class UserPermissionSchema
{
    /** Handle to the MySQL database session. */
	private $db_conn;
	
	/**
	 * Constructor (initializes a new Permission Schema object).
	 * 
	 * @param mysqli $db_cn MySQL database session to be used for DB operations.
	 */
	public function __construct(mysqli $db_cn)
    {
        $this->db_conn = $db_cn;
	}
	
	/**
	 * Retrieves all defined roles from the database.
	 * 
	 * @return string[][] List of defined roles (name, description, symbol).
	 */
	public function getAllRoles()
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
	
	/**
	 * Retrieves all defined privileges for a role from the database.
	 * 
	 * @param string $for_role Name of the role.
	 * @return string[][] List of assinged privileges (role name, permission name, description);
	 */
	public function getRolePrivileges(string $for_role)
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