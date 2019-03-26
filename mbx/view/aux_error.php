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
include_once '../model/app_result.php';
include_once '../model/aux_parameter.php';
include_once '../view/frm_common.php';
include_once '../model/app_warning.php';

set_private_warning_handler();

session_start();
$res = new AppResult($_GET);
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <title><?php echo GlobalParameter::$applicationConfig['applicationTitle'] . ' - Fehlerbericht';?></title>
        <?php FormElements::styleSheetRefs(); ?>
    </head>
    <body>
        <?php FormElements::topNavigationBar('AUX.ERR', 0, 0); ?>
        <?php FormElements::bottomNavigationBar('AUX.ERR', 0, 0); ?>
        
       <div class="container-fluid">
            <div class="row row-fluid"><div class="col"><br></div></div>

            <div class="row row-fluid">
                <div class="col-md-6 col-xl-6">
                    <div class="alert alert-primary" role="alert"><center><h4>Ein schwerer Fehler ist aufgetreten!</h4></center></div>
                </div>
            </div>
            
            <div class="row row-fluid">
                <div class="col-md-6 col-xl-6">
                    <div class="alert alert-danger">
                        <h4>
                            <?php echo $res->text; ?>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        
        <?php FormElements::scriptRefs(); ?>
    </body>
</html>