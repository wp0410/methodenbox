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
    public  $lines_per_page;
    public  $usr_id;
    private $db_conn;
    
    public function __construct($db_cn)
    {
        $this->db_conn = $db_cn;
        $this->usr_id = -1;
        $this->lines_per_page = GlobalParameter::$applicationConfig['admPageNumLines'];
    }
    
    public function jsonSerialize()
    {
        return array(
            'usr_id'         => $this->usr_id,
            'lines_per_page' => $this->lines_per_page
        );
    }
    
    public function InitAdmStatement()
    {
        
    }
    
    public function loadCache($ch_stmt_id)
    {
        
    }
}
?>