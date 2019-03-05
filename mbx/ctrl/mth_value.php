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
include_once '../model/mth_selection.php';

$output = '';

if (!empty($_POST))
{
    if ($_POST['val_type'] == 'mth_area')
    {
        $mth_subj = '';
        if (empty($_POST['mth_subj']))
        {
            $mth_subj = 'NULL';
        }
        else
        {
            $mth_subj = $_POST['mth_subj'];
        }

        $output = $output . '<option></option>';
        foreach(MethodSelectionFactory::getSubjectAreas($mth_subj) as $area)
        {
            $output = $output . '<option value="' . $area['VAL'] .'">' . $area['NAME'] . '</option>';
        }
    }
}

echo $output;
?>