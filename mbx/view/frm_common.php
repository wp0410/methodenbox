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

class FormElements
{
    public static function styleSheetRefs()
    {
        if (! empty($_SESSION['skin']))
        {
            echo '<link rel="stylesheet" href="/mbx/css/' . $_SESSION['skin'] . '.bootstrap.min.css">';
        }
        else 
        {
            if (empty(GlobalParameter::$applicationConfig['formSkin']))
            {
                echo '<link rel="stylesheet" href="/mbx/css/lumen.bootstrap.min.css">';
            }
            else
            {
                echo '<link rel="stylesheet" href="/mbx/css/' . GlobalParameter::$applicationConfig['formSkin'] . '.bootstrap.min.css">';
            }
        }
        echo '<link rel="stylesheet" href="/mbx/font-awesome-4.7.0/css/font-awesome.min.css">';
		echo '<link rel="stylesheet" href="/mbx/css/parsley.css">';
		echo '<link rel="stylesheet" href="/mbx/css/mbx.css">';
    }
    
    public static function scriptRefs()
    {
        echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>';
        echo '<script src="https://unpkg.com/tooltip.js/dist/umd/tooltip.min.js"></script>';
        echo '<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>';
		echo '<script src="/mbx/js/parsley.min.js"></script>';
    }
    
	public static function topNavBar($current_form_id, $usr_session)
	{
		$top_menu = array(
			'MTH_MENU' => array(
			    'EID'  => 'Methode',
				'TEXT' => 'Unterrichtsmethode',
				'PERM' => 'A', // Entry activated/deactivated depending on permission 
				'SUBM' => array(
					array('SUBID' => 'MTH.SRCH', 'TEXT' => 'Suchen', 'LINK' => '../view/mth_search_pg.php'),
					array('SUBID' => 'MTH.NEW',  'TEXT' => 'Anlegen', 'LINK' => '../view/mth_upload.php'), 
					array('SUBID' => 'MTH.RATE', 'TEXT' => 'Bewerten', 'LINK' => '../view/mth_rating_pg.php'), 
					array('SUBID' => 'MTH.ADM',  'TEXT' => 'Verwalten', 'LINK' => '../view/mth_admin_pg.php') 
				)
			),
			'USR_MENU' => array(
			    'EID'  => 'Benutzer',
				'TEXT' => 'Benutzer',
				'PERM' => 'A', // Entry activated/deactivated depending on permission
				'SUBM' => array(
					array('SUBID' => 'USR.REG',  'TEXT' => 'Registrieren', 'LINK' => '../view/usr_register.php'), 
					array('SUBID' => 'USR.CONF', 'TEXT' => 'Registrierung Best&auml;tigen', 'LINK' => '../view/usr_confirm.php'), 
					array('SUBID' => 'USR.IN',   'TEXT' => 'Anmelden', 'LINK' => '../view/usr_login.php'), 
					array('SUBID' => 'USR.OPT',  'TEXT' => 'Einstellungen', 'LINK' => '../view/usr_settings.php'),
					array('SUBID' => 'USR.OUT',  'TEXT' => 'Abmelden', 'LINK' => '../ctrl/usr_logout.php')
				)
			),
			'ADM_MENU' => array(
			    'EID'  => 'Admin',
				'TEXT' => 'Administration',
				'PERM' => 'H',    // Entry activated/hidden depending on permission
				'PTAG' => 'ADM.', // Key for hide decision
				'SUBM' => array(
					array('SUBID' => 'ADM.USR',  'TEXT' => 'Benutzerverwaltung', 'LINK' => '../view/adm_usr_account.php'),
					array('SUBID' => 'ADM.REQ',  'TEXT' => 'Kontaktanfragen', 'LINK' => '../view/adm_aux_contact.php')
				)
			),
			'REP_MENU' => array(
			    'EID'  => 'Statistics',
				'TEXT' => 'Statistik',
				'PERM' => 'A',
				'SUBM' => array(
					array('SUBID' => 'REP.MRNK', 'TEXT' => 'Methoden Ranking', 'LINK' => '../view/rep_mth_ranking.php'),
					array('SUBID' => 'REP.MST',  'TEXT' => 'Methoden Statistiken', 'LINK' => '../view/rep_mth_statistics.php')
				)
			)
		);
		
		// Adjust NAVBAR fixing and BRAND link depending on current form 
		switch($current_form_id)
		{
			case 'AUX.HLP':
				echo '<nav class="navbar fixed-top navbar-expand-lg navbar-primary bg-light">';
				echo '<a class="navbar-brand" href="' . 
					GlobalParameter::$applicationConfig['applicationDefaultLink'] . '">' . 
					GlobalParameter::$applicationConfig['applicationTitle'] . '</a>';
				break;
			case 'MTH.SRCH':
				echo '<nav class="navbar navbar-expand-lg navbar-primary bg-light">';
				echo '<a class="navbar-brand" href="#">' . GlobalParameter::$applicationConfig['applicationTitle'] . '</a>';
				break;
			default:
				echo '<nav class="navbar navbar-expand-lg navbar-primary bg-light">';
				echo '<a class="navbar-brand" href="' . 
					GlobalParameter::$applicationConfig['applicationDefaultLink'] . '">' . 
					GlobalParameter::$applicationConfig['applicationTitle'] . '</a>';
				break;
		}
		
        echo '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#topNavbar" aria-controls="topNavbar" aria-expanded="false" aria-label="Toggle navigation">';
        echo '<span class="navbar-toggler-icon"></span></button>';
        
        echo '<div class="collapse navbar-collapse" id="topNavbar">';
        echo '<ul class="navbar-nav mr-auto">';
        
        // Menu Entry: Unterrichtsmethode
		self::topNavSubMenu($top_menu['MTH_MENU'], $usr_session);
		
		// Menu Entry: Benutzer
		self::topNavSubMenu($top_menu['USR_MENU'], $usr_session);
		
		// Menu Entry: Admin
		self::topNavSubMenu($top_menu['ADM_MENU'], $usr_session);
		
		// Menu Entry: Statistics
		self::topNavSubMenu($top_menu['REP_MENU'], $usr_session);
		
        echo '</ul>';
        
        // Right menu
        echo '<ul class="navbar-nav">';
		
		if ($usr_session->isAuthenticated())
		{
			echo '<li class="nav-item"><a class="nav-link" href="#"><span class="badge badge-success"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;&nbsp;';
			echo $usr_session->ses_usr_full_name . ' (' . $usr_session->ses_usr_email . ')';
		}
		else
		{
			echo '<li class="nav-item"><a class="nav-link" href="#"><span class="badge badge-warning"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;&nbsp;';
			echo 'NICHT ANGEMELDET';
		}
		echo '</span></a></li>';
        echo '<li class="nav-item"><a class="nav-link" href="#">' . GlobalParameter::$applicationConfig['appVersion'] . '</a></li>';
        echo '<li class="nav-item">';
        echo '<a class="nav-link" href="' . GlobalParameter::$applicationConfig['applicationHelpLink'] . '">';
        echo '<span><i class="fa fa-question-circle-o fa-lg" aria-hidden="true"></i>&nbsp;Hilfe</span>';
        echo '</a>';
        echo '</li></ul>';
        echo '</div></nav>';
	}
	
