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
include_once 'app_result.php';
include_once 'usr_permission.php';

/**
 * Identity of a "Methodenbox" User.
 * 
 * @author         Walter Pachlinger <walter.pachlinger@gmail.com>
 * 
 * @property-read int $usrId Getter for the internal identification of the User Account.
 * @property-read string $usrChallenge Getter for the currently valid challenge value.
 * @property-read string $permissionString Getter for the permissions associated with the User Account.
 */
class UserAccount implements JsonSerializable
{
    /** First name of the person owning the User Account. */
    public $usr_fst_name;
    /** Last name of the person owning the User Account. */
    public $usr_lst_name;
    /** E-Mail address of the person owning the User Account. */
    public $usr_email;
    /** Registration date of the User Account (date and time). */
    public $usr_register_date;
    /** Date and time when registration was confirmed by User Account owner. */
    public $usr_confirm_date;
    /** Date and time of last usage of the User Account. */
    public $usr_login_date;
    
    /** Internal identification of the User Account. */
    private $usr_id;
    /** User Account password. */
    private $usr_pwd;
    /** Random salt value for password hashing. */
    private $usr_salt;
    /** Number of subsequent failed login attempts. */
    private $usr_fail_count;
    /** Status of the User Account (0 = Unconfirmed, 1 = Active, 2 = Locked). */
    private $usr_status;
    /** Protection against replay attacks. */
    private $usr_challenge;
    /** List of permission associated with the User Account. */
	private $usr_permissions; // Type: UserPermission
    /** Handle to the MySQL database session. */
    private $db_conn;

    /**
     * Constructor (intializes the properties of a new User Account).
     * 
     * @param mysqli $db_cn MySQL database session to be used for DB operations.
     */
    public function __construct($db_cn)
    {
        $this->db_conn = $db_cn;
        
        $this->usr_id = -1;
        $this->usr_fst_name = $this->usr_lst_name = $this->usr_email = '';
        $this->usr_register_date = $this->usr_confirm_date = $this->usr_login_date = '';
        $this->usr_pwd = $this->usr_salt = $this->usr_challenge = '';
        $this->usr_status = $this->usr_fail_count = 0;
		$this->usr_permissions = null;
    }
    
