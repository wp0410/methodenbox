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
class StatementCache
{
    private $db_conn;
    
    public $cch_owner_id;
    public $cch_sql_stmt;
    public $cch_obj_id;
    public $cch_lines_pp;
    public $cch_store_date;
    public $cch_expiry_date;
    
    public function __construct($db_cn)
    {
        $this->db_conn = $db_cn;
        $this->cch_owner_id = 0;
        $this->cch_lines_pp = GlobalParameter::$applicationConfig['mthPageNumLines'];
        $this->cch_sql_stmt = null;
        $this->cch_obj_id = null;
        $this->cch_store_date = null;
        $this->cch_expiry_date = null;
    }
    
    public function storeCache($co_owner_id, $co_sql_stmt)
    {
        if (empty($co_sql_stmt) || empty($co_owner_id))
        {
            return null;
        }
        
        $this->cch_owner_id = $co_owner_id;
        $this->cch_sql_stmt = $co_sql_stmt;
        $this->cch_obj_id = null;
        $this->cch_store_date = null;
        $this->cch_expiry_date = null;
       
        $this->cch_store_date = Helpers::dateTimeString(time());
        $this->cch_expiry_date = Helpers::dateTimeString(time() + 3600);
        $obj_id = Helpers::randomString(32);

        $cstm = "insert into ta_aux_cache( cch_obj_id, cch_owner_id, cch_obj_data, cch_lines_pp, cch_store_date, cch_expiry_date ) values ( ?, ?, ?, ?, ?, ? );";
        $stm_ch1 = $this->db_conn->prepare($cstm);
        $stm_ch1->bind_param('sisiss', $obj_id, $this->cch_owner_id, $this->cch_sql_stmt, $this->cch_lines_pp, $this->cch_store_date, $this->cch_expiry_date);
        $stm_ch1->execute();
        $stm_ch1->close();

        $this->cch_obj_id = $obj_id;
        
        return $this->cch_obj_id;
    }
    
    public function retrieveCache($co_obj_id)
    {
        if (empty($co_obj_id))
        {
            return;
        }
        
        $this->cch_owner_id = null;
        $this->cch_sql_stmt = null;
        $this->cch_obj_id = null;
        $this->cch_store_date = null;
        $this->cch_expiry_date = null;
        $this->cch_lines_pp = null;
        
        $cur_time = Helpers::dateTimeString(time());

        $cstm = "select cch_obj_id, cch_owner_id, cch_obj_data, cch_lines_pp, cch_store_date, cch_expiry_date from ta_aux_cache where cch_obj_id = ? and cch_expiry_date >= ?";

        $stm_ch3 = $this->db_conn->prepare($cstm);
        $stm_ch3->bind_param('ss', $co_obj_id, $cur_time);
        if ($stm_ch3->execute())
        {
            $stm_ch3->bind_result($this->cch_obj_id, $this->cch_owner_id, $this->cch_sql_stmt, $this->cch_lines_pp, $this->cch_store_date, $this->cch_expiry_date);
            $stm_ch3->fetch();
        }
        
        $stm_ch3->free_result();
        $stm_ch3->close();
    }

    public function clearCacheByOwner($co_owner_id)
    {
        $cstm = "delete from ta_aux_cache where cch_owner_id = ?;";
        
        $stm_ch8 = $this->db_conn->prepare($cstm);
        $stm_ch8->bind_param('i', $co_owner_id);
        $stm_ch8->execute();
        $stm_ch8->close();
    }
    
    public function clearCacheExpired()
    {
        $obj_exp_date = Helpers::dateTimeString(time());
        $cstm = "delete from ta_aux_cache where cch_expiry_date < ?;";
        
        $stm_ch9 = $this->db_conn->prepare($cstm);
        $stm_ch9->bind_param('s', $obj_exp_date);
        $stm_ch9->execute();
        $stm_ch9->close();
    }
}

?>