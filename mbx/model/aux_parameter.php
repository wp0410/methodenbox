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

class GlobalParameter
{
    public static $applicationConfig = array (
            'applicationTitle'       => 'Methodenbox',
            'userAccountFailLimit'   => 3,
            'userSessionLifetimeSec' => 3600,
            'deploymentZone'         => 'DEV',
            'emailSender'            => 'walter.pachlinger@gmail.com',
            'emailSenderFullName'    => 'Methodenbox',
            'mailjetPassword'        => 'a0fbc3344ea28b934ccfaabb76f2df6c:f0105dca8cf8bd76e7ea416ba95266dc',
            'mthResultNumLines'      => 5,
            'mthRatingNumLines'      => 5,
            'mthAdminNumLines'       => 5,
            'rtgListNumLines'        => 15,
            'rtgListMaxAgeDays'      => 90,

            'void'                   => ''
        );
        
    public static $captchaConfig = array (
        'DEV' => array(
            'sitekey' => '6LcnGzMUAAAAAEAy80E68H1vlybVXTKGjss0tjrO',
            'secret'  => '6LcnGzMUAAAAAKo31wWlV2DX7b2rX3lofHOjkCGY' ),
        'DEMO' => array(
            'sitekey' => '6LdngW0UAAAAAN2DkVNuKIkKlCFjKKpyLiWp-7bm',
            'secret'  => '6LdngW0UAAAAAPBUZGEPaQI--Z_YuwwNQ12QNi7t' ),
        );
        
    public static $validMimeTypes = array (
            'application/zip' => '.zip'
        );
}
?>