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
include_once '../model/mth_stat_rating.php';
include_once '../view/mth_rating_details.php';

session_start();
$db_conn = DatabaseConnection::get_connection();

if (! empty($_POST) && (! empty($_POST['mth_id'])))
{
    $max_age_days = GlobalParameter::$applicationConfig['rtgListMaxAgeDays'];
    $cmp_rate_date = Helpers::dateTimeString(time() - $max_age_days * 86400);
    
    $rtg_list = new MethodRatingList($db_conn, $_POST['mth_id']);
    $rtg_list->retrieveList($cmp_rate_date,  GlobalParameter::$applicationConfig['rtgListNumLines']);
    
    $view = new RatingListView();
    foreach($rtg_list->ratings as $rtg)
    {
        $view->addRating($rtg);
    }
    
    $view->renderHtml();
    $view->outputHtml();
}
?>