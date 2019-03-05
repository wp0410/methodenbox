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
include_once 'model/mdl_usr.php';
include_once 'model/mdl_ssn.php';
include_once 'model/mdl_jnl.php';
include_once 'model/mdl_err.php';
include_once 'model/mdl_bs3.php';
  
$success = false;
$db_conn = DatabaseConnection::get_connection();
if ($db_conn == null)
{
    $app_err = DatabaseConnection::get_error();
    $app_err->handle_fatal();
}

if (! empty($_POST))
{
    if ((! empty($_POST['user_email'])) && (! empty($_POST['user_pwd'])))
    {
        //$db_conn = db_connect();

        $user_account = new UserAccount($db_conn);
        $user_account->load_by_email($_POST['user_email']);
        $val_res = $user_account->validate_login($_POST['user_pwd']);

        if ($val_res['code'] == 0)
        {
            $jnl_entry = new JournalEntry($db_conn, $user_account->usr_id(), $user_account->usr_email, 'USER.LOGIN');
            $jnl_entry->set_jnl_result(0, 'OK');
            $jnl_entry->store();
            
            $usr_sess = new UserSession($db_conn, $user_account->validate_role('ADMIN'));
            if ($usr_sess->create($user_account->usr_id(), $user_account->usr_email))
            {
                $jnl_entry = new JournalEntry($db_conn, $user_account->usr_id(), $user_account->usr_email, 'SESSION.CREATE');
                $jnl_entry->set_jnl_result(0, 'OK');
                $jnl_entry->set_jnl_data(json_encode($usr_sess));
                $jnl_entry->store();
                
                $_SESSION['user'] = $usr_sess->sess_id();
                $success = true;
            }
            else
            {
                $jnl_entry = new JournalEntry($db_conn, $user_account->usr_id(), $user_account->usr_email, 'SESSION.CREATE');
                $jnl_entry->set_jnl_result(202, '[E_202] Database error(Store User Session)');
                $jnl_entry->set_jnl_data(json_encode($usr_sess));
                $jnl_entry->store();

                $responseArray = array('type' => 'danger', 'message' => 'Anmeldung fehlgeschlagen');
            }
        }
        else
        {
            $jnl_entry = new JournalEntry($db_conn, $user_account->usr_id(), $user_account->usr_email, 'USER.LOGIN');
            $jnl_entry->set_jnl_result($val_res['code'], $val_res['text']);
            $jnl_entry->store();
            
            $responseArray = array('type' => 'danger', 'message' => 'Anmeldung fehlgeschlagen');
        }
        
        if ($user_account->usr_id() > 0)
        {
            $user_account->store();
        }
    }
    
    if ($success)
    {
        $responseArray = array('type' => 'success', 'message' => 'Anmeldung erfolgreich');

        $prev = array('form' => 'usr_lin', 'result' => 0, 'msg' => 'Sie wurden erfolgreich angemeldet');
        $_SESSION['route_prev'] = $prev;
        header('Location: /php/mth_ovw.php');
        exit;
    }
    else 
    {
        $responseArray = array('type' => 'danger', 'message' => 'Anmeldung fehlgeschlagen');
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
    
        <?php FormatHelper::style_sheet_refs(); ?>
    </head>

    <body>
        <?php FormatHelper::create_menu(false, basename($_SERVER['PHP_SELF'])); ?>

        <div class="container" role="main">
            <div class="row">
                <div class="col col-md-6">
                    <div class="page-header"><h1><?php echo GlobalParam::$app_config['app_title'] . ': Anmelden'; ?></h1></div>
                </div>
            </div>
    
            <form id="login_form" method="post" action="/php/usr_lin.php" data-toggle="validator" role="form">
                <?php 
                if (! $success)
                {
                    echo '<div class="messages"><div class="alert alert-danger" role="alert">' . $responseArray['message'] . '</div></div>';
                }
                ?>
                
                <div class="controls">
                    <div class="row form-row">
                        <div class="col col-md-6">
                            <div class="form-group has-feedback" id="login_email">
                                <label for="user_email">E-Mail Adresse *</label>
                                <input id="user_email" type="email" name="user_email" class="form-control" placeholder="E-Mail Adresse" required>
                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col col-md-6">
                            <div class="form-group has-feedback" id="login_pwd">
                                <label for="user_pwd">Passwort *</label>
                                <input id="user_pwd" type="password" name="user_pwd" class="form-control" required>
                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col col-md-2">
                            <div class="form-group" id="login_submit">
                                <input type="submit" class="btn btn-primary btn-send" value="Anmelden">
                            </div>
                        </div>
                        <div class="col col-md-4">
                            <span class="text-primary">Mit * gekennzeichnete Felder m&uuml;ssen eingegeben werden.</p>
                        </div>
                    </div>
                </div> <!-- controls -->
            </form>
        </div> <!-- container -->
    
        <?php FormatHelper::script_refs(); ?>
    </body>
</html>
