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
include_once '../model/sql_connection.php';
include_once '../model/usr_account.php';
include_once '../model/aux_mailer.php';
include_once '../model/app_warning.php';

set_private_warning_handler();

session_start();

$param_missing = 
    empty($_POST) || 
    empty($_POST['user_email']) || 
    empty($_POST['user_fst_name']) || 
    empty($_POST['user_lst_name']) ||
    empty($_POST['user_pwd']) || 
    empty($_POST['user_pwd_conf']) ||
    empty($_POST['g-recaptcha-response']);

if ($param_missing)
{
    $res = new AppResult(100);
    header('Location: ../view/usr_register.php?res_code=' . $res->code . '&res_text=' . $res->textUrlEncoded());
    exit;
}

// Make sure that the recaptcha - result is validated by Google
$google_secret = GlobalParameter::$captchaConfig[GlobalParameter::$applicationConfig['deploymentZone']]['secret'];
$verify_req = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $google_secret . '&response=' . $_POST['g-recaptcha-response'] . '&remoteip=' . $_SERVER['REMOTE_ADDR'];
$captcha_res = file_get_contents($verify_req);
if ($captcha_res.success == false)
{
    // We are being attacked by a bot
    die('reCaptcha verification failed');
}

$db_conn = DatabaseConnection::get_connection();
$new_usr = new UserAccount($db_conn);
$res = $new_usr->createUserAccount($_POST['user_fst_name'], $_POST['user_lst_name'], $_POST['user_email'], $_POST['user_pwd'], 'USR');
if (! $res->isOK())
{
    header('Location: ../view/usr_register.php?res_code=' . $res->com . '&res_text=' . $res->textUrlEncoded());
    exit();
}

$mailer = new MailjetMailer;
$mailer->setSender(GlobalParameter::$applicationConfig['emailSender'], GlobalParameter::$applicationConfig['emailSenderFullName']);
$mailer->addRecipient($_POST['user_email']);
$mailer->emailSubject = 'Ihre Registrierung bei der Methodenbox';
$mailer->emailText =
    "Vielen Dank für Ihre Registrierung bei der Methodenbox.\n" .
    "\n" .
    "Abschließen der Registrierung:\n" .
    "   1. Maske \"Registrierung Abschließen\" öffnen;\n" .
    "   2. Ihre E-Mail Adresse und Ihr Passwort eingeben;\n" .
    "   3. In das Feld \"Registrierungscode\" folgenden Text eingeben (Copy+Paste):\n" .
    "      " . $new_usr->getChallenge() . "\n" .
    "\n" .
    "--\n" .
    "Ihr Methodenbox Team\n";
$mailer->emailHtml = 
    '<html><head><meta http-equiv="content-type" content="text/html; charset=utf-8"></head>' .
    '<body text="#000000" bgcolor="#FFFFFF">' .
    '<p>Vielen Dank f&uuml;r Ihre Registrierung bei der Methodenbox.</p>' .
    '<p></p>' .
    '<p>Abschlie&szlig;en der Registrierung:</p>' .
    '<ul><li>Maske &quot;Registrierung Abschlie&szlig;en&quot; &ouml;ffnen;</li>' .
    '<li>Ihre E-Mail Adresse und Ihr Passwort eingeben;</li>' .
    '<li>In das Feld &quot;Registrierungscode&quot; folgenden Text eingeben (Copy+Paste)</li><ul>' .
    '<li>' . $new_usr->getChallenge() . '</li></ul></ul>' .
    '<p></p>' .
    '<p>--</p>' .
    '<p>Ihr Methodenbox Team</p></body></html>';
$mailer->sendMail(GlobalParameter::$applicationConfig['doSendEmail']);

$res = new AppResult(901);
header('Location: ../view/usr_confirm.php?res_code=' . $res->code . '&res_text=' . $res->textUrlEncoded());
exit();
?>