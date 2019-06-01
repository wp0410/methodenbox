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
include_once '../model/app_warning.php';

set_private_warning_handler();
session_start();

if (! empty($_POST) && ! empty($_POST['usr_role']) && ! empty($_POST['perm_fn']) && (($_POST['perm_fn'] == "PRV_ADD") || ($_POST['perm_fn'] == "PRV_DEL")))
{
    $current_role = $_POST['usr_role'];
    $priv_action = $_POST['perm_fn'];
    
    echo '<div class="row form-row"><div class="col">';
    echo '   <div id="usrPermChangeMsg" name="usrPermChangeMsg"></div>';
    echo '</div></div>';
    
    echo '<div class="row form-row">';
    echo '   <div class="col col-md-3 col-xl-3"><br></div>';
    echo '   <div class="col col-md-6 col-xl-6">';
    echo '      <div class="card">';
    if ($priv_action == "PRV_ADD")
    {
        echo '         <div class="card-header"><h5>Berechtigungen Erteilen</h5></div>';
    }
    else 
    {
        echo '         <div class="card-header"><h5>Berechtigungen Entziehen</h5></div>';
    }
    echo '         <div class="card-body">';
    
    $format_by_cur_role = array(
        'PRV_ADD' => array(
            'NONE' => array(
                1 => array('id' => 'add_clt', 'value' => 'ADD.CLT', 'property' => 'checked', 'icon' => 'fa fa-user-o fa-2x', 'text' => '(Normaler Benutzer)'),
                2 => array('id' => 'add_clt_upl', 'value' => 'ADD.CLT.UPL', 'property' => '', 'icon' => 'fa fa-cloud-upload fa-2x', 'text' => '(Methoden Hochladen)'),
                3 => array('id' => 'add_clt_upl_adm', 'value' => 'ADD.CLT.UPL.ADM', 'property' => '', 'icon' => 'fa fa-cog fa-2x', 'text' => '(Administration)')
            ),
            'CLIENT' => array(
                1 => array('id' => 'add_clt', 'value' => 'ADD.NON', 'property' => 'disabled', 'icon' => 'fa fa-user-o fa-2x', 'text' => '(Normaler Benutzer)'),
                2 => array('id' => 'add_clt_upl', 'value' => 'ADD.UPL', 'property' => 'checked', 'icon' => 'fa fa-cloud-upload fa-2x', 'text' => '(Methoden Hochladen)'),
                3 => array('id' => 'add_clt_upl_adm', 'value' => 'ADD.UPL.ADM', 'property' => '', 'icon' => 'fa fa-cog fa-2x', 'text' => '(Administration)')
            ),
            'UPLOAD' => array(
                1 => array('id' => 'add_clt', 'value' => 'ADD.NON', 'property' => 'disabled', 'icon' => 'fa fa-user-o fa-2x', 'text' => '(Normaler Benutzer)'),
                2 => array('id' => 'add_clt_upl', 'value' => 'ADD.NON', 'property' => 'disabled', 'icon' => 'fa fa-cloud-upload fa-2x', 'text' => '(Methoden Hochladen)'),
                3 => array('id' => 'add_clt_upl_adm', 'value' => 'ADD.ADM', 'property' => 'checked', 'icon' => 'fa fa-cog fa-2x', 'text' => '(Administration)')
            ),
            'ADMIN' => array(
                1 => array('id' => 'add_clt', 'value' => 'ADD.NON', 'property' => 'disabled', 'icon' => 'fa fa-user-o fa-2x', 'text' => '(Normaler Benutzer)'),
                2 => array('id' => 'add_clt_upl', 'value' => 'ADD.NON', 'property' => 'disabled', 'icon' => 'fa fa-cloud-upload fa-2x', 'text' => '(Methoden Hochladen)'),
                3 => array('id' => 'add_clt_upl_adm', 'value' => 'ADD.NON', 'property' => 'disabled', 'icon' => 'fa fa-cog fa-2x', 'text' => '(Administration)')
            )
        ),
        'PRV_DEL' => array(
            'ADMIN' => array(
                1 => array('id' => 'del_adm', 'value' => 'DEL.ADM', 'property' => 'checked', 'icon' => 'fa fa-cog fa-2x', 'text' => '(Administration)'),
                2 => array('id' => 'del_adm_upl', 'value' => 'DEL.ADM.UPL', 'property' => '', 'icon' => 'fa fa-cloud-upload fa-2x', 'text' => '(Methoden Hochladen)'),
                3 => array('id' => 'del_adm_upl_clt', 'value' => 'DEL.ADM.UPL.CLT', 'property' => 'disabled', 'icon' => 'fa fa-user-o fa-2x', 'text' => '(Normaler Benutzer)')
            ),
            'UPLOAD' => array(
                1 => array('id' => 'del_adm', 'value' => 'DEL.NON', 'property' => 'disabled', 'icon' => 'fa fa-cog fa-2x', 'text' => '(Administration)'),
                2 => array('id' => 'del_adm_upl', 'value' => 'DEL.UPL', 'property' => 'checked', 'icon' => 'fa fa-cloud-upload fa-2x', 'text' => '(Methoden Hochladen)'),
                3 => array('id' => 'del_adm_upl_clt', 'value' => 'DEL.UPL.CLT', 'property' => 'disabled', 'icon' => 'fa fa-user-o fa-2x', 'text' => '(Normaler Benutzer)')
            ),
            'CLIENT' => array(
                1 => array('id' => 'del_adm', 'value' => 'DEL.NON', 'property' => 'disabled', 'icon' => 'fa fa-cog fa-2x', 'text' => '(Administration)'),
                2 => array('id' => 'del_adm_upl', 'value' => 'DEL.NON', 'property' => 'disabled', 'icon' => 'fa fa-cloud-upload fa-2x', 'text' => '(Methoden Hochladen)'),
                3 => array('id' => 'del_adm_upl_clt', 'value' => 'DEL.CLT', 'property' => 'disabled', 'icon' => 'fa fa-user-o fa-2x', 'text' => '(Normaler Benutzer)')
            ),
            'NONE' => array(
                1 => array('id' => 'del_adm', 'value' => 'DEL.NON', 'property' => 'disabled', 'icon' => 'fa fa-cog fa-2x', 'text' => '(Administration)'),
                2 => array('id' => 'del_adm_upl', 'value' => 'DEL.NON', 'property' => 'disabled', 'icon' => 'fa fa-cloud-upload fa-2x', 'text' => '(Methoden Hochladen)'),
                3 => array('id' => 'del_adm_upl_clt', 'value' => 'DEL.NON', 'property' => 'disabled', 'icon' => 'fa fa-user-o fa-2x', 'text' => '(Normaler Benutzer)')
            )
        )
    );
    
    echo '<table class="table table-borderless"><tbody>';
    foreach($format_by_cur_role[$priv_action][$current_role] as $opt)
    {
        echo '<tr><td><div class="form-check">';
        echo '<input class="form-check-input" type="radio" name="permits[]" id="' . $opt['id'] . '" value="' . $opt['value'] . '" ' . $opt['property'] . '>';
        // echo '<label class="form-check-label" for="' . $opt['id'] . '">';
        echo '</div></td><td>';
        echo '<span><i class="' . $opt['icon'] . '" aria-hidden="true"></i></span>';
        echo '</td><td>';
        echo $opt['text'];
        echo '</td></tr>';
    }
    echo '</tbody></table>';
    
    echo '      </div></div>'; // card body + card
    echo '   </div>';   // col
    
    echo '</div>';      // row
}
?>