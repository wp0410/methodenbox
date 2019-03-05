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
include_once 'aux_text.php';

class AppResult
{
    public $code;
    public $text;

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
    
    public function style()
    {
        if ($this->code < 900)
        {
            return 'danger';
        }
        else
        {
            return 'info';
        }
    }
    
    public function isOK()
    {
        return $this->code == 0;
    }
    
    public function textUrlEncoded()
    {
        return urlencode($this->text);
    }
}

?>