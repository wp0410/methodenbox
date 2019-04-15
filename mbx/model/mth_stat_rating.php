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
include_once '../model/app_result.php';
include_once '../model/aux_helpers.php';

class MethodRating
{
    private $rtg_id;
    private $rtg_mth_id;
    private $rtg_usr_id;
    public $rtg_date;
    public $rtg_value;
    public $rtg_summary;
    
    private $db_conn;
    
    public function __construct($db_cn = null)
    {
        $this->rtg_id = -1;
        $this->rtg_mth_id = -1;
        $this->rtg_usr_id = -1;
        $this->rtg_date = Helpers::dateTimeString(time());
        $this->rtg_value = null;
        $this->rtg_summary = '';
        $this->db_conn = $db_cn;
    }
    
    public function initializeCreate($mth_id, $usr_id)
    {
        $this->rtg_mth_id = $mth_id;
        $this->rtg_usr_id = $usr_id;
    }
    
    public function initializeView($rtg_id, $rtg_mth_id, $rtg_usr_id)
    {
        $this->rtg_id = $rtg_id;
        $this->rtg_mth_id = $rtg_mth_id;
        $this->rtg_usr_id = $rtg_usr_id;
    }
    
    public function createRating()
    {
        if ($this->db_conn == null)
        {
            return;
        }
        
        $result = null;
        
        $sql_stmt = 
            'insert into ta_mth_method_rating( rtg_mth_id, rtg_usr_id, rtg_date, rtg_value, rtg_summary ) ' .
            'values( ?, ?, ?, ?, ? );';
        $stm_rt1 = $this->db_conn->prepare($sql_stmt);
        $stm_rt1->bind_param(
                    'iisds', $this->rtg_mth_id, $this->rtg_usr_id, $this->rtg_date, 
                    $this->rtg_value, $this->rtg_summary);
        if ($stm_rt1->execute())
        {
            $this->rtg_id = $stm_rt1->insert_id;
            $result = new AppResult(0);
        }
        else
        {
            $result = new AppResult(671);
        }
        $stm_rt1->close();
        
        return $result;
    }
    
    public function userCanRate()
    {
        $has_downloaded = 0;
        $has_rated = 0;
        
        $stm_rt3 = $this->db_conn->prepare('select count(1) as has_downloaded from ta_mth_method_download where dnl_usr_id=?;');
        $stm_rt3->bind_param('i', $this->rtg_usr_id);
        if ($stm_rt3->execute())
        {
            $stm_rt3->bind_result($has_downloaded);
            $stm_rt3->fetch();
        }
        $stm_rt3->close();
        
        $stm_rt4 = $this->db_conn->prepare('select count(1) as has_rated from ta_mth_method_rating where rtg_usr_id=?;');
        $stm_rt4->bind_param('i', $this->rtg_usr_id);
        if ($stm_rt4->execute())
        {
            $stm_rt4->bind_result($has_rated);
            $stm_rt4->fetch();
        }
        $stm_rt4->close();
        
        return (has_rated == 0) && (has_downloaded > 0); 
    }
}

class MethodRatingList
{
    public $ratings;
    
    private $db_conn;
    private $mth_id;
    
    public function __construct($db_cn, $mth_id)
    {
        $this->ratings = array();
        $this->db_conn = $db_cn;
        $this->mth_id = $mth_id;
    }
    
    public function retrieveList($cmp_rtg_date, $max_lines)
    {
        $sql_stmt =
            'select rtg_id, rtg_mth_id, rtg_date, rtg_value, rtg_summary ' .
            'from   ta_mth_method_rating ' .
            'where  rtg_mth_id = ? and rtg_date >= ? ' .
            'order by rtg_date desc;';
        $stm_rt5 = $this->db_conn->prepare($sql_stmt);
        $stm_rt5->bind_param('is', $this->mth_id, $cmp_rtg_date);
        if ($stm_rt5->execute())
        {
            $rtg_id = $rtg_mth_id = 0;
            $rtg_date = $rtg_summary = '';
            $rtg_value = 0;
            
            $stm_rt5->bind_result($rtg_id, $rtg_mth_id, $rtg_date, $rtg_value, $rtg_summary);
            $has_limit = ($max_lines > 0);
            $line_no = 0;
            
            while($stm_rt5->fetch() && ((! $has_limit) || ($line_no < $max_lines)))
            {
                $rtg = new MethodRating();
                $rtg->initializeView($rtg_id, $rtg_mth_id, 0);
                $rtg->rtg_date = $rtg_date;
                $rtg->rtg_value = $rtg_value;
                $rtg->rtg_summary = $rtg_summary;
                
                $this->ratings[] = $rtg;
                $line_no++;
            }
            
            $stm_rt5->free_result();
        }
        $stm_rt5->close();
    }
}
?>