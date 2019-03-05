<?php
//---------------------------------------------------------------------------------------
//  Copyright (c) 2017 Walter Pachlinger (walter.pachlinger@gmx.at)
//    
//  Licensed under the Apache License, Version 2.0 (the "License"); you may not use this 
//  file except in compliance with the License. You may obtain a copy of the License at
//      http://www.apache.org/licenses/LICENSE-2.0
//  Unless required by applicable law or agreed to in writing, software distributed under 
//  the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF 
//  ANY KIND, either express or implied. See the License for the specific language 
//  governing permissions and limitations under the License.
//----------------------------------------------------------------------------------------
session_start();

include_once 'model/mdl_par.php';
include_once 'model/mdl_dbs.php';
include_once 'model/mdl_ssn.php';
include_once 'model/mdl_jnl.php';
include_once 'model/mdl_err.php';

if (empty($_SESSION) || empty($_SESSION['user']))
{
    // die('Client user is not authenticated (0)');
    $app_err = new ErrorInfo();
    $app_err->err_last_action = 'Validate User Authentication';
    $app_err->err_number = 300;
    $app_err->err_text = 'No User Session';
    $app_err->handle_fatal();
}

//$db_conn = db_connect();
$db_conn = DatabaseConnection::get_connection();
if ($db_conn == null)
{
    $app_err = DatabaseConnection::get_error();
    $app_err->handle_fatal();
}

$usr_session = new UserSession($db_conn);
$usr_session->load_by_id($_SESSION['user']);
if (! $usr_session->valid())
{
    // die('Client user is not authenticated (1)');
    $app_err = new ErrorInfo();
    $app_err->err_last_action = 'Validate User Authentication';
    $app_err->err_number = 301;
    $app_err->err_text = 'Invalid User Session';
    $app_err->handle_fatal();
}

$jnl_entry = new JournalEntry($db_conn, $usr_session->usr_id, $usr_session->usr_name, 'USER.LOGOUT');
$jnl_entry->set_jnl_data(json_encode($usr_session));
$jnl_entry->set_jnl_result(0, 'OK');
$jnl_entry->store();

$usr_session->destroy();

unset($_SESSION['user']);
session_destroy();
    
header('Location: /php/app_ovw.php');
exit;
?>