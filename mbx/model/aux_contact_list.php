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
include_once '../model/aux_text.php';
include_once '../model/app_result.php';
include_once '../model/aux_helpers.php';

class ContactRequestList implements JsonSerializable
{
    public  $usr_id;
    public  $lines;
    public  $lines_per_page;
    public  $total_rows;
    public  $current_page;
    
    private $db_conn;
    
    private $select_stmt;
    private $where_clause;
    
    private $cache_obj_id;
    
    public function __construct($db_cn)
    {
        $this->db_conn = $db_cn;
        $this->usr_id = 0;
        $this->lines = array();
        $this->lines_per_page = GlobalParameter::$applicationConfig['admPageNumLines'];
        $this->total_rows = 0;
        $this->current_page = 0;
        $this->cache_obj_id = '';
    }

    public function jsonSerialize()
    {
        return array(
            'usr_id'         => $this->usr_id,
            'lines_per_page' => $this->lines_per_page,
			'current_page'   => $this->current_page,
            'total_rows'     => $this->total_rows,
            'cache_obj_id'   => $this->cache_obj_id
        );
    }

    public function InitContactListStatement()
    {
        $this->select_stmt =
			"SELECT req_id, usr_addr_form, usr_fst_name, usr_lst_name, usr_email, 
					req_type, req_text, req_create_time 
			 FROM   ta_aux_contact_request WHERE req_close_time IS NULL AND req_close_usr_id IS NULL 
			 ORDER BY req_id ASC ";
		$this->where_clause = "";
	}
	
    public function storeCache()
    {
        $this->cache_obj_id = Helpers::randomString(32);
    }
    
    public function getCacheId()
    {
        return $this->cache_obj_id;
    }
    
    public function loadCache($ch_stmt_id)
    {
        $this->InitContactListStatement();
        $this->cache_obj_id = $ch_stmt_id;
    }

    public function retrieveLines($page_no)
	{
        $full_stmt = $this->select_stmt . ';';
        $stm_arq1 = $this->db_conn->prepare($full_stmt);
        if ($stm_arq1->execute())
        {
            $stm_arq1->store_result();
            $this->total_rows = $stm_arq1->num_rows;
			$stm_arq1->free_result();
        }
        $stm_arq1->close();
		
		$full_stmt = $this->select_stmt . ' limit ' . ($page_no - 1) * $this->lines_per_page . ',' . $this->lines_per_page . ';';
		$stm_arq2 = $this->db_conn->prepare($full_stmt);
		if ($stm_arq2->execute())
		{
			$rq_id = $us_adf = $us_fst_name = $us_lst_name = $us_email = $rq_type = $rq_text = $rq_cretm = null;
			$stm_arq2->bind_result($rq_id, $us_adf, $us_fst_name, $us_lst_name, $us_email, $rq_type, $rq_text, $rq_cretm);
			$stm_arq2->store_result();
			
			while($stm_arq2->fetch())
			{
				$row = new ContactRequestListLine();
				
				$row->req_id = $rq_id;
				$row->usr_addr_form = $us_adf;
				$row->usr_fst_name = $us_fst_name;
				$row->usr_lst_name = $us_lst_name;
				$row->usr_email = $us_email;
				$row->setReqType($rq_type);
				$row->req_create_time = $rq_cretm;
				
				$this->lines[] = $row;
			}
			
			$stm_arq2->free_result();
		}
		$stm_arq2->close();
	}
}

class ContactRequestListLine
{
	public $req_id;
	public $usr_addr_form;
	public $usr_fst_name;
	public $usr_lst_name;
	public $usr_email;
	public $req_type;
	public $req_type_text;
	public $req_text;
	public $req_create_time;
	public $req_close_time;
	public $req_close_usr_id;
	public $req_answer;
	
	public function __construct()
	{
		$this->req_id = $this->usr_addr_form = $this->usr_fst_name = $this->usr_lst_name = $this->usr_email = '';
		$this->req_type = $this->req_type_text = $this->req_text = $this->req_create_time = '';
		$this->req_close_time = $this->req_close_usr_id = $this->req_answer = '';
	}
	
	public function setReqType($rq_type)
	{
		switch($rq_type)
		{
			case "Q":
				$this->req_type_text = "Frage";
				break;
			case "H":
				$this->req_type_text = "Hilfe";
				break;
			case "R":
				$this->req_type_text = "Bemerkung";
				break;
			case "E":
				$this->req_type_text = "Fehlermeldung";
				break;
			case "W":
				$this->req_type_text = "Wunsch";
				break;
			default:
				$this->req_type_text = "Sonstiges";
				break;
		}
	}
}
?>
