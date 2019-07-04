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
include_once '../model/aux_text.php';
include_once '../model/sql_connection.php';
include_once '../model/app_warning.php';
include_once '../model/app_result.php';
include_once '../model/aux_contact.php';

set_private_warning_handler();

session_start();

$param_missing = 
	empty($_POST) || 
	empty($_POST['usr_addr']) || empty($_POST['usr_first_name']) || empty($_POST['usr_last_name']) || empty($_POST['usr_email']) || 
	empty($_POST['req_type']) || empty($_POST['req_desc']);
	
if (GlobalParameter::$applicationConfig['validateCaptcha'])
{
    $param_missing = $param_missing || empty($_POST['g-recaptcha-response']);
}
else
{
    $param_missing = $param_missing || empty($_POST['emul_captcha']);
}

if ($param_missing)
{
    $res = new AppResult(100);
    header('Location: ../view/aux_contact.php?res_code=' . $res->code . '&res_text=' . $res->textUrlEncoded());
    exit;
}

$ctc_req = new ContactRequest(DatabaseConnection::get_connection());
$ctc_req->usr_addr_form = $_POST['usr_addr'];
$ctc_req->usr_first_name = $_POST['usr_first_name'];
$ctc_req->usr_last_name = $_POST['usr_last_name'];
$ctc_req->usr_email = $_POST['usr_email'];
$ctc_req->req_type = $_POST['req_type'];
$ctc_req->req_text = $_POST['req_desc'];
$ctc_req->create();

header('Location: ../view/mth_search_pg.php');
exit;
?>
