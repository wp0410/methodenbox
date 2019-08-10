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
include_once '../model/aux_cache.php';

class MethodResultView
{
    public $lines;
    public $total_rows;
    public $lines_per_page;
    public $current_page;
    public $usr_id;
    
    private $db_conn;

    private $select_stmt;
    private $where_clause;
    private $stm_type;

    private $cache_obj_id;

    public function __construct($db_cn)
    {
        $this->usr_id = 0;
        $this->lines = array();
        $this->db_conn = $db_cn;
        $this->total_rows = $this->lines_per_page = $this->current_page = 0;
        $this->stm_type = null;
        $this->cache_obj_id = '';
    }
    
    public function initSearchResultStmt()
    {
        $this->select_stmt = 
            'SELECT mth_id, mth_name, mth_summary, mth_subject, mth_subject_text, 
                    mth_subject_area, mth_subject_area_text, mth_type, mth_type_text,
                    mth_prep_time, mth_prep_time_text, mth_exec_time, mth_exec_time_text, 
                    mth_elements, mth_soc_form, mth_authors, 
                    mth_owner_id, mth_create_time, 
                    file_guid, file_name, 
                    dnl_cnt, dnl_first_tm, dnl_last_tm, dnl_usr_id, 
                    rtg_cnt, rtg_first_tm, rtg_last_tm, rtg_min_val, rtg_max_val, rtg_avg_val 
             FROM   vi_mth_method_result WHERE mth_id > 0 ';
        $this->stm_type = 'SEARCH_RESULT';
        $this->where_clause = '';
    }
    
    public function initRatingListStmt()
    {
        $this->select_stmt = 
            'SELECT mth_id, mth_name, mth_summary, mth_subject, mth_subject_text, 
                    mth_subject_area, mth_subject_area_text, mth_type, mth_type_text, 
                    mth_prep_time, mth_prep_time_text, mth_exec_time, mth_exec_time_text, 
                    mth_elements, mth_soc_form, mth_authors, 
                    mth_owner_id, mth_create_time, 
                    file_guid, file_name, 
                    dnl_cnt, dnl_first_tm, dnl_last_tm, dnl_usr_id, 
                    rtg_cnt, rtg_first_tm, rtg_last_tm, rtg_min_val, rtg_max_val, rtg_avg_val 
             FROM   vi_mth_method_rating WHERE mth_id > 0 ';
        $this->stm_type = 'RATING_LIST';        
        $this->where_clause = '';
    }
    
    public function InitAdminListStmt()
    {
        $this->select_stmt = 
            'SELECT mth_id, mth_name, mth_summary, mth_subject, mth_subject_text, 
                    mth_subject_area, mth_subject_area_text, mth_type, mth_type_text, 
                    mth_prep_time, mth_prep_time_text, mth_exec_time, mth_exec_time_text, 
                    mth_elements, mth_soc_form, mth_authors, 
                    mth_owner_id, mth_create_time, 
                    file_guid, file_name, 
                    dnl_cnt, dnl_first_tm, dnl_last_tm, dnl_usr_id, 
                    rtg_cnt, rtg_first_tm, rtg_last_tm, rtg_min_val, rtg_max_val, rtg_avg_val 
             FROM   vi_mth_method_result where mth_id > 0 ';
        $this->stm_type = 'ADMIN_LIST'; 
        $this->where_clause = '';
    }
    
    private function compareLike($att_name, $att_value)
    {
        $stm_part = " AND " . $att_name . " LIKE '%" . $att_value . "%' ";
        $this->select_stmt = $this->select_stmt . $stm_part;
        $this->where_clause = $this->where_clause . $stm_part;
    }
    
    private function compareStrEqual($att_name, $att_value)
    {
        $stm_part = " AND " . $att_name . " = '" . $att_value . "' ";
        $this->select_stmt = $this->select_stmt . $stm_part;
        $this->where_clause = $this->where_clause . $stm_part;
    }
    
    private function compareNumEqual($att_name, $att_value)
    {
        $stm_part = " AND " . $att_name . " = " . $att_value . " ";
        $this->select_stmt = $this->select_stmt . $stm_part;
        $this->where_clause = $this->where_clause . $stm_part;
    }
    
