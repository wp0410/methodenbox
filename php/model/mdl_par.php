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
    
    public static $session_timeout_sec = 6000;
    
    // Send mail from Contact form { true, false }
    public static $ctc_send_mail = false;
    
    // Sender for Contact form mails [e-mail address]
    public static $ctc_sender_email = 'walter.pachlinger@gmail.com';
    
    // Full name of sender of Contact form mails
    public static $ctc_sender_name  = 'Walter Pachlinger';
    
    // Password for MailJet access
    public static $mj_password = 'a0fbc3344ea28b934ccfaabb76f2df6c:f0105dca8cf8bd76e7ea416ba95266dc';
    
    // Storage method for uploaded files {'DATABASE' | 'FILESYS' }
    public static $file_storage_type = 'DATABASE';
    
    public static $mime_types = array(
        'application/pdf' => '.pdf',
        'application/msword' => '.doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '.docx',
        'application/vnd.ms-excel' => '.xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => '.xlsx',
        'application/vnd.ms-powerpoint' => '.ppt',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => '.pptx' 
    );
        
    public static $main_menu = array(
        array('item_name' => 'Methode Suchen', 'item_ref' => '/php/mth_src.php', 'item_menu' => 'L', 'auth_req' => 1, 'item_show' => 1),
        array('item_name' => 'Methode Erstellen', 'item_ref' => '/php/mth_new.php', 'item_menu' => 'L', 'auth_req' => 1, 'item_show' => 1),
        array('item_name' => 'Methode Bewerten', 'item_ref' => '/php/mth_rtg.php', 'item_menu' => 'L', 'auth_req' => 1, 'item_show' => 1),
        array('item_name' => 'Methoden Verwalten', 'item_ref' => '/php/mth_adm.php', 'item_menu' => 'L', 'auth_req' => 1, 'item_show' => 1),
        array('item_name' => 'Registrieren', 'item_ref' => '/php/usr_new.php', 'item_menu' => 'R', 'auth_req' => 0, 'item_show' => 1),
        array('item_name' => 'Anmelden', 'item_ref' => '/php/usr_lin.php', 'item_menu' => 'R', 'auth_req' => 0, 'item_show' => 0),
        array('item_name' => 'Abmelden', 'item_ref' => '/php/usr_out.php', 'item_menu' => 'R', 'auth_req' => 1, 'item_show' => 0),
        array('item_name' => 'Kontakt', 'item_ref' => '/php/aux_ctc.php', 'item_menu' => 'R', 'auth_req' => 1, 'item_show' => 1),
        array('item_name' => 'Hilfe', 'item_ref' => '/php/aux_hlp.php', 'item_menu' => 'R', 'auth_req' => -1, 'item_show' => 1)
    );
}
?>