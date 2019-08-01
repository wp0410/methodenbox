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

class BrowserCompatibility
{
	private $browser_info;
	private $printable_name;
	private $compatible;
	private $deprecated;
	
	public function __construct($user_agent)
	{
		$this->compatible = false;
		$this->deprecated = false;
		
		if (strpos($user_agent, 'Opera/') || strpos($user_agent, 'OPR/'))
		{
			$this->browser_info = 'Opera';
			$this->printable_name = 'Opera';
			$this->compatible = true;
		}
		else
		{
			if (strpos($user_agent, 'Edge/') || strpos($user_agent, 'Edg/'))
			{
				$this->browser_info = 'Edge';
				$this->printable_name = 'Microsoft Edge';
				$this->compatible = true;
			}
			else
			{
				if (strpos($user_agent, 'Chrome/'))
				{
					$this->browser_info = 'Chrome';
					$this->printable_name = 'Google Chrome';
					$this->compatible = true;
				}
				else
				{
					if (strpos($user_agent, 'Safari/'))
					{
						$this->browser_info = 'Safari';
						$this->printable_name = 'Apple Safari';
						$this->compatible = true;
					}
					else
					{
						if (strpos($user_agent, 'Firefox/'))
						{
							$this->browser_info = 'Firefox';
							$this->printable_name = 'Mozilla Firefox';
							$this->compatible = true;
						}
						else
						{
							if (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/'))
							{
								$this->browser_info = 'MSIE';
								$this->printable_name = 'Microsoft Internet Explorer';
								$this->deprecated = true;
							}
							else
							{
								$this->browser_info = 'Other';
								$this->printable_name = 'nicht identifiziert';
							}
						}
					}
				}
			}
		}
	}
	
	public function __get($property)
	{
		switch ($property)
		{
			case 'browserInfo':
				return $this->browser_info;
				break;
			case 'browserName':
				return $this->printable_name;
				break;
			/*
			case "version":
				return $browser_info['version'];
				break;
			*/
			case 'isCompatible':
				return $this->compat_flag;
				break;
			case 'isDeprecated':
				return $this->deprecated;
				break;
			default:
				trigger_error('Undefined property for __get(): "' . $property . '" in ' . __FILE__ . ' line ' . __LINE__, E_USER_NOTICE);
				break;
		}
	}
}
?>