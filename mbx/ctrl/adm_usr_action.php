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
include_once '../model/app_result.php';
include_once '../model/aux_helpers.php';
include_once '../model/app_warning.php';
include_once '../model/usr_account.php';
include_once '../model/usr_session.php';

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
        if (! $usr_session->isAuthenticated() || ! $usr_session->checkPermission('ADM.USR'))
        {
            $res = new AppResult(406);
        }
        else
        {
            $_SESSION['user'] = $usr_session->getSessionDescriptor();
        }
    }
}

if (! $res->isOK())
{
    header('Location: ../view/aux_error.php?res_code=' . $res->code . '&res_text=' . $res->textUrlEncoded());
    exit;
}

if (empty($_POST) || empty($_POST['adm_action']) || (
        ($_POST['adm_action'] == 'USR_DEL') && empty($_POST['usr_id']))
   )
{
    $res = new AppResult(100);
}
else 
{
    $success_msg = '';
    switch($_POST['adm_action'])
    {
        case 'USR_DEL':
            $usr_acc = new UserAccount($db_conn);
            $res = $usr_acc->loadById($_POST['usr_id']);
            if ($res->isOK())
            {
                $res = $usr_acc->deleteUserAccount();
                $success_msg = 'Das Benutzerkonto wurde erfolgreich gel&ouml;scht.';
            }
            break;
    }
}

if ($res->isOK())
{
    echo '<div class="alert alert-info" role="alert"><h5 class="action_confirm">' . $success_msg . '</h5></div>';
}
else
{
    echo '<div class="alert alert-danger" role="alert"><h5>' . htmlentities($res->text) . '</h5></div>';
}
?>