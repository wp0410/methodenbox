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
include_once '../model/sql_connection.php';
include_once '../model/usr_session.php';
include_once '../model/mth_selection.php';
include_once '../view/frm_common.php';
include_once '../model/app_warning.php';

set_private_warning_handler();

session_start();
$db_conn = DatabaseConnection::get_connection();
$usr_session = new UserSession($db_conn);
$current_usr_id = -1;
if (empty($_SESSION) || empty($_SESSION['user']))
{
    $current_usr_id = -1;
}
else
{
    $res = $usr_session->validateSession($_SESSION['user']);
    
    if (! $res->isOK())
    {
        session_destroy();
    }
    else
    {
        $_SESSION['user'] = array('sid' => $usr_session->getId(), 'uid' => $usr_session->getUsrId(), 'hash' => $usr_session->getSessionHash());
        $current_usr_id = $usr_session->getUsrId();
    }
}
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <title><?php echo GlobalParameter::$applicationConfig['applicationTitle'] . ' - Methode Suchen';?></title>
        <?php FormElements::styleSheetRefs(); ?>
    </head>
    <body>
        <?php FormElements::topNavigationBar('MTH.SRCH', $usr_session->isAuthenticated(), $usr_session->getRole()); ?>
        <?php FormElements::bottomNavigationBar('MTH.SRCH', $usr_session->isAuthenticated(), $usr_session->getRole()); ?>
        
        <div class="container-fluid">
           <div class="row row-fluid"><br></div>
           <div class="row row-fluid">
                <div class="col-md-12 col-xl-12">
                    <div class="alert alert-primary" role="alert"><center><h4>Methode Suchen</h4></center></div>
                    <!-- ?php if ($res->code != 0) { FormElements::showAlert($res->style, 'col-md-12 col-xl-12', $res->text); } ? -->
                </div>
            </div>
            <div class="row row-fluid"></div>
            <div class="row row-fluid">
                <div class="col-md-8 col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col col-md-4 col-xl-4">
                                    <span class="input-group-text">Ergebnis der Filterung</span>
                                </div>
                                <div class="col col-md-3 col-xl-3"></div>
                                <div class="col col-md-5 col-xl-5">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" for="mth_res_sort">Sortieren nach</label>
                                        </div>
                                        <select class="custom-select" id="mth_res_sort" name="mth_res_sort">
                                            <option value="SRT_RATE">Bewertung - beste zuerst</option>
                                            <option value="SRT_DATE">Erstelldatum - neueste zuerst</option>
                                            <option value="SRT_NDNL">Anzahl Downloads - meiste zuerst</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="accordion" id="mth_result">
                            </div>
                        </div>
                    </div>
                </div> <!-- col-md-8 col-xl-8 -->

                <div class="col-md-4 col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <span class="input-group-text">Filtern nach ...</span>
                        </div>
                        <div class="card-body">
                            <form id="mth_search" method="post" action="#" role="form">
                                <div class="row form-row">
                                    <div class="col-md-6 col-xl-6">
                                        <div class="form-group">
                                            <label for="mth_subject">Unterrichtsfach</label>
                                            <select class="form-control" id="mth_subject" name="mth_subject">
                                                <option></option>
                                                <?php 
                                                    foreach(MethodSelectionFactory::getSubjects() as $sub)
                                                    {
                                                        echo '<option value="' . $sub['VAL'] . '">' . $sub['NAME'] . '</option>';
                                                        // echo '<option>' . $sub['NAME'] . '</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div> <!-- form-group -->
                                    </div> <!-- col -->
                                    <div class="col-md-6 col-xl-6">
                                        <div class="form-group">
                                            <label for="mth_area">Fachbereich</label>
                                            <select class="form-control" id="mth_area" name="mth_area">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row form-row">
                                    <div class="col-md-6 col-xl-6">
                                        <div class="form-group">
                                            <label for="mth_prep_tm">Zeit Vorbereitung</label>
                                            <select class="form-control" id="mth_prep_tm" name="mth_prep_tm">
                                                <option></option>
                                                <?php
                                                    foreach(MethodSelectionFactory::getPrepTime() as $prep)
                                                    {
                                                        // echo '<option>' . $prep['NAME'] . '</option>';
                                                        echo '<option value="' . $prep['VAL'] . '">' . $prep['NAME'] . '</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div> <!-- form-group -->
                                    </div> <!-- col -->

                                    <div class="col-md-6 col-xl-6">
                                        <div class="form-group">
                                            <label for="mth_exec_tm">Zeit Durchf&uuml;hrung</label>
                                            <select class="form-control" id="mth_exec_tm" name="mth_exec_tm">
                                                <option></option>
                                                <?php
                                                    foreach(MethodSelectionFactory::getExecTime() as $exec)
                                                    {
                                                        // echo '<option>' .$exec['NAME'] . '</option>';
                                                        echo '<option value="' . $exec['VAL'] . '">' . $exec['NAME'] . '</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div> <!-- form-group -->
                                    </div> <!-- col -->
                                </div> <!-- row -->
                                
                                <div class="row form-row">
                                    <div class="col-md-6 col-xl-6">
                                        <div class="form-group">
                                            <label for="mth_class">Jahrgang</label>
                                            <select class="form-control" id="mth_class" name="mth_class">
                                                <option></option>
                                                <?php
                                                    foreach(MethodSelectionFactory::getAgeGroups() as $cls)
                                                    {
                                                        // echo '<option>' . $cls['NAME'] . '</option>';
                                                        echo '<option value="' . $cls['VAL'] . '">' . $cls['NAME'] . '</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div> <!-- form-group -->
                                    </div> <!-- col -->

                                    <div class="col-md-6 col-xl-6">
                                        <div class="form-group">
                                            <label for="mth_author">AutorIn</label>
                                            <select class="form-control" id="mth_author" name="mth_author">
                                                <option></option>
                                                <?php
                                                    foreach(MethodSelectionFactory::getAuthors() as $auth)
                                                    {
                                                        echo '<option value="' . $auth['VAL'] . '">' . $auth['NAME'] . '</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div> <!-- form-group -->
                                    </div> <!-- col -->
                                </div> <!-- row -->

                                <div class="row form-row">
                                    <div class="col-md-6 col-xl-6">
                                        <div class="card">
                                            <div class="card-header">
                                                Unterrichtsphase
                                            </div>
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="mth_phase_E" name="mth_phase[]" value="E">
                                                    <label class="form-check-label" for="mth_phase_E">Einstieg</label>
                                                </div>    
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="mth_phase_I" name="mth_phase[]" value="I">
                                                    <label class="form-check-label" for="mth_phase_I">Information</label>
                                                </div>    
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="mth_phase_S" name="mth_phase[]" value="S">
                                                    <label class="form-check-label" for="mth_phase_S">Sicherung</label>
                                                </div>    
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="mth_phase_A" name="mth_phase[]" value="A">
                                                    <label class="form-check-label" for="mth_phase_A">Aktivierung</label>
                                                </div>    
                                            </div>
                                        </div> <!-- card -->
                                    </div> <!-- col -->

                                    <div class="col-md-6 col-xl-6">
                                        <div class="card">
                                            <div class="card-header">
                                                Sozialform
                                            </div>
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="mth_soc_E" name="mth_soc[]" value="E">
                                                    <label class="form-check-label" for="mth_soc_E">Einzelarbeit</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="mth_soc_P" name="mth_soc[]" value="P">
                                                    <label class="form-check-label" for="mth_soc_P">Partnerarbeit</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="mth_soc_G" name="mth_soc[]" value="G">
                                                    <label class="form-check-label" for="mth_soc_G">Gruppenarbeit</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="mth_soc_K" name="mth_soc[]" value="K">
                                                    <label class="form-check-label" for="mth_soc_K">Klassenplenum</label>
                                                </div>
                                            </div>
                                        </div> <!-- card -->
                                    </div> <!-- col -->
                                </div> <!-- form-row -->
                                
                                <div class="row form-row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="mth_name">Name der Methode</label>
                                            <input id="mth_name" type="text" name="mth_name" class="form-control" placeholder="Name">
                                        </div> <!-- form-group -->
                                    </div> <!-- col -->
                                </div> <!-- form-row -->
                                <div class="form-group">
                                    <input type="hidden" id="curr_usr_id" name="curr_usr_id" value=" <?php echo $current_usr_id; ?> ">
                                </div>
                            </form>

                            <div class="row form-row"><div class="col"><br></div></div>
                            <div class="row form-row">
                                <div class="col">
                                    <div class="form-group" id="filter_apply">
                                        <input type="submit" class="btn btn-primary btn-send" value="Filter anwenden ...">
                                    </div>
                                </div>
                            </div>
                        </div> <!-- card-body -->
                    </div> <!-- card -->
                </div> <!-- col-md-4 col-xl-3 -->
            </div> <!-- row row-fluid -->
            <div class="modal fade" id="ratingDetailModal" role="dialog" aria-labelledby="ratingDetailModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ratingDetailModalLabel"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle" aria-hidden="true"></i></span></button>
                        </div> <!-- modal-header -->
                        <div class="modal-body" id="mth_rtg_detail">
                        </div> <!-- modal-body -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Schlie&szlig;en</button>
                        </div>
                    </div> <!-- modal-content -->
                </div> <!-- modal-dialog -->
            </div> <!-- modal -->
        </div> <!-- container-fluid -->
        
        <?php FormElements::scriptRefs(); ?>
        <script type="text/javascript">
            /* global $ */
            $('#mth_search').validate({
                rules: {
                    
                },
                messages: {
                    
                }
            })
        </script>
        <script type="text/javascript">
            /* global $ */
            $(document).ready(function () {
                $("#mth_subject").change(function () {
                    $.post(
                        "/mbx/ctrl/mth_value.php", 
                        { 
                            val_type: "mth_area", 
                            mth_subj: $("#mth_subject").val() 
                        }, 
                        function(data, status) {
                            $("#mth_area").html(data);
                        }
                    )
                })
            })
        </script>
        <script type="text/javascript">
            /* global $ */
            $(document).ready(function () {
                $('#filter_apply').click(function () {
                    $.post(
                        "/mbx/ctrl/mth_search_result.php",
                        {
                            mth_subject: $('#mth_subject').val(),
                            mth_area: $('#mth_area').val(),
                            mth_class: $('#mth_class').val(),
                            mth_prep_tm: $('#mth_prep_tm').val(),
                            mth_exec_tm: $('#mth_exec_tm').val(),
                            mth_phase: $('#mth_phase').val(),
                            mth_soc: $('#mth_soc').val(),
                            mth_name: $('#mth_name').val(),
                            mth_res_sort: $('#mth_res_sort').val(),
                            curr_usr_id: $('#curr_usr_id').val()
                        },
                        function(data, status) {
                            $('#mth_result').html(data);
                        }
                    )
                    
                })
            })
        </script>
        <script type="text/javascript">
            /* global $ */
            $('#mth_result').click(function (e) {
                var elem = e.target;
                var trgElem = null;
                var imgName = null;
                if (elem.id.startsWith("res_img")) {
                    elem.parentElement.click();
                    return;
                }
                if (elem.id.startsWith("res_tgl")) {
                    trgElem = elem;
                    var chld = elem.children;
                    for(var i = 0; i < chld.length; i++) {
                        if (chld[i].id.startsWith("res_img")) {
                            imgName = chld[i].id;
                        }
                    }
                }
                if (elem.id.startsWith("res_btn")) {
                    chld = elem.children;
                    for(i = 0; i < chld.length; i++) {
                        if (chld[i].id.startsWith("res_tgl")) {
                            trgElem = chld[i];
                            var subChld = chld[i].children;
                            if (subChld[i].id.startsWith("res_img")) {
                                imgName = subChld[i].id;
                            }
                        }
                    }
                }
                // alert($(trgElem).html());
                if ($(trgElem).html().indexOf("fa-caret-right") > 0) {
                    $(trgElem).html('<i id="' + imgName + '" class="fa fa-caret-down"></i>');
                }
                else {
                    $(trgElem).html('<i id="' + imgName + '" class="fa fa-caret-right"></i>');
                }
                // alert($(trgElem).html());
            });
        </script>
        <script type="text/javascript">
            /* global $ */
            $('#ratingDetailModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                // var mth_id = button.data('mthid'); 
                var mth_name = button.data('mthname');
                var modal = $(this);
                modal.find('.modal-title').text(mth_name + ': Bewertungen');
                $.post(
                    "/mbx/ctrl/mth_rating_details.php",
                    {
                        mth_id: button.data('mthid')
                    },
                    function(data, status) {
                        $('#mth_rtg_detail').html(data);
                    }
                );
            }); 
        </script>
    </body>
 </html>   
