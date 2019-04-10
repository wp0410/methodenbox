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
            $_SESSION['user'] = array('sid' => $usr_session->getId(), 'uid' => $usr_session->getUsrId(), 'hash' => $usr_session->getSessionHash());
        }
    }
}
if (! $res->isOK())
{
    header('Location: ../view/aux_error.php?res_code=' . $res->code . '&res_text=' . $res->textUrlEncoded());
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <title><?php echo GlobalParameter::$applicationConfig['applicationTitle'] . ' - Verwalten';?></title>
        <?php FormElements::styleSheetRefs(); ?>
    </head>
    <body>
        <?php FormElements::topNavigationBar('MTH.ADM', $usr_session->isAuthenticated(), $usr_session->getPermissions()); ?>
        <?php FormElements::bottomNavigationBar('MTH.ADM'); ?>
        <div class="container-fluid">
            <div class="row row-fluid"><br></div>
            <div class="row row-fluid">
                <div class="col col-md-12 col-xl-12">
                    <div class="alert alert-primary" role="alert"><center><h4>Methoden Verwalten</h4></center></div>
                </div>
            </div> <!-- row row-fluid -->
            <div class="row row-fluid"></div>
            <div class="row row-fluid">
                <div class="col col-md-8 col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col col-md-4 col-xl-4">
                                    <span class="input-group-text">Vorhandene Methoden</span>
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
                <div class="col col-md-4 col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <span class="input-group-text">Filtern nach ...</span>
                        </div>
                        <div class="card-body">
                            <form id="mth_search" method="post" action="#" role="form">
                                <div class="row form-row">
                                    <div class="col col-md-6 col-xl-6">
                                        <div class="form-group">
                                            <select class="form-control" id="mth_ownership" name="mth_ownership">
                                                <option value="M">Meine Methoden</option>
                                                <?php
                                                    $owner_count = count(MethodSelectionFactory::getOwners($usr_session->getUsrId()));
                                                    if (($usr_session->getRole() > 0) && ($owner_count > 0))
                                                    {
                                                        echo '<option value="O">Methoden eines anderen Benutzers: </option>';
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div> <!-- col col-md-6 col-xl-6 -->
                                    <?php
                                        if (($usr_session->getRole() > 0) && ($owner_count > 0))
                                        {
                                            echo '<div class="col col-md-6 col-xl-6"><div class="form-group"><select class="form-control" id="mth_owner" name="mth_owner">';
                                            echo '<option></option>';
                                            echo '</select></div></div>';
                                        }
                                    ?>
                                </div> <!-- row form-row -->
                                <div class="row form-row">
                                    <div class="col col-md-6 col-xl-6">
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
                                    <div class="col col-md-6 col-xl-6">
                                        <div class="form-group">
                                            <label for="mth_area">Fachbereich</label>
                                            <select class="form-control" id="mth_area" name="mth_area">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                </div> <!-- row form-row -->
                                <div class="row form-row">
                                    <div class="col col-md-6 col-xl-6">
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
                                    </div> <!-- col col-md-6 col-xl-6 -->
                                    <div class="col col-md-6 col-xl-6">
                                        <div class="form-group">
                                            <label for="mth_name">Name der Methode</label>
                                            <input id="mth_name" type="text" name="mth_name" class="form-control" placeholder="Name">
                                        </div> <!-- form-group -->
                                    </div> <!-- col col-md-6 col-xl-6 -->
                                </div> <!-- row form-row -->
                                <div class="form-group">
                                    <input type="hidden" id="curr_usr_id" name="curr_usr_id" value=" <?php echo $usr_session->getUsrId(); ?> ">
                                </div>
                            </form>

                            <div class="row form-row"><div class="col"><br></div></div>
                            <div class="row form-row">
                                <div class="col">
                                    <div class="form-group" id="filter_apply">
                                        <input type="submit" class="btn btn-primary btn-send" id="btn_apply" name="btn_apply" value="Filter anwenden ...">
                                    </div>
                                </div>
                            </div>
                        </div> <!-- card-body -->
                    </div>
                </div> <!-- col col-md-4 col-xl-4 -->
            </div> <!-- row row-fluid -->
        </div> <!-- container-fluid -->

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
        
        <div class="modal fade" id="deleteMethodModal" role="dialog" aria-labelledby="deleteMethodModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteMethodModalLabel"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle" aria-hidden="true"></i></span></button>
                    </div> <!-- modal-header -->
                    <div class="modal-body" id="mth_del_body">
                    </div> <!-- modal-body -->
                    <div class="modal-footer" id="mth_del_footer">
                    </div>
                </div> <!-- modal-content -->
            </div> <!-- modal-dialog -->
        </div> <!-- modal -->

        <div class="modal fade" id="uploadFileModal" role="dialog" aria-labelledby="uploadFileModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadFileModalLabel"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle" aria-hidden="true"></i></span></button>
                    </div> <!-- modal-header -->
                    <div class="modal-body" id="mth_upload_body">
                        
                        <form id="mth_upload" enctype="multipart/form-data" method="post" action="/mbx/ctrl/mth_update_file.php">
                            <div class="row form-row">
                                <div class="col-md-12 col-xl-12">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="input-group-btn">
                                                <span class="btn btn-outline-primary">Datei ausw&auml;hlen &hellip; 
                                                    <input type="file" style="display: none;" id="mth_file" name="mth_file" multiple accept=".zip, .gz, .tar">
                                                </span>
                                            </label>
                                            <input type="text" class="form-control" id="mth_file_name", name="mth_file_name" aria-describedby="mth_file">
                                        </div> <!-- input-group -->
                                    </div>
                                </div>
                            </div> <!-- row -->
                            <div class="row form-row">
                                <div class="col col-md-12 col-xl-12">
                                    <div id="mth_upload_success" name="mth_upload_success">
                                        
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                    </div> <!-- modal-body -->
                    <div class="modal-footer">
                    </div>
                </div> <!-- modal-content -->
            </div> <!-- modal-dialog -->
        </div> <!-- modal -->

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
                    );
                });
            });
        </script>
        <script type="text/javascript">
            /* global $ */
            $(document).ready(function () {
                $('#btn_apply').click(function () {
                    $.post(
                        "/mbx/ctrl/mth_search_admin.php",
                        {
                            mth_ownership: $('#mth_ownership').val(),
                            mth_owner: $('#mth_owner').val(),
                            mth_subject: $('#mth_subject').val(),
                            mth_area: $('#mth_area').val(),
                            mth_class: $('#mth_class').val(),
                            mth_name: $('#mth_name').val(),
                            mth_res_sort: $('#mth_res_sort').val(),
                            curr_usr_id: $('#curr_usr_id').val()
                        },
                        function(data, status) {
                            $('#mth_result').html(data);
                        }
                    );
                });
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
        <script type="text/javascript">
            /* global $ */
            $('#deleteMethodModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var mth_id = button.data('mthid');
                var mth_name = button.data('mthname');
                var modal = $(this);
                modal.find('.modal-title').text(mth_name + ': Endgültig löschen?');
                modal.find('.modal-footer').html(
                    '<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Schlie&szlig;en</button>' +
                    '<button type="button" class="btn btn-primary btn-sm" onclick="deleteMethod(' + mth_id + ')">Ja, Methode endg&uuml;ltig l&ouml;schen</button>'
                );
                $('#mth_del_body').html(
                    '<div class="alert alert-danger" role="alert"><h5>Achtung, die Unterrichtsmethode wird endg&uuml;ltig gel&ouml;scht. ' +
                    'Diese Aktion kann nicht r&uuml;ckg&auml;ngig gemacht werden. Bitte best&auml;tigen Sie, dass die Methode endg&uuml;ltig gel&ouml;scht werden soll.</h5></div>'
                );
            });
            $('#deleteMethodModal').on('hidden.bs.modal', function (event) {
                var modal = $(this);
                var confirm = modal.find('.del_confirm').text();
                if (confirm.length > 0) {
                    $('#btn_apply').click();
                }
            });
            function deleteMethod(mth_id) {
                $.post(
                    "/mbx/ctrl/mth_delete.php",
                    {
                        mth_id: mth_id
                    },
                    function(data, status) {
                        $('#mth_del_body').html(data);
                    }
                );
                $('#mth_del_footer').html(
                    '<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Schlie&szlig;en</button>'
                );
            }
        </script>
        <script type="text/javascript">
            /* global $ */
            $('#uploadFileModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var mth_id = button.data('mthid');
                var mth_name = button.data('mthname');
                var modal = $(this);
                modal.find('.modal-title').text(mth_name + ': Neue Datei Hochladen');
                $('#mth_file').val('');
                $('#mth_file_name').val('');
                modal.find('.modal-footer').html(
                    '<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Schlie&szlig;en</button>' +
                    '<button type="button" class="btn btn-primary btn-sm" onclick="newFileUpload(' + mth_id + ')">Datei Hochladen</button>'
                );
                $('#mth_upload_success').html('<div></div>');
            });
            
            $('#uploadFileModal').on('hidden.bs.modal', function(event) {
                var modal = $(this);
                var upconf = modal.find('.upconf').text();
                if (upconf.length > 0)
                {
                    $('#btn_apply').click();
                }
            });
            
            function newFileUpload(mth_id) {
                $('#mth_upload_success').html('<div class="alert alert-light" role="alert"><p align="center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw" aria-hidden="true"></i></p></div>');
                $('#uploadFileModal').find('.modal-footer').html('<div> </div>');
                var fUpForm = document.getElementById('mth_upload');
                var formData = new FormData(fUpForm);
                formData.append('mth_id', mth_id);
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if ((this.readyState) == 4 && (this.status == 200)) {
                        $('#mth_upload_success').html(this.responseText);
                        $('#uploadFileModal').find('.modal-footer').html(
                            '<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Schlie&szlig;en</button>'
                        );
                    }
                };
                xhr.open('POST', '/mbx/ctrl/mth_update_file.php', true);
                xhr.send(formData);
            }
        </script>
        <script type="text/javascript">
            /* global $ */
            $(function() {
                $(document).on('change', ':file', function() {
                    var input = $(this),
                    numFiles = 1, // input.get(0).files ? input.get(0).files.length : 1,
                    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
                    
                    input.trigger('fileselect', [numFiles, label]);
                });
                    
                $(document).ready( function() {
                    $(':file').on('fileselect', function(event, numFiles, label) {
                        var input = $(this).parents('.input-group').find(':text'),
                            log = numFiles > 1 ? numFiles + ' Dateien ausgew&auml;hlt' : label;
                        if (input.length) {
                            input.val(log);
                        } else {
                            if( log ) alert(log);
                        }
                    });
                });
            });
        </script>
    </body>
 </html>   
