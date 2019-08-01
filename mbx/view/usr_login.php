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
include_once '../model/aux_parameter.php';
include_once '../model/app_result.php';
include_once '../view/frm_common.php';
include_once '../model/app_warning.php';
include_once '../model/sql_connection.php';
include_once '../model/usr_session.php';

set_private_warning_handler();
session_start();

$usr_session = new UserSession(DatabaseConnection::get_connection());

$res = new AppResult($_GET);
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <title><?php echo GlobalParameter::$applicationConfig['applicationTitle'] . ' - Anmelden';?></title>
        <?php FormElements::styleSheetRefs(); ?>
    </head>
    <body>
        <?php FormElements::topNavBar('USR.IN', $usr_session); ?>
        <?php FormElements::bottomNavBar('USR.IN'); ?>
        
        <div class="container-fluid">
            <div class="row row-fluid"><div class="col"><br></div></div>

            <div class="row row-fluid">
                <div class="col col-sm-1 col-md-3 col-xl-3"></div>
                <div class="col col-sm-10 col-md-6 col-xl-6">
                    <div class="alert alert-primary" role="alert"><center><h4>Anmelden</h4></center></div>
                </div>
            </div>
            
            <form id="usr_login" method="post" action="../ctrl/usr_login.php" data-parsley-validate="">
                <div class="controls">
                    <div class="row form-row">
                        <div class="col col-sm-1 col-md-3 col-xl-3"></div>
                        <div class="col col-sm-5 col-md-3 col-xl-3">
                            <div class="form-group" id="login_email">
                                <label for="user_email">E-Mail Adresse *</label>
                                <input id="user_email" type="email" name="user_email" class="form-control" placeholder="E-Mail Adresse"
                                	data-parsley-required="" data-parsley-required-message="Die E-Mail Adresse muss eingegeben werden"
                                	data-parsley-type-message="Keine g&uuml;ltige E-Mail Adresse"
                                >
                            </div>
                        </div>
                    <!-- </div>
                    <div class="row form-row"> -->
                        <div class="col col-sm-5 col-md-3 col-xl-3">
                            <div class="form-group" id="login_pwd">
                                <label for="user_pwd">Passwort *</label>
                                <input id="user_pwd" type="password" name="user_pwd" class="form-control" placeholder="Passwort"
                                	data-parsley-required="" data-parsley-required-message="Das Passwort muss angegeben werden"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col col-sm-1 col-md-3 col-xl-3"></div>
                        <div class="col col-sm-8 col-md-5 col-xl-5">
                            <span class="text-dark">Mit * gekennzeichnete Felder m&uuml;ssen eingegeben werden.</span>
                        </div>
                        <div class="col col-sm-2 col-md-1 col-xl-1">
                            <div class="form-group" id="login_submit">
                                <input type="submit" class="btn btn-primary btn-block btn-send" value="Anmelden">
                            </div>
                        </div>
                    </div>
                </div> <!-- controls -->
            </form>
            
            <div class="row form-row">
                <div class="col col-sm-1 col-md-3 col-xl-3"></div>
                <div class="col col-sm-10 col-md-6 col-xl-6">
                    <a href="../view/usr_pwd_reset.php" class="btn btn-outline-primary btn-block btn-sm" role="button">
                        Sie haben Ihr Passwort vergessen? Zum Zur&uuml;cksetzen hier klicken ...
                    </a>
                </div>
            </div>

            <p></p>
            
            <div class="row form-row">
                <div class="col col-sm-1 col-md-3 col-xl-3"></div>
                <div class="col col-sm-10 col-md-6 col-xl-6">
                    <a href="../ctrl/usr_conf_req.php" class="btn btn-outline-primary btn-block btn-sm" role="button">
                        E-Mail zur Best&auml;tigung der Registrierung nicht erhalten? Hier klicken ...
                    </a>
                </div>
            </div>

            <p></p>
            
            <div class="row form-row">
                <div class="col col-sm-1 col-md-3 col-xl-3"></div>
                <div class="col col-sm-10 col-md-6 col-xl-6">
                    <a href="../view/usr_register.php" class="btn btn-outline-primary btn-block btn-sm" role="button">
                        Noch nicht registriert? Zur Registrierung geht es hier ...
                    </a>
                </div>
            </div>
			<?php 
				if (! $res->isOK()) 
				{ 
					FormElements::feedbackModal($res, 'Schlie&szlig;en'); 
				}
				else
				{
					FormElements::suspiciousBrowserModal();
				}
			?>
        </div>
		
        <?php 
			FormElements::scriptRefs(); 
			if (! $res->isOK()) 
			{ 
				FormElements::launchFeedback(); 
			}
			else
			{
				FormElements::launchBrowserModal();
			}
		?>
    </body>
 </html>   
