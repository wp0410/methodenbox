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

class MethodResultView
{
    public $lines;
    
    private $db_conn;
    private $select_stmt;
    private $num_rows_db;
    private $num_rows_view;
    private $stm_type;

    public function __construct($db_cn)
    {
        $this->lines = array();
        
        $this->db_conn = $db_cn;
        $this->stm_type = null;
        $this->num_rows_db = $this->num_rows_view = 0;
    }
    
    public function initSearchResultStmt()
    {
        $this->select_stmt = 
            'select mth_id, mth_name, mth_summary, mth_subject, mth_subject_text, ' . 
            '       mth_subject_area, mth_subject_area_text, mth_age_grp, mth_age_grp_text, ' .
            '       mth_prep_time, mth_prep_time_text, mth_exec_time, mth_exec_time_text, ' .
            '       mth_phase, mth_soc_form, mth_authors, ' .
            '       mth_owner_id, mth_create_time, ' . 
            '       file_guid, file_name, ' .
            '       dnl_cnt, dnl_first_tm, dnl_last_tm, dnl_usr_id, ' .
            '       rtg_cnt, rtg_first_tm, rtg_last_tm, rtg_min_val, rtg_max_val, rtg_avg_val ' .
            'from   vi_mth_method_result where mth_id > 0 ';
        $this->stm_type = 'SEARCH_RESULT';        
    }
    
    public function initRatingListStmt($usr_id)
    {
        $this->select_stmt = 
            'select mth_id, mth_name, mth_summary, mth_subject, mth_subject_text, ' . 
            '       mth_subject_area, mth_subject_area_text, mth_age_grp, mth_age_grp_text, ' .
            '       mth_prep_time, mth_prep_time_text, mth_exec_time, mth_exec_time_text, ' .
            '       mth_phase, mth_soc_form, mth_authors, ' .
            '       mth_owner_id, mth_create_time, ' . 
            '       file_guid, file_name, ' .
            '       dnl_cnt, dnl_first_tm, dnl_last_tm, dnl_usr_id, ' .
            '       rtg_cnt, rtg_first_tm, rtg_last_tm, rtg_min_val, rtg_max_val, rtg_avg_val ' .
            'from   vi_mth_method_rating where dnl_usr_id = ' . $usr_id . ' ';
        $this->stm_type = 'RATING_LIST';        
    }
    
    public function InitAdminListStmt($owner_id)
    {
        $this->select_stmt = 
            'select mth_id, mth_name, mth_summary, mth_subject, mth_subject_text, ' . 
            '       mth_subject_area, mth_subject_area_text, mth_age_grp, mth_age_grp_text, ' .
            '       mth_prep_time, mth_prep_time_text, mth_exec_time, mth_exec_time_text, ' .
            '       mth_phase, mth_soc_form, mth_authors, ' .
            '       mth_owner_id, mth_create_time, ' . 
            '       file_guid, file_name, ' .
            '       dnl_cnt, dnl_first_tm, dnl_last_tm, dnl_usr_id, ' .
            '       rtg_cnt, rtg_first_tm, rtg_last_tm, rtg_min_val, rtg_max_val, rtg_avg_val ' .
            'from   vi_mth_method_result where mth_owner_id = ' . $owner_id . ' ';
        $this->stm_type = 'ADMIN_LIST'; 
    }
    
    public function getNumRemaining()
    {
        return $this->num_rows_db - $this->num_rows_db;
    }
    
    private function compareLike($att_name, $att_value)
    {
        $this->select_stmt = $this->select_stmt . " and " . $att_name . " like '%" . $att_value . "%' ";
    }
    
    private function compareStrEqual($att_name, $att_value)
    {
        $this->select_stmt = $this->select_stmt . " and " . $att_name . " = '" . $att_value . "' ";
    }
    
    private function compareNumEqual($att_name, $att_value)
    {
        $this->select_stmt = $this->select_stmt . " and " . $att_name . " = " . $att_value . " ";
    }
    
    private function compareArrayAll($att_name, $att_value)
    {
        foreach($att_value as $att)
        {
            if (strlen(trim($att)) > 0)
            {
                $this->compareLike($att_name, $att);
            }
        }
    }
    
    private function compareArrayAny($att_name, $att_value)
    {
        $this->select_stmt = $this->select_stmt . " and ((1 = 1) ";
        
        foreach($att_value as $att)
        {
            if (strlen(trim($att)) > 0)
            {
                 $this->select_stmt = $this->select_stmt . " or (" . $att_name . " like '%" . $att . "'%) ";
            }
        }
        
         $this->select_stmt = $this->select_stmt . ") ";
    }
    
