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

if (empty($_POST) || empty($_POST['hlp_topic']))
{
	$help_contents = '<div class="alert alert-primary mt-4 pl-5 pr-5"><h3>Das Hilfethema konnte nicht gefunden werden</h3></div>';
}
else
{
	$hlp_block = new StaticHtmlBlock();
	$help_contents = $hlp_block->retrieveBlockById('aux_help_' . $_POST['hlp_topic']);
	if (! $hlp_block->app_result->isOK())
	{
		$help_contents = '<div class="alert alert-danger mt-5" role="alert"><h5>' . $hlp_block->app_result->text . '</h5></div>';
	}
}

echo $help_contents;
?>