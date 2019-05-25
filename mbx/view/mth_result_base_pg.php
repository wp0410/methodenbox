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

class MethodSearchViewBase
{
    protected $output;
    protected $usr_authenticated;
    protected $mth_view;
    protected $max_pages;
    
    public function __construct($mth_view, $max_pages)
    {
        $this->mth_view = $mth_view;
        $this->output = '';
        $this->usr_authenticated = ($mth_view->usr_id > 0);
        $this->max_pages = $max_pages;
    }
    
    protected function addOutput($text)
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
    
    protected function renderPageEntries($first_page_no, $last_page_no, $active_page_no)
    {
        $cur_page = $first_page_no;
        while($cur_page <= $last_page_no)
        {
            if ($cur_page == $active_page_no)
            {
                $this->addOutput('<li class="page-item active"><a class="page-link" href="javascript:goto_page(\'' . $this->mth_view->getCacheId() . '\',' . $cur_page . ');">' . $cur_page . '</a></li>');
            }
            else
            {
                $this->addOutput('<li class="page-item"><a class="page-link" href="javascript:goto_page(\'' . $this->mth_view->getCacheId() . '\',' . $cur_page . ');">' . $cur_page . '</a></li>');
            }
            $cur_page += 1;
        }
    }
    
    protected function renderPagination()
    {
        $num_pages = ceil($this->mth_view->total_rows / $this->mth_view->lines_per_page);
        $cur_page = $this->mth_view->current_page;
        
        if ($num_pages == 0)
        {
            $this->addOutput('<div class="card"><div class="card-body">');
            $this->addOutput('<span><i class="fa fa-info-circle fa-2x" aria-hidden="true"></i>&nbsp;Keine Daten vorhanden ...</span>');
            $this->addOutput('</div>');
            return;
        }

        if ($num_pages == 1)
        {
            return;
        }
        
        $this->addOutput('<div class="card"><div class="card-body">');
        
        $this->addOutput('<div class="row"><div class="col col-md-10 col-xl-10>');
        
        $this->addOutput('<nav aria-label="ResultPagination">');
        $this->addOutput('<ul class="pagination justify-content-center">');
        
        // GOTO first page
        $target_page = 1;
        $ref_page = '#';
        if ($cur_page == $target_page)
        {
            $this->addOutput('<li class="page-item disabled">');
            $ref_page = '#';
        }
        else
        {
            $this->addOutput('<li class="page-item">');
            $ref_page = 'javascript:goto_page(\'' . $this->mth_view->getCacheId() . '\',' . $target_page . ');';
        }
        $this->addOutput('<a class="page-link" href="' . $ref_page . '" aria-label="First"><i class="fa fa-angle-double-left" aria-hidden="true"></i></a></li>');

        // GOTO previous page
        $target_page = $cur_page - 1;
        if ($target_page <= 0)
        {
            $this->addOutput('<li class="page-item disabled">');
            $ref_page = '#';
        }
        else
        {
            $this->addOutput('<li class="page-item">');
            $ref_page = 'javascript:goto_page(\'' . $this->mth_view->getCacheId() . '\',' . $target_page . ');';
        }
        $this->addOutput('<a class="page-link" href="' . $ref_page . '" aria-label="Previous"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>');
        
        // Create maximum pagination entries
        if ($num_pages > $this->max_pages)
        {
            $offset = floor($this->max_pages / 2);
            
            if (($cur_page - $offset) < 1)
            {
                $first_page = 1;
                $last_page = $this->max_pages;
            }
            else 
            {
                if (($cur_page + $offset) < $num_pages)
                {
                    $first_page = $cur_page - $offset;
                    $last_page = $cur_page + $offset;
                }
                else 
                {
                    $first_page = $num_pages - $this->max_pages;
                    $last_page = $num_pages;
                }
            }
            
            if ($first_page > 1)
            {
                // Add a disabled entry indicating that there are more pages than can be displayed
                $this->addOutput('<li class="page-item disabled"><a class="page-link" href="#"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></a></li>');
            }
            
            $this->renderPageEntries($first_page, $last_page, $cur_page);
            
            if ($last_page < $num_pages)
            {
                // Add a disabled entry indicating that there are more pages than can be displayed
                $this->addOutput('<li class="page-item disabled"><a class="page-link" href="#"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></a></li>');
            }
        }
        else
        {
            $this->renderPageEntries(1, $num_pages, $cur_page);
        }

        // GOTO next page
        $target_page =  $cur_page + 1;
        if ($target_page > $num_pages)
        {
            $this->addOutput('<li class="page-item disabled">');
            $ref_page = '#';
        }
        else
        {
            $this->addOutput('<li class="page-item">');
            $ref_page = 'javascript:goto_page(\'' . $this->mth_view->getCacheId() . '\',' . $target_page . ');';
        }
        $this->addOutput('<a class="page-link" href="' . $ref_page . '" aria-label="Last"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>');

        // GOTO last page
        $target_page = $num_pages;
        if ($cur_page >= $target_page)
        {
            $this->addOutput('<li class="page-item disabled">');
            $ref_page = '#';
        }
        else
        {
            $this->addOutput('<li class="page-item">');
            $ref_page = 'javascript:goto_page(\'' . $this->mth_view->getCacheId() . '\',' . $target_page . ');';
        }
        $this->addOutput('<a class="page-link" href="' . $ref_page . '" aria-label="Last"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a></li>');
        $this->addOutput('</ul></nav>');
        
        $this->addOutput('</div><div class="col col-md-2 col-xl-2">');
        $this->addOutput('<h5>Treffer:&nbsp<span class="badge badge-primary">' . $this->mth_view->total_rows . '</span></h5>');
        
        $this->addOutput('</div></div>'); // col / row
        $this->addOutput('</div></div>'); // card-body / card
    }
	
    public function outputHtml()
    {
        echo $this->output;
    }
}
?>