    private function sortBy($att_name, $direction)
    {
        $this->select_stmt = $this->select_stmt . " order by " . $att_name . " " . $direction . "; ";
    }
    
    public function compareMthName($mth_name)
    {
        $this->compareLike('mth_name', $mth_name);
    }
    
    public function compareMthSummary($mth_summary)
    {
        $this->compareLike('mth_summary', $mth_summary);
    }
    
    public function compareMthSubject($mth_subject)
    {
        $this->compareStrEqual('mth_subject', $mth_subject);
    }
    
    public function compareMthSubArea ($mth_sub_area)
    {
        $this->compareStrEqual('mth_subject_area', $mth_sub_area);
    }
    
    public function compareMthAgeGrp($mth_age_grp)
    {
        $this->compareStrEqual('mth_age_grp', $mth_age_grp);
    }
    
    public function compareMthPrepTime($mth_prep_tm)
    {
        $this->compareStrEqual('mth_prep_time', $mth_prep_tm);
    }
    
    public function compareMthExecTime($mth_exec_tm)
    {
        $this->compareStrEqual('mth_exec_time', $mth_exec_tm);
    }
    
    public function compareMthPhase($mth_phase)
    {
        $this->compareArrayAll('mth_phase', $mth_phase);
    }
    
    public function compareMthSocForm($mth_soc_form)
    {
        $this->compareArrayAll('mth_soc_form', $mth_soc_form);
    }
    
    public function compareMthAuthor($mth_authors)
    {
        $this->compareArrayAny('mth_authors', $mth_authors);
    }
    
    public function compareMthOwner($mth_owner_id)
    {
        $this->compareNumEqual('mth_owner_id', $mth_owner_id);
    }
    
    public function sortByRating()
    {   
        $this->sortBy('rtg_avg_val', 'desc');
    }
    
    public function sortByCreateTime()
    {
        $this->sortBy('mth_create_time', 'desc');
    }
    
    public function sortByDownloadNum()
    {
        $this->sortBy('dnl_cnt', 'desc');
    }
    
    public function sortByDownloadDate()
    {
        $this->sortBy('dnl_last_tm', 'desc');
    }
    
    public function retrieveLines($max_lines = 0)
    {
        $no_limit = $max_lines == 0;
        $stm_mv1 = $this->db_conn->prepare($this->select_stmt);
        if ($stm_mv1->execute())
        {
            $mth_id = -1;
            $mth_name = $mth_summary = $mth_subj = $mth_subj_txt = $mth_subj_area = $mth_subj_area_txt = '';
            $mth_age_grp = $mth_age_grp_txt = $mth_prep_tm = $mth_prep_tm_txt = $mth_exec_tm = $mth_exec_tm_txt = '';
            $mth_soc_form = $mth_phase = $mth_authors = '';
            $mth_owner_id = -1;
            $mth_create_tm = '';
            $mth_dnl_cnt = -1;
            $mth_dnl_first_tm = $mth_dnl_last_tm = $mth_dnl_usr_id = '';
            $mth_rtg_cnt = -1;
            $mth_rtg_first_tm = $mth_rtg_last_tm = '';
            $mth_rtg_min = $mth_rtg_max = $mth_rtg_avg = -1;
            $mth_file_guid = $mth_file_name = '';

            $stm_mv1->store_result();
            $this->num_rows_db = $stm_mv1->num_rows;
            
            $stm_mv1->bind_result(
                $mth_id, $mth_name, $mth_summary, $mth_subj, $mth_subj_txt, $mth_subj_area, $mth_subj_area_txt, 
                $mth_age_grp, $mth_age_grp_txt, $mth_prep_tm, $mth_prep_tm_txt,
                $mth_exec_tm, $mth_exec_tm_txt,
                $mth_phase, $mth_soc_form, $mth_authors, $mth_owner_id, $mth_create_tm,
                $mth_file_guid, $mth_file_name, 
                $mth_dnl_cnt, $mth_dnl_first_tm, $mth_dnl_last_tm, $mth_dnl_usr_id, 
                $mth_rtg_cnt, $mth_rtg_first_tm, $mth_rtg_last_tm, $mth_rtg_min, $mth_rtg_max, $mth_rtg_avg );
            
            $cnt = 0;
            while($stm_mv1->fetch() && (($cnt < $max_lines) || ($no_limit)))
            {
                $mvl = new MethodViewLine();
                
                $mvl->mth_id = $mth_id;
                $mvl->mth_name = $mth_name;
                $mvl->mth_summary = $mth_summary;
                $mvl->mth_subject = $mth_subj;
                $mvl->mth_subject_txt = $mth_subj_txt;
                $mvl->mth_subj_area = $mth_subj_area;
                $mvl->mth_subj_area_txt = $mth_subj_area_txt;
                $mvl->mth_age_grp = $mth_age_grp;
                $mvl->mth_age_grp_txt = $mth_age_grp_txt;
                $mvl->mth_prep_tm = $mth_prep_tm;
                $mvl->mth_prep_tm_txt = $mth_prep_tm_txt;
                $mvl->mth_exec_tm = $mth_exec_tm;
                $mvl->mth_exec_tm_txt = $mth_exec_tm_txt;
                $mvl->mth_phase = $mth_phase;
                $mvl->mth_phase_arr = Helpers::stringToArray($mth_phase);
                $mvl->mth_soc_form = $mth_soc_form;
                $mvl->mth_soc_form_arr = Helpers::stringToArray($mth_soc_form);
                $mvl->mth_authors = $mth_authors;
                $mvl->mth_authors_arr = Helpers::stringToArray($mth_authors);
                $mvl->mth_owner_id = $mth_owner_id;
                $mvl->mth_create_tm = $mth_create_tm;
                $mvl->mth_file_guid = $mth_file_guid;
                $mvl->mth_file_name = $mth_file_name;
                $mvl->mth_dnl_cnt = $mth_dnl_cnt;
                $mvl->mth_dnl_first_tm = $mth_dnl_first_tm;
                $mvl->mth_dnl_last_tm = $mth_dnl_last_tm;
                $mvl->mth_dnl_usr_id = $mth_dnl_usr_id;
                $mvl->mth_rtg_cnt = $mth_rtg_cnt;
                $mvl->mth_rtg_first_tm = $mth_rtg_first_tm;
                $mvl->mth_rtg_last_tm = $mth_rtg_last_tm;
                $mvl->mth_rtg_min = $mth_rtg_min;
                $mvl->mth_rtg_max = $mth_rtg_max;
                $mvl->mth_rtg_avg = $mth_rtg_avg;
                
                $this->lines[] = $mvl;
                
                $cnt ++;
            }
            $stm_mv1->free_result();
            $this->num_rows_view = $cnt;
        }
        
        $stm_mv1->close();
    }
}

