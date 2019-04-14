<?php
//---------------------------------------------------------------------------------------
//  Copyright (c) 2018 Walter Pachlinger (walter.pachlinger@gmx.at)
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
include_once '../model/mth_method_view_pg.php';
include_once '../view/mth_search_admin_pg.php';
include_once '../model/app_warning.php';

set_private_warning_handler();

session_start();
$db_conn = DatabaseConnection::get_connection();
$max_pages = GlobalParameter::$applicationConfig['mthPageNumPages'];
$cur_page = 1;

$res_view = new MethodResultView($db_conn);
$res_view->InitAdminListStmt();
$res_view->lines_per_page = GlobalParameter::$applicationConfig['mthPageNumLines'];
$res_from_cache = false;

$param_missing = 
    empty($_POST) ||
	( 
		empty($_POST['ch_id']) && 
		(
			empty($_POST['mth_ownership']) || empty($_POST['curr_usr_id']) || (($_POST['mth_ownership'] == 'O') && empty($_POST['mth_author']))
		)
	);

if ($param_missing)
{
    $res = new AppResult(406);
    header('Location: ../view/aux_error.php?res_code=' . $res->code . '&res_text=' . $res->textUrlEncoded());
    exit;
}

if (! empty($_POST['ch_id']))
{
	// We have a chached where clause: let's load it
	$res_view->loadCache($_POST['ch_id']);
	$res_from_cache = true;
	
	if (! empty($_POST['pg_no']))
	{
		$cur_page = $_POST['pg_no'];
	}
}
else
{
    $res_view->usr_id = $_POST['curr_usr_id'];
    
    if ($_POST['mth_ownership'] == 'M')
    {
		$res_view->compareMthOwner($_POST['curr_usr_id']);
    }
    else
    {
		$res_view->compareMthOwner($_POST['mth_author']);
    }
    
    
    if (! empty($_POST['mth_name']))
    {
        $res_view->compareMthName($_POST['mth_name']);
    }
    if (! empty($_POST['mth_subject']))
    {
        $res_view->compareMthSubject($_POST['mth_subject']);
    }
    if (! empty($_POST['mth_area']))
    {
        $res_view->compareMthSubArea($_POST['mth_area']);
    }
    if (! empty($_POST['mth_class']))
    {
        $res_view->compareMthAgeGrp($_POST['mth_class']);
    }
    if (! empty($_POST['mth_res_sort']))
    {
        $sort = $_POST['mth_res_sort'];
    }
    else
    {
        $sort = 'SRT_RATE';
    }

    switch($sort)
    {
        case 'SRT_RATE':
            $res_view->sortByRating();
            break;
        case 'SRT_DATE':
            $res_view->sortByCreateTime();
            break;
        case 'SRT_NDNL':
            $res_view->sortByDownloadNum();
            break;
        default:
            $res_view->sortByRating();
            break;
    }

    if (! empty($_POST['lines_per_pg']))
    {
        $res_view->lines_per_page = $_POST['lines_per_pg'];
    }
}

$res_view->retrieveLines($cur_page);

if (! $res_from_cache)
{
    $res_view->storeCache();
}

$adm_view = new MethodAdminResultView($res_view, $max_pages);
$adm_view->renderHtml();
$adm_view->outputHtml();
?>