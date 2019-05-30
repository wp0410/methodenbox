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
include_once '../model/aux_parameter_sec.php';
include_once '../model/sql_connection.php';
include_once '../model/usr_account.php';
include_once '../model/usr_session.php';
include_once '../model/app_result.php';
include_once '../model/app_warning.php';

session_start();

$res = new AppResult(0);

if (empty($_POST) || (empty($_POST['set_type'])))
{
    $res = new AppResult(100);
}
else 
{
    if ($_POST['set_type'] == 'SKIN')
    {
        if (! empty($_POST['usr_skin']))
        {
            $_SESSION['skin'] = $_POST['usr_skin'];
        }
    }
    else
    {
        if ($_POST['set_type'] == 'PWD')
        {
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
                        
                        if (empty($_POST['current_pwd']) || empty($_POST['new_pwd']) || empty($_POST['new_pwd_conf']) || ($_POST['new_pwd'] != $_POST['new_pwd_conf']))
                        {
                            $res = new AppResult(414);
                        }
                        if ($res->isOK())
                        {
                            $usr_acc = new UserAccount($db_conn);
                            $res = $usr_acc->loadById($usr_session->getUsrId());
                        }
                        if ($res->isOK())
                        {
                            $res = $usr_acc->checkUserPassword($_POST['current_pwd']);
                        }
                        if ($res->isOK())
                        {
                            $res = $usr_acc->modifyUserAccount($_POST['new_pwd']);
                        }
                        if ($res->isOK())
                        {
                            $res = new AppResult(952);
                        }
                    }
                }
            }
        }
    }
}

if ($res->code == 0)
{
    header('Location: ../view/usr_settings.php');
}
else 
{
    header('Location: ../view/usr_settings.php?res_code=' . $res->code . '&res_text=' . $res->textUrlEncoded());
}
exit;
?>