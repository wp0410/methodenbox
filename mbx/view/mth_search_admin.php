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
include_once '../model/mth_method_view.php';

class MethodAdminResultView
{
    private $res_lines; 
    private $num_remain;
    private $output;
    private $usr_authenticated;
    private $usr_id;
    
    public function __construct($usr_auth, $usr_id = 0)
    {
        $this->res_lines = array();
        $this->num_remain = 0;
        $this->output = '';
        $this->usr_authenticated = $usr_auth;
        $this->usr_id = $usr_id;
    }
    
    private function addOutput($text)
    {
        $this->output = $this->output . $text;
    }
    
    public function addMethod($method)
    {
        $this->res_lines[] = $method;
    }
    
    public function numRemaining($num)
    {
        $this->num_remain = $num;
    }
    
    private function renderLine($line_no, $line)
    {
        $this->addOutput('<div class="card"><div class="card-header" id="res_line_' . $line_no . '">');
        $this->addOutput('<div class="row">');
        
        // Column 1: toggle button that operates the carousel
        $this->addOutput('<div class="col-md-1 col-xl-1">');
        $this->addOutput('<span><button class="btn btn-sm btn-light collapsed" id="res_btn_' . $line_no . '" type="button" data-toggle="collapse" ');
        $this->addOutput('data-target="#res_coll_' . $line_no . '" aria-expanded="false" aria-controls="res_coll_' . $line_no . '">');
        $this->addOutput('<div id="res_tgl_' . $line_no . '"><i id="res_img_' . $line_no . '" class="fa fa-caret-right"></i></div></button></div>'); // class="col-md-1 col-xl-1"
        
        // Column 2: Method Name
        $this->addOutput('<div class="col-md-4 col-xl-4"><span class="badge badge-light"><h5>' . htmlentities($line->mth_name) . '</h5></span></div>');
        
        // Column 3: Average Rating
        $rating = round($line->mth_rtg_avg, 1);
        $this->addOutput('<div class="col-md-2 col-xl-2">Bewertung: ');
        if ($rating < 2.2)
        {
             $this->addOutput('<span class="badge badge-danger">');
        }
        else
        {
            if ($rating < 3.8)
            {
                 $this->addOutput('<span class="badge badge-warning">');
            }
            else
            {
                 $this->addOutput('<span class="badge badge-success">');
            }
        }
        for($num_stars = 1; $num_stars <= $rating; $num_stars++)
        {
             $this->addOutput('<i class="fa fa-star"></i>');    
        }
        for (; $num_stars <= 5; $num_stars++)
        {
             $this->addOutput('<i class="fa fa-star-o"></i>');    
        }
        $this->addOutput('</span></div>'); 

        // Column 4: number of downloads
        $this->addOutput('<div class="col-md-2 col-xl-2">Downloads: <span class="badge badge-primary">');
        $this->addOutput($line->mth_dnl_cnt);
        $this->addOutput('</span></div>');

        // Column 5: Upload date
        $this->addOutput('<div class="col-md-2 col-xl-2">Erstellt: <span>');
        $this->addOutput(substr($line->mth_create_tm, 0, 10));
        $this->addOutput('</span></div>');
        
        $this->addOutput('</span></div></div>'); //  span + row + card-header

        // Method details (card body with 1 row with table with body only)
        $this->addOutput('<div class="collapse" id="res_coll_' . $line_no . '" aria-labelledby="res_line_' . $line_no . '" data-parent="#mth_result">');
        $this->addOutput('<div class="card-body"><div class="row"><table class="table table-sm"><tbody>');
        
        // Table row 1: method summary
        $this->addOutput('<tr><th scope="row" class="table-secondary">Beschreibung</td><td colspan=5><span>' . htmlentities($line->mth_summary) . '</span></th></tr>');

        // Table row 2: field headers
        $this->addOutput('<tr><th scope="col" class="table-secondary">Unterrichtsfach</th>');
        $this->addOutput('<th scope="col" class="table-secondary">Fachbereich</th>');
        $this->addOutput('<th scope="col" class="table-secondary">Vorbereitungszeit</th>');
        $this->addOutput('<th scope="col" class="table-secondary">Durchf&uuml;hrungszeit</th>');
        $this->addOutput('<th scope="col" class="table-secondary">Jahrgang</th>');
        $this->addOutput('<th scope="col" class="table-secondary">AutorIn</th></tr>');

        // Table row 3: field values (subject, subject area, preparation time, execution time, age group, main author)
        $this->addOutput('<tr><td><span>' . htmlentities($line->mth_subject_txt) . '</span></td>');
        $this->addOutput('<td><span>' . htmlentities($line->mth_subj_area_txt) . '</span></td>');
        $this->addOutput('<td><span>' . htmlentities($line->mth_prep_tm_txt) . '</span></td>');
        $this->addOutput('<td><span>' . htmlentities($line->mth_exec_tm_txt) . '</span></td>');
        $this->addOutput('<td><span>' . htmlentities($line->mth_age_grp_txt) . '</span></td>');
         $this->addOutput('<td><span>' . htmlentities($line->mth_authors_arr[0]) . '</span></td></tr>');

        // Table row 4: field values (social form, phase) + download button
        $this->addOutput('<tr><td colspan="2">');
        $phase_txt = array('E' => 'Einstieg', 'I' => 'Information', 'S' => 'Sicherung', 'A' => 'Aktivierung');
        foreach($line->mth_phase_arr as $phase)
        {
            $this->addOutput('<span class="badge badge-secondary badge-pill badge-sm">' . $phase_txt[$phase] . '</span>');
        }
        $this->addOutput('</td><td colspan="2">');
    
        $soc_txt = array('E' => 'Einzelarbeit', 'P' => 'Partnerarbeit', 'G' => 'Gruppenarbeit', 'K' => 'Klassenplenum');
        foreach($line->mth_soc_form_arr as $soc_form)
        {
            $this->addOutput('<span class="badge badge-secondary badge-pill badge-sm">' . $soc_txt[$soc_form] . '</span>');
        }
        $this->addOutput('</td><td colspan="2">');
        $this->addOutput('<div class="btn-toolbar" role="toolbar">');
        
        // Button launching view ratings overlay
        if ($line->mth_rtg_cnt > 0)
        {
            $this->addOutput('<div class="btn-group mr-2" role="group">');
            $this->addOutput('<button id="rating_for_' . $line->mth_id . '" type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#ratingDetailModal" ');
            $this->addOutput('data-mthid="' . $line->mth_id . '" data-mthname="' . $line->mth_name . '">');
            $this->addOutput('Bewertungen</button></div>');
        }

        $this->addOutput('<div class="btn-group mr-2" role="group">');
        $this->addOutput('<a href="/mbx/ctrl/mth_download.php?mth_id=' . $line->mth_id . '&file_guid=' . $line->mth_file_guid . '" download="' . $line->mth_file_name);
        $this->addOutput('" class="btn btn-primary btn-sm" role="button">');
        $this->addOutput('Download <i class="fa fa-download" aria-hidden="true"></i></a></div>');
        
        $this->addOutput('<div class="btn-group mr-2" role="group">');
        $this->addOutput('<button id="new_file_for_' . $line->mth_id . '" type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploadFileModal" ');
        $this->addOutput('data-mthid="' . $line->mth_id . '" data-mthname="' . $line->mth_name . '">');
        $this->addOutput('Upload <i class="fa fa-upload" aria-hidden="true"></i></button></div>');

        $this->addOutput('<div class="btn-group mr-2" role="group">');
        $this->addOutput('<button id="delete_' . $line->mth_id . '" type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#deleteMethodModal" ');
        $this->addOutput('data-mthid="' . $line->mth_id . '" data-mthname="' . $line->mth_name . '">');
        $this->addOutput('L&ouml;schen</button></div>');

        $this->addOutput('</div>');
        $this->addOutput('</td></tr></tbody></table></div>'); // row (detail)
        $this->addOutput('</div></div></div>');  // card-body + collapse + card
    }
    
    public function renderHtml()
    {
        $line_no = 1;
        foreach($this->res_lines as $line)
        {
            $this->renderLine($line_no, $line);
        }
        
        if ($this->num_remain > 0)
        {
            if ($this->num_remain == 1)
            {
                $this->addOutput('<div class="card"><div class="card-header" id="rest">');
                $this->addOutput('1 weiterer Datensatz vorhanden &hellip;</div></div>');
            }
            else
            {
                $this->addOutput('<div class="card"><div class="card-header" id="rest">');
                $this->addOutput($res_view->getNumRemaining());
                $this->addOutput(' weitere Datens&auml;tze vorhanden &hellip;</div></div>');
            }
        }
        else
        {
            $this->addOutput('<div class="card"><div class="card-header" id="rest">');
            $this->addOutput('Keine weiteren Datens&auml;tze vorhanden &hellip;</div></div>');
        }
    }
    
    public function outputHtml()
    {
        echo $this->output;
    }
}
?>