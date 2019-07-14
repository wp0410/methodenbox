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

include_once 'aux_helpers.php';
include_once 'aux_parameter.php';
include_once 'aux_text.php';
include_once 'app_result.php';
include_once 'usr_permission.php';

/**
 * Application specific session information for an authenticated user
 *
 * @author Walter Pachlinger <walter.pachlinger@gmail.com>
 * @property-read int $sessionId Internal identification of the User Session.
 * @property-read int $userId Internal identification of the associated User Account.
 * @property-read array $sessionDescriptor Session digest.
 * @property-read string $sessionHash Calculated User Session hash value.
 * @property-read boolean $isAuthenticated Result of checking of the associated User Account.
 */
class UserSession implements JsonSerializable
{
    /** Internal identification of the User Account of the authenticated user. */
    public  $ses_usr_id;
    /** EMail address of the authenticated user. */
    public  $ses_usr_email;
    /** Full name (first name and last name) of the authenticated user. */
	public  $ses_usr_full_name;

	/** Internal identification of the User Session. */
    private $ses_id;
	
    /** Date and time when the User Session was started. */
	private $ses_start_time;
	/** Date and time when the User Session will expire. */
    private $ses_end_time;
    /** Date and time of last change of User Session properties. */
    private $ses_last_change;
    /** Random string used to calculate the User challenge. */
    private $ses_salt;
    /** User Permission object associated with the User Session. */
	private $ses_permissions; // TYPE UserPermission
	/** MySQL database connection to be used for database operations. */
    private $db_conn;

    /**
     * Constructor. Initializes a new User Session object.
     *
     * @param mysqli $db_cn MySQL database session to be used by the User Session.
     */
    public function __construct(mysqli $db_cn)
    {
        $this->db_conn = $db_cn;
        
        $this->ses_id = $this->ses_usr_id = -1;
        $this->ses_start_time = $this->ses_end_time = $this->ses_last_change = $this->ses_usr_email = $this->ses_salt = '';
		$this->ses_permissions = new UserPermission($db_cn, -1);
    }
    
    /**
     * Implementation of JsonSerializable::jsonSerialize().
     *
     * @return string[] Instance mapped to an associative array.
     */
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
    
    /**
     * Magic getter function for read only properties.
     *
     * @param string $field Name of the read only property to get
     * @throws Exception Invalid property name.
     * @return string|int|boolean|string[] Value of the read only property in case of success.
     */
    public function __get(string $field)
    {
        switch($field)
        {
            case 'sessionId':
                return $this->getId();
            case 'userId':
                return $this->getUsrId();
            case 'sessionDescriptor':
                return $this->getSessionDescriptor();
            case 'sessionHash':
                return $this->getSessionHash();
            case 'isAuthenticated':
                return $this->isAuthenticated();
            default:
                throw new Exception('exception: undefined property "UserSession::' . $field . '"');
        }
    }
    
    /**
     * Gets the internal identification of the User Session.
     *
     * @return int Internal identification of the User Session.
     */
    public function getId()
    {
        return $this->ses_id;
    }
    
    /**
     * Gets the internal identification of the associated User Account.
     *
     * @return int Internal identification of the associated User Account.
     */
    public function getUsrId()
    {
        return $this->ses_usr_id;
    }

    /**
     * Gets the defining elements of the User Session as array.
     *
     * @return string[] Session descriptor.
     */
    public function getSessionDescriptor()
	{
		return array('sid' => $this->getId(), 'uid' => $this->getUsrId(), 'hash' => $this->getSessionHash());
	}
    
	/**
	 * Starts a User Session based on a validated User Account.
	 *
	 * @param int $usr_id Internal identification of the User Account.
	 * @param string $usr_perm Delimited string containing the User Permissions.
	 * @return AppResult Indication of success (code == 0) or failure.
	 */
	public function startSession(int $usr_id, string $usr_perm)
    {
        $res = new AppResult(0);
        
        $sql_stmt = 'DELETE FROM ta_usr_session WHERE ses_usr_id = ?;';
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
            'INSERT INTO ta_usr_session( 
                ses_start_time, ses_end_time, ses_last_change, ses_usr_id, ses_usr_email, ses_usr_full_name, ses_salt, ses_permissions )
             VALUES ( ?, ?, ?, ?, ?, ?, ?, ? );';
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
    
    /**
     * Loads a persistent User Session from the database.
     *
     * @param int $ses_id Identification of the User Session.
     * @return AppResult Indication of success (code == 0) or failure.
     */
    private function loadSession(int $ses_id)
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
    
    /**
     * Calculates a hash value of the User Session.
     *
     * @return string Calculated hash value.
     */
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
    
    /**
     * Validates the hash value for the User Session.
     *
     * @param int $ses_usr_id Internal identification of a User Session.
     * @param string $hash Hash value for a User Session.
     * @return boolean Hash value is valid (true) or invalid (false).
     */
    private function isSessionValid(int $ses_usr_id, string $hash)
    {
        return
            ($this->ses_id > 0) &&
            ($this->ses_usr_id == $ses_usr_id) &&
            (Helpers::dateTimeString(time()) <= $this->ses_end_time) &&
            ($hash == $this->getSessionHash());
    }
    
    /**
     * Extends the lifetime of the User Session by the default session lifetime.
     *
     * @return AppResult Indication of success (code == 0) or failure.
     */
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
    
    /**
     * Closes and devaluates a User Session.
     *
     * @return AppResult Indication of success (code == 0) or failure.
     */
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
    
    /**
     * Restores and validates a User Session based on a digest value.
     *
     * @param array $session_user Session digest value.
     * @param boolean $do_extend Indicated whether or not the sessin shall be extended.
     * @return AppResult Indication of success (code == 0) or failure.
     */
    public function validateSession(array $session_user, $do_extend = true)
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
    
    /**
     * Checks if the associated User Account is successfully authenticated.
     *
     * @return boolean User Account is authenticated (true) or not (false).
     */
    public function isAuthenticated()
    {
        return ($this->ses_id != -1) && ($this->ses_usr_id != -1);
    }
    
    /**
     * Checks the User Permissions for a specific privilege.
     *
     * @param string $permission_tag Privilege to check.
     * @return boolean User Session has the permission (true) or not (false).
     */
    public function checkPermission(string $permission_tag)
	{
		if (empty($this->ses_permissions))
		{
			return false;
		}
		
		return $this->ses_permissions->checkPermission($permission_tag);
	}
}
?>