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
include_once '../model/sql_connection.php';
include_once '../model/aux_helpers.php';
include_once '../model/usr_session.php';
include_once '../model/mth_method_file.php';
include_once '../model/mth_stat_download.php';
include_once '../model/app_warning.php';

set_private_warning_handler();

session_start();
$db_conn = DatabaseConnection::get_connection();
$usr_session = new UserSession($db_conn);
if (empty($_SESSION) || empty($_SESSION['user']))
{
    $res = new AppResult(405);
}
else
{
    $res = $usr_session->validateSession($_SESSION['user']);
    
    if ($res->isOK())
    {
        if (! $usr_session->isAuthenticated())
        {
            $res = new AppResult(406);
        }
        else
        {
            $_SESSION['user'] = array('sid' => $usr_session->getId(), 'uid' => $usr_session->getUsrId(), 'hash' => $usr_session->getSessionHash());
        }
    }
}
if (! $res->isOK())
{
    header('Location: ../view/aux_error.php?res_code=' . $res->code . '&res_text=' . $res->textUrlEncoded());
    exit;
}

$mth_id = -1;
$file_guid = '';

if (! empty($_POST))
{
    if (! empty($_POST['mth_id'])) { $mth_id = $_POST['mth_id']; }
    if (! empty($_POST['file_guid'])) { $file_guid = $_POST['file_guid']; }
}
else
{
    if (! empty($_GET))
    {
        if (! empty($_GET['mth_id'])) { $mth_id = $_GET['mth_id']; }
        if (! empty($_GET['file_guid'])) { $file_guid = $_GET['file_guid']; }
    }
}

if (($mth_id <= 0) || ($file_guid == ''))
{
    echo null;
}
else
{
    $tmf = new TeachingMethodFile($db_conn);
    $res = $tmf->loadFile($mth_id, $file_guid);
    if (! $res->isOK())
    {
        echo null;
    }
    else
    {
        $dnl = new MethodDownload($db_conn, $mth_id, $usr_session->getUsrId());
        $dnl->saveDownload();
        
        echo $tmf->getFileData();
    }
}
?>