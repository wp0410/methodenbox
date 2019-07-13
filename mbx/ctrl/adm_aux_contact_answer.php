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
include_once '../model/aux_contact.php';

set_private_warning_handler();

session_start();

$param_missing = empty($_POST) || empty($_POST['curr_usr_id']) || empty($_POST['req_id']) || empty($_POST['req_answer']);
if ($param_missing)
{
    $res = new AppResult(100);
}
else
{
	$db_conn = DatabaseConnection::get_connection();
	$req = new ContactRequest($db_conn);
	$res = $req->loadById($_POST['req_id']);
	
	if ($res->isOK())
	{
		$res = $req->saveAnswer($_POST['curr_usr_id'], $_POST['req_answer']);
	}
	
	if ($res->isOK())
	{
		$res = $req->closeContactRequest($_POST['curr_usr_id']);
	}
}

if ($res->isOK())
{
	echo 'OK';
}
else
{
	http_response_code(400);
	echo $res->text;
}
?>