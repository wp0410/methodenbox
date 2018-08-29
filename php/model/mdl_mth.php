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
include_once 'mdl_par.php';

/**
 * MethodDescription    Description file of a teaching method
 * 
 * @package   MethodDescription
 * @author    Walter Pachlinger (walter.pachlinger@gmx.at)
 * @version   $Revision: 1.0 $
 * @access    public
 */
class MethodDescription implements JsonSerializable
{
    public $file_guid;
    public $file_name;
    public $file_type;
    public $file_temp_path;
    
    private $att_mth_id;
    private $file_data;
    private $db_conn;
    private $loaded;
    
    /**
     * Constructor
     * 
     * @param      mysqli    $db_cn         database connection
     * @access     public
     * @return     An initialized MethodDescription instance
     */
    public function __construct($db_cn)
    {
        $this->file_guid = $this->file_name = $this->file_type = $this->file_temp_path = '';
        $this->file_data = null;
        $this->db_conn = $db_cn;
        $this->loaded = false;
        $this->att_mth_id = 0;
    }
    
    /**
     * Composes the array to be used by json_encode() for MethodDescription objects
     * 
     * @access     public
     * @return     Array of UserAccount attributes for json_encode()
     */
    public function jsonSerialize()
    {
        return array(
            'att_mth_id' => $this->att_mth_id,
            'file_guid'  => $this->file_guid,
            'file_name'  => $this->file_name,
            'file_type'  => $this->file_type,
            'loaded'     => $this->loaded,
            'file_data'  => '*****' 
        );
    }
    
    /**
     * Retrieves a description file from the database using the numeric teaching
     * method ID and the file GUID as key
     * 
     * @param      integer   $mid      Numeric ID of the teaching method
     * @param      string    $fguid    Description file GUID
     * @access     public
     * @return     array('code', 'text')
     * @return     array['code']  Numeric error code, 0 if OK
     * @return     array['text']  Error text ('OK' if OK)
     */
    public function load($mid, $fguid)
    {
        $result = array('code' => 0, 'text' => 'OK');
        $this->att_mth_id = $mid;
        $this->file_guid = $fguid;
        
        $sql_stmt =
            'SELECT att_name, att_type, att_data ' .
            'FROM   ta_mth_method_attachment ' .
            'WHERE  att_mth_id = ? AND att_guid = ?;';
    
        $stm1 = $this->db_conn->prepare($sql_stmt);
        $stm1->bind_param('is', $mid, $fguid);
        $this->loaded = $stm1->execute();
        if ($this->loaded)
        {
            $stm1->store_result();
            $stm1->bind_result($this->file_name, $this->file_type, $this->file_data);
            $this->loaded = $stm1->fetch();
            if (! $this->loaded)
            {
                $result = array('code' => 420, 'text' => '[E_420] Methodenbeschreibung nicht gefunden');
            }
            $stm1->free_result();
        }
        else
        {
            $result = array('code' => $stm1->errno, 'text' => $stm1->error);
        }
        $stm1->close();
        
        return $result;
    }
    
    /**
     * Returns the contents of the method description file
     * 
     * @access     public
     * @return     The file contents
     */
    public function get_file_data()
    {
        if ($this->loaded)
        {
            return $this->file_data;
        }
        else
        {
            return null;
        }
    }
}

/**
 * TeachingMethod       Encapsulates the business logic for a teaching method
 * 
 * @package   TeachingMethod
 * @author    Walter Pachlinger (walter.pachlinger@gmx.at)
 * @version   $Revision: 1.0 $
 * @access    public
 */
class TeachingMethod implements JsonSerializable
{
    private $mth_id;
    public $mth_name;
    public $mth_phase;
    public $mth_prep_min;
    public $mth_prep_max;
    public $mth_exec_min;
    public $mth_exec_max;
    public $mth_topic;
    public $mth_type;
    public $mth_socform;
    public $mth_authors;
    public $mth_summary;
    public $mth_age_grp;
    private $mth_owner;
    
    private $db_conn;
    private $mth_description;

    /**
     * Constructor
     * 
     * @param      mysqli    $db_cn         database connection
     * @param      int       $mth_ow        id of current user
     * @access     public
     * @return     An initialized TeachingMethod instance
     */
    public function __construct($db_cn, $mth_ow)
    {
        $this->mth_id = -1;
        $this->db_conn = $db_cn;
        $this->mth_name = '';
        $this->mth_phase = '';
        $this->mth_prep_min = $this->mth_prep_max = 0;
        $this->mth_exec_min = $this->mth_exec_max = 0;
        $this->mth_topic = '';
        $this->mth_type = '';
        $this->mth_socform = '';
        $this->mth_summary = '';
        $this->mth_age_grp = '';
        $this->mth_description = new MethodDescription(null);
        $this->authors = array();
        $this->mth_owner = $mth_ow;
    }
    