	private static function topNavSubMenu($menu_entry, $usr_session)
	{
		if (($menu_entry['PERM'] == 'H') && ! $usr_session->checkPermission($menu_entry['PTAG']))
		{
			return;
		}

        echo '<li class="nav-item dropdown">';
        echo '<a class="nav-link dropdown-toggle" href="#" id="navbar' . $menu_entry['EID'] . 'Link" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
		echo $menu_entry['TEXT'] . '</a>';
		echo '<div class="dropdown-menu" aria-labelledby="navbar' . $menu_entry['EID'] . 'Link">';

		foreach($menu_entry['SUBM'] as $sub_item)
		{
			if ($usr_session->checkPermission($sub_item['SUBID']))
			{
				$htm_line = '<a class="dropdown-item" href="' . $sub_item['LINK'] . '">' . $sub_item['TEXT'] . '</a>'; 
			}
			else
			{
				$htm_line = '<a class="dropdown-item disabled" href="#">' . $sub_item['TEXT'] . '</a>'; 
			}
			
			echo $htm_line;
		}
        echo '</div></li>';		
	}
    
    public static function bottomNavBar($current_form_id)
    {
        echo '<nav class="navbar fixed-bottom navbar-expand-lg navbar-primary bg-light">';
        echo '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#bottomNavbar" aria-controls="bottomNavbar" aria-expanded="false" aria-label="Toggle navigation">';
        echo '<span class="navbar-toggler-icon"></span></button>';
        echo '<div class="collapse navbar-collapse" id="bottomNavbar">';
        echo '<ul class="navbar-nav">';
        echo '<li class="nav-item"><button class="btn btn-link btn-sm" data-toggle="modal" data-target="#ImpressumModal">Impressum</button></li>';
        echo '<li class="nav-item"><a class="btn btn-link btn-sm" href="../view/aux_contact.php">Kontakt</a></li>';
        echo '<li class="nav-item"><button class="btn btn-link btn-sm" data-toggle="modal" data-target="#AGBModal">AGB</button></li>';
        echo '</ul></div></nav>';
        
        // Modal: Impressum
        echo '<div class="modal fade" id="ImpressumModal" tabindex="-1" role="dialog" aria-labelledby="ImpressumModalLabel" aria-hidden="true">';
        echo '   <div class="modal-dialog" role="document">';
        echo '      <div class="modal-content">';
        echo '         <div class="modal-header">';
        echo '            <h5 class="modal-title" id="ImpressumModalLabel">Impressum</h5>';
        echo '            <button type="button" class="close" data-dismiss="modal" aria-label="Schlie&szlig;en"><span aria-hidden="true">&times;</span></button>';
        echo '         </div>';
        echo '         <div class="modal-body">Hier sollte der Text f&uuml;r das Impressum stehen ...</div>';
        echo '         <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Schlie&szlig;en</button></div>';
        echo '      </div>';
        echo '   </div>';
        echo '</div>';

        // Modal: AGB
        echo '<div class="modal fade" id="AGBModal" tabindex="-1" role="dialog" aria-labelledby="AGBModalLabel" aria-hidden="true">';
        echo '   <div class="modal-dialog" role="document">';
        echo '      <div class="modal-content">';
        echo '         <div class="modal-header">';
        echo '            <h5 class="modal-title" id="ImpressumModalLabel">Allgemeine Gesch&auml;ftsbedingungen</h5>';
        echo '            <button type="button" class="close" data-dismiss="modal" aria-label="Schlie&szlig;en"><span aria-hidden="true">&times;</span></button>';
        echo '         </div>';
        echo '         <div class="modal-body">Hier sollte der Text f&uuml;r die Allgemeinen Gesch&auml;ftsbedingungen stehen ...</div>';
        echo '         <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Schlie&szlig;en</button></div>';
        echo '      </div>';
        echo '   </div>';
        echo '</div>';
    }
    