class MethodViewLine
{
    public $mth_id;
    public $mth_name;
    public $mth_summary;
    public $mth_subject;
    public $mth_subject_txt;
    public $mth_subj_area;
    public $mth_subj_area_txt;
    public $mth_age_grp;
    public $mth_age_grp_txt;
    public $mth_prep_tm;
    public $mth_prep_tm_txt;
    public $mth_exec_tm;
    public $mth_exec_tm_txt;
    public $mth_soc_form;
    public $mth_soc_form_arr;
    public $mth_phase;
    public $mth_phase_arr;
    public $mth_authors;
    public $mth_authors_arr;
    public $mth_owner_id;
    public $mth_create_tm;
    public $mth_dnl_cnt;
    public $mth_dnl_first_tm;
    public $mth_dnl_last_tm;
    public $mth_dnl_usr_id;
    public $mth_rtg_cnt;
    public $mth_rtg_first_tm;
    public $mth_rtg_last_tm;
    public $mth_rtg_min;
    public $mth_rtg_max;
    public $mth_rtg_avg;
    public $mth_file_guid;
    public $mth_file_name;

    public function __construct()
    {
        $this->mth_id = $this->mth_owner_id = -1;
        $this->mth_name = $this->mth_summary = '';
        $this->mth_subject = $this->mth_subj_area = $this->mth_age_grp = $this->mth_prep_tm = $this->mth_exec_tm = '';
        $this->mth_subject_txt = $this->mth_subj_area_txt = $this->mth_age_grp_txt = $this->mth_prep_tm_txt = $this->mth_exec_tm_txt = '';
        $this->mth_soc_form = $this->mth_phase = $this->mth_authors = '';
        $this->mth_soc_form_arr = $this->mth_phase_arr = $this->mth_authors_arr = null;
        $this->mth_rtg_cnt = $this->mth_dnl_cnt = $this->mth_rtg_min = $this->mth_rtg_max = $this->mth_rtg_avg = 0;
        $this->mth_dnl_first_tm = $this->mth_dnl_last_tm = $this->mth_dnl_usr_id = '';
        $this->mth_rtg_first_tm = $this->mth_rtg_last_tm = '';
        $this->mth_file_guid = $this->mth_file_name = '';
    }
}

?>