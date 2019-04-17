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
            if (strpos($usr_session->getPermissions(), 'ADM.USR') === false)
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
        <?php FormElements::topNavigationBar('ADM.USR', $usr_session->isAuthenticated(), $usr_session->getPermissions()); ?>
        <?php FormElements::bottomNavigationBar('ADM.USR'); ?>
        
        <div class="container-fluid">
           <div class="row row-fluid"><br></div>
           <div class="row row-fluid">
           		<div class="col-md-2 col-xl-2"> </div>
                <div class="col-md-8 col-xl-8">
                    <div class="alert alert-primary" role="alert"><center><h4>Benutzerverwaltung</h4></center></div>
                </div>
            </div>
            <div class="row row-fluid"></div>

            <div class="row row-fluid">
           		<div class="col-md-2 col-xl-2"> </div>
				<div class="col col-md-8 col-xl-8">
					<div class="card">
						<div class="card-header">
							<div class="row">
                                <div class="col col-md-8 col-xl-8">
                                    <span class="input-group-text">Vorhandene Benutzerkonten</span>
                                </div>
                                <div class="col col-md-4 col-xl-4">
									<div class="input-group">
										<div class="input-group-prepend">
											<label class="input-group-text" for="res_lines_per_page">Eintr&auml;ge pro Seite</label>
										</div>
										<input type="number" id="res_lpp" name="res_lpp" style="text-align:center"
											value="<?php echo GlobalParameter::$applicationConfig['admPageNumLines']; ?>" min="5" max="20" step="1" />
                                    	<input type="hidden" id="curr_usr_id" name="curr_usr_id" value=" <?php echo $usr_session->getUsrId(); ?> ">
									</div>
								</div>
							</div>
						</div>
						<div class="card-body" id="adm_result">
						</div>
					</div>
				</div>            	
            </div>
        </div>
        
        <div class="modal fade" id="usrDeleteModal" role="dialog" aria-labelledby="usrDeleteLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="usrDeleteLabel">Benutzerkonto l&ouml;schen</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle" aria-hidden="true"></i></span></button>
                    </div> <!-- modal-header -->
                    <div class="modal-body" id="usrDeleteBody">
                    	<div id="usrDeleteMessage" name="usrDeleteMessage"></div>
                    </div> <!-- modal-body -->
                    <div class="modal-footer" id="usrDeleteFooter">
                    </div>
                </div> <!-- modal-content -->
            </div> <!-- modal-dialog -->
        </div> <!-- modal -->

        <div class="modal fade" id="usrModifyModal" role="dialog" aria-labelledby="usrModifyLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="usrModifyLabel"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle" aria-hidden="true"></i></span></button>
                    </div> <!-- modal-header -->
                    <div class="modal-body" id="usrModifyBody">
                    	<div id="usrModifyMessage" name="usrModifyMessage"></div>
                    </div> <!-- modal-body -->
                    <div class="modal-footer" id="usrModifyFooter">
                    </div>
                </div> <!-- modal-content -->
            </div> <!-- modal-dialog -->
        </div> <!-- modal -->

        <div class="modal fade" id="usrPermissionModal" role="dialog" aria-labelledby="usrPermissionLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="usrPermissionLabel"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle" aria-hidden="true"></i></span></button>
                    </div> <!-- modal-header -->
                    <div class="modal-body" id="usrPermissionBody">
                    </div> <!-- modal-body -->
                    <div class="modal-footer" id="usrPermissionFooter">
                    </div>
                </div> <!-- modal-content -->
            </div> <!-- modal-dialog -->
        </div> <!-- modal -->

        <?php FormElements::scriptRefs(); ?>
        <script type="text/javascript">
        	/* global $ */
        	function load_contents() {
            	$.post(
                	"/mbx/ctrl/adm_usr_account.php",
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
                load_contents();
            });

            function goto_page(ch_obj_id, page_no) {
                // alert('cch_obj_id=' + ch_obj_id + ' / page_no = ' + page_no);
                $.post(
                    "/mbx/ctrl/adm_usr_account.php",
                    {
                        ch_id: ch_obj_id,
                        pg_no: page_no
                    },
                    function(data, status) {
                        $('#mth_result').html(data);
                    }
                );
            }
        </script>
        <script type="text/javascript">
        	/* global $ */
        	$('#usrPermissionModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var usr_id = button.data('usrid');
                var curr_usr_id = button.data('currid');
                var usr_name = button.data('usrname');
                var usr_permits = button.data('permits');
                var modal = $(this);
      			modal.find('.modal-title').text('Berechtigungen: Benutzerkonto ' + usr_name);  

      			if (usr_permits == 'NONE') {
          			$('#usrPermissionBody').html(
          	      		'<div class="card"><div class="card-header">Berechtigungen ausw&auml;hlen</div><div class="card-body"> ' +
          	      		'<div class="form-check">' + 
          	      		'<input class="form-check-input" type="checkbox" id="perm_add_client" name="perm[]" value="+C">' +
          	      		'<label class="form-check-label" for="perm_add_client"><span><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<i class="fa fa-user-o" aria-hidden="true"></i>&nbsp;&nbsp;Herunterladen von Methoden</span></label></div>' +
          	      		'<div class="form-check">' + 
          	      		'<input class="form-check-input" type="checkbox" id="perm_add_upload" name="perm[]" value="+U">' +
          	      		'<label class="form-check-label" for="perm_add_upload"><span><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<i class="fa fa-cloud-upload" aria-hidden="true"></i>&nbsp;&nbsp;Hochladen von Methoden</span></label></div>' +
          	      		'<div class="form-check">' + 
          	      		'<input class="form-check-input" type="checkbox" id="perm_add_admin" name="perm[]" value="+A">' +
          	      		'<label class="form-check-label" for="perm_add_admin"><span><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<i class="fa fa-cog" aria-hidden="true"></i>&nbsp;&nbsp;Administration</span></label></div>' +          	      		
          	      		'</div></div>' );
      			}
      			if (usr_permits == 'CLIENT') {
          			$('#usrPermissionBody').html(
          	      		'<div class="card"><div class="card-header">Berechtigungen ausw&auml;hlen</div><div class="card-body"> ' +
          	      		'<div class="form-check">' + 
          	      		'<input class="form-check-input" type="checkbox" id="perm_add_upload" name="perm[]" value="+U">' +
          	      		'<label class="form-check-label" for="perm_add_upload"><span><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<i class="fa fa-cloud-upload" aria-hidden="true"></i>&nbsp;&nbsp;Hochladen von Methoden</span></label></div>' +
          	      		'<div class="form-check">' + 
          	      		'<input class="form-check-input" type="checkbox" id="perm_add_admin" name="perm[]" value="+A">' +
          	      		'<label class="form-check-label" for="perm_add_admin"><span><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<i class="fa fa-cog" aria-hidden="true"></i>&nbsp;&nbsp;Administration</span></label></div>' +          	      		
          	      		'</div></div>' );
      			}
        	});

        	$('#usrPermissionModal').on('hidden.bs.modal', function(event) {
            	load_contents();
        	});
        </script>
        <script type="text/javascript">
            /* global $ */
            $('#usrDeleteModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var usr_id = button.data('usrid');
                var curr_usr_id = button.data('currid');
                var usr_name = button.data('usrname');
                var modal = $(this);
                // modal.find('.modal-title').text('Benutzerkonto löschen');
                
                $('#usrDeleteMessage').html(
					'<div class="alert alert-warning" role="alert">' +
					'<h5>Sind Sie sicher, dass Sie das Benutzerkonto f&uuml;r "' + usr_name + '" l&ouml;schen wollen? ' +
					'Die Aktion kann nicht r&uuml;ckg&auml;ngig gemacht werden!</h5></div>');
                
                modal.find('.modal-footer').html(
                        '<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Nein, Schlie&szlig;en</button>' +
                        '<button type="button" class="btn btn-primary btn-sm" onclick="usrAccountDelete(' + usr_id + ')">Ja, Benutzerkonto L&ouml;schen</button>' );
                // $('#mth_upload_success').html('<div></div>');
            }); 

            $('#usrDeleteModal').on('hidden.bs.modal', function(event) {
                load_contents();
            });
			
			function usrAccountDelete(usr_id) {
				$.post(
					"/mbx/ctrl/adm_usr_action.php",
					{
						usr_id: usr_id,
						adm_action: 'USR_DEL'
					},
					function(data,status) {
						$('#usrDeleteMessage').html(data);
					}
				);
				$('#usrDeleteFooter').html(	
	                '<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Schlie&szlig;en</button>');
			}
        </script>
        <script type="text/javascript">
			/* global $ */
			$('#usrModifyModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var usr_id = button.data('usrid');
                var curr_usr_id = button.data('currid');
                var usr_name = button.data('usrname');
                var usr_action = button.data('fn');
                var modal = $(this);
                var act_text = 'sperren';

                if (usr_action == 'USR_LCK') {
                	modal.find('.modal-title').text('Benutzerkonto sperren');
                }
                else {
                    modal.find('.modal-title').text('Benutzerkonto entsperren');
                    act_text = 'entsperren';
                }
                
                $('#usrModifyMessage').html(
					'<div class="alert alert-warning" role="alert">' +
					'<h5>Sind Sie sicher, dass Sie das Benutzerkonto f&uuml;r "' + usr_name + '" ' + act_text + ' wollen? ' +
					'</h5></div>');
                
                modal.find('.modal-footer').html(
                        '<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Nein, Schlie&szlig;en</button>' +
                        '<button type="button" class="btn btn-primary btn-sm" onclick="usrAccountModify(' + usr_id + ',&quot;' + usr_action + '&quot;)">Ja, Benutzerkonto ' + 
                        act_text + '</button>' );
			});

			$('#usrModifyModal').on('hidden.bs.modal', function(event) {
				load_contents();
			});

			function usrAccountModify(usr_id, usr_action) {
				$.post(
					"/mbx/ctrl/adm_usr_action.php",
					{
						usr_id: usr_id,
						adm_action: usr_action
					},
					function(data,status) {
						$('#usrModifyMessage').html(data);
					}
				);
				$('#usrModifyFooter').html(
	                '<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Schlie&szlig;en</button>');
			}
        </script>
     </body>
 </html>   
