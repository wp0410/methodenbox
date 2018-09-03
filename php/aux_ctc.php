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

include_once 'model/mdl_par.php';
include_once 'model/mdl_dbs.php';
include_once 'model/mdl_ssn.php';
include_once 'model/mdl_jnl.php';
include_once 'model/mdl_frm.php';
include_once 'model/mdl_err.php';
include_once 'model/mdl_mlr.php';


// Check for valid user session
$usr_is_authenticated = false;
if (empty($_SESSION) || empty($_SESSION['user']))
{
    /* Unauthenticated users should also be able to use the contact form
    
    $app_err = new ErrorInfo();
    $app_err->err_last_action = 'Validate User Authentication';
    $app_err->err_number = 300;
    $app_err->err_text = 'No User Session';
    $app_err->handle_fatal();
    */
}
else
{
    $db_conn = DatabaseConnection::get_connection();
    if ($db_conn == null)
    {
        $app_err = DatabaseConnection::get_error();
        $app_err->handle_fatal();
    }
    
    $usr_sess = new UserSession($db_conn);
    $usr_sess->load_by_id($_SESSION['user']);
    
    if (! $usr_sess->valid())
    {
        $jnl_entry = new JournalEntry($db_conn, $usr_sess->usr_id, $usr_sess->usr_name, 'SESSION.VALIDATE');
        $jnl_entry->set_jnl_result(301, 'Invalid User Session');
        $jnl_entry->set_jnl_data(json_encode($usr_sess));
        $jnl_entry->store();

        /* Unauthenticated users should also be able to use the contact form
        
        $app_err = new ErrorInfo();
        $app_err->err_last_action = 'Validate User Authentication';
        $app_err->err_number = 301;
        $app_err->err_text = 'Invalid User Session';
        $app_err->handle_fatal();
        */
    }
    else
    {
        $usr_sess->extend();
        $usr_is_authenticated = true;
    }
}

$err_msg = array();
$success = true;

