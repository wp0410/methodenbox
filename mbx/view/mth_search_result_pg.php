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
include_once '../model/mth_method_view_pg.php';

class MethodSearchResultView
{
    private $output;
    private $usr_authenticated;
    private $mth_view;
    private $max_pages;
    
    public function __construct($usr_auth, $mth_view, $max_pages)
    {
        $this->mth_view = $mth_view;
        $this->output = '';
        $this->usr_authenticated = $usr_auth;
        $this->max_pages = $max_pages;
    }
    
    private function addOutput($text)
    {
        $this->output = $this->output . $text;
    }
    
    public function renderHtml()
    {
        $this->renderPagination();
        
        $this->addOutput('<div class="accordion" id="mth_result_lines">');
        
        $line_no = 1;
        foreach($this->mth_view->lines as $line)
        {
            $this->renderLine($line_no, $line);
            $line_no++;
        }
        
        $this->addOutput('</div>');
    }
    
    public function renderPagination()
    {
        $num_pages = ($this->mth_view->total_rows / $this->mth_view->lines_per_page) + 1;
        $cur_page = $this->mth_view->current_page;
        
        if ($num_pages == 1)
        {
            return;
        }
        
        $this->addOutput('<div class="card"><div class="card-body">');
        $this->addOutput('<form id="frm_hidden"><input type="hidden" id="stmt_cch" name="stm_cch" value="' . $this->mth_view->getCacheId() . '"></form>');
        $this->addOutput('<span class="badge badge-warning">' . $num_pages . '</span>');

        $this->addOutput('<nav aria-label="ResultPagination">');
        $this->addOutput('<ul class="pagination justify-content-center">');
        
        // GOTO first page
        $target_page = 1;
        if ($cur_page == $target_page)
        {
            $this->addOutput('<li class="page-item disabled">');
        }
        else
        {
            $this->addOutput('<li class="page-item">');
        }
        $this->addOutput('<a class="page-link" href="javascript:goto_page(' . $target_page . ');" aria-label="First"><i class="fa fa-angle-double-left" aria-hidden="true"></i></a></li>');

        // GOTO previous page
        $target_page = $cur_page - 1;
        if ($target_page > 0)
        {
            $this->addOutput('<li class="page-item disabled">');
        }
        else
        {
            $this->addOutput('<li class="page-item">');
        }
        $this->addOutput('<a class="page-link" href="javascript:goto_page(' . $target_page . ');" aria-label="Previous"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>');
        
        // Create 9 pagination entries
        if ($num_pages > $this->max_pages)
        {
            // Add a disabled entry indicating that there are more pages than can be displayed
            $this->addOutput('<li class="page-item disabled"><a class="page-link" href="#"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></a></li>');

            $offset = $this->max_pages / 2;
            $this->renderPageEntries($cur_page - $offset, $cur_page + $offset, $cur_page);
            
            // Add a disabled entry indicating that there are more pages than can be displayed
            $this->addOutput('<li class="page-item disabled"><a class="page-link" href="#"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></a></li>');
        }
        else
        {
            $this->renderPageEntries(1, $num_pages, $cur_page);
        }

        // GOTO next page
        $target_page = $num_pages;
        if ($cur_page == $target_page)
        {
            $this->addOutput('<li class="page-item disabled">');
        }
        else
        {
            $this->addOutput('<li class="page-item">');
        }
        $this->addOutput('<a class="page-link" href="javascript:goto_page(' . $target_page . ');" aria-label="Last"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>');

        // GOTO last page
        $target_page = $cur_page + 1;
        if ($target_page < $num_pages)
        {
            $this->addOutput('<li class="page-item disabled">');
        }
        else
        {
            $this->addOutput('<li class="page-item">');
        }
        $this->addOutput('<a class="page-link" href="javascript:goto_page(' . $target_page . ');" aria-label="Last"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a></li>');

        $this->addOutput('</ul></nav></div></div>'); // card-body / card
    }
    
    private function renderPageEntries($first_page_no, $last_page_no, $active_page_no)
    {
        $cur_page = $first_page_no;
        while($cur_page <= $last_page_no)
        {
            if ($cur_page == $active_page_no)
            {
                $this->addOutput('<li class="page-item active"><a class="page-link" href="javascript:goto_page(' . $cur_page . ');">1</a></li>');
            }
            else
            {
                $this->addOutput('<li class="page-item"><a class="page-link" href="javascript:goto_page(' . $cur_page . ');">1</a></li>');
            }
            $cur_page += 1;
        }
    }
    
