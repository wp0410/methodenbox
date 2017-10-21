<?php
//---------------------------------------------------------------------------------------
//  Copyright (c) 2017 Walter Pachlinger (walter.pachlinger@gmx.at)
//    
//  Licensed under the Apache License, Version 2.0 (the "License"); you may not use this 
//  file except in compliance with the License. You may obtain a copy of the License at
//      http://www.apache.org/licenses/LICENSE-2.0
//  Unless required by applicable law or agreed to in writing, softwaredistributed under 
//  the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF 
//  ANY KIND, either express or implied. See the License for the specific language 
//  governing permissions and limitations under the License.
//----------------------------------------------------------------------------------------

/**
 * Established a connection to the MySQL database
 * 
 * @return         mysqli    database connection
 */
function db_connect()
{
    $db_username = getenv('C9_USER');
    //$db_password = 'mb$sys$dev';
    $db_password = null;
    $db_host = getenv('IP');
    $db_name = 'c9';
    
    $db_conn = new mysqli($db_host, $db_username, $db_password, $db_name);
    if ($db_conn->connect_error)
    {
        return NULL;
    }
    else 
    {
        return $db_conn;
    }
}
?>