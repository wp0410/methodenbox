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
 * GlobalParam     Encapsulates global parameters
 * 
 * @package   GlobalParam
 * @author    Walter Pachlinger (walter.pachlinger@gmx.at)
 * @version   $Revision: 1.0 $
 * @access    public
 */
class GlobalParam
{
    public static $title = 'Methodenbox';
    
    public static $captcha_sitekey = '6LcnGzMUAAAAAEAy80E68H1vlybVXTKGjss0tjrO';
    
    public static $captcha_secret  = '6LcnGzMUAAAAAKo31wWlV2DX7b2rX3lofHOjkCGY';
    
    public static $invalid_login_limit = 3;
    
    public static $session_timeout_sec = 600;
    
    public static $ctc_send_mail = false;
    public static $ctc_sender_email = 'walter.pachlinger@gmail.com';
    public static $ctc_sender_name  = 'Walter Pachlinger';
    
    public static $mj_password = 'a0fbc3344ea28b934ccfaabb76f2df6c:f0105dca8cf8bd76e7ea416ba95266dc';
    
    public static $mime_types = array(
        'application/pdf' => '.pdf',
        'application/msword' => '.doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '.docx',
        'application/vnd.ms-excel' => '.xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => '.xlsx',
        'application/vnd.ms-powerpoint' => '.ppt',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => '.pptx' );
}
?>