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
include_once '../model/mth_method_view_pg.php';
include_once '../view/mth_search_result_pg.php';
include_once '../model/app_warning.php';

set_private_warning_handler();

session_start();
$db_conn = DatabaseConnection::get_connection();
$res_view = new MethodResultView($db_conn);

$max_pages = GlobalParameter::$applicationConfig['mthPageNumPages'];
$cur_page = 1;

$res_view->lines_per_page = GlobalParameter::$applicationConfig['mthPageNumLines'];
$res_from_cache = false;

$res_view->initSearchResultStmt();
$res_view->usr_id = 0;

if (! empty($_POST))
{
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
        // We do not have a cached where clause: it needs to be composed from the POST parameters
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
        if (! empty($_POST['mth_prep_tm']))
        {
            $res_view->compareMthPrepTime($_POST['mth_prep_tm']);
        }
        if (! empty($_POST['mth_exec_tm']))
        {
            $res_view->compareMthExecTime($_POST['mth_exec_tm']);
        }
        if (! empty($_POST['mth_phase']))
        {
            $res_view->compareMthPhase($_POST['mth_phase']);
        }
        if (! empty($_POST['mth_soc']))
        {
            $res_view->compareMthSocForm($_POST['mth_soc']);
        }
        if (! empty($_POST['mth_author']))
        {
            $res_view->compareMthAuthor($_POST['mth_author']);
        }
        if (! empty($_POST['view_lines']))
        {
            $max_lines = $_POST['view_lines'];
        }
        if (! empty($_POST['curr_usr_id']))
        {
            $res_view->usr_id = $_POST['curr_usr_id'];
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
}

$res_view->retrieveLines($cur_page);

if (! $res_from_cache)
{
    $res_view->storeCache();
}
$mth_view = new MethodSearchResultView($res_view, $max_pages);
$mth_view->renderHtml();
$mth_view->outputHtml();
?>