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
        echo '<nav class="navbar navbar-expand-lg navbar-light bg-light">';
        if ($usr_is_authenticated)
        {
            echo '    <a class="navbar-brand" href="/php/bs4/mth_ovw.php">Methodenbox</a>';
        }
        else
        {
            echo '    <a class="navbar-brand" href="/php/bs4/app_ovw.php">Methodenbox</a>';
        }
        echo '    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">';
        echo '        <span class="navbar-toggler-icon"></span>';
        echo '    </button>';
        
        echo '    <div class="collapse navbar-collapse" id="navbarSupportedContent">';
        echo '        <ul class="navbar-nav mr-auto">';
        
        foreach(GlobalParam::$main_menu_bs4 as $menu_item)
        {
            if ($menu_item['item_group'] != '')
            {
                echo '<li class="nav-item dropdown">';
                echo '    <a class="nav-link dropdown-toggle" href="#" id="menu' . $menu_item['item_group'] . '" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' . $menu_item['item_group'] . '</a>';
                echo '    <div class="dropdown-menu" aria-labelledby="menu' . $menu_item['item_group'] . '">';
                
                foreach($menu_item['item_list'] as $drop_item)
                {
                    $show_item = ($drop_item['item_show'] == 1) || (($drop_item['auth_req'] == 1) && $usr_is_authenticated) || (($drop_item['auth_req'] == 0) && ! $usr_is_authenticated);
                    
                    if ((strpos($drop_item['item_ref'], $current_form) !== false) || (($drop_item['auth_req'] == 1) && ! $usr_is_authenticated) || (($drop_item['auth_req'] == 0) && $usr_is_authenticated))
                    {
                        $item_ref = '#';
                    }
                    else
                    {
                        $item_ref = $drop_item['item_ref'];
                    }
                    
                    if ($show_item)
                    {
                        echo '        <a class="dropdown-item" href="' . $item_ref . '">' . $drop_item['item_name'] . '</a>';
                    }
                }
                
                echo '    </div>';
                echo '</li>';
            }
            else
            {
                $drop_item = $menu_item['item'];
                $show_item = ($drop_item['item_show'] == 1) || (($drop_item['auth_req'] == 1) && $usr_is_authenticated) || (($drop_item['auth_req'] == 0) && ! $usr_is_authenticated);
                
                if ((strpos($drop_item['item_ref'], $current_form) !== false) || (($drop_item['auth_req'] == 1) && ! $usr_is_authenticated) || (($drop_item['auth_req'] == 0) && $usr_is_authenticated))
                {
                    $item_ref = '#';
                }
                else
                {
                    $item_ref = $drop_item['item_ref'];
                }
                
                if ($show_item)
                {
                    echo '<li class="nav-item">';
                    echo '    <a class="nav-link" href="' . $item_ref . '">' . $drop_item['item_name'] . '</a>';
                    echo '</li>';
                }
            }
        }
    
        echo '        </ul>';
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
        echo '<link rel="stylesheet" ' .
                'href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" ' .
                'integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" ' .
                'crossorigin="anonymous">';
        echo '<link rel="stylesheet" href="/css/project.css">';
    }

    /**
     * Creates the common <script> specifications and outputs them to the console
     * 
     * @access         public
     * @return         null
     */
    public static function script_refs()
    {
        echo '<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>';
        echo '<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>';
        echo '<script src="/js/star-rating.js" type="text/javascript"></script>';
    }
}
?>