    /**
     * Implementation of JsonSerializable::jsonSerialize().
     * 
     * @return string[]      Instance mapped to an associative array.
     */
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
			'usr_permissions'   => json_encode($this->usr_permissions)
        );
    }
    
    /**
     * Magic function implementing the read only properties of a User Account.
     * 
     * @param string $field Name of the read only property.
     * @throws Exception Undefined property name.
     * @return number|string Value of the requested read-only property.
     */
    public function __get(string $field)
    {
        switch($field)
        {
            case 'usrId':
                return $this->getId();
            case 'usrChallenge':
                return $this->getChallenge();
            case 'permissionString':
                return $this->getPermissionsString();
            default:
                throw new Exception('exception: undefined property "UserAccount::' . $field . '"');
        }
    }
    
    /**
     * Returns the internal identifier of the User Account.
     * 
     * @return int Internal identifier of the User Account.
     */
    public function getId()
    {
        return $this->usr_id;
    }
    
    /**
     * Returns the current challenge value.
     * 
     * @return string Current value of the challenge value.
     */
    public function getChallenge()
    {
        return $this->usr_challenge;
    }
    
    /**
     * Calculates the password hash.
     * 
     * @param string $password A password.
     * @param string $salt A password salt value.
     * @return void
     */
    private function hashPassword(string $password, string $salt)
    {
        $pwd = hash('sha256', $password . $salt);
        for ($cnt = 0; $cnt < 50000; $cnt++)
        {
            $pwd = hash('sha256', $pwd . $salt);
        }

        return $pwd;
    }

    /**
     * Creates a new User Account and stores it in the database.
     * 
     * @param string $usr_fst_name First name of the person owning the new User Account.
     * @param string $usr_lst_name Last name of the person owning the new User Account.
     * @param string $usr_email EMail address of the person owning the new User Account.
     * @param string $usr_pwd Initial password for the new User Account.
     * @param string $usr_role_name Initial role assigned to the User Account.
     * @return AppResult Indication of success (code == 0) or failure.
     */
    public function createUserAccount(string $usr_fst_name, string $usr_lst_name, string $usr_email, string $usr_pwd, string $usr_role_name)
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
        
        // Check for duplicates
        $usr_exists = 0;
        $sql_stmt = 'SELECT COUNT(1) AS usr_exists FROM ta_usr_account WHERE usr_email = ?;';
        $stm_u1 = $this->db_conn->prepare($sql_stmt);
        $stm_u1->bind_param('s', $this->usr_email);
        if ($stm_u1->execute())
        {
            $stm_u1->bind_result($usr_exists);
            $stm_u1->fetch();
        }
        $stm_u1->close();
        
        if ($usr_exists != 0)
        {
            return new AppResult(401);
        }
        
        $sql_stmt = 'INSERT INTO ta_usr_account( usr_fst_name, usr_lst_name, usr_email, usr_pwd, usr_salt, usr_register_date, usr_challenge ) ' .
                    'VALUES( ?, ?, ?, ?, ?, ?, ?, ? );';
        $stm_u2 = $this->db_conn->prepare($sql_stmt);
        $stm_u2->bind_param('sssssss', 
                    $this->usr_fst_name, $this->usr_lst_name, $this->usr_email, 
                    $this->usr_pwd, $this->usr_salt, $this->usr_register_date, 
                    $this->usr_challenge);
        if (! $stm_u2->execute())
        {
            $stm_u2->close();
            return new AppResult(402);
        }
        $this->usr_id = $stm_u2->insert_id;
        $stm_u2->close();

		$this->usr_permissions = new UserPermission($this->db_conn, $this->usr_id);
		$this->usr_permissions->assignRole($usr_role_name);
        
        return new AppResult(0);
    }
    
    /**
     * Changes the password for a User Account.
     * 
     * @param string $new_pwd New password for the User Account.
     * @return AppResult Indication of success (code == 0) or failure.
     */
    public function modifyUserAccount(string $new_pwd)
    {
        // Hash the password using a random salt
        $this->usr_salt = Helpers::randomString(16);
        $this->usr_pwd = $this->hashPassword($new_pwd, $this->usr_salt);
        
        $this->usr_confirm_date = '';
        $this->usr_login_date = '';
        $this->usr_fail_count = 0;
        $this->usr_status = 0;

        $this->usr_challenge = Helpers::randomString(32);
        
        $sql_stmt = 'update ta_usr_account set usr_pwd = ?, usr_salt = ?, usr_challenge = ? where usr_id = ?;';
        $stm_u5 = $this->db_conn->prepare($sql_stmt);
        $stm_u5->bind_param('sssi', $this->usr_pwd, $this->usr_salt, $this->usr_challenge, $this->usr_id);
        if (! $stm_u5->execute())
        {
            $stm_u5->close();
            return new AppResult(402);
        }
        $stm_u5->close();
        
        return new AppResult(0);
    }
    
    /**
     * Deletes a User Account from the database.
     * 
     * @return AppResult Indication of success or failure.
     */
    public function deleteUserAccount()
    {
		
		if (empty($this->usr_permissions))
		{
			$this->usr_permissions = new UserPermission($this->db_conn, $this->usr_id);
		}
	
		$this->usr_permissions->cleanupOnDropUser();
		
        $sql_stmt = 'DELETE FROM ta_usr_account WHERE usr_id=?;';
        $stm_uad2 = $this->db_conn->prepare($sql_stmt);
        $stm_uad2->bind_param('i', $this->usr_id);
        $stm_uad2->execute();
        $stm_uad2->close();
        
        return new AppResult(0);
    }
	
    /**
     * Looks up an User Account in the database using EMail address as search criterion.
     * 
     * @param string $usr_email EMail address to search for.
     * @return int Internal identifier of a User Account (-1 if User Account does not exist).
     */
	private function findUserIdByEmail(string $usr_email)
	{
		$usr_id = -1;
		
        $sql_stmt =
            'SELECT usr_id ' .
            'FROM   ta_usr_account ' .
            'WHERE  usr_email = ?;';
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
    
	/**
	 * Checks if an EMail address is already in use.
	 * 
	 * @param string $usr_email EMail address to be checked.
	 * @return boolean true = Email adress alread exists, false otherwise.
	 */
    public function checkByEmail(string $usr_email)
    {
		return ($this->findUserIdByEmail($usr_email) > 0);
    }
    
    /**
     * Loads a User Account from the database using the given EMail address.
     * 
     * @param string $usr_email Given EMail address.
     * @return AppResult Indication of success (code == 0) or failure.
     */
    public function loadByEmail(string $usr_email)
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

    /**
     * Loads a User Account from the database.
     * 
     * @param int $usr_id Internal identifier of a User Account.
     * @return AppResult Indication of success (code == 0) or failure.
     */
    public function loadById(int $usr_id)
    {
        $sql_stmt = 
            'select usr_id, usr_fst_name, usr_lst_name, usr_email, usr_pwd, usr_salt, usr_register_date, usr_confirm_date, usr_login_date, usr_fail_count, ' .
            '       usr_status, usr_challenge ' .
            'from   ta_usr_account ' .
            'where  usr_id = ?;';
        $stm_u3 = $this->db_conn->prepare($sql_stmt);
        $stm_u3->bind_param('i', $usr_id);
        if ($stm_u3->execute())
        {
            $stm_u3->bind_result(
				$this->usr_id, $this->usr_fst_name, $this->usr_lst_name, $this->usr_email, $this->usr_pwd, $this->usr_salt, $this->usr_register_date,
                $this->usr_confirm_date, $this->usr_login_date, $this->usr_fail_count, $this->usr_status, $this->usr_challenge);
                                 
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

		$this->usr_permission = new UserPermission($this->db_conn, $this->usr_id);
        
        return new AppResult(0);
    }
    
    /**
     * Compares a password to the password associated with the User Account.
     * 
     * @param string $password Password to be checked.
     * @return boolean Password is correct (true) or incorrect (false)
     */
    private function verifyPassword(string $password)
    {
        $usr_pwd = $this->hashPassword($password, $this->usr_salt);
        return  ($usr_pwd === $this->usr_pwd);
    }
    
    /**
     * Updates the administrative properties of a User Account in the database.
     * @return void
     */
    private function updateStatus()
    {
        $sql_stmt =
            'UPDATE ta_usr_account 
                SET usr_register_date = ?,
                    usr_confirm_date  = ?,
                    usr_login_date    = ?,
                    usr_fail_count    = ?,
                    usr_status        = ? 
             WHERE  usr_id = ?;';
        $stm_u4 = $this->db_conn->prepare($sql_stmt);
        $stm_u4->bind_param('sssiii', $this->usr_register_date, $this->usr_confirm_date, $this->usr_login_date, $this->usr_fail_count, $this->usr_status, $this->usr_id);
        $stm_u4->execute();
        $stm_u4->close();
    }
    
    /**
     * Sets the status of the User Account to LOCKED.
     * @return void
     */
    public function lockUserAccount()
    {
        $this->usr_status = 2;
        $this->updateStatus();
    }
    
    /**
     * Sets the status of the User Account to ACTIVE.
     * @return void
     */
    public function unlockUserAccount()
    {
        $this->usr_status = 1;
        $this->updateStatus();
    }

    /**
     * Performs authentication for a client user trying to access the application.
     * 
     * @param string $email_addr EMail address provided by the client user.
     * @param string $password Password provided by the client user.
     * @return AppResult Indication of success (code == 0) or failure.
     */
    public function userLogin(string $email_addr, string $password)
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
    
    /**
     * Checks the correctness of a password provided by a client user.
     * 
     * @param string $password Password provided by a client user
     * @return AppResult Indication of success (code == 0) or failure.
     */
    public function checkUserPassword(string $password)
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
    
    /**
     * Verifies the challenge shown by a user frontend.
     * 
     * @param string $email_addr EMail address of the client user.
     * @param string $password Password of the client user.
     * @param string $challenge Challenge shown by the frontend.
     * @return AppResult Indication of success (code == 0) or failure.
     */
    public function verifyChallenge(string $email_addr, string $password, string $challenge)
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
    
	/**
	 * Gets the permissions associated with the User Account.
	 * 
	 * @return string|null Permissions associated with the User Account.
	 */
	public function getPermissionsString()
	{
		if (empty($this->usr_permissions))
		{
			return '';
		}
		return $this->usr_permissions->getPermissionsString();
	}
}
?>