    /**
     * Composes the array to be used by json_encode() for TeachingMethod objects
     * 
     * @access     public
     * @return     Array of UserAccount attributes for json_encode()
     */
    public function jsonSerialize()
    {
        return array(
            'mth_id' => $this->mth_id,
            'mth_name' => $this->mth_name,
            'mth_phase' => $this->mth_phase,
            'mth_type'  => $this->mth_type,
            'mth_prep'  => array('min' => $this->mth_prep_min, 'max' => $this->mth_prep_max),
            'mth_exec'  => array('min' => $this->mth_exec_min, 'max' => $this->mth_exec_max),
            'mth_socform' => $this->mth_socform,
            'mth_authors' => $this->mth_authors,
            'mth_topic'   => $this->mth_topic,
            'mth_summary' => $this->mth_summary,
            'mth_age_grp' => $this->mth_age_grp,
            'mth_descr'   => array('guid' => $this->mth_description->file_guid, 'name' => $this->mth_description->file_name, 'type' => $this->mth_description->file_type),
            'mth_owner'   => $this->mth_owner
        );
    }
    
    /**
     * Retrieves the numeric ID of the teaching method
     * 
     * @access     public
     * @return     The numeric teaching method ID
     */
    public function mth_id()
    {
        return $this->mth_id;
    }
    
    /**
     * Converts an array of strings to one single string by appending
     * the array elements to each other
     * 
     * @param      array     $arr      Array of strings
     * @access     private
     * @return     Strings of the array appended to each other
     */
    private function array_to_string($arr)
    {
        $res = '';
        foreach($arr as $item)
        {
            $res = $res . $item;
        }
        return $res;
    }
    
    /**
     * Sets the teaching phase attribute of the teaching method
     * 
     * @param      array     $ph_list  List of teaching phase short-cuts
     * @access     public
     * @return     TRUE      Success
     * @return     FALSE     Error setting the property
     */
    public function set_phase($ph_list)
    {
        if (! empty($ph_list))
        {
            $this->mth_phase = $this->array_to_string($ph_list);
        }
        return true;
    }
    
    /**
     * Sets the teaching type property of the teaching method
     * 
     * @param      array     $ty_list  List of teaching type short-cuts
     * @access     public
     * @return     TRUE      Success
     * @return     FALSE     Error setting the property
     */
    public function set_type($ty_list)
    {
        if (! empty($ty_list))
        {
            $this->mth_type = $this->array_to_string($ty_list);
        }
        return true;
    }
    
    /**
     * Sets the social form property of the teaching method
     * 
     * @param      array     $sf_list  List of social form short-cuts
     * @access     public
     * @return     TRUE      Success
     * @return     FALSE     Error setting the property
     */
    public function set_soc_form($sf_list)
    {
        if (! empty($sf_list))
        {
            $this->mth_socform = $this->array_to_string($sf_list);
        }
        return true;
    }
    
    /**
     * Sets the description file of the teaching method
     * 
     * @param      string    $mth_file Array of description elements for the 
     *                                 uploaded method description file
     * @access     public
     * @return     array('code', 'text')
     * @return     array['code']  Numeric error code, 0 if OK
     * @return     array['text']  Error text ('OK' if OK)
     */ 
    public function set_file($mth_file)
    {
        $this->mth_description = null;
        
        if ($mth_file['error'] === UPLOAD_ERR_OK)
        {
            $ftype = mime_content_type($mth_file['tmp_name']);
            if (! array_key_exists($ftype, GlobalParam::$mime_types))
            {
                $result = array('code' => 401, 'text' => '[E_401] Invalid File Type');
            }
            else
            {
                $fname = $mth_file['name'];
                if (strpos($fname, '/'))
                {
                    $fparts = explode('/', $fname);
                }
                else
                {
                    $fparts = explode('\\', $fname);
                }
                
                $this->mth_description = new MethodDescription(null);
                $this->mth_description->file_guid = rnd_string(32);
                $this->mth_description->file_type = GlobalParam::$mime_types[$ftype];
                $this->mth_description->file_name = end($fparts);
                $this->mth_description->file_temp_path = $mth_file['tmp_name'];
                
                $result = array('code' => 0, 'text' => 'OK');
            }
        }
        else
        { 
            $result = array('code' => 402, 'text' => '[E_402] File Upload Failed');
        }
        
        return $result;
    }
    
