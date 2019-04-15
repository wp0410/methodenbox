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
include_once '../model/usr_account_view.php';

class UserAccountAdminResult
{
    protected $output;
    protected $usr_view;
    protected $max_pages;
    
    public function __construct($usr_view, $max_pages)
    {
        $this->usr_view = $usr_view;
        $this->output = '';
        $this->max_pages = $max_pages;
    }
    
    protected function addOutput($text)
    {
        $this->output = $this->output . $text;
    }
    
    public function renderHtml()
    {
        $this->renderPagination();
        
        $this->addOutput('<table class="table"><thead class="thead-light"><tr>');
        $this->addOutput('<th scope="col">ID</th>');
        $this->addOutput('<th scope="col">Name</th>');
        $this->addOutput('<th scope="col">E-Mail Adresse</th>');
        $this->addOutput('<th scope="col">Registriert am</th>');
        $this->addOutput('<th scope="col">Letzte Anmeldung</th>');
        $this->addOutput('<th scope="col">Status</th>');
        $this->addOutput('<th scope="col">Berechtigungen</th>');
        $this->addOutput('</tr></thead><tbody>');
        
        $line_no = 1;
        foreach($this->usr_view->lines as $line)
        {
            $this->renderLine($line_no, $line);
            $line_no++;
        }
        
        $this->addOutput('</tbody></table>');
    }
    
    protected function renderLine($line_no, $line)
    {
        $this->addOutput('<tr><td>' . $line->usr_id . '</td><td>' . $line->usr_fst_name . ' ' . $line->usr_lst_name . '</td><td>' . $line->usr_email . '</td>');
        $this->addOutput('<td>' . substr($line->usr_reg_date,0,10) . '</td><td>' . substr($line->usr_login_date,0,10) . '</td><td>');
        switch($line->usr_status)
        {
            case 0:
                $this->addOutput('NEU');
                break;
            case 1:
                $this->addOutput('AKTIV');
                break;
            case 2:
                $this->addOutput('GESPERRT');
                break;
        }
        $this->addOutput('</td><td>');
        
        if ($line->role_client > 0)
        {
            $this->addOutput('<i class="fa fa-user-o fa-2x" aria-hidden="true"></i>&nbsp;&nbsp;');
        }
        if ($line->role_upload > 0)
        {
            $this->addOutput('<i class="fa fa-cloud-upload fa-2x" aria-hidden="true"></i>&nbsp;&nbsp;');
        }
        if ($line->role_admin > 0)
        {
            $this->addOutput('<i class="fa fa-cog fa-2x" aria-hidden="true"></i>&nbsp;&nbsp;');
        }
        
        $this->addOutput('</td></tr>');
    }

    protected function renderPageEntries($first_page_no, $last_page_no, $active_page_no)
    {
        $cur_page = $first_page_no;
        while($cur_page <= $last_page_no)
        {
            if ($cur_page == $active_page_no)
            {
                $this->addOutput('<li class="page-item active"><a class="page-link" href="javascript:goto_page(\'' . $this->usr_view->getCacheId() . '\',' . $cur_page . ');">' . $cur_page . '</a></li>');
            }
            else
            {
                $this->addOutput('<li class="page-item"><a class="page-link" href="javascript:goto_page(\'' . $this->usr_view->getCacheId() . '\',' . $cur_page . ');">' . $cur_page . '</a></li>');
            }
            $cur_page += 1;
        }
    }
    protected function renderPagination()
    {
        $num_pages = ceil($this->usr_view->total_rows / $this->usr_view->lines_per_page);
        $cur_page = $this->usr_view->current_page;
        
        if ($num_pages == 0)
        {
            $this->addOutput('<div class="card"><div class="card-body">');
            $this->addOutput('<span><i class="fa fa-info-circle fa-2x" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;Keine Daten vorhanden ...</span>');
            $this->addOutput('</div>');
            return;
        }
        
        if ($num_pages == 1)
        {
            return;
        }
        
        $this->addOutput('<div class="card"><div class="card-body">');
        
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
            $ref_page = 'javascript:goto_page(\'' . $this->usr_view->getCacheId() . '\',' . $target_page . ');';
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
            $ref_page = 'javascript:goto_page(\'' . $this->usr_view->getCacheId() . '\',' . $target_page . ');';
        }
        $this->addOutput('<a class="page-link" href="' . $ref_page . '" aria-label="Previous"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>');
        
        // Create maximum pagination entries
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
        $target_page =  $cur_page + 1;
        if ($target_page > $num_pages)
        {
            $this->addOutput('<li class="page-item disabled">');
            $ref_page = '#';
        }
        else
        {
            $this->addOutput('<li class="page-item">');
            $ref_page = 'javascript:goto_page(\'' . $this->usr_view->getCacheId() . '\',' . $target_page . ');';
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
            $ref_page = 'javascript:goto_page(\'' . $this->usr_view->getCacheId() . '\',' . $target_page . ');';
        }
        $this->addOutput('<a class="page-link" href="' . $ref_page . '" aria-label="Last"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a></li>');
        
        $this->addOutput('</ul></nav></div></div>'); // card-body / card
    }
    
    public function outputHtml()
    {
        echo $this->output;
    }
    
    
}


?>