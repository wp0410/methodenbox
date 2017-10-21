<?php
//---------------------------------------------------------------------------------------
//  Copyright (c) 2017 Walter Pachlinger (walter.pachlinger@gmx.at)
//    
//  Licensed under the Apache License, Version 2.0 (the "License"); you may not use this 
//  file except in compliance with the License. You may obtain a copy of the License at
//      http://www.apache.org/licenses/LICENSE-2.0
//  Unless required by applicable law or agreed to in writing, softwaredistributed under 
//  the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF 
//  ANY KIND, either express or implied. See the License for the specific language 
//  governing permissions and limitations under the License.
//----------------------------------------------------------------------------------------
include_once 'mdl_msc.php';
include_once 'mdl_par.php';

/**
 * UserAccount     Authentication information for an application user
 * 
 * @package   UserAccount
 * @author    Walter Pachlinger (walter.pachlinger@gmx.at)
 * @version   $Revision: 1.0 $
 * @access    public
 */
class UserAccount implements JsonSerializable
{
    public $usr_fstname;
    public $usr_lstname;
    public $usr_email;
    public $usr_registration_tm;
    public $usr_lastlogin_tm;
    public $usr_locked;
    public $num_inv_login;
    
    private $usr_id;
    private $usr_pwd;
    private $usr_salt;
    private $usr_pin;
    private $usr_role;
    private $usr_loaded;
    private $db_conn;
    
    /**
     * Constructor
     * 
     * @param      mysqli    $db_cn         database connection
     * @access     public
     * @return     An initialized UserAccount instance
     */
    public function __construct($db_cn)
    {
        $this->usr_id = -1;
        $this->usr_fstname = '';
        $this->usr_lstname = '';
        $this->usr_email = '';
        $this->usr_registration_tm = '';
        $this->usr_lastlogin_tm = '';
        $this->usr_locked = 0;
        $this->num_inv_login = 0;
        $this->usr_pwd = '';
        $this->usr_salt = '';
        $this->usr_pin = '';
        $this->usr_loaded = false;
        $this->db_conn = $db_cn;
        $this->usr_role = 0;
    }
    
    /**
     * Composes the array to be used by json_encode() for UserAccount objects
     * 
     * @access     public
     * @return     Array of UserAccount attributes for json_encode()
     */
    public function jsonSerialize()
    {
        return array(
            'usr_id'              => $this->usr_id,
            'usr_fst_name'        => $this->usr_fstname,
            'usr_lst_name'        => $this->usr_lstname,
            'usr_email'           => $this->usr_email,
            'usr_registration_tm' => $this->usr_registration_tm,
            'usr_lastlogin_tm'    => $this->usr_lastlogin_tm,
            'usr_locked'          => $this->usr_locked,
            'num_inv_login'       => $this->num_inv_login,
            'usr_pwd'             => '****',
            'usr_salt'            => '****',
            'usr_pin'             => '****',
            'usr_loaded'          => $this->usr_loaded
        );
    }
    
    /**
     * Returns the numeric ID of the User Account
     * 
     * @access     public
     * @return     Numeric ID of the UserAccount object
     */
    public function usr_id()
    {
        return $this->usr_id;
    }
    
    /**
     * Checks if the user account has a given role
     * 
     * @param      string    $role          Name of the user role
     * @access     public
     * @return     TRUE      User account has the requested role
     * @return     FALSE     User account does not have the requested role
     */
    public function validate_role($role)
    {
        $res = false;
        
        switch ($role) {
            case 'ADMIN':
                $res = (($this->usr_role & 0x0010) != 0);
                break;
            case 'USER':
                $res = true;
                break;
            default:
                $res = false;
                break;
        }
        
        return $res;
    }
    
