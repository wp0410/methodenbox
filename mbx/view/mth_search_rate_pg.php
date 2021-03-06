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
include_once '../model/mth_method_view_pg.php';
include_once '../view/mth_result_base_pg.php';

class MethodSearchRatingView extends MethodSearchViewBase
{
    protected function renderLine($line_no, $line)
    {
        $this->addOutput('<div class="card"><div class="card-header" id="res_line_' . $line_no . '">');
        $this->addOutput('<div class="row">');
        
        // Column 1: toggle button that operates the carousel
        $this->addOutput('<div class="col-md-1 col-xl-1">');
        $this->addOutput('<span><button class="btn btn-sm btn-light collapsed" id="res_btn_' . $line_no . '" type="button" data-toggle="collapse" ');
        $this->addOutput('data-target="#res_coll_' . $line_no . '" aria-expanded="false" aria-controls="res_coll_' . $line_no . '">');
        $this->addOutput('<div id="res_tgl_' . $line_no . '"><i id="res_img_' . $line_no . '" class="fa fa-caret-right"></i></div></button></div>'); // class="col-md-1 col-xl-1"
        
        // Column 2: Method Name
        // $this->addOutput('<div class="col-md-4 col-xl-4"><span class="badge badge-light"><h5>' . htmlentities($line->mth_name) . '</h5></span></div>');
        $this->addOutput('<div class="col-md-4 col-xl-4 text-truncate"><span class="badge badge-light"><h5>' . $line->mth_name . '</h5></span></div>');
        
        // Column 3: 
        $this->addOutput('<div class="col-md-2 col-xl-2">');
        $this->addOutput('</div>'); 

        // Column 4: 
        $this->addOutput('<div class="col-md-2 col-xl-2">');
        $this->addOutput('</div>');

        // Column 5: Upload date
        $this->addOutput('<div class="col-md-2 col-xl-2">Erstellt: <span>');
        $this->addOutput(substr($line->mth_create_tm, 0, 10));
        $this->addOutput('</span></div>');
        
        $this->addOutput('</span></div></div>'); //  span + row + card-header

        // Method details (card body with 1 row with table with body only)
        $this->addOutput('<div class="collapse" id="res_coll_' . $line_no . '" aria-labelledby="res_line_' . $line_no . '" data-parent="#mth_result">');
        $this->addOutput('<div class="card-body"><div class="row"><table class="table table-sm"><tbody>');
        
        // Table row 1: method summary
        // $this->addOutput('<tr><th scope="row" class="table-secondary">Beschreibung</td><td colspan=5><span>' . htmlentities($line->mth_summary) . '</span></th></tr>');
        $this->addOutput('<tr><th scope="row" class="table-secondary">Beschreibung</td><td colspan=5><span>' . $line->mth_summary . '</span></th></tr>');

        // Table row 2: field headers
        $this->addOutput('<tr><th scope="col" class="table-secondary">Unterrichtsfach / Phase</th>');
        $this->addOutput('<th scope="col" class="table-secondary">Fachbereich / Sozialform</th>');
        $this->addOutput('<th scope="col" class="table-secondary">Vorbereitungszeit</th>');
        $this->addOutput('<th scope="col" class="table-secondary">Durchf&uuml;hrungszeit</th>');
        // $this->addOutput('<th scope="col" class="table-secondary">Jahrgang</th>');
        $this->addOutput('<th scope="col" class="table-secondary">Art der Methode</th>');
        $this->addOutput('<th scope="col" class="table-secondary">AutorIn</td></th>');

        // Table row 3: field values (subject, subject area, preparation time, execution time, age group, main author)
        $this->addOutput('<tr><td><span>' . $line->mth_subject_txt . '</span></td>');
        $this->addOutput('<td><span>' . $line->mth_subj_area_txt . '</span></td>');
        $this->addOutput('<td><span>' . $line->mth_prep_tm_txt . '</span></td>');
        $this->addOutput('<td><span>' . $line->mth_exec_tm_txt . '</span></td>');
        //$this->addOutput('<td><span>' . $line->mth_age_grp_txt . '</span></td>');
        $this->addOutput('<td><span>' . $line->mth_type_txt . '</span></td>');
		
        $this->addOutput('<td><span>' . $line->mth_authors_arr[0] . '</span></td></tr>');
        
        // Table row 4: field values (social form, phase) + download button
        $this->addOutput('<tr><td>');
        $elems_txt = array('E' => 'Anfangen', 'I' => 'Informieren', 'S' => 'Sicherung - Wissen Abfragen', 'W' => 'Sicherung - Wissen Anwenden', 'A' => 'Auflockerung');
        foreach($line->mth_elems_arr as $elem)
        {
            $this->addOutput('<span class="badge badge-dark badge-pill badge-sm">' . $elems_txt[$elem] . '</span>');
        }
        $this->addOutput('</td><td colspan="2">');
    
        $soc_txt = array('E' => 'Einzelarbeit', 'P' => 'Partnerarbeit', 'G' => 'Gruppenarbeit', 'K' => 'Klassenplenum');
        foreach($line->mth_soc_form_arr as $soc_form)
        {
            $this->addOutput('<span class="badge badge-dark badge-pill badge-sm">' . $soc_txt[$soc_form] . '</span>');
        }
        $this->addOutput('</td><td></td><td></td><td>');

        // Rating Button launching rating overlay
        $this->addOutput('<button id="rate_btn_' . $line->mth_id . '" type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#ratingModal" ');
        $this->addOutput('data-mthid="' . $line->mth_id . '" data-mthname="' . $line->mth_name . '">');
        $this->addOutput('Methode bewerten</button>');

        $this->addOutput('</td></tr></tbody></table></div>'); // row (detail)
        $this->addOutput('</div></div></div>');  // card-body + collapse + card
    }
}
?>