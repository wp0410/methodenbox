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
include_once '../model/mth_method_file.php';

session_start();
$db_conn = DatabaseConnection::get_connection();
$param_missing = 
    empty($_POST) ||
    empty($_POST['mth_id']) ||
    empty($_FILES) ||
    empty($_FILES['mth_file']);
if ($param_missing)
{
    echo '<div class="alert alert-danger" role="alert"><h5>Fehler beim Hochladen der Datei</h5></div>';
    /* echo '<div class="alert alert-danger" role="alert">';
    echo '$_POST:            ' . empty($_POST) . '<br>';
    echo '$_POST[mth_id]:    ' . empty($_POST['mth_id']) . '<br>';
    echo '$_FILES:           ' . empty($_FILES) . '<br>';
    echo '$_FILES[mth_file]: ' . empty($_FILES['mth_file']);
    echo '</div>'; */
}
else
{
    $mth_file = new TeachingMethodFile($db_conn);
    $res = $mth_file->saveFile($_POST['mth_id'], $_FILES['mth_file']);

    echo '<div class="alert alert-info" role="alert"><h5 class="upconf">Datei erfolgreich hochgeladen und gespeichert</h5></div>';
}
?>