    /**
     * Validates the teaching method object
     * 
     * @access     private
     * @return     TRUE      Object is valid
     * @return     FALSE     Object is not valid
     */
    private function validate()
    {
        return ($this->mth_id >= 0);
    }
    
    /**
     * Undos the storing of a teaching method in the database
     * 
     * @access     private
     */
    private function undo_mth()
    {
        $sql_stmt =
            'delete from ta_mth_method ' .
            'where  mth_id = ?;';
        $stm2 = $this->db_conn->prepare($sql_stmt);
        $stm2->bind_param('i', $this->mth_id);
        $stm2->execute();
        $stm2->close();
    }
    
    /**
     * Undos the storing of the author list of a teaching method
     * 
     * @access     private
     */
    private function undo_mth_authors()
    {
        $sql_stmt =
            'delete from ta_mth_method_author ' .
            'where  mth_id = ?;';
        $stm2 = $this->db_conn->prepare($sql_stmt);
        $stm2->bind_param('i', $this->mth_id);
        $stm2->execute();
        $stm2->close();
    }
    
    /**
     * Stores a teaching method object in the database
     * 
     * @access     public
     * @return     array('code', 'text')
     * @return     array['code']  Numeric error code, 0 if OK
     * @return     array['text']  Error text ('OK' if OK)
     */
    public function store()
    {
        if (! $this->validate())
        {
            return array('code' => 410, 'text' => '[E_410] Die Daten der Unterrichtsmethode sind nicht vollständig');
        }
        
        $sql_stmt =
            'insert into ta_mth_method( ' .
                  'mth_name, mth_phase, mth_prep_min, mth_prep_max, mth_exec_min, mth_exec_max, ' .
                  'mth_topic, mth_type, mth_soc_form, mth_summary, mth_age_grp, mth_owner_id ) ' .
            'values( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? );';
        $stm1 = $this->db_conn->prepare($sql_stmt);
        $stm1->bind_param(
            'ssiiiissssii',
            $this->mth_name, $this->mth_phase, $this->mth_prep_min, $this->mth_prep_max, $this->mth_exec_min, $this->mth_exec_min,
            $this->mth_topic, $this->mth_type, $this->mth_socform, $this->mth_summary, $this->mth_age_grp, $this->mth_owner);
        $result = $stm1->execute();
        if ($result)
        {
            $this->mth_id = $stm1->insert_id;
        }
        else
        {
            $err_code = $stm1->errno;
            $err_text = $stm1->error;
            $this->mth_id = -1;
        }
        $stm1->close();
        
        if (! $result)
        {
            return array('code' => 411, 'text' => '[E_411] DB Insert Error on mth ([' . $err_code . '] ' . $err_text . ')');
        }
        
        $sql_stmt =
            'insert into ta_mth_method_author( mth_id, mth_seq, mth_auth_name ) ' .
            'values( ?, ?, ? );';
        $stm3 = $this->db_conn->prepare($sql_stmt);
        $mth_seq = 0;
        $result = true;
        foreach($this->mth_authors as $auth)
        {
            $auth_name = trim($auth);
            if (strlen($auth_name) > 0)
            {
                $stm3->bind_param('iis', $this->mth_id, $mth_seq, $auth_name);
                if (! $stm3->execute())
                {
                    $err_code = $stm3->errno;
                    $err_text = $stm3->error;
                    $result = false;
                    break;
                }
                $mth_seq += 1;
            }
        }
        $stm3->close();
        if (! $result)
        {
            $this->undo_mth();
            return array('code' => 412, 'text' => '[E_411] DB Insert Error on mth_authors ([' . $err_code . '] ' . $err_text . ')');
        }
        
        $null = null;
        $sql_stmt =
            'insert into ta_mth_method_attachment( att_mth_id, att_name, att_type, att_guid, att_data ) ' .
            'values( ?, ?, ?, ?, ? );';
        $stm4 = $this->db_conn->prepare($sql_stmt);
        $stm4->bind_param('isssb', $this->mth_id, $this->mth_description->file_name, $this->mth_description->file_type, $this->mth_description->file_guid, $null);
        $stm4->send_long_data(4, file_get_contents($this->mth_description->file_temp_path));
        $result = $stm4->execute();
        $err_code = $stm3->errno;
        $err_text = $stm3->error;
        $stm4->close();
        
        if (! $result)
        {
            $this->undo_mth_authors();
            $this->undo_mth();
            return array('code' => 413, 'text' => '[E_411] DB Insert Error on mth_attachment ([' . $err_code . '] ' . $err_text . ')');
        }
        
        return array('code' => 0, 'text' => 'OK');
    }
    
