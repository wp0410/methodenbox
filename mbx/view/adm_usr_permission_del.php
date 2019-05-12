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

if (! empty($_POST) && ! empty($_POST['usr_role']))
{
    $current_role = $_POST['usr_role'];
    
    echo '<div class="row form-row">';
    echo '   <div class="col col-md-3 col-xl-3"><br></div>';
    echo '   <div class="col col-md-6 col-xl-6">';
    echo '      <div class="card">';
    echo '         <div class="card-header">^<h5>Berechtigungen Entziehen</h5></div>';
    echo '         <div class="card-body">';

    if ($current_role == 'ADMIN')
    {
        echo '        <div class="form-check">';
        echo '           <input class="form-check-input" type="radio" name="perm_del[]" id="del_adm" value="DEL.ADM" checked>';
        echo '           <label class="form-check-label" for="del_adm">';
        echo '              <span><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<i class="fa fa-cog" aria-hidden="true"></i>&nbsp;&nbsp;(Administration)</span>';
        echo '           </label>';
        echo '        </div>'; // form-check
        
        echo '        <div class="form-check">';
        echo '           <input class="form-check-input" type="radio" name="perm_del[]" id="del_adm_upl" value="DEL.ADM.UPL">';
        echo '           <label class="form-check-label" for="del_adm_upl">';
        echo '              <span><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<i class="fa fa-cloud-upload" aria-hidden="true"></i>&nbsp;&nbsp;(Methoden Hochladen)</span>';
        echo '           </label>';
        echo '        </div>'; // form-check

        echo '        <div class="form-check">';
        echo '           <input class="form-check-input" type="radio" name="perm_del[]" id="del_adm_upl_clt" value="DEL.ADM.UPL.CLT">';
        echo '           <label class="form-check-label" for="del_adm_upl_clt">';
        echo '              <span><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<i class="fa fa-user-o" aria-hidden="true"></i>&nbsp;&nbsp;(Normaler Benutzer)</span>';
        echo '           </label>';
        echo '        </div>'; // form-check
    }
    if ($current_role == 'UPLOAD')
    {
        echo '        <div class="form-check">';
        echo '           <input class="form-check-input" type="radio" name="perm_del[]" id="del_adm" value="DEL.NO1" disabled>';
        echo '           <label class="form-check-label" for="del_adm">';
        echo '              <span><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<i class="fa fa-cog" aria-hidden="true"></i>&nbsp;&nbsp;(Administration)</span>';
        echo '           </label>';
        echo '        </div>'; // form-check
        
        echo '        <div class="form-check">';
        echo '           <input class="form-check-input" type="radio" name="perm_del[]" id="del_adm_upl" value="DEL.UPL" checked>';
        echo '           <label class="form-check-label" for="del_adm_upl">';
        echo '              <span><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<i class="fa fa-cloud-upload" aria-hidden="true"></i>&nbsp;&nbsp;(Methoden Hochladen)</span>';
        echo '           </label>';
        echo '        </div>'; // form-check
        
        echo '        <div class="form-check">';
        echo '           <input class="form-check-input" type="radio" name="perm_del[]" id="del_adm_upl_clt" value="DEL.UPL.CLT">';
        echo '           <label class="form-check-label" for="del_adm_upl_clt">';
        echo '              <span><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<i class="fa fa-user-o" aria-hidden="true"></i>&nbsp;&nbsp;(Normaler Benutzer)</span>';
        echo '           </label>';
        echo '        </div>'; // form-check
    }
    if ($current_role == 'CLIENT')
    {
        echo '        <div class="form-check">';
        echo '           <input class="form-check-input" type="radio" name="perm_del[]" id="del_adm" value="DEL.NO1" disabled>';
        echo '           <label class="form-check-label" for="del_adm">';
        echo '              <span><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<i class="fa fa-cog" aria-hidden="true"></i>&nbsp;&nbsp;(Administration)</span>';
        echo '           </label>';
        echo '        </div>'; // form-check
        
        echo '        <div class="form-check">';
        echo '           <input class="form-check-input" type="radio" name="perm_del[]" id="del_adm_upl" value="DEL.NO2" disabled>';
        echo '           <label class="form-check-label" for="del_adm_upl">';
        echo '              <span><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<i class="fa fa-cloud-upload" aria-hidden="true"></i>&nbsp;&nbsp;(Methoden Hochladen)</span>';
        echo '           </label>';
        echo '        </div>'; // form-check
        
        echo '        <div class="form-check">';
        echo '           <input class="form-check-input" type="radio" name="perm_del[]" id="del_adm_upl_clt" value="DEL.UPL.CLT" checked>';
        echo '           <label class="form-check-label" for="del_adm_upl_clt">';
        echo '              <span><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<i class="fa fa-user-o" aria-hidden="true"></i>&nbsp;&nbsp;(Normaler Benutzer)</span>';
        echo '           </label>';
        echo '        </div>'; // form-check
    }
    if ($current_role == 'NONE')
    {
        echo '        <div class="form-check">';
        echo '           <input class="form-check-input" type="radio" name="perm_del[]" id="del_adm" value="DEL.NO1" disabled>';
        echo '           <label class="form-check-label" for="del_adm">';
        echo '              <span><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<i class="fa fa-cog" aria-hidden="true"></i>&nbsp;&nbsp;(Administration)</span>';
        echo '           </label>';
        echo '        </div>'; // form-check
        
        echo '        <div class="form-check">';
        echo '           <input class="form-check-input" type="radio" name="perm_del[]" id="del_adm_upl" value="DEL.NO2" disabled>';
        echo '           <label class="form-check-label" for="del_adm_upl">';
        echo '              <span><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<i class="fa fa-cloud-upload" aria-hidden="true"></i>&nbsp;&nbsp;(Methoden Hochladen)</span>';
        echo '           </label>';
        echo '        </div>'; // form-check
        
        echo '        <div class="form-check">';
        echo '           <input class="form-check-input" type="radio" name="perm_del[]" id="del_adm_upl_clt" value="DEL.NO3" disabled>';
        echo '           <label class="form-check-label" for="del_adm_upl_clt">';
        echo '              <span><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<i class="fa fa-user-o" aria-hidden="true"></i>&nbsp;&nbsp;(Normaler Benutzer)</span>';
        echo '           </label>';
        echo '        </div>'; // form-check
    }
    
    echo '      </div></div>'; // card body + card
    echo '   </div>';   // col
    
    echo '</div>';      // row
}
?>