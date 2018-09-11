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
    public $emailSubject;
    public $emailText;
    private $emailToList;
    private $emailFrom;

    /**
     * Constructor
     * 
     * @access     public
     * @return     An initialized Mailer instance
     */
    public function __construct()
    {
        $this->emailFrom = array();
        $this->emailToList = array();
        $this->emailSubject = '';
        $this->emailText = '';
    }
    
    /**
     * Sets the sender information for the info mail
     * 
     * @access     public
     * @param      string    $sender_email       Sender's e-mail address
     * @param      string    $sender_full_name   Sender's full name
     */
    public function set_sender($sender_email, $sender_full_name)
    {
        $this->emailFrom['Email'] = $sender_email;
        $this->emailFrom['Name']  = $sender_full_name;
    }
    
    /**
     * Add a recipient to the recipient list of the info mail
     * 
     * @access     public
     * @param      string    $recipient_email    Recipient's e-mail address
     */
    public function add_recipient($recipient_email)
    {
        $this->emailToList[] = array('Email' => $recipient_email);
    }
    
    /**
     * Sends an e-mail using the MailJet API
     * 
     * @access     public
     * @param      boolean   $do_send  TRUE: send e-mail; FALSE: suppress e-mail
     * @return     TRUE      E-mail successfully sent
     * @return     FALSE     Error sending e-mail
     */
    public function send($do_send)
    {
        if ($do_send)
        {
            $emailMsgList   = array();
            $emailMsgList[] = array('From' => $this->emailFrom, 'To' => $this->emailToList, 'Subject' => $this->emailSubject, 'TextPart' => $this->emailText);
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