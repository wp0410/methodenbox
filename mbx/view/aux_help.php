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

if (! empty($_SESSION) && ! empty($_SESSION['user']))
{
    $res = $usr_session->validateSession($_SESSION['user']);
    if ($res->isOK())
    {
        $_SESSION['user'] = $usr_session->getSessionDescriptor();
    }
}
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <title><?php echo GlobalParameter::$applicationConfig['applicationTitle'] . ' - Hilfe';?></title>
        <?php FormElements::styleSheetRefs(); ?>
        <link rel="stylesheet" href="/mbx/css/sidebar-main.css">
        <link rel="stylesheet" href="/mbx/css/sidebar-themes.css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
		<link rel="stylesheet" href="//malihu.github.io/custom-scrollbar/jquery.mCustomScrollbar.min.css">
   </head>
    <body>
        <?php FormElements::topNavBar('AUX.HLP', $usr_session); ?>
		
		<div class="page-wrapper light-theme toggled">
			<a id="show-sidebar" class="btn btn-sm btn-dark" href="#"><i class="fas fa-bars" aria-hidden="true"></i></a>
			<nav id="sidebar" class="sidebar-wrapper">
				<div class="sidebar-content">
					<div class="sidebar-header">
						<h3 class="alert alert-primary">Methodenbox Hilfe</h3>
					</div> <!-- sidebar-header -->

					<div class="sidebar-menu">
						<ul>
							<li class="header-menu">
								<span><h3>Inhalt</h3></span>
							</li>

							<li class="sidebar-dropdown">
								<a href="#"><span><h5>Allgemeine Informationen</h5></span></a>
								<div class="sidebar-submenu">
									<ul>
										<li><a href="#" onclick="javascript:hlpTopic('ht_10.00');">Einf&uuml;hrung</a></li>
										<li><a href="#" onclick="javascript:hlpTopic('ht_20.00');">Rechtliche Hinweise</a></li>
									</ul>
								</div> <!-- sidebar-menu -->
							</li>
							
							<li class="sidebar-dropdown">
								<a href="#"><span><h5>Benutzerkonto</h5></span></a>
								<div class="sidebar-submenu">
									<ul>
										<li><a href="#" onclick="javascript:hlpTopic('ht_30.00');">Registrierung</a></li>
										<li><a href="#" onclick="javascript:hlpTopic('ht_30.10');">Registrierung abschlie&szlig;en</a></li>
										<li><a href="#" onclick="javascript:hlpTopic('ht_30.20');">Anmeldung</a></li>
										<li><a href="#" onclick="javascript:hlpTopic('ht_30.30');">Einstellungen</a></li>
										<li><a href="#" onclick="javascript:hlpTopic('ht_30.40');">Abmeldung</a></li>
									</ul>
								</div> <!-- sidebar-submenu -->
							</li>

							<li class="sidebar-dropdown">
								<a href="#"><span><h5>Unterrichtsmethode</h5></span></a>
								<div class="sidebar-submenu">
									<ul>
										<li><a href="#" onclick="javascript:hlpTopic('ht_40.00');">Methoden suchen</a></li>
										<li><a href="#" onclick="javascript:hlpTopic('ht_40.10');">Methode anlegen</a></li>
										<li><a href="#" onclick="javascript:hlpTopic('ht_40.20');">Methode bewerten</a></li>
										<li><a href="#" onclick="javascript:hlpTopic('ht_40.30');">Methoden herunterladen</a></li>
									</ul>
								</div> <!-- sidebar-submenu -->
							</li>

							<!-- template 
							<li class="sidebar-dropdown">
								<a href="#"><span><h5></h5></span></a>
								<div class="sidebar-submenu">
									<ul>
										<li><a href="#"></a></li>
										<li><a href="#"></a></li>
										<li><a href="#"></a></li>
										<li><a href="#"></a></li>
										<li><a href="#"></a></li>
									</ul>
								</div> 
							</li>
							-->
						</ul>
					</div> <!-- sidebar-menu -->
					
				</div> <!-- sidebar-content -->
			</nav>

			<main class="page-content">
				<div id="overlay" class="overlay"></div>
				<div class="container-fluid">
					<div id="help_contents"></div>
				</div>
			</main>
		</div>

        <?php FormElements::scriptRefs(); ?>
		<script type="text/javascript" src="/mbx/js/sidebar-main.js"></script>
		<script type="text/javascript">
			function hlpTopic(topic) {
				$.post(
					"/mbx/ctrl/aux_help_topic.php",
					{
						hlp_topic: topic
					},
					function(data, status) {
						$('#help_contents').html(data);
					}
				);
			}
		</script>
    </body>
 </html>   
