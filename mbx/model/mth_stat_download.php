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

class MethodDownload
{
    private $dnl_id;
    private $dnl_mth_id;
    private $dnl_usr_id;
    public $dnl_date;
    private $db_conn;
    
    public function __construct($db_cn, $mth_id, $usr_id)
    {
        $this->dnl_mth_id = $mth_id;
        $this->dnl_usr_id = $usr_id;
        $this->dnl_date = Helpers::dateTimeString(time());
        $this->db_conn = $db_cn;
    }
    
    public function saveDownload()
    {
        $result = null;
        
        $sql_stmt = 
            'insert into ta_mth_method_download ( dnl_mth_id, dnl_usr_id, dnl_date ) values ( ?, ?, ? );';
        $stm_rt2 = $this->db_conn->prepare($sql_stmt);
        $stm_rt2->bind_param('iis', $this->dnl_mth_id, $this->dnl_usr_id, $this->dnl_date);
        if ($stm_rt2->execute())
        {
            $this->dnl_id = $stm_rt2->insert_id;
            $result = new AppResult(0);
        }
        else
        {
            $result = new AppResult(681);
        }
        $stm_rt2->close();
        
        return $result;
    }
}
?>