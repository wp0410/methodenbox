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
include_once '../model/app_result.php';
include_once '../model/usr_account.php';
include_once '../model/usr_session.php';
include_once '../model/mth_selection.php';
include_once '../view/frm_common.php';
include_once '../model/app_warning.php';

set_private_warning_handler();

session_start();
$db_conn = DatabaseConnection::get_connection();
$usr_session = new UserSession($db_conn);
if (empty($_SESSION) || empty($_SESSION['user']))
{
    $res = new AppResult(405);
}
else
{
    $res = $usr_session->validateSession($_SESSION['user']);
    
    if ($res->isOK())
    {
        if (! $usr_session->isAuthenticated())
        {
            $res = new AppResult(406);
        }
        else
        {
            $_SESSION['user'] = $usr_session->getSessionDescriptor(); 
        }
    }
}
if (! $res->isOK())
{
    header('Location: ../view/aux_error.php?res_code=' . $res->code . '&res_text=' . $res->textUrlEncoded());
    exit;
}

$usr_account = new UserAccount($db_conn);
$res = $usr_account->loadById($usr_session->getUsrId());
$res = new AppResult($_GET);
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <title><?php echo GlobalParameter::$applicationConfig['applicationTitle'] . ' - Methode Anlegen';?></title>
        <?php FormElements::styleSheetRefs(); ?>
    </head>
    <body>
        <?php FormElements::topNavigationBar('MTH.NEW', $usr_session->isAuthenticated(), $usr_session->getPermissions()); ?>
        <?php FormElements::bottomNavigationBar('MTH.NEW'); ?>

        <div class="container-fluid">
           <div class="row row-fluid"><br></div>
           <div class="row row-fluid">
                <div class="col col-md-2 col-xl-2"></div>
                <div class="col-md-8 col-xl-8">
                    <div class="alert alert-primary" role="alert"><center><h4>Methode Anlegen</h4></center></div>
                </div>
            </div>
            
            <?php 
                // if ($res->code != 0) { FormElements::showAlert($res->style(), 'col-md-8 col-xl-8', $res->text, 'col col-md-2 col-xl-2'); }
                // $res = new AppResult(951);
                if (! $res->isOK())
                {
                    FormElements::feedbackModal($res, 'Weitere Methoden anlegen', array('LABEL' => 'Zur Methoden&uuml;bersicht', 'LINK' => '../view/mth_search_pg.php'));
                }
            ?>
            
            <form id="mth_upload" enctype="multipart/form-data" method="post" action="../ctrl/mth_upload.php" data-parsley-validate="">
                <div class="controls">
                    <div class="row form-row"><div class="col col-md-2 col-xl-2"></div>
                        <div class="col col-md-4 col-xl-4">
                            <div class="form-group">
                                <label for="mth_name">Name der Methode *</label>
                                <input id="mth_name" type="text" name="mth_name" class="form-control" placeholder="Name"
                                    data-parsley-required="" data-parsley-required-message="Der Name der Methode muss eingegeben werden"
                                    data-parsley-maxlength="127" data-parsley-maxlength-message="Der Name darf h&ouml;chstens 127 Zeichen lang sein">
                            </div>
                        </div>
                        <div class="col col-md-2 col-xl-2">
                            <div class="form-group">
                                <label for="mth_subject">Unterrichtsfach *</label>
                                <select class="form-control" id="mth_subject" name="mth_subject"
                                    data-parsley-required="" data-parsley-required-message="Bitte w&auml;hlen Sie ein Unterrichtsfach aus">
                                    <option></option>
                                    <?php
                                        foreach(MethodSelectionFactory::getSubjects() as $sub)
                                        {
                                            echo '<option value="' . $sub['VAL'] . '">' . $sub['NAME'] . '</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div> <!-- col -->
                        <div class="col col-md-2 col-xl-2">
                            <div class="form-group">
                                <label for="mth_area">Fachbereich *</label>
                                <select class="form-control" id="mth_area" name="mth_area"
                                    data-parsley-required="" data-parsley-required-message="Bitte w&auml;hlen Sie einen Fachbereich aus">
                                    <option></option>
                                </select>
                            </div>
                        </div> <!-- col -->
                    </div> <!-- row -->
                    
                    <div class="row form-row"><div class="col col-md-2 col-xl-2"></div>
                        <div class="col-md-4 col-xl-4">
                            <div class="form-group">
                                <label for="mth_summary">Beschreibung *</label>
                                <textarea id="mth_summary" class="form-control" name="mth_summary" form="mth_upload" rows="5" placeholder="Beschreibung"
                                    data-parsley-required="" data-parsley-required-message="Eine Beschreibung der Methode muss eingegeben werden"
                                    data-parsley-maxlength="4000" data-parsley-maxlength-message="Die Beschreibung darf h&ouml;chstens 4000 Zeichen lang sein"></textarea>
                            </div>    
                        </div> <!-- col -->
                        <div class="col-md-2 col-xl-2">
                            <div class="form-group">
                                <label for="mth_prep_tm">Vorbereitungszeit *</label>
                                <select class="form-control" id="mth_prep_tm" name="mth_prep_tm"
                                    data-parsley-required="" data-parsley-required-message="Bitte w&auml;hlen Sie die Vorbereitungszeit aus">
                                    <option></option>
                                    <?php
                                        foreach(MethodSelectionFactory::getPrepTime() as $prep)
                                        {
                                            echo '<option value="' . $prep['VAL'] . '">' . $prep['NAME'] . '</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div> <!-- col -->
                        <div class="col col-md-2 col-xl-2">
                           <div class="form-group">
                                <label for="mth_exec_tm">Durchf&uuml;hrungszeit *</label>
                                <select class="form-control" id="mth_exec_tm" name="mth_exec_tm"
                                    data-parsley-required="" data-parsley-required-message="Bitte w&auml;hlen Sie die Durchf&uuml;hrungszeit aus">
                                    <option></option>
                                    <?php
                                        foreach(MethodSelectionFactory::getExecTime() as $exec)
                                        {
                                            echo '<option value="' . $exec['VAL'] . '">' . $exec['NAME'] . '</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="mth_class">Jahrgang *</label>
                                <select class="form-control" id="mth_age_grp" name="mth_age_grp"
                                    data-parsley-required="" data-parsley-required-message="Bitte w&auml;hlen Sie einen Jahrgang aus">
                                    <option></option>
                                    <?php
                                        foreach(MethodSelectionFactory::getAgeGroups() as $cls)
                                        {
                                            // echo '<option>' . $cls['NAME'] . '</option>';
                                            echo '<option value="' . $cls['VAL'] . '">' . $cls['NAME'] . '</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div> <!-- col -->
                    </div> <!-- row -->
                    
                    <div class="row form-row"><div class="col col-md-2 col-xl-2"></div>
                        <div class="col-md-2 col-xl-2">
                            <label>Unterrichtsphase *</label>
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="mth_phase_E" name="mth_phase[]" value="E"
                                        	data-parsley-required="" data-parsley-required-message="Bitte w&auml;hlen Sie mindestens eine Unterrichtsphase aus"
                                            data-parsley-mincheck="1" data-parsley-mincheck-message="Bitte w&auml;hlen Sie mindestens eine Unterrichtsphase aus">
                                        <label class="form-check-label" for="mth_phase_E">Einstieg</label>
                                    </div>    
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="mth_phase_I" name="mth_phase[]" value="I">
                                        <label class="form-check-label" for="mth_phase_I">Information</label>
                                    </div>    
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="mth_phase_S" name="mth_phase[]" value="S">
                                        <label class="form-check-label" for="mth_phase_S">Sicherung</label>
                                    </div>    
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="mth_phase_A" name="mth_phase[]" value="A">
                                        <label class="form-check-label" for="mth_phase_A">Aktivierung</label>
                                    </div>    
                                </div>
                            </div>
                        </div> <!-- col -->
                        <div class="col-md-2 col-xl-2">
                            <label>Sozialform *</label>
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="mth_soc_E" name="mth_soc[]" value="E"
                                        	data-parsley-required="" data-parsley-required-message="Bitte w&auml;hlen Sie mindestens eine Sozialform aus"
                                            data-parsley-mincheck="1" data-parsley-mincheck-message="Bitte w&auml;hlen Sie mindestens eine Sozialform aus">
                                        <label class="form-check-label" for="mth_soc_E">Einzelarbeit</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="mth_soc_P" name="mth_soc[]" value="P">
                                        <label class="form-check-label" for="mth_soc_P">Partnerarbeit</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="mth_soc_G" name="mth_soc[]" value="G">
                                        <label class="form-check-label" for="mth_soc_G">Gruppenarbeit</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="mth_soc_K" name="mth_soc[]" value="K">
                                        <label class="form-check-label" for="mth_soc_K">Klassenplenum</label>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- col -->
                        <div class="col-md-2 col-xl-2">
                            <div class="form-group">
                                <label for="mth_author">AutorIn</label>
                                <input id="mth_author" type="text" name="mth_author" class="form-control" value="<?php echo $usr_account->usr_fst_name . ' ' . $usr_account->usr_lst_name; ?>" disabled>
                                <input id="mth_prime_author" name="mth_prime_author" value="<?php echo $usr_account->usr_fst_name . ' ' . $usr_account->usr_lst_name; ?>" type="hidden">
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="confirm_author" name="confirm_author" value="auth_confirm">
                                    <label class="form-check-label" for="confirm_author">Ich habe die Erlaubnis der zus&auml;tzlichen AutorInnen für die Eintragung eingeholt</label>
                                </div>
                            </div>
                        </div> <!-- col -->
                        <div class="col-md-2 col-xl-2">
                            <div class="form-group">
                                <label for="mth_add_author">Zus&auml;tzliche AutorInnen</label>
                                <textarea id="mth_add_author" class="form-control" name="mth_add_author" form="mth_upload" rows="5" placeholder="Name"
                                    data-parsley-maxlength="120" data-parsley-maxlength-message="F&uuml;r die zus&auml;tzlichen AutorInnen sind h&ouml;chstens 120 Zeichen vorgesehen"></textarea>
                            </div>
                        </div>
                    </div> <!-- row -->
                    <div class="row form-row"><div class="col col-md-2 col-xl-2"></div>
                        <div class="col-md-8 col-xl-8">
                            <div class="form-group">
                            	<input type="hidden" name="MAX_FILE_SIZE" value="4194304" />
                                <div class="input-group">
                                    <label class="input-group-btn">
                                        <span class="btn btn-outline-dark">
                                            Datei ausw&auml;hlen (max. 4 MB) &hellip; 
                                            <input type="file" style="display: none;" id="mth_file" name="mth_file" 
                                                   accept="<?php echo GlobalParameter::$applicationConfig['mthUploadFileTypes'] ?>"
												   data-parsley-max-file-size="4096"
												   data-parsley-max-file-size-message="Die Datei ist gr&ouml;&szlig;er als 4 MB">
                                        </span>
                                    </label>
                                    <input type="text" class="form-control" id="mth_file_name", name="mth_file_name" aria-describedby="mth_file"
                                        data-parsley-required="" data-parsley-required-message="Bitte eine Archivdatei (ZIP, GZ, TAR) ausw&auml;hlen"
                                        data-parsley-remote="../ctrl/mth_file_check.php" data-parsley-remote-message="Die Datei hat das falsche Format. Bitte eine Archivdatei (ZIP, GZ, TAR) ausw&auml;hlen">
                                </div> <!-- input-group -->
                            </div>
                        </div>
                    </div> <!-- row -->
                    
                    <div class="row form-row"><div class="col col-md-2 col-xl-2"></div>
                        <div class="col-md-4 col-xl-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="confirm_agb" name="confirm_agb" value="agb_confirm"
                                    data-parsley-required="" data-parsley-required-message="Bitte best&auml;tigen Sie die AGB">
                                <label class="form-check-label" for="confirm_agb">
                                    * AGB/Richtlinie f&uuml;r die Bereitstellung von Unterrichtsmaterial gelesen und akzeptiert
                                </label>
                            </div>
                        </div> <!-- col -->
                        <div class="col-md-2 col-xl-2">
                            <button type="button" class="btn btn-outline-dark" data-toggle="modal" data-target="#AGBModal">Richtlinie anzeigen</button>
                        </div>
                        <div class="col-md-2 col-xl-2">
                            <div class="form-group float-right">
                                <input type="submit" class="btn btn-primary btn-send" value="Neue Methode Speichern">
                            </div>
                        </div> <!-- col -->
                    </div>
                        
                    <div class="row form-row"><div class="col col-md-2 col-xl-2"></div>
                        <div class="col-md-4 col-xl-4">
                            <label class="form-check-label">Mit * gekennzeichnete Felder m&uuml;ssen eingegeben werden.</label>
                        </div> <!-- col -->

                    </div> <!-- row -->
                </div> <!-- controls -->
            </form>
        </div> <!-- container-fluid -->
        
        <?php FormElements::scriptRefs(); ?>
        <?php if (!$res->isOK()) { FormElements::launchFeedback(); } ?>

        <script type="text/javascript">
            /* global $ */
            $(document).ready(function () {
                $("#mth_subject").change(function () {
                    $.post(
                        "/mbx/ctrl/mth_value.php", 
                        { 
                            val_type: "mth_area", 
                            mth_subj: $("#mth_subject").val() 
                        }, 
                        function(data, status) {
                            $("#mth_area").html(data);
                            // $("#mth_area").html(data.replace('<option></option>',''));
                        }
                    );
                });
                $("#mth_add_author").change(function() {
                    if ($("[name=mth_add_author]").val().length == 0) {
                        $("[name=confirm_author]").removeAttr("data-parsley-required");
                        $("[name=confirm_author]").removeAttr("data-parsley-required-message");
                    }
                    else {
                        $("[name=confirm_author]").attr("data-parsley-required", "");
                        $("[name=confirm_author]").attr("data-parsley-required-message", "Bitte best&auml;tigen Sie das Einverst&auml;ndnis der zus&auml;tzlichen AutorInnen");
                    }
                });
            });

            $(function() {
                $(document).on('change', ':file', function() {
                    var input = $(this),
                    numFiles = 1, // input.get(0).files ? input.get(0).files.length : 1,
                    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
                    
                    input.trigger('fileselect', [numFiles, label]);
                });
                    
                $(document).ready( function() {
                    $(':file').on('fileselect', function(event, numFiles, label) {
                        var input = $(this).parents('.input-group').find(':text'),
                            log = numFiles > 1 ? numFiles + ' Dateien ausgew&auml;hlt' : label;
                        if (input.length) {
                            input.val(log);
                        } else {
                            if( log ) alert(log);
                        }
                    });
                });
            });
        </script>
		<script type="text/javascript">
			window.Parsley.addValidator('maxFileSize', {
				validateString: function(_value, maxSize, parsleyInstance) {
					var files = parsleyInstance.$element[0].files;
					return (files.length != 1)  || (files[0].size <= maxSize * 1024);
				},
				requirementType: 'integer',
				messages: {
					en: 'This file should not be larger than %s MBytes',
				}
			});
		</script>
    </body>
 </html>   
