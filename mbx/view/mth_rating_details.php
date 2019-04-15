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
include_once '../model/mth_stat_rating.php';

class RatingListView
{
    private $rtg_lines; 
    private $output;

    public function __construct()
    {
        $this->rtg_lines = array();
        $this->output = '';
    }
    
    private function addOutput($text)
    {
        $this->output = $this->output . $text;
    }
    
    public function addRating($rtg)
    {
        $this->rtg_lines[] = $rtg;
    }
    
    private function renderLine($line_no, $rtg)
    {
        $this->addOutput('<tr><th scope="row">' . substr($rtg->rtg_date, 0, 10) . '</th>');
        $this->addOutput('<td>');
        $rate_val = $rtg->rtg_value;
        if ($rate_val < 3)
        {
            $this->addOutput('<span class="badge badge-danger">');
        }
        else
        {
            if ($rate_val < 4)
            {
                $this->addOutput('<span class="badge badge-warning">');
            }
            else
            {
                $this->addOutput('<span class="badge badge-success">');
            }
        }
        
        for ($cnt = 0; $cnt < $rate_val; $cnt++)
        {
            $this->addOutput('<i class="fa fa-star"></i>');
        }
        for (; $cnt < 5; $cnt++)
        {
            $this->addOutput('<i class="fa fa-star-o"></i>'); 
        }
        $this->addOutput('</span></td>');
        $this->addOutput('<td>' . htmlentities($rtg->rtg_summary) . '</td></tr>');
    }
    
    public function renderHtml()
    {
        $this->addOutput('<table class="table table-striped"><thead>');
        $this->addOutput('<tr><th scope="col">Datum</th><th scope="col">Bewertung</th><th scope="col">Kommentar</th></thead>');
        $this->addOutput('<tbody>');
        $line_no = 1;
        foreach($this->rtg_lines as $rtg)
        {
            $this->renderLine($line_no, $rtg);
            $line_no++;
        }
        $this->addOutput('</tbody></table>');
    }
    
    public function outputHtml()
    {
        echo $this->output;
    }
}
?>