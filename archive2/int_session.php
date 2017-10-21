<?php
    //---------------------------------------------------------------------------------------------
    // int_session.php       User session validation
    //
    // Copyright (c) 2017 Walter Pachlinger (walter.pachlinger@gmx.at)
    //
    // Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file 
    // except in compliance with the License. You may obtain a copy of the License at
    //    http://www.apache.org/licenses/LICENSE-2.0
    // Unless required by applicable law or agreed to in writing, softwaredistributed under the 
    // License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, 
    // either express or implied. See the License for the specific language governing permissions 
    // and limitations under the License.
    //---------------------------------------------------------------------------------------------
    function validate_user_session($db_conn, $session_user)    
    {
        $usr_id = 0;
        $usr_name = null;
        $usr_sess_end = null;
        $sess_valid = true;
        $curr_tm = strftime('%Y-%m-%d %H:%M:%S', time());
        
        $sess_sel_stmt =
            'select sess_usr_id, sess_usr_name, sess_end ' .
            '  from ta_sec_user_session ' .
            ' where sess_id = ?;';
        $stm1 = $db_conn->prepare($sess_sel_stmt);
        $stm1->bind_param('s', $session_user);
        $stm1->execute();
        $stm1->bind_result($usr_id, $usr_name, $usr_sess_end);
        if (!$stm1->fetch())
        {
            $sess_valid = false;
        }
        $stm1->free_result();
        $stm1->close();
        
        if (! $invalid)
        {
            if ($usr_sess_end < $curr_tm)
            {
                $sess_valid = false;
            }
        }
        
        if (! $invalid)
        {
            $new_tm = strftime('%Y-%m-%d %H:%M:%S', time() + 600);
            $sess_upd_stmt =
                'update ta_sec_user_session set sess_end = ? where sess_id = ?;';
            $stm2 = $db_conn->prepare($sess_upd_stmt);
            $stm2->bind_param('ss', $new_tm, $session_user);
            $stm2->execute();
            $stm2->close();
        }
        
        return array('status' => $sess_valid, 'user_id' => $usr_id, 'user_name' => $usr_name);
    }