    /**
     * Deletes the dependent objects pof a teching method from the database and sets the
     * status of the record to deleted (1)
     * 
     * @access     public
     * @return     TRUE      Teaching method successfully deleted
     * @return     FALSE     Error deleting the data from the database
     */
    public function delete()
    {
        if (! $this->validate())
        {
            return false;
        }
        
        if ($this->db_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE))
        {
            $result = db_execute_stmt_one_param($this->db_conn, 'delete from ta_mth_statistics_rating where rtg_mth_id = ?;', 'i', $this->mth_id);
            if ($result)
            {
                $result = db_execute_stmt_one_param($this->db_conn, 'delete from ta_mth_statistics_download where dld_mth_id = ?;', 'i', $this->mth_id);
            }
            if ($result)
            {
                $result = db_execute_stmt_one_param($this->db_conn, 'delete from ta_mth_method_author where mth_id = ?;', 'i', $this->mth_id);
            }
            if ($result)
            {
                $result = db_execute_stmt_one_param($this->db_conn, 'delete from ta_mth_method_attachment where att_mth_id = ?;', 'i', $this->mth_id);
            }
            if ($result)
            {
                $result = db_execute_stmt_one_param($this->db_conn, 'update ta_mth_method set mth_status = 1 where mth_id = ?;', 'i', $this->mth_id);
            }
            
            if ($result)
            {
                $this->db_conn->commit();
            }
            else
            {
                $this->db_conn->rollback();
            }
            
            return $result;
        }
        else
        {
            return false;
        }
    }
}

/**
 * TeachingMethodSearcher    Encapsulates the search functionality for Teaching Methods
 * 
 * @package   TeachingMethodSearcher
 * @author    Walter Pachlinger (walter.pachlinger@gmx.at)
 * @version   $Revision: 1.0 $
 * @access    public
 */
class TeachingMethodSearcher
{
    private $db_conn;
    private $sql_stmt;
    private $sql_order_clause;
    private $sql_par_values;
    private $sql_par_types;
    private $sql_par_num;

    /**
     * Constructor
     * 
     * @param      mysqli    $db_cn         database connection
     * @access     public
     * @return     An initialized TeachingMethodSearcher instance
     */
    public function __construct($db_cn)
    {
        $this->sql_stmt = 
            'select mth.mth_id, mth.mth_name, mth.mth_phase, mth.mth_prep_min, mth.mth_prep_max, ' .
            '       mth.mth_exec_min, mth.mth_exec_max, mth.mth_topic, mth.mth_type, mth.mth_soc_form, ' .
            '       mth.mth_age_grp, mth.mth_summary, rtg.rtg_count, rtg.rtg_sum, ' .
            '       dld.dld_count, dld.dld_last_date, ' .
            '       mau.mth_auth_name, att.att_guid, att.att_name ' .
            'from   ta_mth_method mth ' .
            '       inner join ta_mth_method_attachment att on mth.mth_id = att.att_mth_id ' .
            '       inner join ( select mth_id, group_concat(mth_auth_name order by mth_seq separator "<br>") as mth_auth_name ' .
            '                    from ta_mth_method_author ' .
            '                    group by mth_id ) mau on mth.mth_id = mau.mth_id ' .
            '       left join  ( select rtg_mth_id, count(1) as rtg_count, sum(rtg_rating) as rtg_sum ' .
            '                    from ta_mth_statistics_rating ' .
            '                    group by rtg_mth_id ) rtg on mth.mth_id = rtg.rtg_mth_id ' .
            '       left join ( select dld_mth_id, count(1) as dld_count, max(dld_date) as dld_last_date' .
            '                   from ta_mth_statistics_download ' .
            '                   group by dld_mth_id ) dld on mth.mth_id = dld.dld_mth_id ' .
            'where  mth.mth_status = 0 ';

        $this->sql_order_clause = ' order by mth_name, mth_id ';
        $this->sql_par_values = array();
        $this->sql_par_types = '';
        $this->sql_par_num = 0;
        
        $this->db_conn = $db_cn;
    }
    
    /**
     * Sets the search criterion to be compared to the method name
     * 
     * @param      string    $mth_name      Search criterion for method name
     * @access     public
     */
    public function set_mth_name($mth_name)
    {
        if (! empty($mth_name))
        {
            $this->sql_stmt = $this->sql_stmt . ' and mth.mth_name like ? ';
            $this->sql_par_types = $this->sql_par_types . 's';
            $this->sql_par_values[$this->sql_par_num] = '%' . $mth_name . '%';
            $this->sql_par_num += 1;
        }
    }
    
