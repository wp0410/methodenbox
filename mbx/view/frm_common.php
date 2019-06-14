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
        // Bootswatch themes including bootstrap: default theme is "lumen"
        // echo '<link rel="stylesheet" href="/mbx/css/flatly.bootstrap.min.css">';
        // echo '<link rel="stylesheet" href="/mbx/css/lumen.bootstrap.min.css">';
        // echo '<link rel="stylesheet" href="/mbx/css/materia.bootstrap.min.css">';
        // echo '<link rel="stylesheet" href="/mbx/css/pulse.bootstrap.min.css">';
        // echo '<link rel="stylesheet" href="/mbx/css/sandstone.bootstrap.min.css">';
        // echo '<link rel="stylesheet" href="/mbx/css/simplex.bootstrap.min.css">';
        // echo '<link rel="stylesheet" href="/mbx/css/slate.bootstrap.min.css">';
        // echo '<link rel="stylesheet" href="/mbx/css/solar.bootstrap.min.css">';
        // echo '<link rel="stylesheet" href="/mbx/css/superhero.bootstrap.min.css">';
        // echo '<link rel="stylesheet" href="/mbx/css/united.bootstrap.min.css">';
        // echo '<link rel="stylesheet" href="/mbx/css/yeti.bootstrap.min.css">';
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
        // echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>';
		echo '<script src="/mbx/js/parsley.min.js"></script>';
    }
    
    public static function topNavigationBar($current_form_id, $usr_authenticated, $usr_permissions = '')
    {
        FormElements::persTopNavigationBar($current_form_id, $usr_authenticated, '', $usr_permissions);    
    }
    
    public static function persTopNavigationBar($current_form_id, $usr_authenticated, $usr_name, $usr_permissions = '')
    {
        // Sub Menu Configuration: client user is authenticated
        $sub_menu_auth_1 = array(
            'ADM.USR'  => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>2, 'MTH.RATE'=>1, 'MTH.ADM'=>2, 'USR.REG'=>0, 'USR.CONF'=>0, 'USR.IN'=>0, 'USR.OUT'=>1, 'USR.OPT'=>1, 'ADM.USR'=>0 ),
            'AUX.ERR'  => array( 'MTH.SRCH'=>0, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>0, 'USR.CONF'=>0, 'USR.IN'=>0, 'USR.OUT'=>0, 'USR.OPT'=>0, 'ADM.USR'=>0 ),
            'AUX.HLP'  => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>2, 'MTH.RATE'=>1, 'MTH.ADM'=>2, 'USR.REG'=>0, 'USR.CONF'=>0, 'USR.IN'=>0, 'USR.OUT'=>1, 'USR.OPT'=>1, 'ADM.USR'=>2 ),
            'MTH.SRCH' => array( 'MTH.SRCH'=>0, 'MTH.NEW'=>2, 'MTH.RATE'=>1, 'MTH.ADM'=>2, 'USR.REG'=>0, 'USR.CONF'=>0, 'USR.IN'=>0, 'USR.OUT'=>1, 'USR.OPT'=>1, 'ADM.USR'=>2 ),
            'MTH.NEW'  => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>0, 'MTH.RATE'=>1, 'MTH.ADM'=>2, 'USR.REG'=>0, 'USR.CONF'=>0, 'USR.IN'=>0, 'USR.OUT'=>1, 'USR.OPT'=>1, 'ADM.USR'=>2 ),
            'MTH.RATE' => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>2, 'MTH.RATE'=>0, 'MTH.ADM'=>2, 'USR.REG'=>0, 'USR.CONF'=>0, 'USR.IN'=>0, 'USR.OUT'=>1, 'USR.OPT'=>1, 'ADM.USR'=>2 ),
            'MTH.ADM'  => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>2, 'MTH.RATE'=>1, 'MTH.ADM'=>0, 'USR.REG'=>0, 'USR.CONF'=>0, 'USR.IN'=>0, 'USR.OUT'=>1, 'USR.OPT'=>1, 'ADM.USR'=>2 ),
            'USR.REG'  => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>0, 'USR.CONF'=>1, 'USR.IN'=>1, 'USR.OUT'=>0, 'USR.OPT'=>0, 'ADM.USR'=>0 ),
            'USR.CONF' => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>1, 'USR.CONF'=>0, 'USR.IN'=>1, 'USR.OUT'=>0, 'USR.OPT'=>0, 'ADM.USR'=>0 ),
            'USR.IN'   => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>1, 'USR.CONF'=>1, 'USR.IN'=>0, 'USR.OUT'=>0, 'USR.OPT'=>0, 'ADM.USR'=>0 ),
            'USR.OPT'  => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>1, 'MTH.RATE'=>1, 'MTH.ADM'=>1, 'USR.REG'=>0, 'USR.CONF'=>0, 'USR.IN'=>0, 'USR.OUT'=>1, 'USR.OPT'=>0, 'ADM.USR'=>2 ),
            'USR.OUT'  => array()
        );
            
        // Sub Menu Configuration: client user is NOT authenticated
        $sub_menu_auth_0 = array(
            'ADM.USR'  => array( 'MTH.SRCH'=>0, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>1, 'USR.CONF'=>0, 'USR.IN'=>1, 'USR.OUT'=>0, 'USR.OPT'=>0, 'ADM.USR'=>0 ),
            'AUX.ERR'  => array( 'MTH.SRCH'=>0, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>0, 'USR.CONF'=>0, 'USR.IN'=>0, 'USR.OUT'=>0, 'USR.OPT'=>0, 'ADM.USR'=>0 ),
            'AUX.HLP'  => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>1, 'USR.CONF'=>1, 'USR.IN'=>1, 'USR.OUT'=>0, 'USR.OPT'=>0, 'ADM.USR'=>0 ),
            'MTH.SRCH' => array( 'MTH.SRCH'=>0, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>1, 'USR.CONF'=>1, 'USR.IN'=>1, 'USR.OUT'=>0, 'USR.OPT'=>0, 'ADM.USR'=>0 ),
            'MTH.NEW'  => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>0, 'USR.CONF'=>0, 'USR.IN'=>0, 'USR.OUT'=>0, 'USR.OPT'=>0, 'ADM.USR'=>0 ),
            'MTH.RATE' => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>0, 'USR.CONF'=>0, 'USR.IN'=>0, 'USR.OUT'=>0, 'USR.OPT'=>0, 'ADM.USR'=>0 ),
            'MTH.ADM'  => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>0, 'USR.CONF'=>0, 'USR.IN'=>0, 'USR.OUT'=>0, 'USR.OPT'=>0, 'ADM.USR'=>0 ),
            'USR.REG'  => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>0, 'USR.CONF'=>1, 'USR.IN'=>1, 'USR.OUT'=>0, 'USR.OPT'=>0, 'ADM.USR'=>0 ),
            'USR.CONF' => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>1, 'USR.CONF'=>0, 'USR.IN'=>1, 'USR.OUT'=>0, 'USR.OPT'=>0, 'ADM.USR'=>0 ),
            'USR.IN'   => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>1, 'USR.CONF'=>1, 'USR.IN'=>0, 'USR.OUT'=>0, 'USR.OPT'=>0, 'ADM.USR'=>0 ),
            'USR.OPT'  => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>1, 'USR.CONF'=>1, 'USR.IN'=>0, 'USR.OUT'=>0, 'USR.OPT'=>0, 'ADM.USR'=>0 ),
            'USR.PWD'  => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>1, 'USR.CONF'=>1, 'USR.IN'=>0, 'USR.OUT'=>0, 'USR.OPT'=>0, 'ADM.USR'=>0 ),
            'USR.OUT'  => array()
        );
            
        $sub_menu_config = array(
            'MTH.SRCH' => array('TEXT' => 'Suchen', 'LINK' => '../view/mth_search_pg.php'), 
            'MTH.NEW'  => array('TEXT' => 'Anlegen', 'LINK' => '../view/mth_upload.php'), 
            'MTH.RATE' => array('TEXT' => 'Bewerten', 'LINK' => '../view/mth_rating_pg.php'), 
            'MTH.ADM'  => array('TEXT' => 'Verwalten', 'LINK' => '../view/mth_admin_pg.php'), 
            'USR.REG'  => array('TEXT' => 'Registrieren', 'LINK' => '../view/usr_register.php'), 
            'USR.CONF' => array('TEXT' => 'Registrierung Best&auml;tigen', 'LINK' => '../view/usr_confirm.php'), 
            'USR.IN'   => array('TEXT' => 'Anmelden', 'LINK' => '../view/usr_login.php'), 
            'USR.OPT'  => array('TEXT' => 'Einstellungen', 'LINK' => '../view/usr_settings.php'),
            'USR.OUT'  => array('TEXT' => 'Abmelden', 'LINK' => '../ctrl/usr_logout.php'),
            'AUX.HLP'  => array('TEXT' => 'Hilfe', 'LINK' => '../view/aux_help.php'),
            'ADM.USR'  => array('TEXT' => 'Benutzerverwaltung', 'LINK' => '../view/adm_usr_account.php')
        );

        if ($current_form_id == 'AUX.HLP')
        {
            echo '<nav class="navbar fixed-top navbar-expand-lg navbar-primary bg-light">';
        }
        else 
        {
            echo '<nav class="navbar navbar-expand-lg navbar-primary bg-light">';
        }
        if ($current_form_id == 'MTH.SRCH')
        {
            echo '<a class="navbar-brand" href="#">' . GlobalParameter::$applicationConfig['applicationTitle'] . '</a>';
        }
        else
        {
            echo '<a class="navbar-brand" href="' . $sub_menu_config['MTH.SRCH']['LINK'] . '">' . GlobalParameter::$applicationConfig['applicationTitle'] . '</a>';
        }
        echo '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#topNavbar" aria-controls="topNavbar" aria-expanded="false" aria-label="Toggle navigation">';
        echo '<span class="navbar-toggler-icon"></span></button>';
        
        echo '<div class="collapse navbar-collapse" id="topNavbar">';
        echo '<ul class="navbar-nav mr-auto">';
        
        // Menu Entry: Unterrichtsmethode
        echo '<li class="nav-item dropdown">';
        echo '<a class="nav-link dropdown-toggle" href="#" id="navbarMethodeLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Unterrichtsmethode</a>';
        echo '<div class="dropdown-menu" aria-labelledby="navbarMethodeLink">';
        $sub_items = array('MTH.SRCH', 'MTH.NEW', 'MTH.RATE', 'MTH.ADM');
        foreach($sub_items as $sub_item)
        {
            if ($usr_authenticated)
            {
                $link_id = $sub_menu_auth_1[$current_form_id][$sub_item];
                $link_type = '';
            }
            else
            {
                $link_id = $sub_menu_auth_0[$current_form_id][$sub_item];
                $link_type = '';
            }
			
			switch($link_id)
			{
				case 0:
					$link_ref = '#';
					$link_type = ' disabled';
					break;
				case 2:
					if (strpos($usr_permissions, $sub_item) === false)
					{
						$link_ref = '#';
						$link_type = ' disabled';
					}
					else
					{
						$link_ref = $sub_menu_config[$sub_item]['LINK'];
 					}
					break;
				default:
					$link_ref = $sub_menu_config[$sub_item]['LINK'];
					break;
			}
			
            echo '<a class="dropdown-item' . $link_type . '" href="' . $link_ref . '">' . $sub_menu_config[$sub_item]['TEXT'] . '</a>';
        }
        echo '</div></li>';
        
        // Menu Entry: Benutzer
        echo '<li class="nav-item dropdown">';
        echo '<a class="nav-link dropdown-toggle" href="#" id="navbarBenutzerLink" role="button" data-toggle="dropdown" aria-haspopup="true" area-expanded="false">';
        if (empty($usr_name))
        {
            echo 'Benutzer';
        }
        else 
        {
            echo 'Benutzer (' . $usr_name . ')';
        }
        echo '</a>';
        echo '<div class="dropdown-menu" aria-labelledby="navbarBenutzerLink">';
        $sub_items = array('USR.REG', 'USR.OPT', 'USR.CONF', 'USR.IN', 'USR.OUT');
        foreach($sub_items as $sub_item)
        {
            if ($usr_authenticated)
            {
                $link_id = $sub_menu_auth_1[$current_form_id][$sub_item];
                $link_type = '';
            }
            else
            {
                $link_id = $sub_menu_auth_0[$current_form_id][$sub_item];
                $link_type = '';
            }
            if ($link_id == 0)
            {
                $link_ref = '#';
                $link_type = ' disabled';
            }
            else
            {
                $link_ref = $sub_menu_config[$sub_item]['LINK'];
            }
            echo '<a class="dropdown-item' . $link_type . '" href="' . $link_ref . '">' . $sub_menu_config[$sub_item]['TEXT'] . '</a>';
        }
        echo '</div></li>';
        
        if ( !(strpos($usr_permissions, ':ADM.') === false))
        {
            echo '<li class="nav-item dropdown">';
            echo '<a class="nav-link dropdown-toggle" href="#" id="navbarAdminLink" role="button" data-toggle="dropdown" aria-haspopup="true" area-expanded="false">Administration</a>';
            echo '<div class="dropdown-menu" aria-labelledby="navbarAdminLink">';
            
            $sub_items = array('ADM.USR');
            
            foreach($sub_items as $sub_item)
            {
                if ($usr_authenticated)
                {
                    $link_id = $sub_menu_auth_1[$current_form_id][$sub_item];
                    $link_type = '';
                }
                else
                {
                    $link_id = $sub_menu_auth_0[$current_form_id][$sub_item];
                    $link_type = '';
                }
                if ($link_id == 0)
                {
                    $link_ref = '#';
                    $link_type = ' disabled';
                }
                else
                {
                    $link_ref = $sub_menu_config[$sub_item]['LINK'];
                }
                echo '<a class="dropdown-item' . $link_type . '" href="' . $link_ref . '">' . $sub_menu_config[$sub_item]['TEXT'] . '</a>';
            }
        
            echo '</div></li>';
        }
        
        echo '</ul>';
        
        // right menu
        echo '<ul class="navbar-nav">';
        echo '<li class="nav-item"><a class="nav-link" href="#">' . GlobalParameter::$applicationConfig['appVersion'] . '</a></li>';
        echo '<li class="nav-item">';
        echo '<a class="nav-link" href="' . $sub_menu_config['AUX.HLP']['LINK'] . '">';
        echo '<span><i class="fa fa-question-circle-o fa-lg" aria-hidden="true"></i>&nbsp;' . $sub_menu_config['AUX.HLP']['TEXT'] . '</span>';
        echo '</a>';
        echo '</li></ul>';
        echo '</div></nav>';
    }
    
    public static function bottomNavigationBar($current_form_id)
    {
        echo '<nav class="navbar fixed-bottom navbar-expand-lg navbar-primary bg-light">';
        echo '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#bottomNavbar" aria-controls="bottomNavbar" aria-expanded="false" aria-label="Toggle navigation">';
        echo '<span class="navbar-toggler-icon"></span></button>';
        echo '<div class="collapse navbar-collapse" id="bottomNavbar">';
        echo '<ul class="navbar-nav">';
        // echo '<li class="nav-item"><a class="nav-link" href="#">Impressum</a></li>';
        // echo '<li class="nav-item"><a class="nav-link" href="#">Kontakt</a></li>';
        // echo '<li class="nav-item"><a class="nav-link" href="#">AGB</a></li>';
        echo '<li class="nav-item"><button class="btn btn-link btn-sm" data-toggle="modal" data-target="#ImpressumModal">Impressum</button></li>';
        echo '<li class="nav-item"><botton class="btn btn-link btn-sm" data-toggle="modal" data-target="#KontaktModal">Kontakt</button></a></li>';
        echo '<li class="nav-item"><botton class="btn btn-link btn-sm" data-toggle="modal" data-target="#AGBModal">AGB</button></li>';
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

        // Modal: Kontakt
        echo '<div class="modal fade" id="KontaktModal" tabindex="-1" role="dialog" aria-labelledby="KontaktModalLabel" aria-hidden="true">';
        echo '   <div class="modal-dialog" role="document">';
        echo '      <div class="modal-content">';
        echo '         <div class="modal-header">';
        echo '            <h5 class="modal-title" id="KontaktModalLabel">Kontakt</h5>';
        echo '            <button type="button" class="close" data-dismiss="modal" aria-label="Schlie&szlig;en"><span aria-hidden="true">&times;</span></button>';
        echo '         </div>';
        echo '         <div class="modal-body">Achtung, Baustelle!</div>';
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