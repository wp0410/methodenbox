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
include_once '../model/app_result.php';
include_once '../model/usr_session.php';
include_once '../model/mth_method.php';

session_start();
if (empty($_POST) || empty($_POST['mth_id']))
{
    $result = new AppResult(100);
}
else
{
    $mth = new TeachingMethod;
    $result = $mth->deleteMethod($_POST['mth_id']);
}

if ($result->isOK())
{
    echo '<div class="alert alert-info" role="alert"><h5 class="del_confirm">Die Unterrichtsmethode wurde erfolgreich gel&ouml;scht</h5></div>';
}
else
{
    echo '<div class="alert alert-danger" role="alert"><h5>' . htmlentities($result->text) . '</h5></div>';
}
?>