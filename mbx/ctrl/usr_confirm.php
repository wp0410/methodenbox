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
include_once '../model/aux_parameter_sec.php';
include_once '../model/aux_text.php';
include_once '../model/sql_connection.php';
include_once '../model/usr_account.php';
include_once '../model/usr_session.php';
include_once '../model/app_warning.php';

set_private_warning_handler();

session_start();

$param_missing = 
    empty($_POST) || 
    empty($_POST['user_email']) || 
    empty($_POST['user_pwd']) || 
    empty($_POST['user_challenge']);

if (GlobalParameter::$applicationConfig['validateCaptcha'])
{
    $param_missing = $param_missing || empty($_POST['g-recaptcha-response']);
}
else
{
    $param_missing = $param_missing || empty($_POST['emul_captcha']);
}
    
if ($param_missing)
{
    $res = new AppResult(100);
    header('Location: ../view/usr_confirm.php?res_code=' . $res->code . '&res_text=' . $res->textUrlEncoded());
    exit;
}

// Make sure that the recaptcha - result is validated by Google
if (GlobalParameter::$applicationConfig['validateCaptcha'])
{
    $google_secret = GlobalSecretParameter::$captchaConfig[GlobalParameter::$applicationConfig['deploymentZone']]['secret'];
    // $google_secret = GlobalParameter::$captchaConfig[GlobalParameter::$applicationConfig['deploymentZone']]['secret'];
    $verify_req = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $google_secret . '&response=' . $_POST['g-recaptcha-response'] . '&remoteip=' . $_SERVER['REMOTE_ADDR'];
    $captcha_res = file_get_contents($verify_req);
    if ($captcha_res.success == false)
    {
        // We are being attacked by a bot
        die('reCaptcha verification failed');
    }
}

$db_conn = DatabaseConnection::get_connection();
$usr = new UserAccount($db_conn);

$res = $usr->verifyChallenge($_POST['user_email'], $_POST['user_pwd'], $_POST['user_challenge']);
if (! $res->isOK())
{
    header('Location: ../view/usr_confirm.php?res_code=' . $res->code . '&res_text=' . $res->textUrlEncoded());
    exit;
}

$sess = new UserSession($db_conn);
$res = $sess->startSession($usr->getId(), $usr->getRole());
if (! $res->isOK())
{
    header('Location: ../view/usr_confirm.php?res_code=' . $res->code . '&res_text=' . $res->textUrlEncoded());
    exit;
}
else
{
    $_SESSION['user'] = array('sid' => $sess->getId(), 'uid' => $usr->getId(), 'hash' => $sess->getSessionHash());
    header('Location: ../view/mth_search.php');
    exit;
}
?>