    /**
     * Load a UserAccount from the database using the e-mail address as key
     * 
     * @param      string    $email_addr    E-Mail address of the User Account
     * @access     public
     * @return     TRUE (object loaded successfully)
     * @return     FALSE (object not found or error loading object)
     */
    public function load_by_email($email_addr)
    {
        $sql_stmt = 
            'select usr_id, usr_fst_name, usr_lst_name, usr_email, ' .
            '       usr_pwd, usr_salt, ' .
            '       DATE_FORMAT(usr_registered,\'%Y-%m-%d %H:%i:%S\'), ' .
            '       DATE_FORMAT(usr_lastlogin, \'%Y-%m-%d %H:%i:%S\'), ' .
            '       usr_numinvlogin, usr_locked, usr_pin, usr_role ' .
            'from   ta_sec_user ' .
            'where  usr_email = ?;';
        $stm1 = $this->db_conn->prepare($sql_stmt);
        $stm1->bind_param('s', $email_addr);
        $stm1->execute();
        $stm1->bind_result(
            $this->usr_id, $this->usr_fstname, $this->usr_lstname, $this->usr_email,
            $this->usr_pwd, $this->usr_salt, 
            $this->usr_registration_tm, $this->usr_lastlogin_tm, 
            $this->num_inv_login, $this->usr_locked, $this->usr_pin, $this->usr_role );
        $this->usr_loaded = $stm1->fetch();
        $stm1->free_result();
        $stm1->close();
        
        if (! $this->usr_loaded)
        {
            $this->usr_id = 0;
        }

        return $this->usr_loaded;
    }
    
    /**
     * Load a UserAccount from the database using the numeric ID as key
     * 
     * @param      integer   $id       Numeric ID of the User Account
     * @access     public
     * @return     TRUE (object loaded successfully)
     * @return     FALSE (object not found or error loading object)
     */
    public function load_by_id($id)
    {
        $sql_stmt = 
            'select usr_id, usr_fst_name, usr_lst_name, usr_email, ' .
            '       usr_pwd, usr_salt, ' .
            '       DATE_FORMAT(usr_registered,\'%Y-%m-%d %H:%i:%S\'), ' .
            '       DATE_FORMAT(usr_lastlogin, \'%Y-%m-%d %H:%i:%S\'), ' .
            '       usr_numinvlogin, usr_locked, usr_pin, usr_role ' .
            'from   ta_sec_user ' .
            'where  usr_id = ?;';
        $stm1 = $this->db_conn->prepare($sql_stmt);
        $stm1->bind_param('i', $id);
        $stm1->execute();
        $stm1->bind_result(
            $this->usr_id, $this->usr_fstname, $this->usr_lstname, $this->usr_email,
            $this->usr_pwd, $this->usr_salt, 
            $this->usr_registration_tm, $this->usr_lastlogin_tm, 
            $this->num_inv_login, $this->usr_locked, $this->usr_pin, $this->usr_role );
        $this->usr_loaded = $stm1->fetch();
        $stm1->free_result();
        $stm1->close();
        
        if (! $this->usr_loaded)
        {
            $this->usr_id = 0;
        }

        return $this->usr_loaded;
    }
    
    /**
     * Hashes the password string 
     * 
     * @param      string    $password The user password
     * @param      string    $salt     The salt value to be used for hashing
     * @access     private
     * @return     The hashed password
     */
    private function hash_pwd($password, $salt)
    {
        $pwd = hash('sha256', $password . $salt);
        for ($cnt = 0; $cnt < 50000; $cnt++)
        {
            $pwd = hash('sha256', $pwd . $salt);
        }

        return $pwd;
    }

    /**
     * Sets the password string for a User Account
     * 
     * @param      string    $password The user password
     * @access     public
     */
    public function set_password($password)
    {
        $this->usr_salt = rnd_string(16);
        $this->usr_pwd = $this->hash_pwd($password, $this->usr_salt);
    }
    
    /**
     * Validates the password for a User Account
     * 
     * @param      string    $password The user password
     * @access     private
     * @return     TRUE      Password validation successful
     * @return     FALSE     Invalid password
     */
    private function validate_password($password)
    {
        $pwd = $this->hash_pwd($password, $this->usr_salt);
        return ($pwd == $this->usr_pwd);
    }
    
