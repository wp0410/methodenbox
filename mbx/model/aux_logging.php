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

class SessionLog
{
    private $db_conn;
    
    public function __construct($db_cn)
    {
        $this->db_conn = $db_cn;
    }
    
    public function logLoginAttempt($usr_email, $usr_pwd, $log_status, $log_details)
    {
        $log_time = Helpers::dateTimeString(time());
        $sql_stmt = 'insert into ta_log_usr_session( log_time, log_usr_email, log_usr_pwd, log_status, log_details ) values( ?, ?, ?, ?, ? );';
        $stm_lg1 = $this->db_conn->prepare($sql_stmt);
        $stm_lg1->bind_param('sssis', $log_time, $usr_email, $usr_pwd, $log_status, $log_details);
        $stm_lg1->execute();
        $stm_lg1->close();
    }
}

?>
