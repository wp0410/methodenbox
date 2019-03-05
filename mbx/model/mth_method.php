<?php
//---------------------------------------------------------------------------------------
//  Copyright (c) 2018 Walter Pachlinger (walter.pachlinger@gmx.at)
//    
//  Licensed under the Apache License, Version 2.0 (the "License"); you may not use this 
//  file except in compliance with the License. You may obtain a copy of the License at
//      http://www.apache.org/licenses/LICENSE-2.0
//  Unless required by applicable law or agreed to in writing, software distributed under 
//  the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF 
//  ANY KIND, either express or implied. See the License for the specific language 
//  governing permissions and limitations under the License.
//----------------------------------------------------------------------------------------
include_once '../model/sql_connection.php';
include_once '../model/app_result.php';
include_once '../model/aux_helpers.php';
include_once '../model/mth_selection.php';

class TeachingMethod implements JsonSerializable
{
    private $mth_id;
    public  $mth_name;
    public  $mth_summary;
    public  $mth_subject;
    public  $mth_subject_area;
    public  $mth_age_grp;
    public  $mth_prep_time;
    public  $mth_exec_time;
    public  $mth_phase;
    public  $mth_soc_form;
    private $mth_authors;
    private $mth_owner_id;
    
    private $db_conn;
    
    public function __construct($db_cn, $mth_owner_id)
    {
        $this->db_conn = $db_cn;
        $this->mth_owner_id = $mth_owner_id;
        $this->mth_id = -1;
        $this->mth_name = $this->mth_summary = '';
        $this->mth_subject = $this->mth_subject_area = '';
        $this->mth_age_grp = $this->mth_prep_time = $this->mth_exec_time = '';
        $this->mth_phase = $this->mth_soc_form = '';
        $this->mth_authors = '';
    }
    
    public function getId()
    {
        return $this->mth_id;
    }
    
    public function jsonSerialize()
    {
        return array(
                  'mth_id' => $this->mth_id,
                  'mth_name' => $this->mth_name,
                  'mth_summary' => $this->mth_summary,
                  'mth_subject' => array ($this->mth_subject, MethodSelectionFactory::getSubjectName($this->mth_subject)),
                  'mth_subject_area' => array($this->mth_subject_area, MethodSelectionFactory::getSubjectAreaName($this->mth_subject, $this->mth_subject_area)),
                  'mth_age_group'    => array($this->mth_age_grp, MethodSelectionFactory::getAgeGroupName($this->mth_age_grp)),
                  'mth_prep_time'    => array($this->mth_prep_time, MethodSelectionFactory::getPrepTimeName($this->mth_prep_time)),
                  'mth_exec_time'    => array($this->mth_exec_time, MethodSelectionFactory::getExecTimeName($this->mth_exec_time)),
                  'mth_phase'        => $this->mth_phase,
                  'mth_soc_form'     => $this->mth_soc_form,
                  'mth_owner_id'     => $this->mth_owner_id,
                  'mth_authors'      => $this->mth_authors
               );
    }
    
    public function setAuthors($auth_prim, $auth_add)
    {
        $this->mth_authors = array();
        $this->mth_authors[] = $auth_prim;

        $add_list = explode('<br>', nl2br($auth_add, false));
        foreach($add_list as $add_author)
        {
            if (strlen(trim($add_author)) > 0)
            {
                $this->mth_authors[] = $add_author;
            }
        }
    }
    
    public function getAuthors()
    {
        return $this->mth_authors;
    }
    
    public function createMethod()
    {
        $result = new AppResult(0);

        $mth_phase = Helpers::arrayToString($this->mth_phase);
        $mth_soc_form = Helpers::arrayToString($this->mth_soc_form);
        $mth_authors = Helpers::arrayToString($this->mth_authors);
        
        $sql_stmt =
            'insert into ta_mth_method_header( ' . 
            '   mth_name, mth_summary, mth_subject, mth_subject_area, mth_age_grp, mth_prep_time, mth_exec_time, ' .
            '   mth_phase, mth_soc_form, mth_authors, mth_owner_id ) ' .
            'values ( ' .
            '   ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? );';
        $stm_mh1 = $this->db_conn->prepare($sql_stmt);
        $stm_mh1->bind_param('ssssssssssi', $this->mth_name, $this->mth_summary, $this->mth_subject, $this->mth_subject_area,
                    $this->mth_age_grp, $this->mth_prep_time, $this->mth_exec_time, $mth_phase, $mth_soc_form, 
                    $mth_authors, $this->mth_owner_id);
        if ($stm_mh1->execute())
        {
            $this->mth_id = $stm_mh1->insert_id;
        }
        else
        {
            $result = new AppResult(601);
        }
        $stm_mh1->close();
        
        return $result;
    }
    
    public function loadMethod($mth_id)
    {
        $result = null;
        
        $sql_stmt =
            'select mth_id, mth_name, mth_summary, mth_subject, mth_subject_area, mth_age_grp, mth_prep_time, mth_exec_time, ' .
            '       mth_phase, mth_soc_form, mth_authors, mth_owner_id ' .
            'from   ta_mth_method_header ' .
            'where  mth_id=?;';
        $stm_mh2 = $this->db_conn->prepare($sql_stmt);
        $stm_mh2->bind_param('i', $mth_id);
        if ($stm_mh2->execute())
        {
            $stm_mh2->store_result();
            
            $mth_phase = '';
            $mth_soc_form = '';
            $mth_authors = '';
            
            $stm_mh2->bind_result(
                        $this->mth_id, $this->mth_name, $this->mth_summary, $this->mth_subject, $this->mth_subject_area,
                        $this->mth_age_grp, $this->mth_prep_time, $this->mth_exec_time, $mth_phase, $mth_soc_form, $mth_authors,
                        $this->mth_owner_id );
            if ($stm_mh2->fetch())
            {
                $this->mth_phase = Helpers::stringToArray($mth_phase);
                $this->mth_soc_form = Helpers::stringToArray($mth_soc_form);
                $this->mth_authors = Helpers::stringToArray($mth_authors);
                
                $result = new AppResult(0); // OK
            }
            else
            {
                $result = new AppResult(602);
            }
            $stm_mh2->free_result();
        }
        else
        {
            $result = new AppResult(603);
        }
        $stm_mh2->close();
        
        return $result;
    }
    
    public function deleteMethod($mth_id)
    {
        $stm_mh3 = $this->db_conn->prepare('delete from ta_mth_method_header where mth_id=?;');
        $stm_mh3->bind_param('i', $mth_id);
        if ($stm_mh3->execute())
        {
            $result = new AppResult(0);
        }
        else
        {
            $result = new AppResult(604);
        }
        $stm_mh3->close();
        
        return $result;
    }
}
?>
