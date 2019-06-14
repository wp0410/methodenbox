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

?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <title><?php echo GlobalParameter::$applicationConfig['applicationTitle'] . ' - Hilfe';?></title>
        <?php FormElements::styleSheetRefs(); ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">
    </head>
    <body>
        <?php FormElements::persTopNavigationBar('AUX.HLP', $usr_session->isAuthenticated(), $usr_name, $usr_session->getPermissions()); ?>
              
		<div class="wrapper">
			<nav id="sidebar" class="bg-info">
				<div class="sidebar-header bg-primary">
					<h2>Inhaltsverzeichnis</h2>
				</div> <!-- sidebar-header -->
				
				<ul class="list-unstyled components border-bottom border-primary">
					<li><a href="#" onclick="javascript:hlpTopic('ht_10.00');"><h4>Einf&uuml;hrung</h4></a></li>
					<li><a href="#" onclick="javascript:hlpTopic('ht_20.00');"><h4>Rechtliche Hinweise</h4></a></li>
					<li>
						<a href="#accountSubMenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><h4>Benutzerkonto</h4></a>
						<ul class="collapse list-unstyled" id="accountSubMenu">
							<li><a href="#" onclick="javascript:hlpTopic('ht_30.00');"><h5>Registrierung</h5></a></li>
							<li><a href="#" onclick="javascript:hlpTopic('ht_30.10');"><h5>Registrierung abschlie&szlig;en</h5></a></li>
							<li><a href="#" onclick="javascript:hlpTopic('ht_30.20');"><h5>Anmeldung</h5></a></li>
							<li><a href="#" onclick="javascript:hlpTopic('ht_30.30');"><h5>Abmeldung</h5></a></li>
						</ul>
					</li>
					<li>
						<a href="#methodSubMenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><h4>Unterrichtsmethode</h4></a>
						<ul class="collapse list-unstyled" id="methodSubMenu">
							<li><a href="#" onclick="javascript:hlpTopic('ht_40.00');"><h5>Methoden suchen</h5></a></li>
							<li><a href="#" onclick="javascript:hlpTopic('ht_40.10');"><h5>Methode anlegen</h5></a></li>
							<li><a href="#" onclick="javascript:hlpTopic('ht_40.20');"><h5>Methode bewerten</h5></a></li>
							<li><a href="#" onclick="javascript:hlpTopic('ht_40.30');"><h5>Methoden verwalten</h5></a></li>
						</ul>
					</li>
					<li><a href="#" onclick="javascript:hlpTopic('ht_50.00');"><h4>Fragen und Antworten</h4></a></li>
				</ul> <!-- list-unstyled components -->
			</nav> <!-- sidebar -->
		
			<div id="content">

			</div> <!-- content -->

		</div> <!-- wrapper -->

        <?php FormElements::scriptRefs(); ?>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>
		<script type="text/javascript">
			/* global $ */
			$(document).ready(function () {
				$("#sidebar").mCustomScrollbar({
					theme: "minimal"
				});
				
				$('#sidebarCollapse').on('click', function () {
					$('#sidebar').toggleClass('active');
					$('.collapse.in').toggleClass('in');
					$('a[aria-expanded=true]').attr('aria-expanded', 'false');
				});
			});
			
			function hlpTopic(topic) {
				var fName = 'aux_help_' + topic + '.html';
				alert(fName);
				// $('#content').load(fName);
			}
		</script>
    </body>
 </html>   