    private function renderLine($line_no, $line)
    {
        $this->addOutput('<div class="card"><div class="card-header" id="res_line_' . $line_no . '">');
        $this->addOutput('<div class="row">');
        
        // Column 1: toggle button that operates the accordion
        $this->addOutput('<div class="col-md-1 col-xl-1">');
        // $this->addOutput('<span><button class="btn btn-sm btn-light collapsed" id="res_btn_' . $line_no . '" type="button" data-toggle="collapse" ');
        $this->addOutput('<button class="btn btn-sm btn-light" id="res_btn_' . $line_no . '" data-toggle="collapse" ');
        
        if ($line_no == 1)
        {
            $this->addOutput('data-target="#res_coll_' . $line_no . '" aria-expanded="true" aria-controls="res_coll_' . $line_no . '">');
        }
        else
        {
            $this->addOutput('data-target="#res_coll_' . $line_no . '" aria-expanded="false" aria-controls="res_coll_' . $line_no . '">');
            
        }
        $this->addOutput('<div id="res_tgl_' . $line_no . '"><i id="res_img_' . $line_no . '" class="fa fa-caret-right"></i></div></button></div>'); // class="col-md-1 col-xl-1"
        
        // Column 2: Method Name
        $this->addOutput('<div class="col-md-4 col-xl-4"><span class="badge badge-light"><h5>' . htmlentities($line->mth_name) . '</h5></span></div>');
        
        // Column 3: Average Rating
        $rating = round($line->mth_rtg_avg, 1);
        $this->addOutput('<div class="col-md-2 col-xl-2">Bewertung: ');
        if ($rating < 2.2)
        {
            if ($rating == 0)
            {
                $this->addOutput('<span class="badge badge-light">');
            }
            else
            {
                $this->addOutput('<span class="badge badge-danger">');
            }
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
        
        // $this->addOutput('</span></div></div>'); //  span + row + card-header
        $this->addOutput('</div></div>'); //  row + card-header

        // Method details (card body with 1 row with table with body only)
        $this->addOutput('<div class="collapse" id="res_coll_' . $line_no . '" aria-labelledby="res_line_' . $line_no . '" data-parent="#mth_result">');
        $this->addOutput('<div class="card-body"><div class="row"><table class="table table-sm"><tbody>');
        
        // Table row 1: method summary
        $this->addOutput('<tr><th scope="row" class="table-secondary">Beschreibung</th><td colspan=5><span>' . htmlentities($line->mth_summary) . '</span></th></tr>');

        // Table row 2: field headers
        $this->addOutput('<tr><th scope="col" class="table-secondary">Unterrichtsfach</th>');
        $this->addOutput('<th scope="col" class="table-secondary">Fachbereich</th>');
        $this->addOutput('<th scope="col" class="table-secondary">Vorbereitungszeit</th>');
        $this->addOutput('<th scope="col" class="table-secondary">Durchf&uuml;hrungszeit</th>');
        $this->addOutput('<th scope="col" class="table-secondary">Jahrgang</th>');
        $this->addOutput('<th scope="col" class="table-secondary">AutorIn</th></tr>');

        // Table row 3: field values (subject, subject area, preparation time, execution time, age group, main author)
        $this->addOutput('<tr><td><span>' . $line->mth_subject_txt . '</span></td>');
        $this->addOutput('<td><span>' . $line->mth_subj_area_txt . '</span></td>');
        $this->addOutput('<td><span>' . $line->mth_prep_tm_txt . '</span></td>');
        $this->addOutput('<td><span>' . $line->mth_exec_tm_txt . '</span></td>');
        $this->addOutput('<td><span>' . $line->mth_age_grp_txt . '</span></td>');
        $this->addOutput('<td><span>' . $line->mth_authors_arr[0] . '</span></td></tr>');

        // Table row 4: field values (social form, phase) + download button
        $this->addOutput('<tr><td>');
        $phase_txt = array('E' => 'Einstieg', 'I' => 'Information', 'S' => 'Sicherung', 'A' => 'Aktivierung');
        foreach($line->mth_phase_arr as $phase)
        {
            $this->addOutput('<span class="badge badge-secondary badge-pill badge-sm">' . $phase_txt[$phase] . '</span>');
        }
        $this->addOutput('</td><td colspan="3">');
    
        $soc_txt = array('E' => 'Einzelarbeit', 'P' => 'Partnerarbeit', 'G' => 'Gruppenarbeit', 'K' => 'Klassenplenum');
        foreach($line->mth_soc_form_arr as $soc_form)
        {
            $this->addOutput('<span class="badge badge-secondary badge-pill badge-sm">' . $soc_txt[$soc_form] . '</span>');
        }
        $this->addOutput('</td><td>');
        
        if (! $this->usr_authenticated)
        {
            $this->addOutput('</td><td>');
        }
        
        // Button launching "view ratings" overlay
        if ($line->mth_rtg_cnt > 0)
        {
            $this->addOutput('<button id="rating_for_' . $line->mth_id . '" type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#ratingDetailModal" ');
            $this->addOutput('data-mthid="' . $line->mth_id . '" data-mthname="' . $line->mth_name . '">');
            $this->addOutput('Bewertungen</button>');
        }
        $this->addOutput('</td><td>');
        
        if ($this->usr_authenticated)
        {
            $this->addOutput('<a href="/mbx/ctrl/mth_download.php?mth_id=' . $line->mth_id . '&file_guid=' . $line->mth_file_guid . '" download="' . $line->mth_file_name);
            $this->addOutput('" class="btn btn-primary btn-sm" role="button">');
            $this->addOutput('Download <i class="fa fa-download" aria-hidden="true"></i></a>');
        }

        $this->addOutput('</td></tr></tbody></table></div>'); // row (detail)
        $this->addOutput('</div></div></div>');  // card-body + collapse + card
    }
    
    public function outputHtml()
    {
        echo $this->output;
    }
}
?>