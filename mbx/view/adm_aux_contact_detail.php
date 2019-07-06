<?php
//---------------------------------------------------------------------------------------
//  Copyright (c) 2018, 2019 Walter Pachlinger (walter.pachlinger@gmail.com)
//    
//  Licensed under the Apache License, Version 2.0 (the "License"); you may not use this 
//  file except in compliance with the License. You may obtain a copy of the License at
//      http://www.apache.org/licenses/LICENSE-2.0
//  Unless required by applicable law or agreed to in writing, software distributed under 
//  the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF 
//  ANY KIND, either express or implied. See the License for the specific language 
//  governing permissions and limitations under the License.
//----------------------------------------------------------------------------------------
include_once '../model/sql_connection.php';
include_once '../model/app_result.php';
include_once '../model/aux_helpers.php';
include_once '../model/app_warning.php';
include_once '../model/usr_account.php';
include_once '../model/aux_contact.php';

if (empty($_POST) || empty($_POST['curr_usr_id']) || empty($_POST['req_id']))
{
    $res = new AppResult(100);
    echo $res->alert('h5');
}
else 
{
    $db_conn = DatabaseConnection::get_connection();
    $usr = new UserAccount($db_conn);
    $res = $usr->loadById($_POST['curr_usr_id']);
    if (! $res->isOK())
    {
        echo $res->alert('h5');
    }
    else 
    {
        $req = new ContactRequest($db_conn);
        $res = $req->loadById($_POST['req_id']);
        if (! $res->isOK())
        {
            echo $res->alert('h5');
        }
        else 
        {
            showContactReqDetail($req);
        }
    }
}

function showContactReqDetail($req)
{
    echo '<div class="card"><div class="card-header"><div class="row"><div class="col">';
    echo '<h4 align="center">Details zur Anfrage</h4>';
    echo '</div></div></div>'; // col + rol + card-header
    echo '<div class="card-body">';
    
    echo '<div class="row">';
    echo '   <div class="col">';
    echo '      <table class="table table-striped table-sm">';
    echo '         <thead class="thead-dark"><tr>';
    echo '            <th scope="col">ID</th><th scope="col">Datum</th><th scope="col">Art</th><th scope="col">Status</th>';
    echo '            <th scope="col">Anrede</th><th scope="col">Name</th><th scope="col">E-Mail Adresse</th>';
    echo '         </tr></thead>';
    echo '         <tbody><tr>';
    echo '            <td>' . $req->getId() . '</td><td>' . $req->getCreateTime() . '</td>';
    echo '            <td><span class="badge badge-primary">' . $req->getTypeText() . '</span></td>';
    echo '            <td><span class="badge badge-warning">OFFEN</span></td>';
    echo '            <td>' . $req->usr_addr_form . '</td><td> ' . $req->usr_first_name . ' ' . $req->usr_last_name . '</td>';
    echo '            <td>' . $req->usr_email . '</td>';
    echo '         </tr></tbody>';
    echo '      </table>';
    echo '   </div>'; // col
    echo '</div>'; // row
    
    echo '<div class="row">';
    echo '   <div class="col">';
    echo '      <table class="table table-striped table-sm">';
    echo '         <thead class="thead-dark"><tr>';
    echo '            <th scope="col">Anfrage Text</th>';
    echo '         </tr></thead>';
    echo '         <tbody><tr>';
    echo '            <td><h5>' . $req->req_text . '</h5></td>';
    echo '         </tr></tbody>';
    echo '      </table>';
    echo '   </div>'; // col
    echo '</div>'; // row

    echo '<div class="row">';
    echo '   <div class="col">';
    echo '      <table class="table table-borderless table-sm">';
    echo '         <thead class="thead-dark"><tr>';
    echo '            <th scope="col" colspan="8">Antwort</th>';
    echo '         </tr></thead>';
    echo '         <tbody>';
    echo '            <tr>';
    echo '               <td>An:</td><td colspan="3">' . $req->usr_email . '</td>';
    echo '               <td>Betreff:</th><td colspan="3">Methodenbox Kontaktanfrage vom ' . substr($req->getCreateTime(),0,10) . '</td>';
    echo '            </tr>';
    echo '            <tr><td colspan="8">';
    
    if ($req->usr_addr_form == "Frau")
    {
        echo 'Sehr geehrte Frau ';
    }
    else 
    {
        echo 'Sehr geehrter Herr ';
    }
    echo $req->usr_last_name . ',';
    
    echo '            </td></tr>';
    echo '            <tr><td colspan="8">';
    // echo '               <form id="rq_answer_frm"><div class="form-group" id="rq_answer">';
    echo '               <div class="form-group" id="rq_answer">';
    echo '                  <textarea id="req_ans_ta" name="req_ans_ta" form="rq_answer_frm" class="form-control" rows="8"></textarea>';
    echo '               </div>'; // form-group
    // echo '               </div></form>'; // form-group
    echo '            </td></tr>';
    echo '            <tr>';
    echo '               <td colspan="4">Liebe Gr&uuml;&szlig;e<br>Das Methodenbox Team</td>';
    echo '               <td>';
    echo '                  <a href="#" class="btn btn-primary btn-block" onclick="javascript:post_answer(' . $req->getId() . ')">Senden</a>';
    echo '               </td>';
    echo '               <td>';
    echo '                  <a href="mailto:' . $req->usr_email . '?subject=Methodenbox Kontaktanfrage vom ' . substr($req->getCreateTime(),0,10) . 
                            '" class="btn btn-secondary">E-Mail Programm verwenden</a>';
    echo '               </td>';
    echo '               <td colspan="2">';
    echo '               </td>';
    echo '            </tr>';
    echo '         </tbody>';
    echo '      </table>';
    echo '   </div>'; // col
    echo '</div>'; // row
    
    echo '</div></div>'; // card-body + card + col + row;
    
}
?>