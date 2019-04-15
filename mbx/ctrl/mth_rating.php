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
include_once '../model/aux_parameter.php';
include_once '../model/aux_helpers.php';
include_once '../model/app_result.php';
include_once '../model/mth_stat_rating.php';
include_once '../model/app_warning.php';

set_private_warning_handler();

session_start();
$db_conn = DatabaseConnection::get_connection();
if (empty($_SESSION) || empty($_SESSION['user']))
{
    $usr_id = -1;
}
else
{
    $usr_id = $_SESSION['user']['uid'];
}

if (empty($_POST) || empty($_POST['mth_id']) || empty($_POST['mth_rtg_val']) || ($usr_id < 0))
{
    if (empty($_POST)) echo '$_POST: EMPTY --';
    if (empty($_POST['mth_id'])) echo 'MTH_ID: EMPTY --';
    if (empty($_POST['mth_rtg_val'])) echo 'MTH_RTG_VAL: EMPTY --';
    if (empty($_POST['mth_rtg_comm'])) echo 'MTH_RTG_COMMENT: EMPTY -- ';
    if ($usr_id < 0) echo 'USR_ID: EMPTY --';
}
else
{
    $mth_rtg = new MethodRating($db_conn);
    $mth_rtg->initializeCreate($_POST['mth_id'], $usr_id);
    $mth_rtg->rtg_date = Helpers::dateTimeString(time());
    $mth_rtg->rtg_value = $_POST['mth_rtg_val'];
    $mth_rtg->rtg_summary = $_POST['mth_rtg_comm'];
    $mth_rtg->createRating();
}
?>