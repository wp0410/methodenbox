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
include_once '../model/aux_parameter_sec.php';
include_once '../view/frm_common.php';
include_once '../model/app_warning.php';
include_once '../model/usr_session.php';

set_private_warning_handler();
session_start();

$usr_session = new UserSession(DatabaseConnection::get_connection(), -1);
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <title><?php echo GlobalParameter::$applicationConfig['applicationTitle'] . ' - Passwort zur&uuml;cksetzen';?></title>
        <?php FormElements::styleSheetRefs(); ?>
        <script src='https://www.google.com/recaptcha/api.js'></script>
    </head>
    <body>
        <?php FormElements::topNavBar('USR.PWD', $usr_session); ?>
        <?php FormElements::bottomNavBar('USR.PWD'); ?>

        <div class="container-fluid">
            <div class="row row-fluid"><div class="col"><br></div></div>
            <div class="row row-fluid">
                <div class="col col-sm-1 col-md-3 col-xl-3"></div>
                <div class="col col-sm-10 col-md-6 col-xl-6">
                    <div class="alert alert-primary" role="alert"><center><h4>Passwort zur&uuml;cksetzen</h4></center></div>
                </div>
            </div>
            
            <!-- ?php if ($res->code != 0) { FormElements::showAlert($res->style, 'col-md-6 col-xl-6', $res->text); } ? -->
            
            <form id="usr_pwdres" method="post" action="../ctrl/usr_pwd_reset.php" data-parsley-validate="">
                 <div class="controls">
                    <div class="row form-row">
                        <div class="col col-sm-1 col-md-3 col-xl-3"></div>
                        <div class="col col-sm-10 col-md-6 col-xl-6">
                            <div class="form-group" id="login_email">
                                <label for="user_email">E-Mail Adresse *</label>
                                <input id="user_email" type="email" name="user_email" class="form-control" placeholder="E-Mail Adresse"
									data-parsley-required="" data-parsley-required-message="Die E-Mail Adresse muss eingegeben werden"
									data-parsley-type-message="Keine g&uuml;ltige E-Mail Adresse"
								>
                            </div>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col col-sm-1 col-md-3 col-xl-3"></div>
                        <div class="col col-sm-5 col-md-3 col-xl-3">
                            <div class="form-group" id="reset_pwd">
                                <label for="reset_pwd_1">Neues Passwort *</label>
                                <input id="reset_pwd_1" type="password" name="reset_pwd_1" class="form-control" placeholder="Passwort"
									data-parsley-required="" data-parsley-required-message="Das neue Passwort muss eingegeben werden"
									data-parsley-minlength="8" data-parsley-minlength-message="Das neue Passwort muss mindestens 8 Zeichen lang sein"
									data-parsley-remote="../ctrl/usr_check.php" data-parsley-remote-message="Das neue Passwort muss Gro&szlig;buchstaben, Kleinbuchstaben und Ziffern enthalten"
								>
                            </div>
                        </div>
                        <div class="col col-sm-5 col-md-3 col-xl-3">
                            <div class="form_group" id="conf_pwd">
                                <label for="reset_pwd_2">Passwort Best&auml;tigung *</label>
                                <input id="reset_pwd_2" type="password" name="reset_pwd_2" class="form-control" placeholder="Passwort Best&auml;tigung"
									data-parsley-required="" data-parsley-required-message="Bitte das Passwort zur Best&auml;tigung wiederholen"
									data-parsley-equalto="#reset_pwd_1" data-parsley-equalto-message="Die eingegebenen Passw&ouml;rter stimmen nicht überein"
								>
                            </div>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col col-sm-1 col-md-3 col-xl-3"></div>
                        <div class="col col-sm-5 col-md-3 col-xl-3">
                        	<?php
                        	if (GlobalParameter::$applicationConfig['validateCaptcha'])
                        	{
                        	    echo '<div class="g-recaptcha" data-sitekey="';
                        	    echo GlobalSecretParameter::$captchaConfig[GlobalParameter::$applicationConfig['deploymentZone']]['sitekey'];
                        	    echo '"></div>';
                        	}
                        	else 
                        	{
                        	    echo '<div class="form-check"><input class="form-check-input" type="checkbox" id="emul_captcha" name="emul_captcha" value="emul_captcha">';
                        	    echo '<label class="form-check-label" for="emul_captcha">';
                        	    echo 'Ich bin kein Roboter';
                        	    echo '</label></div>';
                        	}
                        	?>
                        </div>
                        <div class="col col-sm-5 col-md-3 col-xl-3">
                            <span class="text-dark">Mit * gekennzeichnete Felder m&uuml;ssen eingegeben werden.</p>
                        </div>
                    </div>

                    <div class=="row form-row"><div class="col col-sm-1 col-md-3 col-xl-3"></div><div class="col-md-6 col-xl-6"><br></div></div>

                    <div class="row form-row">
                        <div class="col col-sm-1 col-md-3 col-xl-3"></div>
                        <div class="col col-sm-4 col-md-2 col-xl-2">
                            <div class="form-group" id="login_submit">
                                <input type="submit" class="btn btn-primary btn-send" value="Passwort zur&uuml;cksetzen">
                            </div>
                        </div>
                    </div>
                </div> <!-- controls -->
            </form>
        </div>
        
        <?php FormElements::scriptRefs(); ?>
    </body>
 </html>   
