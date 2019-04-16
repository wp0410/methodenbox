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
 * ErrorInfo       Encapsulates the description of an error that occurred within the
 *                 application
 * 
 * @package        DatabaseConnection
 * @author         Walter Pachlinger (walter.pachlinger@gmx.at)
 * @version        $Revision: 1.0 $
 * @access         public
 */
class ErrorInfo
{
    public $err_last_action;
    public $err_number;
    public $err_text;
    
    /**
     * Constructor           Initializes an "ErrorInfo" object
     * 
     * @access     public
     * @return     Initialized ErrorInfo object
     */
    public function __construct()
    {
        $this->err_last_action = null;
        $this->err_number = null;
        $this->err_text = null;
    }
    
    /**
     * Handles the error described by the ErrorInfo instance as a critical
     * failure
     * 
     * @access     public
     * @return     none
     */
    public function handle_fatal()
    {
        if ($this->err_text)
        {
            $target = 'Location: /php/app_err.php?err_last_action=' . $this->err_last_action . '&err_number=' . $this->err_number . '&err_text=' . $this->err_text;
            header($target);
            exit;
        }
    }
    
    /**
     * Creates HTML code that displays the ErrorInfo instance and outputs this
     * code to the console
     * 
     * @access     public
     * @return     null
     */
    public function format_error()
    {
        echo '<p class="lead>Kritischer Fehler"</p>';
        if ($this->err_last_action != null)
        {
            echo '<p>          Ausgef&uuml;hrte Aktion: ' . $this->err_last_action . '</p>';
            echo '<p>          Fehler Detail: [' . $this->err_number . ']   ' . $this->err_text . '</p>';
        }
        else
        {
            echo '<p>Ursache nicht spezifiziert</p>';
        }
        echo '</p>';
    }
}
?>