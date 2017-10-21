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

/**
 * JournalEntry    Logging and Journalling information to be stored in the database
 * @package   JournalEntry
 * @author    Walter Pachlinger (walter.pachlinger@gmx.at)
 * @version   $Revision: 1.0 $
 * @access    public
 */
class JournalEntry
{
    private $jnl_id;
    private $jnl_time;
    private $jnl_usr_id;
    private $jnl_usr_name;
    private $jnl_action;
    private $jnl_result;
    private $jnl_result_txt;
    private $jnl_old_data;
    private $jnl_comment;
    private $db_conn;

    /**
     * Constructor
     * 
     * @param      mysqli    $cn_db    database connection
     * @param      integer   $usr_id   Numeric ID of a user account
     * @param      string    $usr_name User name associated with a user account
     * @param      string    $action   Identification of the action to be audited
     * @access     public
     * @return     An intialized JournalEntry instance
     */
    public function __construct($db_cn, $usr_id, $usr_name, $action)
    {
        $this->jnl_time = strftime('%Y-%m-%d %H:%M:%S', time());
        $this->jnl_usr_id = $usr_id;
        $this->jnl_usr_name = $usr_name;
        $this->jnl_action = $action;
        $this->jnl_result = 0;
        $this->jnl_result_txt = '';
        $this->jnl_data = '';
        $this->jnl_comment = '';
        
        $this->db_conn = $db_cn;
    }
    
    /**
     * Sets the result information to be stored with the Journal Entry
     * 
     * @param      integer   $result        Result code
     * @param      string    $result_txt    Result text
     * @access     public
     */
    public function set_jnl_result($result, $result_txt)
    {
        $this->jnl_result = $result;
        $this->jnl_result_txt = $result_txt;
    }
    
    /**
     * Sets additional data to be stored with the Journal Entry
     * 
     * @param      string    $data     Additional data
     * @access     public
     */
    public function set_jnl_data($data)
    {
        $this->jnl_data = $data;
    }
    
    /**
     * Sets a comment to be stored with the Journal Entry
     * 
     * @param      string    $comment  Comment text
     * @access     public
     */
    public function set_jnl_comment($comment)
    {
        $this->jnl_comment = $comment;
    }
    
    /**
     * Stores the Journal Entry in the database
     * 
     * @access     public
     * @return     TRUE      Journal Entry stored successfully
     * @return     FALSE     Error storing the Journal Entry
     */
    public function store()
    {
        $sql_stmt =
            'insert into ta_jnl_journal( ' .
            '   jnl_ip, jnl_time, jnl_usr_id, jnl_usr_name, jnl_action, jnl_result, jnl_result_txt, jnl_data, jnl_comment ) ' .
            'values( ' .
            '   ?, ?, ?, ?, ?, ?, ?, ?, ? );';
        $stm1 = $this->db_conn->prepare($sql_stmt);
        $stm1->bind_param(
            'ssississs', 
            $_SERVER['REMOTE_ADDR'], 
            $this->jnl_time, $this->jnl_usr_id, $this->jnl_usr_name, $this->jnl_action, 
            $this->jnl_result, $this->jnl_result_txt, $this->jnl_data, $this->jnl_comment);
        $res = $stm1->execute();
        if ($res)
        {
            $this->jnl_id = $stm1->insert_id;
        }
        $stm1->close();
        
        return $res;
    }
}
?>