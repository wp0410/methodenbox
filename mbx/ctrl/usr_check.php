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
include_once '../model/aux_parameter.php';
include_once '../model/sql_connection.php';
include_once '../model/usr_account.php';

if (empty($_GET))
{
   echo 'true';
   http_response_code(400);
}
else
{
    if (! empty($_GET['user_email']))
    {
        $usr_email = $_GET['user_email'];
        
        if (($usr_email == null) || (strlen($usr_email) == 0))
        {
            echo 'false';
            http_response_code(400);
        }
        else
        {
            $db_conn = DatabaseConnection::get_connection();
            $usr = new UserAccount($db_conn);
            if ($usr->checkByEmail($usr_email))
            {
                echo 'false';
                http_response_code(400);
            }
            else
            {
                echo 'true';
            }
        }
    }
    else
    {
        if (! empty($_GET['user_pwd']))
        {
            $user_pwd = $_GET['user_pwd'];
            $lower = preg_match('/[a-z]/', $user_pwd);
            $upper = preg_match('/[A-Z]/', $user_pwd);
            $digit = preg_match('/[0-9]/', $user_pwd);
            
            if (($lower > 0) && ($upper > 0) && ($digit > 0))
            {
                echo 'true';
            }
            else
            {
                http_response_code(400);
                echo 'false';
            }
        }
        else
        {
            if (! empty($_GET['reset_pwd_1']))
            {
                $user_pwd = $_GET['reset_pwd_1'];
                $lower = preg_match('/[a-z]/', $user_pwd);
                $upper = preg_match('/[A-Z]/', $user_pwd);
                $digit = preg_match('/[0-9]/', $user_pwd);
                
                if (($lower > 0) && ($upper > 0) && ($digit > 0))
                {
                    echo 'true';
                }
                else
                {
                    http_response_code(400);
                    echo 'false';
                }
            }
            else 
            {
                echo 'true';
            }
        }
    }
}
?>