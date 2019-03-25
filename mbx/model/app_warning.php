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
function handle_warning(int $errno, string $errstr, string $errfile = null, int $errline = null, array $errcontext = null)
{
    $result = false;
    
    switch($errno)
    {
        case E_USER_WARNING:
            log_msg('E_USER_WARNING', $errstr, $errfile, $errline);
            $result = true;
            break;
        case E_USER_NOTICE:
            log_msg('E_USER_NOTICE', $errstr, $errfile, $errline);
            $result = true;
            break;
        default:
            break;
    }
    
    return $result;
}

function set_private_warning_handler()
{
    set_error_handler(handle_warning, E_USER_WARNING | E_USER_NOTICE);
}

function log_msg(string $err_type, string $errstr, string $errfile, int $errline)
{
    $log_str = strftime('%Y-%m-%d %H:%M:%S', time()) + '   ' + $err_type;
    if (($errfile != null) && (strlen($errfile) > 0))
    {
        $log_str = $log_str + '   file: ' + $errfile;
    }
    if ($errline != null)
    {
        $log_str = $log_str + '   line: ' + $errline;
    }
    if ($errstr != null)
    {
        $log_str = $log_str + '\n' + $errstr + '\n';
    }
    
    error_log($log_str, 3, GlobalParameter::$applicationConfig['logDestination']);
}
?>