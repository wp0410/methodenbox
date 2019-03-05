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
include_once '../model/app_result.php';
include_once '../view/frm_common.php';

session_start();
$res = new AppResult($_GET);
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <title><?php echo GlobalParameter::$applicationConfig['applicationTitle'] . ' - Registrierung';?></title>
        <?php FormElements::styleSheetRefs(); ?>
        <script src='https://www.google.com/recaptcha/api.js'></script>
    </head>
    <body>
        <?php FormElements::topNavigationBar('USR.REG', 0, 0); ?>
        <?php FormElements::bottomNavigationBar('USR.REG', 0, 0); ?>
        
        <div class="container-fluid">
            <div class="row row-fluid"><div class="col"><br></div></div>

            <div class="row row-fluid">
                <div class="col-sm-1 col-md-3 col-xl-3"></div>
                <div class="col-sm-10 col-md-6 col-xl-6">
                    <div class="alert alert-primary" role="alert"><center><h4>Registrierung</h4></center></div>
                </div>
            </div>
            
            <?php 
            if ($res->code != 0) 
            {
                echo '<div class="col-sm-1 col-md-3 col-xl-3"></div>';
                FormElements::showAlert($res->style(), 'col-sm-10 col-md-6 col-xl-6', $res->text); 
            } 
            ?>
            
            <form id="usr_register" method="post" action="../ctrl/usr_register.php">
                <div class="controls">
                    <div class="row form-row">
                        <div class="col-sm-1 col-md-3 col-xl-3"></div>
                        <div class="col-sm-10 col-md-6 col-xl-6">
                            <div class="form-group" id="reg_email">
                                <label for="user_email">E-Mail Adresse *</label>
                                <input id="user_email" type="email" name="user_email" class="form-control" placeholder="E-Mail Adresse">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row form-row">
                        <div class="col-sm-1 col-md-3 col-xl-3"></div>
                        <div class="col-sm-5 col-md-3 col-xl-3">
                            <div class="form-group" id="reg_fst_name">
                                <label for="user_fst_name">Vorname *</label>
                                <input id="user_fst_name" type="text" name="user_fst_name" class="form-control" placeholder="Vorname"> 
                            </div>
                        </div>

                        <div class="col-sm-5 col-md-3 col-xl-3">
                            <div class="form-group" id="reg_lst_name">
                                <label for="user_lst_name">Familienname *</label>
                                <input id="user_lst_name" type="text" name="user_lst_name" class="form-control" placeholder="Familienname"> 
                            </div>
                        </div>
                    </div>

                    <div class="row form-row">
                        <div class="col-sm-1 col-md-3 col-xl-3"></div>
                        <div class="col-sm-5 col-md-3 col-xl-3">
                            <div class="form-group" id="reg_pwd">
                                <label for="user_pwd">Passwort *</label>
                                <input id="user_pwd" type="password" name="user_pwd" class="form-control" placeholder="Passwort">
                            </div>
                        </div>

                        <div class="col-sm-5 col-md-3 col-xl-3">
                            <div class="form-group" id="reg_pwd_conf">
                                <label for="user_pwd_conf">Passwort Best&auml;tigung *</label>
                                <input id="user_pwd_conf" type="password" name="user_pwd_conf" class="form-control" placeholder="Passwort Best&auml;tigung"> 
                            </div>
                        </div>
                    </div>

                    <div class="row form-row">
                        <div class="col-sm-1 col-md-3 col-xl-3"></div>
                        <div class="col col-sm-5 col-md-3 col-xl-3">
                            <div class="g-recaptcha" data-sitekey="<?php echo GlobalParameter::$captchaConfig[GlobalParameter::$applicationConfig['deploymentZone']]['sitekey']; ?>"></div>
                        </div>
                        <div class="col col-sm-5 col-md-3 col-xl-3">
                            <span class="text-dark">Mit * gekennzeichnete Felder m&uuml;ssen eingegeben werden.</p>
                        </div>
                    </div>

                    <div class=="row form-row"><div class="col-md-6 col-xl-6"><br></div></div>
                    
                    <div class="row form-row">
                        <div class="col-sm-1 col-md-3 col-xl-3"></div>
                        <div class="col col-sm-4 col-md-2 col-xl-2">
                            <div class="form-group" id="login_submit">
                                <input type="submit" class="btn btn-primary btn-send" value="Registrieren">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <?php FormElements::scriptRefs(); ?>
        
        <script type="text/javascript">
            /* global $ */
            $('#usr_register').validate({
                rules: {
                    user_email: {
                        required: true,
                        remote: "../ctrl/usr_check.php"
                    },
                    user_fst_name: {
                        required: true
                    },
                    user_lst_name: {
                        required: true
                    },
                    user_pwd: {
                        required: true,
                        minlength: 8,
                        remote: "../ctrl/usr_check.php"
                    },
                    user_pwd_conf: {
                        required: true,
                        equalTo: '#user_pwd'
                    }
                },
                messages: {
                    user_email: {
                        email: "Keine g&uuml;ltige E-Mail Adresse",
                        required: "Die E-Mail Adresse muss eingegeben werden",
                        remote: "Es gibt bereits eine Registrierung unter dieser E-Mail Adresse"
                    },
                    user_fst_name: {
                        required: "Der Vorname muss eingegeben werden"
                    },
                    user_lst_name: {
                        required: "Der Familienname muss eingegeben werden"
                    },
                    user_pwd: {
                        required: "Bitte ein g&uuml;ltiges Passwort eingeben",
                        minlength: "Das Passwort muss mindestens 8 Zeichen lang sein",
                        remote: "Das Passwort muss Gro&szlig;buchstaben, Kleinbuchstaben und Ziffern enthalten"
                    },
                    user_pwd_conf: {
                        required: "Bitte das Passwort zur Best&auml;tigung wiederholen",
                        equalTo: "Die eingegebenen Passw&ouml;rter stimmen nicht überein"
                    }
                }
            })
        </script>
    </body>
 </html>   
