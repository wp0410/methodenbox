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
include_once '../model/aux_helpers.php';

if (empty($_GET))
{
   echo 'false';
}
else
{
    $mth_file = $_GET['mth_file_name'];
    if ($mth_file == null)
    {
        echo 'false';
    }
    else
    {
        if (Helpers::stringEndsWith($mth_file, '.zip') || Helpers::stringEndsWith($mth_file, '.tar') || Helpers::stringEndsWith($mth_file, '.gz'))
        {
            echo 'true';
        }
        else
        {
            echo 'false';
        }
    }
}