    public static function feedbackModal($app_result, $close_btn_label, $alt_btn = null)
    {
        echo '<div class="modal fade" id="FeedbackMd" name="FeedbackMd" aria-labelled-by="FeedbackMdTitle" aria-hidden="true">';
        echo '   <div class="modal-dialog modal-dialog-centered" role="document">';
        echo '      <div class="modal-content">';
        echo '         <div class="modal-header">';
        echo '            <h5 class="modal-title" id="FeedbackMdTitle"><span><i class="fa fa-exclamation" aria-hidden="true"></i>&nbsp;&nbsp;';
        if ($app_result->style() == 'danger')
        {
            echo 'Fehler';
        }
        else 
        {
            echo 'Hinweis';
        }
        echo '            </span></h5>';
        echo '            <button type="button" class="close" data-dismiss="modal" aria-label="Schlie&szlig;en"><span aria-hidden="true">&times;</span></button>';
        echo '         </div>';
        echo '         <div class="modal-body">';
        echo '            <div class = "alert alert-' . $app_result->style() . '" role="alert"><h5>' . $app_result->text . '</h5></div>';
        echo           '</div>';
        echo '         <div class="modal-footer">';
        echo '            <button type="button" class="btn btn-light" data-dismiss="modal">' . $close_btn_label . '</button>';
        if (! empty($alt_btn) && ! empty($alt_btn['LABEL']) && ! empty($alt_btn['LINK']))
        {
            echo '        <a class="btn btn-primary" href="' . $alt_btn['LINK'] . '" role="button">' . $alt_btn['LABEL'] . '</a>';
        }
        echo '         </div>';
        echo '      </div>';
        echo '   </div>';
        echo '</div>';
    }
    
    public static function launchFeedback()
    {
        echo '<script type="text/javascript"> /* global $ */';
        echo '$(window).on(\'load\',function(){ $(\'#FeedbackMd\').modal(\'show\'); });';
        echo '</script>';
    }
    
    public static function showAlert($alert_type, $col_format, $alert_text, $col_intend = null)
    {
        echo '<div class="row row-fluid">';
        if (($col_intend != null) && (strlen($col_intend) > 0))
        {
            echo '<div class="' . $col_intend . '"></div>';
        }
        echo '<div class="' . $col_format . '">';
        echo '<div id="global_alert" class="alert alert-' . $alert_type . '" role="alert"><center><h5>' . $alert_text . '</h4></center></div></div></div>';
    }
}

?>