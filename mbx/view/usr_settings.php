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
include_once '../model/usr_session.php';
include_once '../view/frm_common.php';
include_once '../model/app_warning.php';
include_once '../model/app_result.php';

set_private_warning_handler();
session_start();
$db_conn = DatabaseConnection::get_connection();
$usr_session = new UserSession($db_conn);
$usr_name = '';
if (! empty($_SESSION) && ! empty($_SESSION['user']))
{
    $res = $usr_session->validateSession($_SESSION['user']);
    
    if (! $res->isOK())
    {
        $usr_session->closeSession();
        session_destroy();
        header('Location: ../view/aux_error.php?res_code=' . $res->code . '&res_text=' . $res->textUrlEncoded());
        exit;
    }
    else
    {
        $_SESSION['user'] = $usr_session->getSessionDescriptor();
        $usr_name = $usr_session->ses_usr_email;
    }
}

$res = new AppResult($_GET);
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <title><?php echo GlobalParameter::$applicationConfig['applicationTitle'] . ' - Einstellungen';?></title>
        <?php FormElements::styleSheetRefs(); ?>
    </head>
    <body>
        <?php FormElements::persTopNavigationBar('USR.OPT', $usr_session->isAuthenticated(), $usr_name, $usr_session->getPermissions()); ?>
        <?php FormElements::bottomNavigationBar('USR.OPT', 0, 0); ?>
        
        <div class="container-fluid">
           <div class="row row-fluid"><br></div>
           <div class="row row-fluid">
                <div class="col-sm-1 col-md-3 col-xl-3"></div>
                <div class="col-sm-10 col-md-6 col-xl-6">
                    <div class="alert alert-primary" role="alert"><center><h4>Benutzer Einstellungen</h4></center></div>
                </div>
            </div>
            <div class="row row-fluid"></div>
            <?php 
            if ($res->code != 0) 
            {
                FormElements::showAlert($res->style(), 'col-sm-10 col-md-6 col-xl-6', $res->text, 'col-sm-1 col-md-3 col-xl-3'); 
            } 
            ?>

            <div class="row row-fluid">
            	<div class="col-sm-1 col-md-3 col-xl-3"></div>
				<div class="col-sm-10 col-md-6 col-xl-6">
                    <div id="SettingsAccordion">
                    
                    	<!-- Change the password -->
                    	<div class="card">
                    		<div class="card-header" id="set_pwd_hdr">
                    			<button class="btn btn-link" data-toggle="collapse" data-target="#set_pwd_bdy" aria-expanded="false" aria-controls="set_pwd_bdy">
                    				Passwort &Auml;ndern
                    			</button>
                    		</div> <!-- card-header -->
                    		<div id="set_pwd_bdy" class="collapse" aria-labelledby="set_pwd_hdr" data-parent="#SettingsAccordion">
                    			<div class="card-body">
                                    <form id="usr_settings" method="post" action="../ctrl/usr_settings.php" data-parsley-validate="">
                                    	<input type="hidden" name="set_type" id="set_type" value="PWD">
                                    	<div class="row form-row">
                                    		<div class="col col-sm-4 col-md-4 col-xl-4">
                                    			<div class="form-group" id="current_pwd_grp">
                                					<label for="current_pwd">Aktuelles Passwort *</label>
                                					<input id="current_pwd" type="password" name="current_pwd" class="form-control" placeholder="Passwort"
                                						data-parsley-required="" data-parsley-required-message="Das Passwort muss angegeben werden">
                            					</div>
                                    		</div> <!-- col -->

                                    		<div class="col col-sm-4 col-md-4 col-xl-4">
                                                <div class="form-group" id="new_pwd_grp">
                                                    <label for="new_pwd">Neues Passwort *</label>
                                                    <input id="new_pwd" type="password" name="new_pwd" class="form-control" placeholder="Passwort"
                    									data-parsley-required="" data-parsley-required-message="Bitte ein g&uuml;ltiges Passwort eingeben"
                    									data-parsley-minlength="8" data-parsley-minlength-message="Das Passwort muss mindestens 8 Zeichen lang sein"
                    									data-parsley-remote="../ctrl/usr_check.php" data-parsley-remote-message="Das Passwort muss Gro&szlig;buchstaben, Kleinbuchstaben und Ziffern enthalten"
                    								>
                                                </div> 
                                            </div> <!-- col -->

                                    		<div class="col col-sm-4 col-md-4 col-xl-4">
                                                <div class="form-group" id="new_pwd_conf_grp">
                                                    <label for="new_pwd_conf">Best&auml;tigung (Neues Passwort) *</label>
                                                    <input id="new_pwd_conf" type="password" name="new_pwd_conf" class="form-control" placeholder="Passwort Best&auml;tigung"
                    									data-parsley-required="" data-parsley-required-message="Bitte das Passwort zur Best&auml;tigung wiederholen"
                    									data-parsley-equalto="#new_pwd" data-parsley-equalto-message="Die eingegebenen Passw&ouml;rter stimmen nicht &uuml;berein"
                    								> 
                                                </div>
                                    		</div> <!-- col -->

                                    	</div> <!-- row -->
                                    	
                                    	<div class="row form-row">
                                    		<div class="col col-sm-6 col-md-8 col-xl-8">
                                    			<span class="text-dark">Mit * gekennzeichnete Felder m&uuml;ssen eingegeben werden.</p>
                                    		</div> <!-- col -->
                                    		<div class="col col-sm-6 col-md-4 col-xl-4">
                                                <div class="form-group" id="set_pwd_submit">
                                                    <input type="submit" class="btn btn-primary btn-send float-right" value="Passwort &Auml;ndern">
                                                </div>
                                    		</div> <!-- col -->
                                    	</div> <!-- row -->
                                    </form>
                    			</div> <!-- card-body -->
                    		</div> <!-- set_pwd_body -->
                     	</div> <!-- card -->
						
						<!-- change the form skin -->
                    	<div class="card">
                            <div class="card-header" id="set_skin_hdr">
                                <button class="btn btn-link" data-toggle="collapse" data-target="#set_skin_bdy" aria-expanded="false" aria-controls="set_skin_bdy">
                                    Benutzeroberfl&auml;che
                                </button>
                            </div> <!-- card-header -->
                            <div id="set_skin_bdy" class="collapse" aria-labelledby="set_skin_hdr" data-parent="#SettingsAccordion">
                                <div class="card-body">
                                	<div class="row">
                                		<div class="col col-sm-1 col-md-2 col-xl-2"><br></div>
                                		<div class="col-sm-10 col-md-8 col-xl-8">
                                			<div class="input-group">
  												<select class="custom-select" id="usr_skin" name="usr_skin" aria-label="Themen f&uuml;r die Benutzeroberfl&auml;che">
                                                    <option selected value="lumen">lumen</option>
                                                    <?php
                                                        foreach(GlobalParameter::$applicationConfig['formAvailableSkins'] as $skin)
                                                        {
                                                            if ($skin != "lumen")
                                                            {
                                                                echo '<option value="' . $skin . '">' . $skin . '</option>';
                                                            }
                                                        }
                                                    ?>
  												</select>
                                                <div class="input-group-append">
  		                                        	<button class="btn btn-outline-primary" id="skin_apply" name="skin_apply" type="button">Anwenden</button>
                                                </div>
											</div> <!-- input-group -->
                                		</div> <!-- col -->
                                	</div>
                                </div>
                            </div>
                    	</div> <!-- card -->

                    </div> <!-- SettingsAccordion -->
				</div> <!-- col -->
            </div> <!-- row -->
        </div>

        <?php FormElements::scriptRefs(); ?>
        
        <script type="text/javascript">
            /* global $ */
            $(document).ready(function () {
                $('#skin_apply').click(function() {
                    $.post(
                        "/mbx/ctrl/usr_settings.php", 
                        { 
                            set_type: "SKIN", 
                            usr_skin: $("#usr_skin").val() 
                        }, 
                        function(data, status) {
                            location.reload(true);
                        }
                    );
                });
            });
        </script>
    </body>
 </html>   
