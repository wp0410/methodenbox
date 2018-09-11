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
session_start();

include_once '../model/mdl_par.php';
include_once '../model/mdl_bs4.php';

$usr_is_authenticated = false;
?>

<!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Ilse Pachlinger: Sammlung von Unterrichtsmethoden">
        <meta name="author" content="Walter Pachlinger (walter.pachlinger@gmx.at)">

        <?php style_sheet_refs(); ?>
    </head>

    <body>
        <?php create_menu($usr_is_authenticated, basename($_SERVER['PHP_SELF'])); ?>

        <div class="container" role="main">
            <div class="page-header"><h1><?php echo GlobalParam::$app_config['app_title'] . ': '; ?> &Uuml;bersicht</h1></div>
    
            <?php
                if ( !empty($_GET) && !empty($_GET['add_msg']) )
                {
                    echo '<div class="row"><div class="alert alert-warning" role="alert"><p class="lead">';
                    echo $_GET['add_msg'];
                    echo '</p></div></div>';
                }
            ?>
            <div class="row">
                <div class="alert alert-info" role="alert">
                    <p class="lead">
                        Sie m&uuml;ssen sich anmelden, um auf den Inhalt der Methodenbox zugreifen zu k&ouml;nnen. 
                        Wenn Sie noch keine Anmeldedaten haben, m&uuml;ssen Sie die Registrierung durchführen (Men&uuml; "Registrieren").
                    </p>
                </div>
            </div>
        </div>
    
        <?php script_refs(); ?>
    </body>
</html>