if (! empty($_POST))
{
    if ((!empty($_POST['user_fst_name'])) && (!empty($_POST['user_lst_name'])) && (!empty($_POST['user_email'])) && (!empty($_POST['user_text'])))
    {
        if (! empty($_POST['g-recaptcha-response']))
        {
            $google_secret = GlobalParam::$captcha_cnf[GlobalParam::$app_config['deploy_zone']]['secret'];
            $verify_req = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $google_secret . '&response=' . $_POST['g-recaptcha-response'] . '&remoteip=' . $_SERVER['REMOTE_ADDR'];
            $captcha_res = file_get_contents($verify_req);
            
            if ($captcha_res.success == false)
            {
                // We are being attacked by a bot
                $jnl_entry = new JournalEntry($db_conn, 0, $_POST['user_email'], 'CONTACT');
                $jnl_entry->set_jnl_result(103, '[E_103] Client captcha validation failed');
                $jnl_data = array('user_fst_name' => $_POST['user_fst_name'], 'usr_lst_name' => $_POST['user_lst_name'], 'user_email' => $_POST['user_email']);
                $jnl_entry->set_jnl_data(json_encode($jnl_data));
                $jnl_entry->store();

                die();
            }
            else
            {
                $mj_mailer = new MailjetMailer();
                
                $mj_mailer->emailSubject = 'Methodenbox: Anfrage von ' . $_POST['user_fst_name'] . ' ' . $_POST['user_lst_name'];
                $mj_mailer->emailText = $_POST['user_text'];
                $mj_mailer->set_sender(GlobalParam::$mailer_cnf['sender_email'], GlobalParam::$mailer_cnf['sender_name']);
                $mj_mailer->add_recipient(GlobalParam::$mailer_cnf['target_email']);
                $mj_mailer->add_recipient($_POST['user_email']);
                
                $mj_mailer->send(GlobalParam::$app_config['ctc_send_mail']);
                
                $add_msg = 'Ihre Anfrage wurde erfolgreich verarbeitet. Sie werden in den n&auml;chsten Tagen eine R&uuml;ckmeldung erhalten.';
                if ($usr_is_authenticated)
                {
                    header('Location: /php/mth_ovw.php?add_msg=' . $add_msg);
                }
                {
                    header('Location: /php/app_ovw.php?add_msg=' . $add_msg);
                }
                exit;
            }
        }
        else
        {
            $err_msg[] = 'Bitte best&auml;tigen Sie, dass Sie das Kontaktformular manuell ausf&uuml;llen, indem Sie das K&auml;stchen "Ich bin kein Roboter" anklicken. ' .
            'Das ist unbedingt notwendig, um Missbrauch des Kontaktformulars zu verhindern.';
            $success = false;
        }
    }
    else
    {
        $err_msg[] = 'Bitte geben Sie Werte f&uuml;r alle Pflichtfelder ein';
        $success = false;
    }
}
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Ilse Pachlinger: Sammlung von Unterrichtsmethoden">
        <meta name="author" content="Walter Pachlinger (walter.pachlinger@gmx.at)">
        
        <?php style_sheet_refs(); ?>
        
        <script src='https://www.google.com/recaptcha/api.js'></script>
    </head>

    <body>
        <?php create_menu($usr_is_authenticated, basename($_SERVER['PHP_SELF'])); ?>
        
        <div class="container" role="main">
            <div class="page-header"><h1><?php echo GlobalParam::$app_config['app_title'] . ': Kontakt'; ?></h1></div>
            <div class="row">
                <form id="contact_form" method="post" action="/php/aux_ctc.php" data-toggle="validator" role="form">
                    <?php 
                        if ($success)
                        {
                            echo '<div class="messages"></div>'; 
                        }
                        else
                        {
                            echo '<div class="messages"><div class="alert alert-danger" role="alert">';
                            foreach ($err_msg as $msg)
                            {
                                echo '<p>' . $msg . '</p>';
                            }
                            echo '</div></div>';
                        }
                    ?>
                    <div class="controls">
                        <div class="col-md-5">
                            <div class="form-group" id="ctc_fst_name">
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
                            </div> <!-- form-group -->
                            
                            <div class="form-group" id="ctc_lst_name">
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
                            </div> <!-- form-group -->
                            
                            <div class="form-group" id="ctc_email">
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
                            
                            <div class="form-group" id="ctc_phone">
                                <label for="user_phone">Telefonnummer</label>
                                    <?php
                                        if ($success || empty($_POST) || empty($_POST['user_phone']))
                                        {
                                            echo '<input id="user_phone" type="tel" name="user_phone" class="form-control" placeholder="Telefonnummer">';
                                        }
                                        else 
                                        {
                                            echo '<input id="user_phone" type="tel" name="user_phone" class="form-control" value="' . $_POST['user_phone'] . '">';
                                        }
                                    ?>
                                <div class="help-block with-errors"></div>
                            </div>
                            
                            <div class="form-group" id="ctc_text">
                                <label for="user_text">Nachricht *</label>
                                    <?php
                                        if ($success || empty($_POST) || empty($_POST['user_text']))
                                        {
                                            echo '<textarea id="user_text" name="user_text" form="contact_form" required cols="62" rows="10"></textarea>';
                                        }
                                        else 
                                        {
                                            echo '<textarea id="user_text" name="user_text" form="contact_form" required cols="62" rows="10">' . $_POST['user_text'] . '</textarea>';
                                        }
                                    ?>
                                <div class="help-block with-errors"></div>
                            </div> <!-- form-group -->
                            
                            <!-- Google Re-Captcha -->
                            <div class="g-recaptcha" data-sitekey="<?php echo GlobalParam::$captcha_cnf[GlobalParam::$app_config['deploy_zone']]['sitekey']; ?>"></div>
                            
                            <!-- Submit Button -->
                            <div class="form-group" id="ctc_submit">
                                <input type="submit" class="btn btn-primary btn-send" value="Absenden">
                            </div><!-- form-group -->
                            
                            <div class="form-group">
                                <p class="text-info"><strong>*</strong>Pflichtfelder</p>
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