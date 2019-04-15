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
include_once '../model/sql_connection.php';

class MethodSelectionFactory
{
    public static function getSubjects()
    {
        $subjects = array();
        $db_conn = DatabaseConnection::get_connection();
        
        $stm_ms2 = $db_conn->prepare('select distinct mth_sub_seq, mth_sub_val, mth_sub_name from ta_mth_subject_area order by mth_sub_seq;');
        if ($stm_ms2->execute())
        {
            $sub_seq = 0;
            $sub_val = '';
            $sub_name = '';
            $stm_ms2->bind_result($sub_seq, $sub_val, $sub_name);
            
            while($stm_ms2->fetch())
            {
                $subjects[] = array('SEQ' => $sub_seq, 'VAL' => $sub_val, 'NAME' => $sub_name);
            }
            
            $stm_ms2->free_result();
        }
        $stm_ms2->close();
        
        return $subjects;
    }
    
    public static function getSubjectAreas($mth_sub_val)
    {
       $areas = array();
       $db_conn = DatabaseConnection::get_connection();
       
       $stm_ms1 = $db_conn->prepare('select mth_area_seq, mth_area_val, mth_area_name from ta_mth_subject_area where mth_sub_val = ? order by mth_area_seq;');
       $stm_ms1->bind_param('s', $mth_sub_val);
       if ($stm_ms1->execute())
       {
           $area_seq = 0;
           $area_val = '';
           $area_name = '';
           $stm_ms1->bind_result($area_seq, $area_val, $area_name);
           
           while($stm_ms1->fetch())
           {
               $areas[] = array('SEQ' => $area_seq, 'VAL' => $area_val, 'NAME' => $area_name);
           }
           
           $stm_ms1->free_result();
       }
       $stm_ms1->close();
       
       return $areas;
    }

    public static function getSubjectName($sub_val)
    {
        $sub_name = '';
        $db_conn = DatabaseConnection::get_connection();
        
        $stm_ms4 = $db_conn->prepare('select mth_sub_name from ta_mth_subject_area where mth_sub_val=?;');
        $stm_ms4->bind_param('s', $sub_val);
        if ($stm_ms4->execute())
        {
            $stm_ms4->bind_result($sub_name);
            $stm_ms4->fetch();
            $stm_ms4->free_result();
        }
        $stm_ms4->close();
        
        return $sub_name;
    }
    
    public static function getSubjectAreaName($sub_val, $area_val)
    {
        $area_name = '';
        $db_conn = DatabaseConnection::get_connection();
        
        $stm_ms5 = $db_conn->prepare('select mth_area_name from ta_mth_subject_area where mth_sub_val=? and mth_area_val=?;');
        $stm_ms5->bind_param('ss', $sub_val, $area_val);
        if ($stm_ms5->execute())
        {
            $stm_ms5->bind_result($area_name);
            $stm_ms5->fetch();
            $stm_ms5->free_result();
        }
        $stm_ms5->close();
        
        return $area_name;
    }
    
    private static function getOptions($key_val)
    {
        $result_array = array();

        $db_conn = DatabaseConnection::get_connection();

        $stm_ms3 = $db_conn->prepare('select mth_opt_seq, mth_opt_val, mth_opt_name from ta_mth_selections where mth_sel_val = ? order by mth_opt_seq;');
        $stm_ms3->bind_param('s', $key_val);
        if ($stm_ms3->execute())
        {
            $opt_seq = 0;
            $opt_val = '';
            $opt_name = '';
            
            $stm_ms3->bind_result($opt_seq, $opt_val, $opt_name);
            while($stm_ms3->fetch())
            {
                $result_array[] = array('SEQ' => $opt_seq, 'VAL' => $opt_val, 'NAME' => $opt_name);
            }
            $stm_ms3->free_result();
        }
        $stm_ms3->close();
        
        return $result_array;
    }
    
    private static function getOptionName($key_val, $opt_val)
    {
        $opt_name = '';
        $db_conn = DatabaseConnection::get_connection();
        
        $stm_ms6 = $db_conn->prepare('select mth_opt_name from ta_mth_selections where mth_sel_val=? and mth_opt_val=?;');
        $stm_ms6->bind_param('ss', $key_val, $opt_val);
        if ($stm_ms6->execute())
        {
            $stm_ms6->bind_result($opt_name);
            $stm_ms6->fetch();
            $stm_ms6->free_result();
        }
        $stm_ms6->close();
        
        return $opt_name;
    }
    
    public static function getAgeGroups()
    {
        return MethodSelectionFactory::getOptions('JG');
    }
    
    public static function getAgeGroupName($age_val)
    {
        return MethodSelectionFactory::getOptionName('JG', $age_val);
    }
    
    public static function getPrepTime()
    {
        return MethodSelectionFactory::getOptions('TM.P');
    }
    
    public static function getPrepTimeName($prep_val)
    {
        return MethodSelectionFactory::getOptionName('TM.P', $prep_val);
    }
    
    public static function getExecTime()
    {
        return MethodSelectionFactory::getOptions('TM.E');
    }
    
    public static function getExecTimeName($exec_val)
    {
        return MethodSelectionFactory::getOptionName('TM.E', $exec_val);
    }
    
    public static function getAuthors()
    {
        $result_array = array();
        $temp_authors = array();

        $db_conn = DatabaseConnection::get_connection();
        $stm_ms7 = $db_conn->prepare('select mth_authors from ta_mth_method_header;');
        if ($stm_ms7->execute())
        {
            $authors = '';
            $stm_ms7->bind_result($authors);
            while($stm_ms7->fetch())
            {
                $auth_array = Helpers::stringToArray($authors);
                foreach($auth_array as $auth)
                {
                    if (! in_array($auth, $temp_authors))
                    {
                        $temp_authors[] = $auth;
                    }
                }
            }
            $stm_ms7->free_result();
        }
        $stm_ms7->close();
        
        $line_no = 1;
        sort($temp_authors, SORT_STRING);
        foreach($temp_authors as $auth)
        {
            $result_array[] = array('SEQ' => $line_no, 'VAL' => $auth, 'NAME' => $auth);
            $line_no++;
        }
        
        return $result_array;
    }
    
    public static function getOwners($except = 0)
    {
        $result_array = array();
        
        $db_conn = DatabaseConnection::get_connection();
        $sql_stmt = 
            'select mth.mth_owner_id, acc.usr_fst_name, acc.usr_lst_name, acc.usr_email ' .
            'from   ta_mth_method_header mth ' .
            '          left join ta_usr_account acc on mth.mth_owner_id = acc.usr_id ' .
            'where  acc.usr_id != ? ' .
            'order by acc.usr_lst_name asc, acc.usr_fst_name asc, acc.usr_email asc;';
        $stm_ms8 = $db_conn->prepare($sql_stmt);
        $stm_ms8->bind_param('i', $except);
        if ($stm_ms8->execute())
        {
            $usr_id = 0;
            $usr_fst_name = $usr_lst_name = $usr_email = '';
            
            $stm_ms8->bind_result($usr_id, $usr_fst_name, $usr_lst_name, $usr_email);
            $line_no = 1;
            while($stm_ms8->fetch())
            {
                $result_array[] = array('SEQ' => $line_no, 'VAL' => $usr_id, 'NAME' => $usr_fst_name . ' ' . $usr_lst_name); // . ' (' . $usr_email . ')');
                $line_no++;
            }
        }
        $stm_ms8->close();
        
        return $result_array;
    }
}

?>
