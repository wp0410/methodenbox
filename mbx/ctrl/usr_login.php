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
include_once '../model/usr_session.php';
include_once '../model/app_warning.php';
include_once '../model/aux_logging.php';

set_private_warning_handler();

session_start();

if (empty($_POST) || empty($_POST['user_email']) || empty($_POST['user_pwd']))
{
    $res = new AppResult(100);
    header('Location: ../view/aux_error.php?res_code=' . $res->code . '&res_text=' . $res->textUrlEncoded());
    exit;
}

$db_conn = DatabaseConnection::get_connection();
$usr = new UserAccount($db_conn);
$res = $usr->userLogin($_POST['user_email'], $_POST['user_pwd']);
if (! $res->isOK())
{
	$log_entry = new InvalidLoginAttemptEntry();
	$log_entry->usr_email = $_POST['user_email'];
	$log_entry->rem_ip_address = $_SERVER['REMOTE_ADDR'];
	$log_entry->rem_host_name = $_SERVER['REMOTE_HOST'];
	$log_entry->err_code = $res->code;
	$log_entry->err_text = $res->text;

    if (($res->code == 400) || ($res->code == 410))
    {
		$log_entry->usr_password = $_POST['user_pwd'];
    }
	else
	{
		$log_entry->usr_password = '********';
	}

	$logger = new SecurityLog($db_conn);
	$logger->storeLogEntry($log_entry);
    
    header('Location: ../view/usr_login.php?res_code=' . $res->code . '&res_text=' . $res->textUrlEncoded());
    exit;
}

$sess = new UserSession($db_conn);
$sess->ses_usr_email = $usr->usr_email;
$sess->ses_usr_full_name = $usr->usr_fst_name . ' ' . $usr->usr_lst_name;
$res = $sess->startSession($usr->getId(), $usr->getPermissionsString());
if (! $res->isOK())
{
    header('Location: ../view/usr_login.php?res_code=' . $res->code . '&res_text=' . $res->textUrlEncoded());
    exit;
}
else
{
    $_SESSION['user'] = array('sid' => $sess->getId(), 'uid' => $usr->getId(), 'hash' => $sess->getSessionHash());
    header('Location: ../view/mth_search_pg.php');
    exit;
}
?>