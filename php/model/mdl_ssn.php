<?php
//---------------------------------------------------------------------------------------
//  Copyright (c) 2017 Walter Pachlinger (walter.pachlinger@gmx.at)
//    
//  Licensed under the Apache License, Version 2.0 (the "License"); you may not use this 
//  file except in compliance with the License. You may obtain a copy of the License at
//      http://www.apache.org/licenses/LICENSE-2.0
//  Unless required by applicable law or agreed to in writing, software distributed under 
//  the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF 
//  ANY KIND, either express or implied. See the License for the specific language 
//  governing permissions and limitations under the License.
//----------------------------------------------------------------------------------------
include_once 'mdl_msc.php';

/**
 * UserSession     Information about an authenticated user
 * 
 * @package   UserSession
 * @author    Walter Pachlinger (walter.pachlinger@gmx.at)
 * @version   $Revision: 1.0 $
 * @access    public
 */
class UserSession implements JsonSerializable
{
    private $sess_id;
    public $usr_id;
    public $usr_name;
    public $admin;
    private $sess_start_tm;
    private $sess_end_tm;
    private $valid;
    private $db_conn;
    private $sess_type;
    
    /**
     * Constructor
     * 
     * @param      mysqli    $db_cn         database connection
     * @param      boolean   $admin         Create administrator session (true/false)
     * @access     public
     * @return     An initialized UserSession instance
     */
    public function __construct($db_cn, $admin = false)
    {
        $this->sess_id = '';
        $this->usr_id = -1;
        $this->usr_name = '';
        $this->sess_start_tm = '';
        $this->sess_end_tm = '';
        $this->valid = false;

        $this->db_conn = $db_cn;
        if ($admin)
        {
            $this->sess_type = 1;
        }
        else
        {
            $this->sess_type = 0;
        }
    }
    
    /**
     * Composes the array to be used by json_encode() for UserAccount objects
     * 
     * @access     public
     * @return     Array of UserSession attributes for json_encode()
     */
    public function jsonSerialize()
    {
        return array(
            'sess_id'       => $this->sess_id,
            'usr_id'        => $this->usr_id,
            'usr_name'      => $this->usr_name,
            'sess_start_tm' => $this->sess_start_tm,
            'sess_end_tm'   => $this->sess_end_tm,
            'sess_type'     => $this->sess_type,
            'valid'         => $this->valid
        );
    }
    
    /**
     * Returns the numeric ID of the UserSession instance
     * 
     * @access     public
     * @return     Numeric ID of the UserSession instance
     */
    public function sess_id()
    {
        return $this->sess_id;
    }
    
    /**
     * Check if the session is authorized as ADMIN
     * 
     * @access     public
     * @return     TRUE      Session is ADMIN
     * @return     FALSE     Session is normal user
     */
     public function validate_admin()
     {
         return ($this->sess_type == 1);
     }
    
    /**
     * Load a UserSession object from the database using the session identifier as key
     * 
     * @param      string    $sid      Session identifier
     * @access     public
     * @return     TRUE      Object loaded successfully
     * @return     FALSE     Error loading object or object not found in database
     */
    public function load_by_id($sid)
    {
        $sql_stmt =
            'select sess_id, sess_usr_id, sess_usr_name, ' .
            'date_format(sess_start,\'%Y-%m-%d %H:%i:%S\'), ' .
            'date_format(sess_end,\'%Y-%m-%d %H:%i:%S\'), ' .
            'sess_type ' .
            'from   ta_sec_user_session ' .
            'where  sess_id = ?;';
        $stm1 = $this->db_conn->prepare($sql_stmt);
        $stm1->bind_param('s', $sid);
        $stm1->execute();
        $stm1->bind_result(
            $this->sess_id,
            $this->usr_id,
            $this->usr_name,
            $this->sess_start_tm,
            $this->sess_end_tm,
            $this->sess_type);
        $this->valid = $stm1->fetch();
        $stm1->free_result();
        $stm1->close();
        
        return $this->valid;
    }
    
    /**
     * Creates a new UserSession object for an authenticated user
     * 
     * @param      integer   $user_id       Numeric ID of a UserAccount object
     * @param      string    $user_name     User Account name
     * @access     public
     * @return     TRUE      UserSession object successfully created
     * @return     FALSE     Error creating UserSession object
     */
    public function create($user_id, $user_name)
    {
        $this->sess_id = rnd_string(16);
        $this->usr_id = $user_id;
        $this->usr_name = $user_name;
        $this->sess_start_tm = strftime('%Y-%m-%d %H:%M:%S', time());
        $this->sess_end_tm = strftime('%Y-%m-%d %H:%M:%S', time() + GlobalParam::$session_timeout_sec);
        
        // Clean up old left-over sessions for the same user:
        $stm_cl = $this->db_conn->prepare("delete from ta_sec_user_session where sess_usr_id = ? and sess_end < now();");
        $stm_cl->bind_param('i', $this->usr_id);
        $stm_cl->execute();
        $stm_cl->close();
        
        // Insert the data for the new user session into the database:
        $sql_stmt =
            'insert into ta_sec_user_session( sess_id, sess_start, sess_end, sess_usr_id, sess_usr_name, sess_type ) ' .
            'values( ?, ?, ?, ?, ?, ? ); ';
        $stm2 = $this->db_conn->prepare($sql_stmt);
        $stm2->bind_param('sssisi', $this->sess_id, $this->sess_start_tm, $this->sess_end_tm, $this->usr_id, $this->usr_name, $this->sess_type);
        $this->valid = $stm2->execute();
        $stm2->close();
        
        return $this->valid;
    }
    
    /**
     * Extends the validity of a UserSession
     * 
     * @access     public
     * @return     TRUE      Validity successfully extended
     * @return     FALSE     Error extending the session validity
     */
    public function extend()
    {
        if (! $this->valid)
        {
            return $this->valid;
        }
        $this->sess_end_tm = strftime('%Y-%m-%d %H:%M:%S', time() + GlobalParam::$session_timeout_sec);

        $sql_stmt =
            'update ta_sec_user_session set sess_end = ? where sess_id = ?; ';
        $stm3 = $this->db_conn->prepare($sql_stmt);
        $stm3->bind_param('ss', $this->sess_end_tm, $this->sess_id);
        $this->valid = $stm3->execute();
        $stm3->close();
        
        return $this->valid;
    }
    
    /**
     * Checks the validity of a UserSession instance
     * 
     * @access     public
     * @return     TRUE      UserSession instance is valid
     * @return     FALSE     UserSession instance is not valid
     */
    public function valid()
    {
        $cur_tm = strftime('%Y-%m-%d %H:%M:%S', time());
        
        return 
            $this->valid &&
            ($this->sess_end_tm != '') &&
            ($this->sess_end_tm >= $cur_tm);
    }
    
    /**
     * Destroys a UserSession instance
     * 
     * @access  public
     * @return  TRUE         Session successfully destroyed
     */
    public function destroy()
    {
        if ($this->valid)
        {
            $sql_stmt = 'delete from ta_sec_user_session where sess_id = ?;';
            $stm4 = $this->db_conn->prepare($sql_stmt);
            $stm4->bind_param('s', $this->sess_id);
            $stm4->execute();
            $stm4->close();
        }
        
        $this->sess_id = '';
        $this->sess_start_tm = '';
        $this->sess_end_tm = '';
        $this->usr_id = -1;
        $this->usr_name = '';
        
        return true;
    }
}
?>