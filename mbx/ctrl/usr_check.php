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

if (empty($_GET))
{
   echo 'true';
}
else
{
    if (! empty($_GET['user_email']))
    {
        if ($_GET['user_email'] == 'test@test.com')
        {
            echo 'false';
        }
        else
        {
            echo 'true';
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
                echo 'false';
            }
        }
        else
        {
            echo 'true';
        }
    }
}
?>