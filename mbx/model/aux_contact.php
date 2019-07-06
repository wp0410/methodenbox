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
	
	public function getId(): int
	{
	    return $this->req_id;
	}
	
	public function getCreateTime(): string
	{
	    return $this->req_create_time;
	}
	
	public function getTypeText(): string
	{
	    $req_type_text = '';
	    
	    switch($this->req_type)
	    {
	        case "Q":
	            $req_type_text = "Frage";
	            break;
	        case "H":
	            $req_type_text = "Hilfe";
	            break;
	        case "R":
	            $req_type_text = "Bemerkung";
	            break;
	        case "E":
	            $req_type_text = "Fehlermeldung";
	            break;
	        case "W":
	            $req_type_text = "Wunsch";
	            break;
	        default:
	            $req_type_text = "Sonstiges";
	            break;
	    }
	    
	    return $req_type_text;
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
	
	public function loadById($req_id)
	{
	    $res = new AppResult(0);
	    
	    $sql_stmt =
	       "SELECT req_id, usr_addr_form, usr_fst_name, usr_lst_name, usr_email,
                   req_type, req_text, req_create_time, req_close_time, req_close_usr_id, req_answer 
            FROM   ta_aux_contact_request 
            WHERE  req_id = ?";
	    $stm_c2 = $this->db_conn->prepare($sql_stmt);
	    $stm_c2->bind_param('i', $req_id);
	    if ($stm_c2->execute())
	    {
	        $stm_c2->bind_result(
	            $this->req_id, $this->usr_addr_form, $this->usr_first_name, $this->usr_last_name, $this->usr_email,
	            $this->req_type, $this->req_text, $this->req_create_time, $this->req_close_time, $this->req_close_usr_id, 
	            $this->req_answer);
	        
	        if (! $stm_c2->fetch())
	        {
	            $res = new AppResult(702);
	        }
	        
	        $stm_c2->close();
	    }
	    else 
	    {
	        $res = new AppResult(701);
	    }
	    
	    return $res;
	}
}

?>