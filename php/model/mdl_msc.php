<?php
//---------------------------------------------------------------------------------------
//  Copyright (c) 2017 Walter Pachlinger (walter.pachlinger@gmx.at)
//    
//  Licensed under the Apache License, Version 2.0 (the "License"); you may not use this 
//  file except in compliance with the License. You may obtain a copy of the License at
//      http://www.apache.org/licenses/LICENSE-2.0
//  Unless required by applicable law or agreed to in writing, softwaredistributed under 
//  the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF 
//  ANY KIND, either express or implied. See the License for the specific language 
//  governing permissions and limitations under the License.
//----------------------------------------------------------------------------------------

/**
 * Creates a random string of hexadecimal digits of the requested length
 * 
 * @param     integer        $length   Requested length of the result string
 * @return    string         Random string of hexadecimal digits
 */
function rnd_string($length)
{
    $temp = str_pad(dechex(mt_rand(0, 0xffffffff)), 8, '0', STR_PAD_LEFT);
    while (strlen($temp) < $length)
    {
        $temp = $temp . str_pad(dechex(mt_rand(0, 0xffffffff)), 8, '0', STR_PAD_LEFT);
    }
    
    return substr($temp, 0, $length);
}
?>