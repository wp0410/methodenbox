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
include_once '../model/app_result.php';

class StaticHtmlBlock
{
	public $static_text;
	public $app_result;
	
	public function __construct()
	{
		$this->static_text = '';
		$this->app_result = new AppResult(0);
	}
	
	public function retrieveBlockById($block_id)
	{
		$file_name = GlobalParameter::$applicationConfig['staticBlockDir'] . '/' . $block_id . '.html';
		return $this->retrieveBlock($file_name);
	}
	
	public function retrieveBlockByKey($form_id, $div_id, $var_id)
	{
		$file_name = GlobalParameter::applicationConfig['staticBlockDir'] . '/' . $form_id . '.' . $div_id . '.' . $var_id . '.html';
		return $this->retrieveBlock($file_name);
	}
	
	private function retrieveBlock($file_name)
	{
		if (file_exists($file_name))
		{
			$result_text = file_get_contents($file_name);
			if ($result_text === false)
			{
				$this->app_result = new AppResult(201);
				$this->app_result->text = $this->app_result->text . ' [ Datei = "' . $file_name . '"]';
			}
			else
			{
				$this->static_text = $result_text;
			}
		}
		else
		{
			$this->app_result = new AppResult(202);
			$this->app_result->text = $this->app_result->text . ' [ Datei = "' . $file_name . '"]';
		}

		return $this->static_text;
	}
}
?>