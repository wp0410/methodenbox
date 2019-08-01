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
include_once '../model/sql_connection.php';
include_once '../model/usr_session.php';
include_once '../model/mth_selection.php';
include_once '../view/frm_common.php';
include_once '../model/app_warning.php';

set_private_warning_handler();

session_start();
$db_conn = DatabaseConnection::get_connection();
$usr_session = new UserSession($db_conn);

if (empty($_SESSION) || empty($_SESSION['user']))
{
    $res = new AppResult(405);
}
else
{
    $res = $usr_session->validateSession($_SESSION['user']);
    
    if ($res->isOK())
    {
        if (! $usr_session->isAuthenticated())
        {
            $res = new AppResult(406);
        }
        else
        {
            $_SESSION['user'] = $usr_session->getSessionDescriptor();
        }
    }
}
if (! $res->isOK())
{
	$usr_session->closeSession();
    session_destroy();
    header('Location: ../view/aux_error.php?res_code=' . $res->code . '&res_text=' . $res->textUrlEncoded());
    exit;
}

?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <title><?php echo GlobalParameter::$applicationConfig['applicationTitle'] . ' - Methoden Bewerten';?></title>
        <?php FormElements::styleSheetRefs(); ?>
    </head>
    <body>
        <?php FormElements::topNavBar('MTH.RATE', $usr_session); ?>
        <!-- php FormElements::bottomNavigationBar('MTH.RATE'); ? -->
        
        <div class="container-fluid">
           <div class="row row-fluid"><br></div>
           <div class="row row-fluid">
                <div class="col-md-12 col-xl-12">
                    <div class="alert alert-primary" role="alert"><center><h4>Methode Bewerten</h4></center></div>
                    <!-- ?php if ($res->code != 0) { FormElements::showAlert($res->style, 'col-md-12 col-xl-12', $res->text); } ? -->
                </div>
            </div>
            <div class="row row-fluid"></div>
            <div class="row row-fluid">
                <div class="col col-md-8 col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col col-md-4 col-xl-4">
                                    <span class="input-group-text">Offene Bewertungen</span>
                                </div>
                                <div class="col col-md-3 col-xl-3">
									<div class="input-group">
										<div class="input-group-prepend">
											<label class="input-group-text" for="res_lines_per_page">Eintr&auml;ge pro Seite</label>
										</div>
										<input type="number" id="res_lpp" name="res_lpp" style="text-align:center"
											value="<?php echo GlobalParameter::$applicationConfig['mthPageNumLines']; ?>" min="3" max="10" step="1" />
									</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" id="mth_result">
                        </div>
                    </div>
                </div> <!-- col-md-8 col-xl-8 -->

                <div class="col col-md-4 col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <span class="input-group-text">Filtern nach ...</span>
                        </div>
                        <div class="card-body">
                            <form id="mth_search" method="post" action="#" role="form">
                                <div class="row form-row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="mth_name">Name der Methode</label>
                                            <input id="mth_name" type="text" name="mth_name" class="form-control" placeholder="Name">
                                        </div> <!-- form-group -->
                                    </div> <!-- col -->
                                </div> <!-- form-row -->
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
                                            <select class="form-control" id="mth_author" name="mth_author" multiple size="3">
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
                                
                                <div class="form-group">
                                    <input type="hidden" id="curr_usr_id" name="curr_usr_id" value=" <?php echo $usr_session->getUsrId(); ?> ">
                                </div>
                            </form>

                            <!--   div class="row form-row"><div class="col"><br></div></div -->
                            <div class="row form-row">
                                <div class="col">
                                    <div class="form-group" id="filter_apply">
                                        <input type="submit" class="btn btn-primary btn-send" value="Filter anwenden ...">
                                    </div>
                                </div>
                                <div class="col">
                                	<a class="btn btn-secondary float-right" href="<?php echo $_SERVER['PHP_SELF']; ?>" role="button">Filter zur&uuml;cksetzen</a>
                                </div>
                            </div>
                        </div> <!-- card-body -->
                    </div> <!-- card -->
                </div> <!-- col-md-4 col-xl-3 -->
                
            </div> <!-- row row-fluid -->
        
            <div class="modal fade" id="ratingModal" role="dialog" aria-labelledby="ratingModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ratingModalLabel"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle" aria-hidden="true"></i></span></button>
                        </div> <!-- modal-header -->
                        <div class="modal-body">
                            <form id="mth_rtg_form">
                                <div class="form-group" id="mth_id_grp">
                                    <input class="mthid" type="hidden" id="mth_id" name="mth_id" value="">
                                </div>
                                <div class="form-row">
                                    <div class="col">
                                        <div class="card">
                                            <div class="card-header">Bewertung</div>
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" id="mth_rtg_5" name="mth_rate" value="5" onchange="selectRate()">
                                                    <label class="form-check-label" for="mth_rtg_5">
                                                        <span class="badge badge-success">
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                        </span>
                                                    </label>
                                                </div> <!-- form-check -->
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" id="mth_rtg_4" name="mth_rate" value="4" onchange="selectRate()">
                                                    <label class="form-check-label" for="mth_rtg_4">
                                                        <span class="badge badge-success">
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                        </span>
                                                    </label>
                                                </div> <!-- form-check -->
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" id="mth_rtg_3" name="mth_rate" value="3" onchange="selectRate()">
                                                    <label class="form-check-label" for="mth_rtg_3">
                                                        <span class="badge badge-warning">
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                        </span>
                                                    </label>
                                                </div> <!-- form-check -->
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" id="mth_rtg_2" name="mth_rate" value="2" onchange="selectRate()">
                                                    <label class="form-check-label" for="mth_rtg_2">
                                                        <span class="badge badge-danger">
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                        </span>
                                                    </label>
                                                </div> <!-- form-check -->
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" id="mth_rtg_1" name="mth_rate" value="1" onchange="selectRate()">
                                                    <label class="form-check-label" for="mth_rtg_1">
                                                        <span class="badge badge-danger">
                                                            <i class="fa fa-star"></i>
                                                        </span>
                                                    </label>
                                                </div> <!-- form-check -->
                                            </div> <!-- card-body -->
                                        </div> <!-- card -->
                                    </div> <!-- col -->
                                </div> <!-- row -->
                                <div class="form-row"><div class="col"><p></p></div></div>
                                <div class="form-row">
                                    <div class="col">
                                        <div class="card">
                                            <div class="card-header">Kommentar</div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <textarea id="rtg_comment" class="form-control" name="rtg_comment" form="mth_rtg_form" rows="5" placeholder="Kommentar"></textarea>
                                                </div>    
                                            </div>
                                        </div> <!-- card -->
                                    </div> <!-- col -->
                                </div> <!-- row -->
								<div class="form-row">
									<div class="col">
										<div id="rtg_feedback"></div>
									</div>
								</div>
                            </form>
                        </div> <!-- modal-body -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Schlie&szlig;en</button>
                            <div id="rtg_hide"><button type="button" id="rtg_save" name="rtg_save" class="btn btn-primary btn-sm" onclick="saveRating()" disabled>Bewertung abschicken</button></div>
                        </div>
                    </div> <!-- modal-content -->
                </div> <!-- modal-dialog -->
            </div> <!-- modal -->
        </div> <!-- container-fluid -->
        
        <?php FormElements::scriptRefs(); ?>
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
            });
        </script>
        <script type="text/javascript">
            /* global $ */
            function load_contents() {
                var mphase = '';
                if (document.getElementById('mth_phase_E').checked) {
                    mphase = 'E:';
                }
                if (document.getElementById('mth_phase_A').checked) {
                    mphase = mphase + 'A:';
                }
                if (document.getElementById('mth_phase_I').checked) {
                    mphase = mphase + 'I:';
                }
                if (document.getElementById('mth_phase_S').checked) {
                    mphase = mphase + 'S:';
                }
                
                var msoc = '';
                if (document.getElementById('mth_soc_E').checked) {
                    msoc = 'E:';
                }
                if (document.getElementById('mth_soc_G').checked) {
                    msoc = msoc + 'G:';
                }
                if (document.getElementById('mth_soc_K').checked) {
                    msoc = msoc + 'K:'
                }
                if (document.getElementById('mth_soc_P').checked) {
                    msoc = msoc + 'P:';
                }

                $.post(
                    "/mbx/ctrl/mth_search_rate_pg.php",
                    {
                        mth_subject: $('#mth_subject').val(),
                        mth_area: $('#mth_area').val(),
                        mth_class: $('#mth_class').val(),
                        mth_prep_tm: $('#mth_prep_tm').val(),
                        mth_exec_tm: $('#mth_exec_tm').val(),
                        mth_phase: mphase, 
                        mth_soc: msoc, 
                        mth_author: $('#mth_author').val(),
                        mth_name: $('#mth_name').val(),
                        curr_usr_id: $('#curr_usr_id').val(),
                        lines_per_pg: $('#res_lpp').val()
                    },
                    function(data, status) {
                        $('#mth_result').html(data);
                    }
                );
            }
            
            $(document).ready(function () {
                $('#filter_apply').click(function () {
                    load_contents();
                });
                $('#res_lpp').change(function() {
                    load_contents();
                });
            });

            $(window).on('load', function() {
                load_contents();
            });
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
            $('#ratingModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var mth_id = button.data('mthid'); 
                var mth_name = button.data('mthname');
                var modal = $(this);
                modal.find('.modal-title').text('Bewerten: ' + mth_name);
                modal.find('.mthid').val(mth_id);
                
                $('#mth_rtg_1').prop("checked", false);
                $('#mth_rtg_2').prop("checked", false);
                $('#mth_rtg_3').prop("checked", false);
                $('#mth_rtg_4').prop("checked", false);
                $('#mth_rtg_5').prop("checked", false);
                $('#rtg_comment').val("");
            }); 
			
			$('#ratingModal').on('hidden.bs.modal', function(event) {
				load_contents();
			});
            
            function saveRating() {
                var mth_rtg_val = null;
                var radios = document.getElementsByName('mth_rate');
                for (var i = 0, length = radios.length; i < length; i++) {
                    if (radios[i].checked) {
                        mth_rtg_val = radios[i].value;
                        break;
                    }
                }
                $.post(
                    "/mbx/ctrl/mth_rating.php",
                    {
                        mth_id: $('#mth_id').val(),
                        mth_rtg_val: mth_rtg_val,
                        mth_rtg_comm: $('#rtg_comment').val()
                    },
                    function(data, status) {
						// load_contents();
						$('#rtg_feedback').html('<div class="alert alert-success" role="alert" ><h4>Vielen Dank f&uuml;r die Bewertung</h4></div>');
						// $('#rtg_save').prop("disabled", true);
						
						var x = document.getElementById("rtg_hide");
						x.style.display = "none";
                    }
                );
            };
            
            function selectRate() {
                $('#rtg_save').prop("disabled", false);
            }
        </script>
        <script type="text/javascript">
            /* global $ */
            function goto_page(ch_obj_id, page_no) {
                // alert('cch_obj_id=' + ch_obj_id + ' / page_no = ' + page_no);
                $.post(
                    "/mbx/ctrl/mth_search_rate_pg.php",
                    {
                        ch_id: ch_obj_id,
                        pg_no: page_no
                    },
                    function(data, status) {
                        $('#mth_result').html(data);
                    }
                );
            }
        </script>
    </body>
 </html>   
