<?php
//---------------------------------------------------------------------------------------
//  Copyright (c) 2017 Walter Pachlinger (walter.pachlinger@gmx.at)
//    
//  Licensed under the Apache License, Version 2.0 (the "License"); you may not use this 
//  file except in compliance with the License. You may obtain a copy of the License at
//      http://www.apache.org/licenses/LICENSE-2.0
//  Unless required by applicable law or agreed to in writing, software distributed under 
//  the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF 
//  ANY KIND, either express or implied. See the License for the specific language 
//  governing permissions and limitations under the License.
//----------------------------------------------------------------------------------------

/**
 * Encapsulates common HTML formatting features
 * 
 * @package        FormatHelper
 * @author         Walter Pachlinger (walter.pachlinger@gmx.at)
 * @version        $Revision: 1.0 $
 * @access         public
 */

class FormatHelper
{
    /**
     * Creates the navigation menu on top of a HTML form out of global configuration
     * (GlobalParam::$main_menu) and outputs it to stdout
     * 
     * @access         public
     * @param          boolean   $usr_is_authenticated    Flag indicating the authentication
     *                                                    status of the current user
     * @param          string    $current_form            File name of the current form
     */
    public static function create_menu($usr_is_authenticated, $current_form)
    {
        echo '<nav class="navbar navbar-default">';
        echo '    <div class="container-fluid">';
        echo '        <div class="navbar-header">';
        echo '            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">';
        echo '                <span class="sr-only">Toggle navigation</span>';
        echo '                <span class="icon-bar"></span>';
        echo '                <span class="icon-bar"></span>';
        echo '                <span class="icon-bar"></span>';
        echo '            </button>';
        echo '            <a class="navbar-brand" href="/php/app_ovw.php">' . GlobalParam::$app_config['app_title'] . '</a>';
        echo '        </div>';
        echo '        <div id="navbar" class="collapse navbar-collapse">';
        echo '            <ul class="nav navbar-nav">';
    
        foreach(GlobalParam::$main_menu as $menu_item)
        {
            $show_item = ($menu_item['item_show'] == 1) || (($menu_item['auth_req'] == 1) && $usr_is_authenticated) || (($menu_item['auth_req'] == 0) && ! $usr_is_authenticated);
            $show_item = $show_item && ($menu_item['item_menu'] == 'L');
            
            if ((strpos($menu_item['item_ref'], $current_form) !== false) || (($menu_item['auth_req'] == 1) && ! $usr_is_authenticated) || (($menu_item['auth_req'] == 0) && $usr_is_authenticated))
            {
                $item_ref = '#';
            }
            else
            {
                $item_ref = $menu_item['item_ref'];
            }
            
            if ($show_item)
            {
                echo '<li><a href="' . $item_ref . '">' . $menu_item['item_name'] . '</a></li>';
            }
        }
        
        echo '            </ul>';
        echo '            <ul class="nav navbar-nav navbar-right">';
        
        foreach(GlobalParam::$main_menu as $menu_item)
        {
            $show_item = ($menu_item['item_show'] == 1) || (($menu_item['auth_req'] == 1) && $usr_is_authenticated) || (($menu_item['auth_req'] == 0) && ! $usr_is_authenticated);
            $show_item = $show_item && ($menu_item['item_menu'] == 'R');
            
            if ((strpos($menu_item['item_ref'], $current_form) !== false) || (($menu_item['auth_req'] == 1) && ! $usr_is_authenticated) || (($menu_item['auth_req'] == 0) && $usr_is_authenticated))
            {
                $item_ref = '#';
            }
            else
            {
                $item_ref = $menu_item['item_ref'];
            }
            
            if ($show_item)
            {
                echo '<li><a href="' . $item_ref . '">' . $menu_item['item_name'] . '</a></li>';
            }
        }
    
        echo '            </ul>';
        echo '        </div>';
        echo '    </div>';
        echo '</nav>';
    }

    /**
     * Creates the common <link> specifications and outputs them to the console
     * 
     * @access         public
     * @return         null
     */
    public static function style_sheet_refs()
    {
        echo '<link rel="stylesheet" href="/css/bootstrap.min.css">';
        echo '<link rel="stylesheet" href="/css/bootstrap-theme.min.css">';
        echo '<link rel="stylesheet" href="/css/project.css">';
    }

    /**
     * Creates the common <script> specifications and outputs them to the console
     * 
     * @return         null
     */
    public static function script_refs()
    {
        echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>';
        echo '<script src="/js/bootstrap.min.js"></script>';
        echo '<script src="/js/validator.js"></script>';
        echo '<script src="/js/star-rating.js" type="text/javascript"></script>';
    }
}
?>