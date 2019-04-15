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
include_once 'aux_helpers.php';
include_once 'app_result.php';

class UserAccountView implements JsonSerializable
{
    public  $lines;
    public  $lines_per_page;
    public  $total_rows;
    public  $current_page;
    public  $usr_id;
    
    private $db_conn;
    
    private $select_stmt;
    private $where_clause;
    
    private $cache_obj_id;
    
    public function __construct($db_cn)
    {
        $this->db_conn = $db_cn;
        $this->lines = array();
        $this->usr_id = 0;
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
            'total_rows'     => $this->total_rows,
            'cache_obj_id'   => $this->cache_obj_id
        );
    }
    
    public function InitAdmStatement()
    {
        $this->select_stmt =
            'select usr.usr_id, usr.usr_fst_name, usr.usr_lst_name, usr.usr_email, usr.usr_register_date, usr.usr_login_date, usr.usr_status, ' .
            'uro.per_role_client, uro.per_role_upload, uro.per_role_admin ' .
            'from   ta_usr_account usr ' .
	        '  inner join vi_usr_roles uro on uro.usr_id = usr.usr_id';
        $this->where_clause = '';
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
        $this->InitAdmStatement();
        $this->cache_obj_id = $ch_stmt_id;
    }
    
    public function retrieveLines($page_no)
    {
        $full_stmt = $this->select_stmt . ';';
        $stm_ad1 = $this->db_conn->prepare($full_stmt);
        if ($stm_ad1->execute())
        {
            $stm_ad1->store_result();
            $this->total_rows = $stm_ad1->num_rows;
        }
        $stm_ad1->free_result();
        $stm_ad1->close();
        
        $full_stmt = $this->select_stmt . ' limit ' . ($page_no - 1) * $this->lines_per_page . ',' . $this->lines_per_page . ';';
        $stm_ad2 = $this->db_conn->prepare($full_stmt);
        if ($stm_ad2->execute())
        {
            $usr_id = 0;
            $usr_fst_name = $usr_lst_name = $usr_email = '';
            $usr_reg_date = $usr_login_date = '';
            $usr_status = -1;
            $role_client = $role_upload = $role_admin = 0;
            
            $stm_ad2->store_result();
            $this->current_page = $page_no;
            $stm_ad2->bind_result( 
                $usr_id, $usr_fst_name, $usr_lst_name, $usr_email, 
                $usr_reg_date, $usr_login_date, $usr_status, 
                $role_client, $role_upload, $role_admin);
            
            while ($stm_ad2->fetch())
            {
                $row = new AccountViewLine();
                $row->usr_id = $usr_id;
                $row->usr_fst_name = $usr_fst_name;
                $row->usr_lst_name = $usr_lst_name;
                $row->usr_email = $usr_email;
                $row->usr_reg_date = $usr_reg_date;
                $row->usr_login_date = $usr_login_date;
                $row->usr_status = $usr_status;
                $row->role_client = $role_client;
                $row->role_upload = $role_upload;
                $row->role_admin = $role_admin;
                
                $this->lines[] = $row;
            }
            
            $stm_ad2->free_result();
        }
        $stm_ad2->close();
    }
}

class AccountViewLine
{
    public $usr_id;
    public $usr_fst_name;
    public $usr_lst_name;
    public $usr_email;
    public $usr_reg_date;
    public $usr_login_date;
    public $usr_status;
    public $role_client;
    public $role_upload;
    public $role_admin;
    
    public function __construct()
    {
        $this->usr_id = 0;
        $this->usr_fst_name = $this->usr_lst_name = $this->usr_email = '';
        $this->usr_reg_date = $this->usr_login_date = '';
        $this->usr_status = -1;
        $this->role_client = $this->role_upload = $this->role_admin = 0;
    }
}

?>