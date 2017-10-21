<?php
    //---------------------------------------------------------------------------------------------
    // mailjet.php           Send an e-mail using the Mailjet E-Mail API
    //
    // Copyright (c) 2017 Walter Pachlinger (walter.pachlinger@gmx.at)
    //
    // Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file 
    // except in compliance with the License. You may obtain a copy of the License at
    //    http://www.apache.org/licenses/LICENSE-2.0
    // Unless required by applicable law or agreed to in writing, softwaredistributed under the 
    // License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, 
    // either express or implied. See the License for the specific language governing permissions 
    // and limitations under the License.
    //---------------------------------------------------------------------------------------------
    function mailjet_mail($recipient, $subject, $text)
    {
        $emailFrom      = array('Email' => 'walter.pachlinger@gmail.com', 'Name' => 'Walter Pachlinger');
        $emailToList    = array();
        $emailToList[]  = array('Email' => $recipient);
        $emailMsgList   = array();
        $emailMsgList[] = array('From' => $emailFrom, 'To' => $emailToList, 'Subject' => $subject, 'TextPart' => $text);
        $emailData      = array('Messages' => $emailMsgList);

        $json_data = json_encode($emailData);        
    
        $curl_sess = curl_init('https://api.mailjet.com/v3.1/send');
        curl_setopt($curl_sess, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl_sess, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($curl_sess, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_sess, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($json_data)));
        curl_setopt($curl_sess, CURLOPT_USERPWD, 'a0fbc3344ea28b934ccfaabb76f2df6c:f0105dca8cf8bd76e7ea416ba95266dc');
    
        $json_res = curl_exec($curl_sess);
        $result = json_decode($json_res, true);
        $success = true;
        foreach ($result['Messages'] as $idx => $msg) 
        {
            $success = $success && ($msg['Status'] == "success");
        }

        return $success;
    }
?>