    private function compareNumGen($att_name, $att_value, $operator)
    {
        $stm_part = " AND " . $att_name . " " . $operator . " " . $att_value . " ";
        $this->select_stmt = $this->select_stmt . $stm_part;
        $this->where_clause = $this->where_clause . $stm_part;
    }
    
    private function compareArrayAll($att_name, $att_value)
    {
        foreach($att_value as $att)
        {
            if (strlen(trim($att)) > 0)
            {
                $this->compareLike($att_name, htmlentities($att));
            }
        }
    }
    
    private function compareArrayAny($att_name, $att_value)
    {
        $stm_part = " AND ((1 = 1) ";
        
        foreach($att_value as $att)
        {
            if (strlen(trim($att)) > 0)
            {
                 $stm_part = $stm_part . " or (" . $att_name . " like '%" . htmlentities($att) . "'%) ";
            }
        }
        
        $stm_part = $stm_part . ") ";
        $this->select_stmt = $this->select_stmt . $stm_part;
        $this->where_clause = $this->where_clause . $stm_part;
    }
    
    private function sortBy($att_name, $direction)
    {
        $stm_part = " order by " . $att_name . " " . $direction . ", mth_id DESC ";
        $this->select_stmt = $this->select_stmt . $stm_part;
        $this->where_clause = $this->where_clause . $stm_part;
    }
    
    public function compareMthName($mth_name)
    {
        $this->compareLike('mth_name', htmlentities($mth_name));
    }
    
