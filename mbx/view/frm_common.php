<?php
//---------------------------------------------------------------------------------------
//  Copyright (c) 2018 Walter Pachlinger (walter.pachlinger@gmx.at)
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
        /*
        echo 
            '<link ' .
                'rel="stylesheet" ' . 
                'href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" ' . 
                'integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">';
        */
        // echo '<link rel="stylesheet" href="/mbx/css/superhero.bootstrap.min.css">';
        // echo '<link rel="stylesheet" href="/mbx/css/materia.bootstrap.min.css">';
        // echo '<link rel="stylesheet" href="/mbx/css/united.bootstrap.min.css">';
        echo '<link rel="stylesheet" href="/mbx/css/lumen.bootstrap.min.css">';
        // echo '<link rel="stylesheet" href="/mbx/css/flatly.bootstrap.min.css">';
        echo '<link rel="stylesheet" href="/mbx/css/mbx.css">';
        echo '<link rel="stylesheet" href="/mbx/font-awesome-4.7.0/css/font-awesome.min.css">';
    }
    
    public static function scriptRefs()
    {
        echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>';
        echo '<script src="https://unpkg.com/tooltip.js/dist/umd/tooltip.min.js"></script>';
        echo '<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>';
    }
    
    public static function topNavigationBar($current_form_id, $usr_authenticated, $usr_role)
    {
        // Sub Menu Configuration: client user is authenticated
        $sub_menu_auth_1 = array(
            'AUX.ERR'  => array( 'MTH.SRCH'=>0, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>0, 'USR.CONF'=>0, 'USR.IN'=>0, 'USR.OUT'=>0 ),
            'AUX.HLP'  => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>1, 'MTH.RATE'=>1, 'MTH.ADM'=>1, 'USR.REG'=>0, 'USR.CONF'=>0, 'USR.IN'=>0, 'USR.OUT'=>1 ),
            'MTH.SRCH' => array( 'MTH.SRCH'=>0, 'MTH.NEW'=>1, 'MTH.RATE'=>1, 'MTH.ADM'=>1, 'USR.REG'=>0, 'USR.CONF'=>0, 'USR.IN'=>0, 'USR.OUT'=>1 ),
            'MTH.NEW'  => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>0, 'MTH.RATE'=>1, 'MTH.ADM'=>1, 'USR.REG'=>0, 'USR.CONF'=>0, 'USR.IN'=>0, 'USR.OUT'=>1 ),
            'MTH.RATE' => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>1, 'MTH.RATE'=>0, 'MTH.ADM'=>1, 'USR.REG'=>0, 'USR.CONF'=>0, 'USR.IN'=>0, 'USR.OUT'=>1 ),
            'MTH.ADM'  => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>1, 'MTH.RATE'=>1, 'MTH.ADM'=>0, 'USR.REG'=>0, 'USR.CONF'=>0, 'USR.IN'=>0, 'USR.OUT'=>1 ),
            'USR.REG'  => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>0, 'USR.CONF'=>1, 'USR.IN'=>1, 'USR.OUT'=>0 ),
            'USR.CONF' => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>1, 'USR.CONF'=>0, 'USR.IN'=>1, 'USR.OUT'=>0 ),
            'USR.IN'   => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>1, 'USR.CONF'=>1, 'USR.IN'=>0, 'USR.OUT'=>0 ),
            'USR.OUT'  => array()
        );
            
        // Sub Menu Configuration: client user is NOT authenticated
        $sub_menu_auth_0 = array(
            'AUX.ERR'  => array( 'MTH.SRCH'=>0, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>0, 'USR.CONF'=>0, 'USR.IN'=>0, 'USR.OUT'=>0 ),
            'AUX.HLP'  => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>1, 'USR.CONF'=>1, 'USR.IN'=>1, 'USR.OUT'=>0 ),
            'MTH.SRCH' => array( 'MTH.SRCH'=>0, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>1, 'USR.CONF'=>1, 'USR.IN'=>1, 'USR.OUT'=>0 ),
            'MTH.NEW'  => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>0, 'USR.CONF'=>0, 'USR.IN'=>0, 'USR.OUT'=>0 ),
            'MTH.RATE' => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>0, 'USR.CONF'=>0, 'USR.IN'=>0, 'USR.OUT'=>0 ),
            'MTH.ADM'  => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>0, 'USR.CONF'=>0, 'USR.IN'=>0, 'USR.OUT'=>0 ),
            'USR.REG'  => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>0, 'USR.CONF'=>1, 'USR.IN'=>1, 'USR.OUT'=>0 ),
            'USR.CONF' => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>1, 'USR.CONF'=>0, 'USR.IN'=>1, 'USR.OUT'=>0 ),
            'USR.IN'   => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>1, 'USR.CONF'=>1, 'USR.IN'=>0, 'USR.OUT'=>0 ),
            'USR.PWD'  => array( 'MTH.SRCH'=>1, 'MTH.NEW'=>0, 'MTH.RATE'=>0, 'MTH.ADM'=>0, 'USR.REG'=>1, 'USR.CONF'=>1, 'USR.IN'=>0, 'USR.OUT'=>0 ),
            'USR.OUT'  => array()
        );
            
        $sub_menu_config = array(
            'MTH.SRCH' => array('TEXT' => 'Suchen', 'LINK' => '../view/mth_search.php'), 
            'MTH.NEW'  => array('TEXT' => 'Anlegen', 'LINK' => '../view/mth_upload.php'), 
            'MTH.RATE' => array('TEXT' => 'Bewerten', 'LINK' => '../view/mth_rating.php'), 
            'MTH.ADM'  => array('TEXT' => 'Verwalten', 'LINK' => '../view/mth_admin.php'), 
            'USR.REG'  => array('TEXT' => 'Registrieren', 'LINK' => '../view/usr_register.php'), 
            'USR.CONF' => array('TEXT' => 'Registrierung Best&auml;tigen', 'LINK' => '../view/usr_confirm.php'), 
            'USR.IN'   => array('TEXT' => 'Anmelden', 'LINK' => '../view/usr_login.php'), 
            'USR.OUT'  => array('TEXT' => 'Abmelden', 'LINK' => '../ctrl/usr_logout.php'),
            'AUX.HLP'  => array('TEXT' => 'Hilfe', 'LINK' => '../view/aux_help.php')
        );

        echo '<nav class="navbar navbar-expand-lg navbar-primary bg-light">';
        echo '<a class="navbar-brand" href="#">' . GlobalParameter::$applicationConfig['applicationTitle'] . '</a>';
        echo '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#topNavbar" aria-controls="topNavbar" aria-expanded="false" aria-label="Toggle navigation">';
        echo '<span class="navbar-toggler-icon"></span></button>';
        
        echo '<div class="collapse navbar-collapse" id="topNavbar">';
        echo '<ul class="navbar-nav mr-auto">';
        echo '<li class="nav-item dropdown">';

        // Menu Entry: Unterrichtsmethode
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
        
        echo '<li class="nav-item dropdown">';
        // Menu Entry: Benutzer
        echo '<a class="nav-link dropdown-toggle" href="#" id="navbarBenutzerLink" role="button" data-toggle="dropdown" aria-haspopup="true" area-expanded="false">Benutzer</a>';
        echo '<div class="dropdown-menu" aria-labelledby="navbarBenutzerLink">';
        $sub_items = array('USR.REG', 'USR.CONF', 'USR.IN', 'USR.OUT');
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
        echo '</div></li></ul>';
        
        // Rechtes Menü
        echo '<ul class="navbar-nav">';
        echo '<li class="nav-item"><a class="nav-link" href="#">' . GlobalParameter::$applicationConfig['appVersion'] . '</a></li>';
        echo '<li class="nav-item">';
        echo '<a class="nav-link" href="' . $sub_menu_config['AUX.HLP']['LINK'] . '">';
        echo '<span><i class="fa fa-question-circle-o fa-lg" aria-hidden="true"></i>&nbsp;' . $sub_menu_config['AUX.HLP']['TEXT'] . '</span>';
        echo '</a>';
        echo '</li></ul>';
        echo '</div></nav>';
    }
    
    public static function bottomNavigationBar($current_form_id, $usr_authenticated, $usr_role)
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
    
    public static function showAlert($alert_type, $col_format, $alert_text, $col_intend = null)
    {
        echo '<div class="row row-fluid">';
        if (($col_intend != null) && (strlen($col_intend) > 0))
        {
            echo '<div class="' . $col_intend . '"></div>';
        }
        echo '<div class="' . $col_format . '">';
        echo '<div class="alert alert-' . $alert_type . '" role="alert"><center><h5>' . $alert_text . '</h4></center></div></div></div>';
    }
}

?>