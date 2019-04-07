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

class Helpers
{
    public static function randomString($length)
    {
        $temp = str_pad(dechex(mt_rand(0, 0x7fffffff)), 8, '0', STR_PAD_LEFT);
        while (strlen($temp) < $length)
        {
            $temp = $temp . str_pad(dechex(mt_rand(0, 0x7fffffff)), 8, '0', STR_PAD_LEFT);
        }
        
        return substr($temp, 0, $length);
    }
    
    public static function dateTimeString($datetime)
    {
        return strftime('%Y-%m-%d %H:%M:%S', $datetime);
    }
    
    public static function arrayToString($the_array, $delimiter = ':')
    {
        $str_res = '';
        foreach($the_array as $the_entry)
        {
            $str_res = $str_res . $the_entry . $delimiter;
        }
        return $str_res;
    }
    
    public static function stringToArray($the_string, $delimiter = ':')
    {
        $temp_array = explode($delimiter, $the_string);
        $result = array();
        
        foreach($temp_array as $temp)
        {
            if (trim(strlen($temp)) > 0)
            {
                $result[] = trim($temp);
            }
        }
        
        return $result;
    }
    
    public static function stringEndsWith($the_str, $the_end)
    {
        $len = strlen($the_end); 
        if ($len == 0) 
        { 
            return true; 
        }
        if ($len > strlen($the_str))
        {
            return false;
        }
        
        return (substr($the_str, -$len) === $the_end); 
    }
}
?>