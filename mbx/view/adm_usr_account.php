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
        </script>
     </body>
 </html>   