    /**
     * Sets the search criterion to be compared to the method topic
     * 
     * @param      string    $mth_topic     Search criterion for method topic
     * @access     public
     */
    public function set_mth_topic($mth_topic)
    {
        if (! empty($mth_topic))
        {
            $this->sql_stmt = $this->sql_stmt . ' and mth.mth_topic like ? ';
            $this->sql_par_types = $this->sql_par_types . 's';
            $this->sql_par_values[$this->sql_par_num] = '%' . $mth_topic . '%';
            $this->sql_par_num += 1;
        }
    }
    
    /**
     * Sets the search criterion to be compared to the preparation duration of the method
     * 
     * @param      integer   $mth_prep      Search criterion for preparation duration
     * @access     public
     */
    public function set_mth_prep($mth_prep)
    {
        if (! empty($mth_prep))
        {
            $this->sql_stmt = $this->sql_stmt . ' and mth.mth_prep_min >= ? and mth.mth_prep_max <= ? ';
            $this->sql_par_types = $this->sql_par_types . 'i';
            $this->sql_par_values[$this->sql_par_num] = $mth_prep;
            $this->sql_par_num += 1;
            $this->sql_par_types = $this->sql_par_types . 'i';
            $this->sql_par_values[$this->sql_par_num] = $mth_prep;
            $this->sql_par_num += 1;
        }
    }

    /**
     * Sets the search criterion to be compared to the execution duration of the method
     * 
     * @param      integer   $mth_exec      Search criterion for execution duration
     * @access     public
     */
    public function set_mth_exec($mth_exec)
    {
        if (! empty($mth_exec))
        {
            $this->sql_stmt = $this->sql_stmt . ' and mth.mth_exec_min >= ? and mth.mth_exec_max <= ? ';
            $this->sql_par_types = $this->sql_par_types . 'i';
            $this->sql_par_values[$this->sql_par_num] = $mth_exec;
            $this->sql_par_num += 1;
            $this->sql_par_types = $this->sql_par_types . 'i';
            $this->sql_par_values[$this->sql_par_num] = $mth_exec;
            $this->sql_par_num += 1;
        }
    }
    
    /**
     * Sets the search criterion to be compared to the age group of the teaching method
     * 
     * @param      integer   $mth_age_grp   Search criterion for age group
     * @access     public
     */
    public function set_mth_age_grp($mth_age_grp)
    {
        if (! empty($mth_age_grp))
        {
            $this->sql_stmt = $this->sql_stmt . ' and mth.mth_age_grp is not null and mth.mth_age_grp = ? ';
            $this->sql_par_types = $this->sql_par_types . 'i';
            $this->sql_par_values[$this->sql_par_num] = $mth_age_grp;
            $this->sql_par_num += 1;
        }
    }
    
    /**
     * Sets the search criterion for the teaching phase of a teaching method
     * 
     * @param      string    $mth_phase     Search criterion for the teaching phase
     * @access     public
     */
    public function set_mth_phase($mth_phase)
    {
        if (! empty($mth_phase))
        {
            $ph_cnt = count($mth_phase);
            for ($i = 0; $i < $ph_cnt; $i++)
            {
                if ($i == 0)
                {
                    $this->sql_stmt = $this->sql_stmt . ' and (instr(mth.mth_phase,?) > 0 ';
                }
                else
                {
                    $this->sql_stmt = $this->sql_stmt . ' or instr(mth.mth_phase,?) > 0 ';
                }
                $this->sql_par_types = $this->sql_par_types . 's';
                $this->sql_par_values[$this->sql_par_num] = $mth_phase[$i];
                $this->sql_par_num += 1;
            }
            if ($ph_cnt > 0)
            {
                $this->sql_stmt = $this->sql_stmt . ') ';
            }
        }
    }
    
    /**
     * Sets the search criterion for the teaching type of a teaching method
     * 
     * @param      string    $mth_type      Search criterion for the teaching type
     * @access     public
     */
    public function set_mth_type($mth_type)
    {
        if (! empty($mth_type))
        {
            $ty_cnt = count($mth_type);
            for ($i = 0; $i < $ty_cnt; $i++)
            {
                if ($i == 0)
                {
                    $this->sql_stmt = $this->sql_stmt . ' and (instr(mth.mth_type,?) > 0 ';
                }
                else
                {
                    $this->sql_stmt = $this->sql_stmt . ' or instr(mth.mth_type,?) > 0 ';
                }
                $this->sql_par_types = $this->sql_par_types . 's';
                $this->sql_par_values[$this->sql_par_num] = $mth_type[$i];
                $this->sql_par_num += 1;
            }
            if ($ty_cnt > 0)
            {
                $this->sql_stmt = $this->sql_stmt . ') ';
            }
        }
    }

