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
include_once '../model/aux_parameter.php';
include_once '../model/aux_parameter_sec.php';
include_once '../model/app_result.php';
include_once '../model/app_warning.php';

session_start();

$res = new AppResult(0);

if (empty($_POST) || (empty($_POST['set_type'])))
{
    $res = new AppResult(949);
}
else 
{
    if ($_POST['set_type'] == 'SKIN')
    {
        if (! empty($_POST['usr_skin']))
        {
            $_SESSION['skin'] = $_POST['usr_skin'];
        }
    }
    else
    {
        if ($_POST['set_type'] == 'PWD')
        {
            $res = new AppResult(949);
        }
    }
}

if ($res === 0)
{
    header('Location: ../view/usr_settings.php');
}
else 
{
    header('Location: ../view/usr_settings.php?res_code=' . $res->code . '&res_text=' . $res->textUrlEncoded());
}
exit;
?>