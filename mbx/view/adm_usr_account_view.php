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
        if (! $this->renderPagination())
        {
            return;
        }
        
        $this->addOutput('<table class="table table-striped"><thead class="thead-dark"><tr>');
        $this->addOutput('<th scope="col">ID</th>');
        $this->addOutput('<th scope="col">Name</th>');
        $this->addOutput('<th scope="col">E-Mail Adresse</th>');
        $this->addOutput('<th scope="col">Registriert am</th>');
        $this->addOutput('<th scope="col">Letzte Anmeldung</th>');
        $this->addOutput('<th scope="col">Status</th>');
        $this->addOutput('<th scope="col">Rolle</th>');
        $this->addOutput('<th scope="col">Aktion</th>');
		$this->addOutput('<th scope="col">Rechte</td>');
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
        $add_perm = false;
        $del_perm = false;
        
        $this->addOutput('<tr><td>' . $line->usr_id . '</td><td>' . $line->usr_fst_name . ' ' . $line->usr_lst_name . '</td><td>' . $line->usr_email . '</td>');
        $this->addOutput('<td>' . substr($line->usr_reg_date,0,10) . '</td><td>' . substr($line->usr_login_date,0,16) . '</td><td>');
        switch($line->usr_status)
        {
            case 0:
				$this->addOutput('<h5><span class="badge badge-primary">');
                $this->addOutput('NEU');
                break;
            case 1:
				$this->addOutput('<h5><span class="badge badge-success">');
                $this->addOutput('AKTIV');
                break;
            case 2:
				$this->addOutput('<h5><span class="badge badge-danger">');
                $this->addOutput('GESPERRT');
                break;
        }
		$this->addOutput('</span></h5>');
        $this->addOutput('</td><td>');
		$this->addOutput('<h4><span class="badge badge-info badge-lg"><i class="fa ' . $line->role_symbol . '" aria-hidden="true"></i>&nbsp;&nbsp;' . $line->role_description . '</span></h4>');
        $this->addOutput('</td><td>');
        
        // Administrator actions
        switch ($line->usr_status)
        {
            case 0:
                $min_age = Helpers::dateTimeString(time() - 7 * 86400);
                if ($line->usr_reg_date < $min_age)
                {
                    // Unconfirmed user accounts can be deleted after one week:
                    $this->addOutput('<button type="button" class="btn btn-danger btn-sm btn-block" data-toggle="modal" data-target="#usrDeleteModal" ');
                    $this->addOutput('id="usr_del_' . $line->usr_id . '" data-usrid="' . $line->usr_id . '" data-currid="' . $this->usr_view->usr_id  . '" ');
                    $this->addOutput('data-usrname="' . $line->usr_fst_name . ' ' . $line->usr_lst_name . '(' . $line->usr_email . ')" >');
                    $this->addOutput('L&ouml;schen</button>');
                }
                else 
                {
                    // Within one week, unconfirmed accounts can be activated
                    $this->addOutput('<button type="button" class="btn btn-success btn-sm btn-block" data-toggle="modal" data-target="#usrModifyModal" ');
                    $this->addOutput('id="usr_act_' . $line->usr_id . '" data-usrid="' . $line->usr_id . '" data-currid="' . $this->usr_view->usr_id . '" ');
                    $this->addOutput('data-usrname="' . $line->usr_fst_name . ' ' . $line->usr_lst_name . '(' . $line->usr_email . ')" data-fn="USR_ACT" >');
                    $this->addOutput('Aktivieren</button>');
                }
                break;
            case 1:
                // Active user accounts can be locked:
                $this->addOutput('<button type="button" class="btn btn-warning btn-sm btn-block" data-toggle="modal" data-target="#usrModifyModal" ');
                $this->addOutput('id="usr_lock_' . $line->usr_id . '" data-usrid="' . $line->usr_id . '" data-currid="' . $this->usr_view->usr_id . '" ');
                $this->addOutput('data-usrname="' . $line->usr_fst_name . ' ' . $line->usr_lst_name . '(' . $line->usr_email . ')" data-fn="USR_LCK" >');
                $this->addOutput('Sperren</button>');
                break;
            case 2:
                // Locked user accounts can be unlocked:
                $this->addOutput('<button type="button" class="btn btn-success btn-sm btn-block" data-toggle="modal" data-target="#usrModifyModal" ');
                $this->addOutput('id="usr_lock_' . $line->usr_id . '" data-usrid="' . $line->usr_id . '" data-currid="' . $this->usr_view->usr_id . '" ');
                $this->addOutput('data-usrname="' . $line->usr_fst_name . ' ' . $line->usr_lst_name . '(' . $line->usr_email . ')" data-fn="USR_UNL" >');
                $this->addOutput('Sperre aufheben</button>');
                break;
        }
        
        // Change permissions
        $this->addOutput('</td><td>');
        $this->addOutput('<button type="button" class="btn btn-primary btn-sm btn-block" data-toggle="modal" data-target="#usrPermissionModal" ');
        $this->addOutput('id="usr_perm_' . $line->usr_id . '"' . 'data-usrid="' . $line->usr_id . '" data-currid="' . $this->usr_view->usr_id . '" ');
        $this->addOutput('data-usrname="' . $line->usr_fst_name . ' ' . $line->usr_lst_name . ' (' . $line->usr_email . ')" ');
        $this->addOutput('data-permit="' . $line->role_name . '">&Auml;ndern</button>');
        // $this->addOutput('</td><td>');
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
            return false;
        }
        
        if ($num_pages == 1)
        {
            return true;
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
        
        return true;
    }
    
    public function outputHtml()
    {
        echo $this->output;
    }
}


?>