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
include_once '../model/aux_static.php';

if (empty($_POST) || empty($_POST['static_block_id']))
{
	$result_text = '<div class="alert alert-danger" role="alert"><h5>Fehler: ein notwendiger Parameter fehlt. Bitte kontaktieren Sie einen Administrator.</h5></div>';
	
	$line_num = 0;
	$result_text = $result_text . '<div class="alert alert-danger" role="alert"><h5>';
	foreach($_POST as $par_name => $par_value)
	{
		if ($line_num++ == 0)
		{
			$result_text = $result_text . '"' . $par_name . '": "' . $par_value . '"';			
		}
		else
		{
			$result_text = $result_text . '<br><hr>"' . $par_name . '": "' . $par_value . '"';			
		}
	}
	$result_text = $result_text . '</h5></div>';
}
else
{
	$result_text = StaticHtmlBlock::retrieveBlockById($_POST['static_block_id']);
}

echo $result_text;
?>