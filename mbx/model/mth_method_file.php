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
include_once '../model/sql_connection.php';
include_once '../model/app_result.php';

class TeachingMethodFile
{
    private $file_mth_id;
    private $file_guid;
    private $file_type;
    private $file_name;
    private $file_data;
    private $db_conn;
    
    public function __construct($db_cn)
    {
        $this->db_conn = $db_cn;
        $this->file_mth_id = -1;
        $this->file_guid = '';
        $this->file_type = '';
        $this->file_name = '';
        $this->file_data = null;
    }
    
    public function loadFile($mth_id, $file_guid)
    {
        $result = null;
        $this->file_mth_id = $mth_id;
        
        $stm_mt2 = $this->db_conn->prepare('select file_guid, file_type, file_name, file_data from ta_mth_method_file where file_mth_id=? and file_guid=?;');
        $stm_mt2->bind_param('is', $this->file_mth_id, $file_guid);
        if ($stm_mt2->execute())
        {
            $stm_mt2->store_result();
            $stm_mt2->bind_result($this->file_guid, $this->file_type, $this->file_name, $this->file_data);
            if ($stm_mt2->fetch())
            {
                $result = new AppResult(0); // OK
            }
            else
            {
                // No file found
                $result = new AppResult(654);
            }
            $stm_mt2->free_result();
        }
        else
        {
            $result = new AppResult(655);
        }
        $stm_mt2->close();
        
        return $result;
    }
    
    public function saveFile($mth_id, $files_elem)
    {
        $result = new AppResult(0);
        
        if ($files_elem['error'] === UPLOAD_ERR_OK)
        {
            $f_type = mime_content_type($files_elem['tmp_name']);
            
            if (! array_key_exists($f_type, GlobalParameter::$validMimeTypes))
            {
                // Invalid file type
                $result = new AppResult(651);
            }
            else
            {
                // extract file name from path
                $f_name = $files_elem['name'];
                if (strpos($f_name, '/'))
                {
                    $fn_parts = explode('/', $f_name);
                }
                else
                {
                    $fn_parts = explode('\\', $f_name);
                }
                $f_name = end($fn_parts);
                
                $this->file_mth_id = $mth_id;
                $this->file_guid = Helpers::randomString(32);
                $this->file_type = GlobalParameter::$validMimeTypes[$f_type];
                $this->file_name = $f_name;
            }
        }
        else
        {
            // File upload failed
            $result = new AppResult(652);
        }
        
        if (! $result->isOK())
        {
            return $result;
        }
        
        $file_exists = false;
        $stm_mt2 = $this->db_conn->prepare('select file_guid from ta_mth_method_file where file_mth_id=?;');
        $stm_mt2->bind_param('i', $this->file_mth_id);
        if ($stm_mt2->execute())
        {
            $tmp_guid = '';
            $stm_mt2->bind_result($tmp_guid);
            if ($stm_mt2->fetch())
            {
                $this->file_guid = $tmp_guid;
                $file_exists = true;
            }
        }
        $stm_mt2->close();
        
        $hlp_null = null;
        if ($file_exists)
        {
            $stm_mt3 = $this->db_conn->prepare('update ta_mth_method_file set file_guid=?, file_type=?, file_name=?, file_data=? where file_mth_id=?;');
            $stm_mt3->bind_param('sssbi', $this->file_guid, $this->file_type, $this->file_name, $hlp_null, $this->file_mth_id);
            $stm_mt3->send_long_data(3,  file_get_contents($files_elem['tmp_name']));
            if ($stm_mt3->execute())
            {
                $result = new AppResult(0);
            }
            else
            {
                $result = new AppResult(653);
                $result->text = $result.text . ' Detail: DB result = [' . $stm_mt1->errno . ', ' . $stm_mt1->error . ']';
            }
            $stm_mt3->close();
        }
        else
        {
            $stm_mt1 = $this->db_conn->prepare('insert into ta_mth_method_file( file_mth_id, file_guid, file_type, file_name, file_data ) values ( ?, ?, ?, ?, ? );');
            $stm_mt1->bind_param('isssb', $this->file_mth_id, $this->file_guid, $this->file_type, $this->file_name, $hlp_null);
            $stm_mt1->send_long_data(4, file_get_contents($files_elem['tmp_name']));
            if ($stm_mt1->execute())
            {
                $result = new AppResult(0); // OK
            }
            else
            {
                $result = new AppResult(653);
                $result->text = $result.text . ' Detail: DB result = [' . $stm_mt1->errno . ', ' . $stm_mt1->error . ']';
            }
            $stm_mt1->close();
        }
        
        return $result;
    }
    
    public function getFileData()
    {
        return $this->file_data;
    }
}
?>
