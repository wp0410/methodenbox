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
include_once '../model/mth_method.php';

session_start();

if (empty($_POST) || empty($_POST['frm_type']))
{
    echo '<div class="alert alert-danger">Interner Fehler: empty($_POST)</div>';
}
else
{
    $db_conn = DatabaseConnection::get_connection();
    
    if ($_POST['frm_type'] == 'MTH_UPLOAD')
    {
        echo '<form id="mth_upload" enctype="multipart/form-data" method="post" action="../ctrl/mth_update_file.php">';
        echo '    <div class="row form-row"><div class="col-md-12 col-xl-12"><div class="form-group">';
        echo '        <div class="input-group">';
        echo '            <label class="input-group-btn">';
        echo '                <span class="btn btn-outline-dark">';
        echo '                    Datei ausw&auml;hlen &hellip; ';
        echo '                    <input type="file" style="display: none;" id="mth_file" name="mth_file" multiple accept=".zip, .gz, .tar">';
        echo '                </span>';
        echo '            </label>';
        echo '            <input type="text" class="form-control" id="mth_file_name", name="mth_file_name" aria-describedby="mth_file">';
        echo '        </div> <!-- input-group -->';
        echo '    </div></div></div> <!-- row -->';
        echo '</form>';
    }
}
?>