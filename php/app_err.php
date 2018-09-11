<?php
//---------------------------------------------------------------------------------------
//  Copyright (c) 2017 Walter Pachlinger (walter.pachlinger@gmx.at)
//    
//  Licensed under the Apache License, Version 2.0 (the "License"); you may not use this 
//  file except in compliance with the License. You may obtain a copy of the License at
//      http://www.apache.org/licenses/LICENSE-2.0
//  Unless required by applicable law or agreed to in writing, software distributed under 
//  the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF 
//  ANY KIND, either express or implied. See the License for the specific language 
//  governing permissions and limitations under the License.
//----------------------------------------------------------------------------------------
include_once 'model/mdl_par.php';
include_once 'model/mdl_err.php';
include_once 'model/mdl_bs3.php';

$error_info = new ErrorInfo();

if (empty($_GET))
{
    $error_info->err_last_action = null;
    $error_info->err_number = null;
    $error_info->err_text = null;
    
}
else
{
    $error_info->err_last_action = $_GET['err_last_action'];
    $error_info->err_number = $_GET['err_number'];
    $error_info->err_text = $_GET['err_text'];
}
$usr_is_authenticated = false;
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Ilse Pachlinger: Sammlung von Unterrichtsmethoden">
        <meta name="author" content="Walter Pachlinger (walter.pachlinger@gmx.at)">
        
        <?php FormatHelper::style_sheet_refs(); ?>
    </head>

    <body>
        <?php FormatHelper::create_menu($usr_is_authenticated, basename($_SERVER['PHP_SELF'])); ?>

        <div class="container" role="main">
            <div class="page-header"><h1> <?php echo GlobalParam::$app_config['app_title'] . ': '; ?> Kritischer Fehler</h1></div>
    
            <div class="row">
                <div class="alert alert-danger" role="alert">
                    <?php $error_info->format_error(); ?>
                </div>
            </div>
        </div>

        <?php FormatHelper::script_refs(); ?>
    </body>
</html>