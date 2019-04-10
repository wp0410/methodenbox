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
include_once '../model/aux_parameter_sec.php';
include_once '../model/app_result.php';
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
        <title><?php echo GlobalParameter::$applicationConfig['applicationTitle'] . ' - Registrierung best&auml;tigen';?></title>
        <?php FormElements::styleSheetRefs(); ?>
        <script src='https://www.google.com/recaptcha/api.js'></script>
    </head>
    <body>
        <?php FormElements::topNavigationBar('USR.CONF', 0); ?>
        <?php FormElements::bottomNavigationBar('USR.CONF'); ?>
        
        <div class="container-fluid">
            <div class="row row-fluid"><div class="col"><br></div></div>
           <div class="row row-fluid">
                <div class="col col-sm-1 col-md-3 col-xl-3"></div> 
                <div class="col col-sm-10 col-md-6 col-xl-6">
                   <div class="alert alert-primary" role="alert"><center><h4>Registrierung Best&auml;tigen</h4></center></div>
                </div>
            </div>

            <?php 
            if ($res->code != 0) 
            { 
                FormElements::showAlert($res->style(), 'col col-sm-10 col-md-6 col-xl-6', $res->text, 'col col-sm-1 col-md-3 col-xl-3'); 
            } 
            ?>

            <form id="usr_confirm" method="post" action="../ctrl/usr_confirm.php">
                <div class="controls">
                    <div class="row form-row">
                        <div class="col col-sm-1 col-md-3 col-xl-3"></div>
                        <div class="col col-sm-5 col-md-3 col-xl-3">
                            <div class="form-group" id="login_email">
                                <label for="user_email">E-Mail Adresse *</label>
                                <input id="user_email" type="email" name="user_email" class="form-control" placeholder="E-Mail Adresse">
                            </div>
                        </div>

                    <!-- </div>
                    <div class="row form-row"> -->

                        <div class="col col-sm-5 col-md-3 col-xl-3">
                            <div class="form-group" id="login_pwd">
                                <label for="user_pwd">Passwort *</label>
                                <input id="user_pwd" type="password" name="user_pwd" class="form-control" placeholder="Passwort">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row form-row">
                        <div class="col col-sm-1 col-md-3 col-xl-3"></div>
                        <div class="col col-sm-10 col-md-6 col-xl-6">
                            <div class="form-group">
                                <label for="user_challenge">Registrierungscode *</label>
                                <input id="user_challenge" type="text" name="user_challenge" class="form-control" placeholder="Registrierungscode">
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

                    <div class=="row form-row"><div class="col-md-6 col-xl-6"><br></div></div>

                    <div class="row form-row">
                        <div class="col col-sm-1 col-md-3 col-xl-3"></div>
                        <div class="col col-sm-5 col-md-3 col-xl-3">
                            <div class="form-group" id="login_submit">
                                <input type="submit" class="btn btn-primary btn-send" value="Best&auml;tigung abschicken">
                            </div>
                        </div>
                    </div>
                </div> <!-- controls -->
            </form>
        </div>
        
        <?php FormElements::scriptRefs(); ?>
        <script type="text/javascript">
            /* global $ */
            $('#usr_confirm').validate({
                rules: {
                    user_email: {
                        required: true
                    },
                    user_pwd: {
                        required: true
                    },
                    user_challenge: {
                        required: true,
                        minlength: 32,
                        maxlength: 32
                    }
                },
                messages: {
                    user_email: {
                        email: "Keine g&uuml;ltige E-Mail Adresse",
                        required: "Die E-Mail Adresse muss eingegeben werden"
                    },
                    user_pwd: {
                        required: "Das Passwort muss angegeben werden"
                    },
                    user_challenge: {
                        required: "Der Registrierungscode muss angegeben werden",
                        minlength: "Bitte 32 Zeichen f&uuml;r den Registrierungscode eingeben",
                        maxlength: "Bitte 32 Zeichen f&uuml;r den Registrierungscode eingeben"
                    }
                }
            })
        </script>
    </body>
 </html>   
