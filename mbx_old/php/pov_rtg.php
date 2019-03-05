<?php
//---------------------------------------------------------------------------------------
//  Copyright (c) 2017 Walter Pachlinger (walter.pachlinger@gmx.at)
//    
//  Licensed under the Apache License, Version 2.0 (the "License"); you may not use this 
//  file except in compliance with the License. You may obtain a copy of the License at
//      http://www.apache.org/licenses/LICENSE-2.0
//  Unless required by applicable law or agreed to in writing, software distributed under 
//  the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF 
//  ANY KIND, either express or implied. See the License for the specific language 
//  governing permissions and limitations under the License.
//----------------------------------------------------------------------------------------
session_start();

include_once 'model/mdl_par.php';
include_once 'model/mdl_dbs.php';
include_once 'model/mdl_ssn.php';
include_once 'model/mdl_jnl.php';
include_once 'model/mdl_mth.php';
include_once 'model/mdl_sta.php';
include_once 'model/mdl_err.php';
include_once 'model/mdl_bs3.php';

$output = '';
$mth_id = -1;

if (! empty($_GET))
{
    if (! empty($_GET['mth_id']))
    {
        $mth_id = $_GET['mth_id'];
    }
}

if ($mth_id > 0)
{
    $db_conn = DatabaseConnection::get_connection();
    if ($db_conn != null)
    {
        $rtg_stat = new RatingStatisticsSearch($db_conn);
        $rtg_stat->set_method_id($mth_id);
        $rtg_list = $rtg_stat->get_result();
        
        if (count($rtg_list) > 0)
        {
           $output = '<table class="table table-striped">' .
                        '<thead>' .
                            '<tr>' .
                                '<th>Benutzer</th>' .
                                '<th>Datum</th>' .
                                '<th>Bewertung</th>' .
                                '<th>Kommentar</th>' .
                            '</tr>' .
                        '</thead>' .
                        '<tbody>';

        
            foreach($rtg_list as $rtg)
            {
                $output = $output . '<tr><td>' . $rtg['usr_email'] . '</td><td>' . substr($rtg['rtg_date'], 0, 10) . '</td><td>';
                
                if ($rtg['rtg_rating'] < 2)
                {
                    $label_class = 'label-alert';
                }
                else
                {
                    if ($rtg['rtg_rating'] < 4)
                    {
                        $label_class = 'label-warning';
                    }
                    else
                    {
                        $label_class = 'label-success';
                    }
                }
                
                $output = $output . '<span class="label ' . $label_class . '">' . number_format($rtg['rtg_rating'],1) . '</span></td><td>' . $rtg['rtg_comment'] . '</td></tr>';
            }
            
            $output = $output . '</tbody></table>';
        }
    }
}

echo $output;
?>