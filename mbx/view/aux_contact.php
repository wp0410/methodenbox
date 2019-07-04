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
include_once '../view/frm_common.php';
include_once '../model/app_warning.php';
include_once '../model/app_result.php';
include_once '../model/sql_connection.php';
include_once '../model/usr_session.php';

set_private_warning_handler();
session_start();

$db_conn = DatabaseConnection::get_connection();
$usr_session = new UserSession($db_conn);
$usr_name = '';

if (! empty($_SESSION) && ! empty($_SESSION['user']))
{
    $res = $usr_session->validateSession($_SESSION['user']);
    if ($res->isOK())
    {
        $_SESSION['user'] = $usr_session->getSessionDescriptor();
        $usr_name = $usr_session->ses_usr_email;
    }
}

$res = new AppResult($_GET);
if (! $res->isOK())
{
	unset($_GET['res_code']);
	unset($_GET['res_text']);
	FormElements::feedbackModal($res, 'Schlie&szlig;en');
}

?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <title><?php echo GlobalParameter::$applicationConfig['applicationTitle'] . ' - Kontakt';?></title>
        <?php FormElements::styleSheetRefs(); ?>
        <script src='https://www.google.com/recaptcha/api.js' async defer></script>
     </head>
    <body>
        <?php FormElements::persTopNavigationBar('AUX.CON', $usr_session->isAuthenticated(), $usr_name, $usr_session->getPermissions()); ?>
        <?php FormElements::bottomNavigationBar('AUX.CON', 0, 0); ?>
        
        <div class="container-fluid">
            <div class="row row-fluid"><div class="col"><br></div></div>
		
            <div class="row row-fluid">
                <div class="col col-sm-1 col-md-3 col-xl-3"></div>
				<div class="col col-sm-10 col-md-6 col-xl-6">
					<div class="alert alert-primary" role="alert"><center><h4>Kontakt</h4></center></div>
				</div>
			</div> <!-- row row-fluid-->
			
			<form id="aux_ctc" method="post" action="../ctrl/aux_contact.php" data-parsley-validate=""><div class="controls">
				<div class="row form-row">
					<div class="col col-sm-1 col-md-3 col-xl-3"></div>
					<div class="col col-sm-10 col-md-6 col-xl-6">
						<div class="card">
							<div class="card-body">
								<h5 class="alert alert-info">Bitte geben Sie einige pers&ouml;nliche Informationen an. Das Methodenbox-Team ben&ouml;tigt diese Daten, um mit Ihnen Kontakt aufzunehmen.</h5>
								
								<div class="row form-row">
									<div class="col col-sm-2 col-md-2 col-xl-2">
										<div class="form-group" id="ctc_anrede">
											<label for="usr_addr">Anrede *</label>
											<select id="usr_addr" name="usr_addr" class="form-control"
												data-parsley-required="" data-parsley-required-message="Bitte die Anrede ausw&auml;hlen.">
												<option></option>
												<option value="Frau">Frau</option>
												<option value="Herr">Herr</option>
											</select>
										</div> <!-- form-group -->
									</div>
									<div class="col col-sm-5 col-md-5 col-xl-5">
										<div class="form-group" id="ctc_first_name">
											<label for="usr_first_name">Vorname *</label>
											<input id="usr_first_name" type="text" name="usr_first_name" class="form-control" placeholder="Vorname"
												data-parsley-required="" data-parsley-required-message="Bitte geben Sie Ihren Vornamen ein.">
										</div> <!-- form-group -->
									</div> <!-- col -->
									
									<div class="col col-sm-5 col-md-5 col-xl-5">
										<div class="form-group" id="ctc_last_name">
											<label for="usr_last_name">Familienname *</label>
											<input id="usr_last_name" type="text" name="usr_last_name" class="form-control" placeholder="Familienname"
												data-parsley-required="" data-parsley-required-message="Bitte geben Sie Ihren Familiennamen ein.">
										</div> <!-- form-group -->
									</div> <!-- col -->
								</div> <!-- row form-row -->
								
								<div class="row form-row">
									<div class="col">
										<div class="form-group" id="ctc_email">
											<label for="usr_email">E-Mail Adresse *</label>
											<input id="usr_email" type="email" name="usr_email" class="form-control"
												data-parsley-required="" data-parsley-required-message="Bitte geben Sie Ihre E-Mail Adresse ein.">
										</div> <!-- form-group -->
									</div> <!-- col -->
								</div> <!-- row form-row -->
								
							</div> <!-- card-body -->
						</div> <!-- card -->
					</div> <!-- col col-sm-10 ... -->
				</div> <!-- row form-row -->
				<div class="row form-row"><br></div>
				<div class="row form-row">
					<div class="col col-sm-1 col-md-3 col-xl-3"></div>
					<div class="col col-sm-10 col-md-6 col-xl-6">
						<div class="card">
							<div class="card-body">
								<h5 class="alert alert-info">Bitte beschreiben Sie Ihr Anliegen</h5>
								
								<div class="row form-row">
									<div class="col">
										<label for="req_type">Art des Anliegens *</label>
										<select id="req_type" class="form-control" name="req_type" 
											data-parsley-required="" data-parsley-required-message="Bitte geben Sie die Art Ihren Anliegens an.">
											<option></option>
											<option value="Q">Liebes Methodenbox-Team, ich m&ouml;chte eine Frage stellen:</option>
											<option value="H">Liebes Methodenbox-Team, ich ben&ouml;tige Hilfe:</option>
											<option value="R">Liebes Methodenbox-Team, ich m&ouml;chte eine Bemerkung machen:</option>
											<option value="E">Liebes Methodenbox-Team, ich m&ouml;chte einen Fehler melden:</option>
											<option value="W">Liebes Methodenbox-Team, ich h&auml;tte einen Wunsch:</option>											
										</select>
										<br>
										<label for="req_desc">Beschreibung (maximal 4000 Zeichen) *</label>
										<textarea id="req_desc" class="form-control" name="req_desc" form="aux_ctc" rows="7" 
											placeholder="Bitte beschreiben Sie Ihr Anliegen so genau wie m&ouml;glich ..."
											data-parsley-required="" data-parsley-required-message="Bitte beschreiben Sie Ihr Anliegen so genau wie m&ouml;glich."
											data-parsley-maxlength="4000" data-parsley-maxlength-message="Die Beschreibung darf h&ouml;chstens 4000 Zeichen lang sein"></textarea>
									</div>
								</div> <!-- row form-row -->

								<div class="row form-row"><div class="col"><br></div></div>

								<div class="row form-row">
									<div class="col col-sm-9 col-md-9 col-xl-9">
										<?php
										if (GlobalParameter::$applicationConfig['validateCaptcha'])
										{
											echo '<div class="g-recaptcha" data-sitekey="';
											echo GlobalSecretParameter::$captchaConfig[GlobalParameter::$applicationConfig['deploymentZone']]['sitekey'];
											echo '"></div>';
										}
										else 
										{
											echo '<div class="form-check"><input class="form-check-input" type="checkbox" id="emul_captcha" name="emul_captcha" value="emul_captcha" ';
											echo 'data-parsley-required="" data-parsley-required-message="Sind Sie ein Roboter?">';
											echo '<label class="form-check-label" for="emul_captcha">';
											echo 'Ich bin kein Roboter *';
											echo '</label></div>';
										}
										?>
									</div>
									<div class="col col-sm-3 col-md-3 col-xl-3">
										<input id="ctc_submit" name="ctc_submit" type="submit" class="btn btn-primary btn-block btn-send" value="Absenden"> 
									</div>
								</div> <!-- row form-row -->
							</div> <!-- card-body -->
						</div> <!-- card -->
					</div> <!-- col col-sm-10 ... -->
				</div> <!-- row form-row -->
				
			</div></form>
           
        </div> <!-- container-fluid -->

        <?php FormElements::scriptRefs(); ?>
        <?php if (!$res->isOK()) { FormElements::launchFeedback(); } ?>
    </body>
 </html>   