    /**
     * Sets the search criterion for the social form of a teaching method
     * 
     * @param      string    $mth_socform   Search criterion for the social form
     * @access     public
     */
    public function set_mth_socform($mth_socform)
    {
        if (! empty($mth_socform))
        {
            $sfcnt = count($mth_socform);
            for ($i = 0; $i < $sfcnt; $i++)
            {
                if ($i == 0)
                {
                    // $this->sql_stmt = $this->sql_stmt . ' and mth.mth_soc in (?';
                    $this->sql_stmt = $this->sql_stmt . ' and (instr(mth.mth_soc_form,?) > 0 ';
                }
                else
                {
                    // $this->sql_stmt = $this->sql_stmt . ',?';
                    $this->sql_stmt = $this->sql_stmt . ' or instr(mth.mth_soc_form,?) > 0 ';
                }
                $this->sql_par_types = $this->sql_par_types . 's';
                $this->sql_par_values[$this->sql_par_num] = $mth_socform[$i];
                $this->sql_par_num += 1;
            }
            if ($sfcnt > 0)
            {
                $this->sql_stmt = $this->sql_stmt . ') ';
            }
        }
    }
    
    /**
     * Sets the search criterion for the author of a teaching method
     * 
     * @param      string    $mth_author    Search criterion for the author
     * @access     public
     */
    public function set_mth_author($mth_autor)
    {
        if (! empty($mth_autor))
        {
            $this->sql_stmt = $this->sql_stmt . ' and mau.mth_auth_name like ? ';
            $this->sql_par_types = $this->sql_par_types . 's';
            $this->sql_par_values[$this->sql_par_num] = '%' . $mth_autor . '%';
            $this->sql_par_num += 1;
        }
    }
    
    /**
     * Executes the search using the given search criteria and returns the
     * found teaching method items
     * 
     * @access     public
     * @return     array()   Associative array of teaching method items
     */
     // @return     array('mth_id','mth_name','mth_phase','mth_prep_min','mth_prep_max','mth_exec_min','mth_exec_max',
     //                   'mth_topic','mth_type','mth_soc_form','mth_seq','mth_auth_name','mth_att_guid','mth_att_name')
    public function get_result()
    {
        $method_list = array();
        $stm1 = $this->db_conn->prepare($this->sql_stmt . $this->sql_order_clause);
        
        if ($this->sql_par_num > 0)
        {
            $param_array = array();
            $param_array[] = & $stm1;
            $param_array[] = & $this->sql_par_types;
            for ($cnt = 0; $cnt < $this->sql_par_num; $cnt++)
            {
                $param_array[] = & $this->sql_par_values[$cnt];
            }
            call_user_func_array("mysqli_stmt_bind_param", $param_array);
        }
        
        if ($stm1->execute())
        {
            $mth_id = 0;
            $mth_name = '';
            $mth_topic = '';
            $mth_prep_min = 0;
            $mth_prep_max = 0;
            $mth_exec_min = 0;
            $mth_exec_max = 0;
            $mth_phase = '';
            $mth_type = '';
            $mth_soc = '';
            $mth_age_grp = 0;
            $mth_summary = '';
            $rtg_count = 0;
            $rtg_sum = 0;
            $dld_count = 0;
            $dld_last_date = '';
            $mth_author = '';
            $mth_att_guid = '';
            
            $stm1->bind_result(
                $mth_id, $mth_name, $mth_phase, $mth_prep_min, $mth_prep_max, $mth_exec_min, $mth_exec_max, $mth_topic, $mth_type, $mth_soc, 
                $mth_age_grp, $mth_summary, $rtg_count, $rtg_sum, $dld_count, $dld_last_date, $mth_author, $mth_att_guid, $mth_att_name);
    
            while ($stm1->fetch() != NULL)
            {
                $method_list[] = 
                    array(
                        'mth_id' => $mth_id,
                        'mth_name' => $mth_name,
                        'mth_phase' => $mth_phase,
                        'mth_prep_min' => $mth_prep_min,
                        'mth_prep_max' => $mth_prep_max,
                        'mth_exec_min' => $mth_exec_min,
                        'mth_exec_max' => $mth_exec_max,
                        'mth_topic' => $mth_topic,
                        'mth_type' => $mth_type,
                        'mth_soc_form' => $mth_soc,
                        'mth_age_grp' => $mth_age_grp,
                        'mth_summary' => $mth_summary,
                        'rtg_count' => $rtg_count,
                        'rtg_sum' => $rtg_sum,
                        'dld_count' => $dld_count,
                        'dld_last_date' => $dld_last_date,
                        'mth_auth_name' => $mth_author,
                        'mth_att_guid' => $mth_att_guid,
                        'mth_att_name' => $mth_att_name
                    );
            }
        }
        
        $stm1->free_result();
        $stm1->close();
        
        return $method_list;
    }
}