    public function compareMthSummary($mth_summary)
    {
        $this->compareLike('mth_summary', htmlentities($mth_summary));
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
	
	public function compareMthType($mth_type)
	{
		$this->compareStrEqual('mth_type', $mth_type);
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
        $this->compareArrayAll('mth_phase', Helpers::stringToArray($mth_phase));
    }
	
	public function compareMthElements($mth_elems)
	{
		$this->compareArrayAll('mth_elements', Helpers::stringToArray($mth_elems));
	}
    
    public function compareMthSocForm($mth_soc_form)
    {
        $this->compareArrayAll('mth_soc_form', Helpers::stringToArray($mth_soc_form));
    }
    
    public function compareMthAuthor($mth_authors)
    {
        // $this->compareArrayAny('mth_authors', $mth_authors);
        $this->compareArrayAll('mth_authors', $mth_authors);
    }
    
    public function compareMthOwner($mth_owner_id)
    {
        $this->compareNumEqual('mth_owner_id', $mth_owner_id);
    }
	
	public function compareDnlUserId($dnl_usr_id)
	{
		$this->compareNumEqual('dnl_usr_id', $dnl_usr_id);
	}
	
	public function excludeCurrentUser($usr_id)
	{
	    $this->compareNumGen('mth_owner_id', $usr_id, '!=');
	    // $this->compareNumGen('dnl_usr_id', $usr_id, '!=');
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
    
    public function storeCache()
    {
        $ch_stm = new StatementCache($this->db_conn);
        $ch_stm->cch_lines_pp = $this->lines_per_page;
        $this->cache_obj_id = $ch_stm->storeCache($this->usr_id, $this->where_clause);
    }
    
    public function getCacheId()
    {
        return $this->cache_obj_id;
    }
    
    public function loadCache($cch_id)
    {
        $ch_stm = new StatementCache($this->db_conn);
        $ch_stm->retrieveCache($cch_id);
        
        $this->cache_obj_id = $ch_stm->cch_obj_id;
		$this->usr_id = $ch_stm->cch_owner_id;
        $this->where_clause = $ch_stm->cch_sql_stmt;
        $this->select_stmt = $this->select_stmt . ' ' . $this->where_clause;
        $this->lines_per_page = $ch_stm->cch_lines_pp;
    }
    
    public function retrieveLines($page_no)
    {
        // Retrieve the overall number of result lines created by the search statement
        $full_stmt = $this->select_stmt . ';';
        $stm_mv2 = $this->db_conn->prepare($full_stmt);
        if ($stm_mv2->execute())
        {
            // Retrieve the total number of rows in the result
            $stm_mv2->store_result();
            $this->total_rows = $stm_mv2->num_rows;
        }
        $stm_mv2->free_result();
        $stm_mv2->close();
		
        // Retrieve the lines for the requested page
        $full_stmt = $this->select_stmt . ' limit ' . ($page_no - 1) * $this->lines_per_page . ',' . $this->lines_per_page . ';';
        $stm_mv1 = $this->db_conn->prepare($full_stmt);
        if ($stm_mv1->execute())
        {
            $mth_id = -1;
            $mth_name = $mth_summary = $mth_subj = $mth_subj_txt = $mth_subj_area = $mth_subj_area_txt = '';
            $mth_type = $mth_type_txt = $mth_prep_tm = $mth_prep_tm_txt = $mth_exec_tm = $mth_exec_tm_txt = '';
            $mth_soc_form = $mth_elems = $mth_authors = '';
            $mth_owner_id = -1;
            $mth_create_tm = '';
            $mth_dnl_cnt = -1;
            $mth_dnl_first_tm = $mth_dnl_last_tm = $mth_dnl_usr_id = '';
            $mth_rtg_cnt = -1;
            $mth_rtg_first_tm = $mth_rtg_last_tm = '';
            $mth_rtg_min = $mth_rtg_max = $mth_rtg_avg = -1;
            $mth_file_guid = $mth_file_name = '';

            $stm_mv1->store_result();
            
            // Set the pagination parameters:
            $this->current_page = $page_no;
            
            $stm_mv1->bind_result(
                $mth_id, $mth_name, $mth_summary, $mth_subj, $mth_subj_txt, $mth_subj_area, $mth_subj_area_txt, 
                $mth_type, $mth_type_txt, $mth_prep_tm, $mth_prep_tm_txt,
                $mth_exec_tm, $mth_exec_tm_txt,
                $mth_elems, $mth_soc_form, $mth_authors, $mth_owner_id, $mth_create_tm,
                $mth_file_guid, $mth_file_name, 
                $mth_dnl_cnt, $mth_dnl_first_tm, $mth_dnl_last_tm, $mth_dnl_usr_id, 
                $mth_rtg_cnt, $mth_rtg_first_tm, $mth_rtg_last_tm, $mth_rtg_min, $mth_rtg_max, $mth_rtg_avg );
            
            $cnt = 0;
            while($stm_mv1->fetch())
            {
                $mvl = new MethodViewLine();
                
                $mvl->mth_id = $mth_id;
                $mvl->mth_name = $mth_name;
                $mvl->mth_summary = $mth_summary;
                $mvl->mth_subject = $mth_subj;
                $mvl->mth_subject_txt = $mth_subj_txt;
                $mvl->mth_subj_area = $mth_subj_area;
                $mvl->mth_subj_area_txt = $mth_subj_area_txt;
                $mvl->mth_type = $mth_type;
                $mvl->mth_type_txt = $mth_type_txt;
                $mvl->mth_prep_tm = $mth_prep_tm;
                $mvl->mth_prep_tm_txt = $mth_prep_tm_txt;
                $mvl->mth_exec_tm = $mth_exec_tm;
                $mvl->mth_exec_tm_txt = $mth_exec_tm_txt;
                $mvl->mth_elems = $mth_elems;
                $mvl->mth_elems_arr = Helpers::stringToArray($mth_elems);
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
    public $mth_type;
    public $mth_type_txt;
    public $mth_prep_tm;
    public $mth_prep_tm_txt;
    public $mth_exec_tm;
    public $mth_exec_tm_txt;
    public $mth_soc_form;
    public $mth_soc_form_arr;
    public $mth_elems;
    public $mth_elems_arr;
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
        $this->mth_subject = $this->mth_subj_area = $this->mth_type = $this->mth_prep_tm = $this->mth_exec_tm = '';
        $this->mth_subject_txt = $this->mth_subj_area_txt = $this->mth_type_txt = $this->mth_prep_tm_txt = $this->mth_exec_tm_txt = '';
        $this->mth_soc_form = $this->mth_elems = $this->mth_authors = '';
        $this->mth_soc_form_arr = $this->mth_elems_arr = $this->mth_authors_arr = null;
        $this->mth_rtg_cnt = $this->mth_dnl_cnt = $this->mth_rtg_min = $this->mth_rtg_max = $this->mth_rtg_avg = 0;
        $this->mth_dnl_first_tm = $this->mth_dnl_last_tm = $this->mth_dnl_usr_id = '';
        $this->mth_rtg_first_tm = $this->mth_rtg_last_tm = '';
        $this->mth_file_guid = $this->mth_file_name = '';
    }
}

?>