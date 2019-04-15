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
include_once 'model/mdl_mth.php';
include_once 'model/mdl_sta.php';
include_once 'model/mdl_err.php';

// Check for valid user session
if (empty($_SESSION) || empty($_SESSION['user']))
{
    // die('Client user is not authenticated (0)');
    $app_err = new ErrorInfo();
    $app_err->err_last_action = 'Validate User Authentication';
    $app_err->err_number = 300;
    $app_err->err_text = 'No User Session';
    $app_err->handle_fatal();
}

// $db_conn = db_connect();
$db_conn = DatabaseConnection::get_connection();
if ($db_conn == null)
{
    $app_err = DatabaseConnection::get_error();
    $app_err->handle_fatal();
}

$usr_sess = new UserSession($db_conn);
$usr_sess->load_by_id($_SESSION['user']);

if (! $usr_sess->valid())
{
    $jnl_entry = new JournalEntry($db_conn, $usr_sess->usr_id, $usr_sess->usr_name, 'SESSION.VALIDATE');
    $jnl_entry->set_jnl_result(301, 'Invalid User Session');
    $jnl_entry->set_jnl_data(json_encode($usr_sess));
    $jnl_entry->store();

    // die('Client user is not authenticated (1)');
    $app_err = new ErrorInfo();
    $app_err->err_last_action = 'Validate User Authentication';
    $app_err->err_number = 301;
    $app_err->err_text = 'Invalid User Session';
    $app_err->handle_fatal();
}

$usr_sess->extend();

if (empty($_GET) || empty($_GET['fid']) || empty($_GET['fguid']))
{
    echo 'null';
}
else
{
    $mth_desc = new MethodDescription($db_conn);
    $result = $mth_desc->load($_GET['fid'], $_GET['fguid']);

    $jnl_entry = new JournalEntry($db_conn, $usr_sess->usr_id, $usr_session->usr_name, 'METHOD.DOWNLOAD');
    $jnl_entry->set_jnl_result($result['code'], $result['text']);
    $jnl_entry->set_jnl_data(json_encode($mth_desc));
    $jnl_entry->store();

    if ($result->code == 0)
    {
        $stat = new DownloadStatistics($db_conn, $usr_sess->usr_id, $_GET['fid']);
        $stat->save();
        
        echo $mth_desc->get_file_data();
    }
    else
    {
        echo 'null';
    }
}
?>