/**
 * MethodListByDownload      Retrieve all teaching methods downloaded by the current 
 *                           user but not yet rated
 * 
 * @package   MethodListByDownload
 * @author    Walter Pachlinger (walter.pachlinger@gmx.at)
 * @version   $Revision: 1.0 $
 * @access    public
 */
class MethodListByDownload
{
    private $db_conn;
    private $usr_id;

    /**
     * Constructor: creates and initializes the object
     * 
     * @param      mysqli    $db_cn         database connection
     * @param      int       $us_id         Identifier of a user account
     * @access     public
     * @return     An initialized MethodListByDownload object
     */
    public function __construct($db_cn, $us_id)    
    {
        $this->db_conn = $db_cn;
        $this->usr_id = $us_id;
    }
    
    /**
     * Executes the select statement based on the given user identification and returns
     * the list of found teaching methods as an array
     * 
     * @access     public
     * @return     Array of teaching methods that are mapped to an associative array each
     */
    public function get_result()
    {
        $select_stmt = 
            'select mth.mth_id, mth.mth_name, mth.mth_phase, mth.mth_prep_min, mth.mth_prep_max, ' .
            '       mth.mth_exec_min, mth.mth_exec_max, mth.mth_topic, mth.mth_type, mth.mth_soc_form, ' .
            '       mth.mth_age_grp, mth.mth_summary, ' .
            '       dld.dld_count, dld.dld_last_date, ' .
            '       coalesce(rtg.rtg_count,0) rtg_count ' .
            'from   ta_mth_method mth ' .
            '       inner join ( select dld_usr_id, dld_mth_id, count(1) as dld_count, max(dld_date) as dld_last_date' .
            '                    from ta_mth_statistics_download ' .
            '                    group by dld_mth_id, dld_usr_id ) dld on mth.mth_id = dld.dld_mth_id ' .
            '       left join  ( select rtg_mth_id, count(1) as rtg_count ' .
            '                    from ta_mth_statistics_rating ' .
            '                    group by rtg_mth_id ) rtg on mth.mth_id = rtg.rtg_mth_id ' .
            'where  mth.mth_status = 0 ' .
            '  and  dld.dld_usr_id = ? ' .
            'order by dld_last_date desc;';


        $method_list = array();
        $stm5 = $this->db_conn->prepare($select_stmt);
        $stm5->bind_param('i', $this->usr_id);
        if ($stm5->execute())
        {
            $mth_id = 0;
            $mth_name = '';
            $mth_topic = '';
            $mth_prep_min = 0;
            $mth_prep_max = 0;
            $mth_exec_min = 0;
            $mth_exec_max = 0;
            $mth_phase = '';
            $mth_type = '';
            $mth_soc = '';
            $mth_age_grp = 0;
            $mth_summary = '';
            $dld_count = 0;
            $dld_last_date = '';
            $rtg_count = 0;
            
            $stm5->bind_result(
                $mth_id, $mth_name, $mth_phase, $mth_prep_min, $mth_prep_max, $mth_exec_min, $mth_exec_max, $mth_topic, $mth_type, $mth_soc, 
                $mth_age_grp, $mth_summary, $dld_count, $dld_last_date, $rtg_count);
            
            while($stm5->fetch() != NULL)
            {
                $method_list[] = 
                    array(
                        'mth_id' => $mth_id,
                        'mth_name' => $mth_name,
                        'mth_phase' => $mth_phase,
                        'mth_prep_min' => $mth_prep_min,
                        'mth_prep_max' => $mth_prep_max,
                        'mth_exec_min' => $mth_exec_min,
                        'mth_exec_max' => $mth_exec_max,
                        'mth_topic' => $mth_topic,
                        'mth_type' => $mth_type,
                        'mth_soc_form' => $mth_soc,
                        'mth_age_grp' => $mth_age_grp,
                        'mth_summary' => $mth_summary,
                        'dld_count' => $dld_count,
                        'dld_last_date' => $dld_last_date,
                        'rtg_count' => $rtg_count
                    );
                
            }
            
            $stm5->free_result();
            $stm5->close();
            
            return $method_list;
        }
    }
}

