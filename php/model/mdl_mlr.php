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
 * MailjetMailer        Send E-Mail using the MailJet API
 * 
 * @package   MailjetMailer
 * @author    Walter Pachlinger (walter.pachlinger@gmx.at)
 * @version   $Revision: 1.0 $
 * @access    public
 */
class MailjetMailer
{
    public $recipient;
    public $subject;
    public $text;
    
    /**
     * Constructor
     * 
     * @access     public
     * @return     An initialized Mailer instance
     */
    public function __construct()
    {
        $this->recipient = '';
        $this->subject = '';
        $this->text = '';
    }
    
    /**
     * Sends an e-mail using the MailJet API
     * 
     * @access     public
     * @return     TRUE      E-mail successfully sent
     * @return     FALSE     Error sending e-mail
     */
    public function send()
    {
        if (GlobalParam::$app_config['ctc_send_mail'])
        {
            $emailFrom      = array('Email' => GlobalParam::$mailer_cnf['sender_email'], 'Name' => GlobalParam::$mailer_cnf['sender_name']);
            $emailToList    = array();
            $emailToList[]  = array('Email' => $this->recipient);
            $emailMsgList   = array();
            $emailMsgList[] = array('From' => $emailFrom, 'To' => $emailToList, 'Subject' => $this->subject, 'TextPart' => $this->text);
            $emailData      = array('Messages' => $emailMsgList);
    
            $json_data = json_encode($emailData);        
        
            $curl_sess = curl_init('https://api.mailjet.com/v3.1/send');
            curl_setopt($curl_sess, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl_sess, CURLOPT_POSTFIELDS, $json_data);
            curl_setopt($curl_sess, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_sess, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($json_data)));
            curl_setopt($curl_sess, CURLOPT_USERPWD, GlobalParam::$mailer_cnf['mj_password']);
        
            $json_res = curl_exec($curl_sess);
            $result = json_decode($json_res, true);
            $success = true;
            foreach ($result['Messages'] as $idx => $msg) 
            {
                $success = $success && ($msg['Status'] == "success");
            }
    
            return $success;
        }
        else
        {
            return true;
        }
    }
}
?>