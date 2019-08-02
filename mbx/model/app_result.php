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
/**
 * "Methodenbox" Miscellaneous functions.
 *
 * @package        MBX/Model/Misc
 * @author         Walter Pachlinger <walter.pachlinger@gmail.com>
 * @copyright      2019 Walter Pachlinger (walter.pachlinger@gmail.com)
 * @license        Apache License, Version 2.0
 * @license        http://www.apache.org/licenses/LICENSE-2.0
 */

include_once 'aux_text.php';

/**
 * Success or failure indicator.
 * 
 * @author Walter Pachlinger <walter.pachlinger@gmail.com>
 * @property-read boolean $isOK Checks for success or failure.
 * @property-read string $textURLEncoded Gets the text encoded for insertion into an URL.
 * @property-read string $style Gets the style for a bootstrap alert based on the error code.
 * @property-read string $alert Gets the HTML code for a bootstrap alert containing the error text.
 */
class AppResult
{
    /** Numeric error code, 0 indicates success. */
    public $code;
    /** Error text associated with the error code. */
    public $text;

    /**
     * Constructor. Initializes an Error Indication object.
     * 
     * @param int|array $res_code Serialized Error object, optional.
     */
    public function __construct($res_code)
    {
        $this->code = 0;
        $this->text = 'OK';
        
        if (empty($res_code))
        {
            return;
        }

        if (is_array($res_code))
        {
            if (empty($res_code['res_code']))
            {
                return;
            }
            else
            {
                $this->code = $res_code['res_code'];
                $this->text = $res_code['res_text'];
                return;
            }
        }
            
        $this->code = $res_code;
            
        if ($res_code == 0)
        {
            $this->text = 'OK';
        }
        else
        {
            if ($res_code < 100)
            {
                $this->text = '';
            }
            else
            {
                $this->text =  GlobalResultText::$resultText['E_' . $res_code];
            }
        }
    }
    
    /**
     * Magic getter for the readonly properties of the class.
     * 
     * @param string $field Name of the readonly property.
     * @throws Exception Invalid property name.
     * @return boolean|string Value of the readonly property.
     */
    public function __get(string $field)
    {
        switch($field)
        {
            case 'isOK':
                return $this->isOK();
            case 'style':
                return $this->style();
            case 'textURLEncoded':
                return $this->textUrlEncoded();
            case 'alert':
                return $this->alert();
            default:
                throw new Exception('exception: undefined property "AppResult::' . $field . '"');
        }
    }
    
    /**
     * Gets the alert style associated with the error code.
     * 
     * @return string Alert style (warning, danger, info).
     */
    public function style()
    {
        if ($this->code < 900)
        {
            return 'danger';
        }
        else
        {
            if ($this->code > 950)
            {
                return 'success';
            }
            else
            {
                return 'info';
            }
        }
    }
    
    /**
     * Checks the Error Indication object for success.
     * 
     * @return boolean Success (true) or failure (false).
     */
    public function isOK()
    {
        return $this->code == 0;
    }
    
    /**
     * Encodes the error text for insertion into an URL.
     * 
     * @return string URL-encoded error text.
     */
    public function textUrlEncoded()
    {
        return urlencode($this->text);
    }
    
    /**
     * Constructs the HTML code for a bootstrap alert containing the Error Indicator object.
     * 
     * @param string $size HTML paragraph text size.
     * @return string Bootstrap alert HTML code.
     */
    public function alert(string $size = '')
    {
        if (empty($size))
        {
            return '<span class="alert alert-' . $this->style() . '">' . $this->text . '</span>';
        }
        else 
        {
            return '<' . $size . '><span class="alert alert-' . $this->style() . '">' . $this->text . '</span></' . $size . '>';
        }
    }
}

?>