/**
 * MethodListByOwnership     Retrieve all teaching methods that are owned by the current
 *                           user
 * 
 * @package   MethodListByOwnership
 * @author    Walter Pachlinger (walter.pachlinger@gmx.at)
 * @version   $Revision: 1.0 $
 * @access    public
 */
class MethodListByOwnership
{
    private $db_conn;
    private $usr_id;
    
    /**
     * Constructor: creates and initializes the object
     * 
     * @param      mysqli    $db_cn         database connection
     * @param      int       $us_id         Identifier of a user account
     * @access     public
     * @return     An initialized MethodListByOwnership object
     */
    public function __construct($db_cn, $us_id)
    {
        $this->db_conn = $db_cn;
        $this->usr_id = $us_id;
    }
    
    /**
     * Executes the select statement based on the given user identification and returns
     * the list of found teaching methods as an array
     * 
     * @access     public
     * @return     Array of teaching methods that are mapped to an associative array each
     */
    public function get_result()
    {
        $select_stmt = 
            'select mth.mth_id, mth.mth_name, mth.mth_phase, mth.mth_prep_min, mth.mth_prep_max, ' .
            '       mth.mth_exec_min, mth.mth_exec_max, mth.mth_topic, mth.mth_type, mth.mth_soc_form, ' .
            '       mth.mth_age_grp, mth.mth_summary, ' .
            '       dld.dld_count, dld.dld_last_date, ' .
            '       coalesce(rtg.rtg_count,0) rtg_count, coalesce(rtg.rtg_sum,0) rtg_sum, ' .
            '       coalesce(rtg.rtg_sum,0) / (coalesce(rtg.rtg_count,0) + 1) rtg_sort ' .
            'from   ta_mth_method mth ' .
            '       left join ( select dld_usr_id, dld_mth_id, count(1) as dld_count, max(dld_date) as dld_last_date' .
            '                   from ta_mth_statistics_download ' .
            '                   group by dld_mth_id, dld_usr_id ) dld on mth.mth_id = dld.dld_mth_id ' .
            '       left join  ( select rtg_mth_id, count(1) as rtg_count, sum(rtg_rating) as rtg_sum ' .
            '                    from ta_mth_statistics_rating ' .
            '                    group by rtg_mth_id ) rtg on mth.mth_id = rtg.rtg_mth_id ' .
            'where  mth.mth_status = 0 and mth.mth_owner_id = ? ' .
            'order by rtg_sort asc;';
        
        $method_list = array();
        $stm6 = $this->db_conn->prepare($select_stmt);
        $stm6->bind_param('i', $this->usr_id);
        if ($stm6->execute())
        {
            $mth_id = 0;
            $mth_name = '';
            $mth_topic = '';
            $mth_prep_min = 0;
            $mth_prep_max = 0;
            $mth_exec_min = 0;
            $mth_exec_max = 0;
            $mth_phase = '';
            $mth_type = '';
            $mth_soc = '';
            $mth_age_grp = 0;
            $mth_summary = '';
            $dld_count = 0;
            $dld_last_date = '';
            $rtg_count = 0;
            $rtg_sum = 0;
            $rtg_sort = 0;
            
            $stm6->bind_result(
                $mth_id, $mth_name, $mth_phase, $mth_prep_min, $mth_prep_max, $mth_exec_min, $mth_exec_max, $mth_topic, $mth_type, $mth_soc, 
                $mth_age_grp, $mth_summary, $dld_count, $dld_last_date, $rtg_count, $rtg_sum, $rtg_sort);
            
            while($stm6->fetch() != NULL)
            {
                $method_list[] = 
                    array(
                        'mth_id' => $mth_id,
                        'mth_name' => $mth_name,
                        'mth_phase' => $mth_phase,
                        'mth_prep_min' => $mth_prep_min,
                        'mth_prep_max' => $mth_prep_max,
                        'mth_exec_min' => $mth_exec_min,
                        'mth_exec_max' => $mth_exec_max,
                        'mth_topic' => $mth_topic,
                        'mth_type' => $mth_type,
                        'mth_soc_form' => $mth_soc,
                        'mth_age_grp' => $mth_age_grp,
                        'mth_summary' => $mth_summary,
                        'dld_count' => $dld_count,
                        'dld_last_date' => $dld_last_date,
                        'rtg_count' => $rtg_count,
                        'rtg_sum' => $rtg_sum
                    );
                
            }
            
            $stm6->free_result();
            $stm6->close();
            
            return $method_list;
        }
    }
}
?>