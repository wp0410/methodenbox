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

include 'model/mdl_usr.php';
include 'model/mdl_jnl.php';
include_once 'model/mdl_dbs.php';
include_once 'model/mdl_par.php';
include 'frm_gen.php';

$success = false;
$db_conn = db_connect();

if (! empty($_POST))
{
    if (empty($_POST['user_fst_name']) || empty($_POST['user_lst_name']) || empty($_POST['user_email']) || empty($_POST['user_pwd']) || empty($_POST['user_pwd_conf']))
    {
        $responseArray = array('type' => 'danger', 'message' => 'Registrierung fehlgeschlagen');
    }
    else
    {
        if (! empty($_POST['g-recaptcha-response']))
        {
            // Input was validated by Google re-Captcha
            // Get the result from Google
            $verify_req = 'https://www.google.com/recaptcha/api/siteverify?secret=' . GlobalParam::$captcha_cnf['secret'] . '&response=' . $_POST['g-recaptcha-response'] . '&remoteip=' . $_SERVER['REMOTE_ADDR'];
            $captcha_res = file_get_contents($verify_req);

            if ($captcha_res.success == false)
            {
                // We are being attacked by a bot
                $jnl_entry = new JournalEntry($db_conn, 0, $_POST['user_email'], 'USER.REGISTER');
                $jnl_entry->set_jnl_result(103, '[E_103] Client captcha validation failed');
                $jnl_data = array('user_fst_name' => $_POST['user_fst_name'], 'usr_lst_name' => $_POST['user_lst_name'], 'user_email' => $_POST['user_email']);
                $jnl_entry->set_jnl_data(json_encode($jnl_data));
                $jnl_entry->store();
                
                die();
            }
            else
            {
                // Check if user name (E-Mail Address) is unique
                $user_account = new UserAccount($db_conn);
                if ($user_account->load_by_email($_POST['user_email']))
                {
                    $jnl_entry = new JournalEntry($db_conn, 0, $_POST['user_email'], 'USER.REGISTER');
                    $jnl_entry->set_jnl_result(104, '[E_104] Duplicate e-mail address');
                    $jnl_data = array('user_fst_name' => $_POST['user_fst_name'], 'usr_lst_name' => $_POST['user_lst_name'], 'user_email' => $_POST['user_email']);
                    $jnl_entry->set_jnl_data(json_encode($jnl_data));
                    $jnl_entry->store();
                    
                    $responseArray = array('type' => 'danger', 'message' => 'Es gibt bereits eine Registrierung mit dieser E-Mail Adresse (' . $_POST['user_email'] . ')');
                }
                else
                {
                    $user_account = new UserAccount($db_conn);
                    $user_account->usr_fst_name = $_POST['user_fst_name'];
                    $user_account->usr_lstname = $_POST['user_lst_name'];
                    $user_account->usr_email = $_POST['user_email'];
                    $user_account->set_password($_POST['user_pwd']);
                    
                    $success = $user_account->store();
                    if ($success)
                    {
                        $jnl_entry = new JournalEntry($db_conn, $user_account->usr_id(), $user_account->usr_email, 'USER.REGISTER');
                        $jnl_entry->set_jnl_result(0, 'OK');
                        $jnl_entry->set_jnl_data(json_encode($user_account));
                        $jnl_entry->store();
                        
                        $responseArray = array('type' => 'success', 'message' => 'Registrierung erfolgreich');
                    }
                    else 
                    {
                        $jnl_entry = new JournalEntry($db_conn, 0, $user_account->usr_email, 'USER.REGISTER');
                        $jnl_entry->set_jnl_result(201, '[E_201] Database error(Store User Account)');
                        $jnl_entry->set_jnl_data(json_encode($user_account));
                        $jnl_entry->store();
                        
                        $responseArray = array('type' => 'danger', 'message' => 'Fehler beim Speichern der Registrierungsdaten');
                    }
                }
            }
        }
        else
        {
            $responseArray = array('type' => 'warning', 
                'message' => 'Bitte best&auml;tigen Sie, dass Sie selbst das Formular ausf&uuml;llen, indem Sie das K&auml;stchen "Ich bin kein Roboter" anklicken');
        }
    }

    if ($success)
    {
        header('Location: /php/mth_ovw.php');
        exit;
    }
}
else
{
    $success = true;
}
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Ilse Pachlinger: Sammlung von Unterrichtsmethoden">
        <meta name="author" content="Walter Pachlinger (walter.pachlinger@gmx.at)">
        
        <!-- link rel="stylesheet" href="/css/bootstrap.min.css">
        <link rel="stylesheet" href="/css/bootstrap-theme.css" -->
        <?php style_sheet_refs(); ?>
        <script src='https://www.google.com/recaptcha/api.js'></script>
    </head>
    <body>
        <?php create_menu(false, basename($_SERVER['PHP_SELF'])); ?>
    
        <div class="container" role="main">
            <div class="page-header"><h1><?php echo GlobalParam::$app_config['app_title'] . ': Benutzerregistrierung'; ?></h1></div>
            <div class="row">
                <form data-toggle="validator" id="register_form" method="post" action="/php/usr_new.php" role="form">
                <?php 
                    if ($success)
                    { 
                        echo '<div class="messages"></div>'; 
                    }
                    else
                    {
                        echo '<div class="messages"><div class="alert alert-' . $responseArray['type'] . '" role="alert">' . $responseArray['message'] . '</div></div>';
                    }
                ?>
                    <div class="controls">
                        <div class="col-md-5">
                            <div class="form-group" id="reg_fst_name">
                                <label for="user_fst_name">Vorname *</label>
                                <?php
                                    if ($success || empty($_POST) || empty($_POST['user_fst_name']))
                                    {
                                        echo '<input id="user_fst_name" type="text" name="user_fst_name" class="form-control" placeholder="Vorname" required>';
                                    }
                                    else 
                                    {
                                        echo '<input id="user_fst_name" type="text" name="user_fst_name" class="form-control" value="' . $_POST['user_fst_name'] . '" required>';
                                    }
                                ?>
                                <div class="help-block with-errors"></div>
                            </div>
                            
                            <div class="form-group" id="reg_lst_name">
                                <label for="user_lst_name">Nachname *</label>
                                <?php
                                    if ($success || empty($_POST) || empty($_POST['user_lst_name']))
                                    {
                                        echo '<input id="user_lst_name" type="text" name="user_lst_name" class="form-control" placeholder="Nachname" required>';
                                    }
                                    else
                                    {
                                        echo '<input id="user_lst_name" type="text" name="user_lst_name" class="form-control" value="' . $_POST['user_lst_name'] . '" required>';
                                    }
                                    ?>
                                <div class="help-block with-errors"></div>
                            </div>
                            
                            <div class="form-group" id="reg_email">
                                <label for="user_email">E-Mail Adresse *</label>
                                <?php
                                    if ($success || empty($_POST) || empty($_POST['user_email']))
                                    {
                                        echo '<input id="user_email" type="email" name="user_email" class="form-control" placeholder="E-Mail Adresse" required>';
                                    }
                                    else
                                    {
                                        echo '<input id="user_email" type="email" name="user_email" class="form-control" value="' . $_POST['user_email'] . '" required>';
                                    }
                                ?>
                                <div class="help-block with-errors"></div>
                            </div> <!-- form-group -->
                            
                            <div class="form-group" id="reg_pwd">
                                <label for="user_pwd">Passwort *</label>
                                <input id="user_pwd" type="password" name="user_pwd" class="form-control" required>
                                <div class="help-block with-errors"></div>
                            </div> <!-- form-group -->
                            
                            <div class="form-group" id="reg_pwd_conf">
                                <label for="user_pwd_conf">Passwort Bestätigung *</label>
                                <input id="user_pwd_conf" type="password" name="user_pwd_conf" class="form-control" required data-match="#user_pwd" 
                                data-match-error="Passwort Bestätigung stimmt nicht mit dem Passwort überein">
                                <div class="help-block with-errors"></div>
                            </div> <!-- form-group -->
                            
                            <div class="g-recaptcha" data-sitekey="<?php echo GlobalParam::$captcha_cnf['sitekey']; ?>"></div>
                            
                            <div class="form-group" id="login_submit">
                                <input type="submit" class="btn btn-primary btn-send" value="Bestätigen">
                                </div><!-- form-group -->
                            
                            <div class="form-group">
                                <p class="text-muted"><strong>*</strong>Pflichtfelder</p>
                            </div>
                        </div> <!-- col-md-5 -->
                    </div> <!-- controls -->
                </form>
            </div> <!-- row -->
        </div> <!-- container -->
    
        <!-- script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="/js/bootstrap.min.js"></script>
        <script src="/js/validator.js"></script> -->
        <?php script_refs(); ?>
    </body>
</html>
