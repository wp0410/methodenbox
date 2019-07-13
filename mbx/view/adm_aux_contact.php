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
include_once '../model/aux_parameter.php';
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
            if (! $usr_session->checkPermission('ADM.REQ'))
            {
                $res = new AppResult(407);
            }
            else
            {
                $_SESSION['user'] = $usr_session->getSessionDescriptor();
            }
        }
    }
}
if (! $res->isOK())
{
    header('Location: ../view/aux_error.php?res_code=' . $res->code . '&res_text=' . $res->textUrlEncoded());
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <title><?php echo GlobalParameter::$applicationConfig['applicationTitle'] . ' - Benutzerverwaltung';?></title>
        <?php FormElements::styleSheetRefs(); ?>
    </head>
    <body>
        <?php FormElements::topNavBar('ADM.USR', $usr_session); ?>
        <?php FormElements::bottomNavBar('ADM.USR'); ?>
		
        <div class="container-fluid">
           <div class="row row-fluid"><br></div>
           <div class="row row-fluid">
                <div class="col">
                    <div class="alert alert-primary" role="alert"><center><h4>Anfragen bearbeiten</h4></center></div>
                </div>
            </div> <!-- row row-fluid -->
            <div class="row row-fluid"></div>

            <div class="row row-fluid">
				<div class="col-sm-6 col-md-5 col-lg-4 col-xl-4">
					<div class="card">
						<div class="card-header">
							<div class="row justify-content-between">
								<div class="col">
									<span class="input-group-text">Offene Anfragen</span>
								</div> <!-- col -->
								<div class="col">
									<div class="input-group">
										<div class="input-group-prepend">
											<label class="input-group-text" for="res_lines_per_page">Eintr&auml;ge pro Seite</label>
										</div>
										<input type="number" id="res_lpp" name="res_lpp" style="text-align:center"
											value="<?php echo GlobalParameter::$applicationConfig['admPageNumLines']; ?>" min="5" max="20" step="1" />
                                    	<input type="hidden" id="curr_usr_id" name="curr_usr_id" value=" <?php echo $usr_session->getUsrId(); ?> ">
									</div> <!-- input-group -->
								</div> <!-- col  -->
							</div> <!-- row form-row -->
						</div> <!-- card-header -->

						<div class="card-body" id="adm_result"></div>
					</div> <!-- card -->
				</div> <!-- col-sm-6 col-md-5 ... -->
				
				<div class="col-sm-6 col-md-7 col-lg-8 col-xl-8">
					<div id="adm_detail"></div>
				</div> <!-- col-sm-6 col-md-7 ... -->
			</div> <!-- row row-fluid -->
			
			<div class="modal modal-fade" id="reqOkModal" name="reqOkModal" role="dialog" aria-labelledby="reqOkModal" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title"><span><i class="fa fa-check-circle" aria-hidden="true"></i>&nbsp;&nbsp;Erfolg</span></h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>					
						</div>
						<div class="modal-body">
							<div class="alert alert-success"><h4>Die Antwort wurde erfolgreich gesendet, die Kontaktanfrage wurde geschlossen</h4></div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal modal-fade" id="reqErrorModal" name="reqErrorModal" role="dialog" aria-labelledby="reqErrorModal" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title"><span><i class="fa fa-exclamation" aria-hidden="true"></i>&nbsp;&nbsp;Fehler</span></h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>					
						</div>
						<div class="modal-body">
							<div class="alert alert-danger" id="reqErrTxt"></div>
						</div>
					</div>
				</div>
			</div>
		</div> <!-- container-fluid -->

		
        <?php FormElements::scriptRefs(); ?>
		<script type="text/javascript">
			/* global */
			function load_req_list() {
            	$.post(
                	"/mbx/ctrl/adm_aux_contact.php",
                	{
                		curr_usr_id: $('#curr_usr_id').val(),
                        lines_per_pg: $('#res_lpp').val()
                	},
                	function(data, status) {
                    	$('#adm_result').html(data);
                	}
                );                    	
			}

			$(window).on('load', function() {
                load_req_list();
            });

            function get_req_detail(req_id) {
                $.post(
                	"/mbx/view/adm_aux_contact_detail.php",
                	{
                    	curr_usr_id: $('#curr_usr_id').val(),
                    	req_id: req_id
                	},
                	function(data, status) {
                    	$('#adm_detail').html(data);
                	}
                );
            }
			
			function answer_changed() {
                var req_answer = $('#req_ans_ta').val().trim();

                if ((req_answer !== null) && (req_answer !== '') && (req_answer.length > 0)) {
                	document.getElementById('btn_send').disabled = false;
                }
				else {
                	document.getElementById('btn_send').disabled = true;
				}
			}
		</script>

		<script type="text/javascript">
            function post_answer(par_req_id) {
                var loc_answer = $('#req_ans_ta').val().trim();
                if ((loc_answer !== null) && (loc_answer !== '') && (loc_answer.length > 0)) {
					$.post(
						"/mbx/ctrl/adm_aux_contact_answer.php",
						{
							curr_usr_id: $('#curr_usr_id').val(),
							req_id: par_req_id,
							req_answer: loc_answer
						},
						function(data, status) {
							if (data == "OK") {
								$('#reqOkModal').modal('show');
							}
							else {
								$('#reqErrTxt').html('<h4>' + data + '</h4>');
								$('#reqErrorModal').modal('show');
							}
						}
					);                    	
				}
            }
			
			$('#reqOkModal').on('hidden.bs.modal', function (e) {
				location.reload();
			});			

			$('#reqErrorModal').on('hidden.bs.modal', function (e) {
				location.reload();
			});			
		</script>
     </body>
 </html>   
