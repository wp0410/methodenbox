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
include_once '../model/aux_parameter.php';
include_once '../view/frm_common.php';
include_once '../model/app_warning.php';

set_private_warning_handler();
session_start();
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <title><?php echo GlobalParameter::$applicationConfig['applicationTitle'] . ' - Hilfe';?></title>
        <?php FormElements::styleSheetRefs(); ?>
    </head>
    <body>
        <?php FormElements::topNavigationBar('AUX.HLP', 0, 0); ?>
        <?php FormElements::bottomNavigationBar('AUX.HLP', 0, 0); ?>
        
        <div class="container-fluid">
            <div class="row row-fluid">
                <div class="col">
                    <div class="alert alert-primary" role="alert"><center><h4>Hilfe</h4></center></div>
                </div>
            </div>
            
            <div class="row row-fluid">
                <div class="col">
                    <div id="HelpAccordion">
                        <div class="card">
                            <div class="card-header" id="help_header_01">
                                <button class="btn btn-link" data-toggle="collapse" data-target="#collChapter_01" aria-expanded="false" aria-controls="collChapter_01">
                                    <h5>Hilfethema 1</h5>
                                </button>
                            </div> <!-- card-header -->
                            <div id="collChapter_01" class="collapse" aria-labelledby="help_header_01" data-parent="#HelpAccordion">
                                <div class="card-body">
                                    Hier sollte der Text f&uuml;r das erste Hilfethema stehen ...
                                </div>
                            </div>
                        </div> <!-- card -->
                        
                        <div class="card">
                            <div class="card-header" id="help_header_01">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collChapter_02" aria-expanded="false" aria-controls="collChapter_02">
                                    <h5>Hilfethema 2</h5>
                                </button>
                            </div> <!-- card-header -->
                            <div id="collChapter_02" class="collapse" aria-labelledby="help_header_02" data-parent="#HelpAccordion">
                                <div class="card-body">
                                    Hier sollte der Text f&uuml;r das zweite Hilfethema stehen ...
                                </div>
                            </div>
                        </div> <!-- card -->

                        <div class="card">
                            <div class="card-header" id="help_header_03">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collChapter_03" aria-expanded="false" aria-controls="collChapter_03">
                                    <h5>Hilfethema 3</h5>
                                </button>
                            </div> <!-- card-header -->
                            <div id="collChapter_03" class="collapse" aria-labelledby="help_header_03" data-parent="#HelpAccordion">
                                <div class="card-body">
                                    Hier sollte der Text f&uuml;r das dritte Hilfethema stehen ...
                                </div>
                            </div>
                        </div> <!-- card -->

                        <div class="card">
                            <div class="card-header" id="help_header_NN">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collChapter_NN" aria-expanded="false" aria-controls="collChapter_NN">
                                    <h5>Weiteres Hilfethema</h5>
                                </button>
                            </div> <!-- card-header -->
                            <div id="collChapter_NN" class="collapse" aria-labelledby="help_header_NN" data-parent="#HelpAccordion">
                                <div class="card-body">
                                    Hier sollte der Text f&uuml;r ein weiteres Hilfethema stehen ...
                                </div>
                            </div>
                        </div> <!-- card -->

                    </div>
                </div>
            </div>
        </div>

        <?php FormElements::scriptRefs(); ?>
    </body>
 </html>   
