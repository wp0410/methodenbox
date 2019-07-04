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

class ContactRequest
{
	public $usr_addr_form;
	public $usr_first_name;
	public $usr_last_name;
	public $usr_email;
	public $req_type;
	public $req_text;
	
	private $req_id;
	private $req_create_time;
	private $req_close_time;
	private $req_close_usr_id;
	private $req_answer;
	
    private $db_conn;
    
    public function __construct($db_cn)
    {
        $this->db_conn = $db_cn;
		$this->req_id = -1;
		$this->usr_addr_form = $this->usr_first_name = $this->usr_last_name = $this->usr_email = '';
		$this->req_type = $this->req_text = '';
		$this->req_create_time = Helpers::dateTimeString(time());
		$this->req_close_time = $this->req_answer = '';
		$this->req_close_usr_id = 0;
	}
	
	public function create()
	{
		$sql_stmt = 
			"INSERT INTO ta_aux_contact_request( usr_addr_form, usr_fst_name, usr_lst_name, usr_email, req_type, req_text, req_create_time ) 
			 VALUES ( ?, ?, ?, ?, ? ,?, ? );";
		$stm_c1 = $this->db_conn->prepare($sql_stmt);
		$stm_c1->bind_param('sssssss', 
			$this->usr_addr_form, $this->usr_first_name, $this->usr_last_name, $this->usr_email,
			$this->req_type, $this->req_text, $this->req_create_time);
		$stm_c1->execute();
		$this->req_id = $stm_c1->insert_id;
		$stm_c1->close();
	}
}

class ContactRequestList
{
	public static function retrieve($req_type, $include_closed = false): array
	{
		$result = array();
		
		$sql_stmt = 
			"SELECT req_id, usr_addr_form, usr_fst_name, usr_lst_name, usr_email, 
					req_type, req_text, req_create_time, req_close_time, req_close_usr_id, req_answer 
			 FROM   ta_aux_contact_request ";
		if ($include_closed))
		{
			$sql_stmt = $sql_stmt . "WHERE req_id>0 ";
		}
		else
		{
			$sql_stmt = $sql_stmt . "WHERE req_close_time IS NULL AND req_close_usr_id IS NULL ";
		}
		if (! empty($req_type))
		{
			$sql_stmt = $sql_stmt . " AND req_type=? "
		}
		$sql_stmt = $sql_stmt . " ORDER BY req_create_time ASC;";
		
		$stm_c2 = $this->db_conn->prepare($sql_stmt);
		if (! empty($req_type))
		{
			$stm_c2->bind_param('s', $req_type);
		}
		if ($stm_c2->execute())
		{
			$rq_id = $us_adf = $us_fst_name = $us_lst_name = $us_email = $rq_type = $rq_text = $rq_cretm = $rq_clstm = $rq_clusrid = $rq_answer = null;
			$stm_c2->bind_result(
				$rq_id, $us_adf, $us_fst_name, $us_lst_name, $us_email, $rq_type, $rq_text, $rq_cretm, $rq_clstm, $rq_clusrid, $rq_answer);
			$stm_c2->store_result();
			
			while($stm_c2->fetch())
			{
				$result[] = array(
								'req_id' => $rq_id, 
								'usr_addr_form' => $us_adf,
								'usr_fst_name' => $us_fst_name,
								'usr_lst_name' => $us_lst_name,
								'usr_email' => $us_email,
								'req_type' => $rq_type,
								'req_text' => $rq_text,
								'req_create_time' => $rq_cretm,
								'req_close_time' => $rq_clstm,
								'req_close_usr_id' => $rq_clusrid,
								'req_answer' => $rq_answer
							);
			}
			
			$stm_c2->free_result();
		}
		$stm_c2->close();
		
		return $result;
	}
}

?>