    /**
     * Performs the necessary steps for validing the login for a User Acccount
     * 
     * @param      string    $password The user password
     * @access     public
     * @return     array('code', 'text')
     * @return     array['code']  Numeric error code, 0 if OK
     * @return     array['text']  Error text ('OK' if OK)
     */
    public function validate_login($password)
    {
        $result = array('code' => 0, 'text' => 'OK');
        if ($this->usr_loaded)
        {
            if ($this->usr_locked == 0)
            {
                if ($this->validate_password($password))
                {
                    $this->usr_lastlogin_tm = strftime('%Y-%m-%d %H:%M:%S', time());
                    $this->num_inv_login = 0;
                }
                else 
                {
                    $this->num_inv_login += 1;
                    
                    if ($this->num_inv_login > GlobalParam::$invalid_login_limit)
                    {
                        $this->usr_locked = 1;
                        $result['code'] = 109;
                        $result['text'] = '[E-109] Invalid password - maximum number of retries exceeded - account locked';
                    }
                    else
                    {
                        $result['code'] = 102;
                        $result['text'] = '[E-102] Invalid password';
                    }
                }
            }
            else 
            {
                $result['code'] = 101;
                $result['text'] = '[E-101] User account is locked';
                $this->num_inv_login += 1;
            }
        }
        else
        {
            $result['code'] = 100;
            $result['text'] = '[E-100] User account does not exist';
        }
        
        return $result;
    }
    
    /**
     * Saves a User Account in the database
     * 
     * @access     public
     * @return     TRUE      object successfully saved
     * @return     FALSE     error saving the UserAccount object
     */
    public function store()
    {
        $err_num = 0;
        $err_txt = '';
        
        if ($this->usr_loaded)
        {
            $sql_stmt = 
                'update ta_sec_user ' .
                '   set usr_fst_name = ?, usr_lst_name = ?, usr_pwd = ?, usr_salt = ?, ' .
                '       usr_lastlogin = ?, usr_numinvlogin = ?, usr_locked = ?, usr_pin = ? ' .
                'where  usr_id = ?;';
            $stm3 = $this->db_conn->prepare($sql_stmt);
            $stm3->bind_param(
                'sssssiisi',
                $this->usr_fstname, $this->usr_lstname, $this->usr_pwd, $this->usr_salt,
                $this->usr_lastlogin_tm, $this->num_inv_login, $this->usr_locked, $this->usr_pin,
                $this->usr_id);
            $result = $stm3->execute();
            if (! $result)
            {
                $err_num = $stm3->errno;
                $err_txt = $stm3->error;
            }
            $stm3->close();

            return $result;
        }
        else 
        {
            $this->usr_registration_tm = strftime('%Y-%m-%d %H:%M:%S', time());
            $this->usr_lastlogin_tm = '';
            $this->num_inv_login = 0;
            $this->usr_locked = 0;
            $this->usr_pin = 0;
            
            $sql_stmt = 
                'insert into ta_sec_user( ' .
                '    usr_fst_name, usr_lst_name, usr_email, ' .
                '    usr_pwd, usr_salt, usr_registered, usr_lastlogin,  ' .
                '    usr_numinvlogin, usr_locked, usr_pin ) ' .
                'values( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? );';
            $stm2 = $this->db_conn->prepare($sql_stmt);
            $stm2->bind_param(
                'sssssssiis',
                $this->usr_fstname, $this->usr_lstname, $this->usr_email,
                $this->usr_pwd, $this->usr_salt, $this->usr_registration_tm, $this->usr_lastlogin_tm,
                $this->num_inv_login, $this->usr_locked, $this->usr_pin);
            $this->usr_loaded = $stm2->execute(); 
            if (! $this->usr_loaded)
            {
                $this->usr_id = -1;
                $err_num = $stm2->errno;
                $err_txt = $stm2->error;
            }
            else
            {
                $this->usr_id = $stm2->insert_id;
            }
            $stm2->close();
            
            return $this->usr_loaded;
        }
    }
}
?>