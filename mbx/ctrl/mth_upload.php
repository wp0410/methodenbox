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
include_once '../model/usr_session.php';
include_once '../model/mth_method.php';
include_once '../model/mth_method_file.php';
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

$param_missing =
    empty($_POST) ||
    empty($_POST['mth_name']) ||
    empty($_POST['mth_summary']) ||
    empty($_POST['mth_subject']) ||
    empty($_POST['mth_area']) ||
    // empty($_POST['mth_age_grp']) ||
	empty($_POST['mth_type']) ||
    empty($_POST['mth_prep_tm']) ||
    empty($_POST['mth_exec_tm']) ||
    // empty($_POST['mth_phase']) ||
    empty($_POST['mth_elem']) ||
    empty($_POST['mth_soc']) ||
    empty($_POST['mth_prime_author']) ||
    empty($_FILES) ||
    empty($_FILES['mth_file']) ||
    empty($_POST['confirm_agb']) ||
    (! empty($_POST['mth_add_author']) && empty($_POST['confirm_author']));
if ($param_missing)
{
    $res = new AppResult(100);
    header('Location: ../view/aux_error.php?res_code=' . $res->code . '&res_text=' . $res->textUrlEncoded());
    exit;
}

$mth = new TeachingMethod($db_conn, $usr_session->getUsrId());
$mth->setAuthors($_POST['mth_prime_author'], $_POST['mth_add_author']);
$mth->mth_name = $_POST['mth_name'];
$mth->mth_summary = $_POST['mth_summary'];
$mth->mth_subject = $_POST['mth_subject'];
$mth->mth_subject_area = $_POST['mth_area'];
if (array_key_exists('mth_age_grp', $_POST))
{
	$mth->mth_age_grp = $_POST['mth_age_grp'];
}
$mth->mth_type = $_POST['mth_type'];
$mth->mth_prep_time = $_POST['mth_prep_tm'];
$mth->mth_exec_time = $_POST['mth_exec_tm'];
$mth->mth_soc_form = $_POST['mth_soc'];
if (array_key_exists('mth_phase', $_POST))
{
	$mth->mth_phase = $_POST['mth_phase'];
}
$mth->mth_elem = $_POST['mth_elem'];

$res = $mth->createMethod();
if (! $res->isOK())
{
    $mth->deleteMethod();
    header('Location: ../view/mth_upload.php?res_code=' . $res->code . '&res_text=' . $res->textUrlEncoded());
    exit;
}

$mth_file = new TeachingMethodFile($db_conn);
$res = $mth_file->saveFile($mth->getId(), $_FILES['mth_file']);
if (! $res->isOK())
{
    $mth->deleteMethod();
}
else
{
    $res = new AppResult(951);
    $res->text = str_replace('[%M]', $mth->mth_name, $res->text);
}
header('Location: ../view/mth_upload.php?res_code=' . $res->code . '&res_text=' . $res->textUrlEncoded());
exit;
?>