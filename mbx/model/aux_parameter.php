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
        'appVersion'             => 'v1.2.7b(2019-04-10)',
        'applicationTitle'       => 'Methodenbox',
        'userAccountFailLimit'   => 3,
        'userSessionLifetimeSec' => 3600,
        'deploymentZone'         => 'DEV',
        'mthResultNumLines'      => 5,
        'mthRatingNumLines'      => 5,
        'mthAdminNumLines'       => 5,
        'rtgListNumLines'        => 15,
        'rtgListMaxAgeDays'      => 90,
        
        // Paging parameters
        'mthPageNumLines'        => 5,
        'mthPageNumPages'        => 9,
        
        // Upload file types
        'mthUploadFileTypes'     => '.zip, .tar, .gz',
        
        'logDestination'         => '/php_warning.log',
        'doSendEmail'            => false,
        'validateCaptcha'        => false,

        'void'                   => ''
    );
        
    public static $validMimeTypes = array (
        'application/zip' => '.zip',
        'application/x-tar' => '.tar',
        'application/gzip' => '.gz'
    );
}
?>