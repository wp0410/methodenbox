<?php
foreach($res_view->lines as $line)
{
    $mth_view->addMethod($line);
    
    echo '<div class="card"><div class="card-header" id="res_line_' . $line_no . '">';

    echo '<div class="row">';
    echo '<div class="col-md-1 col-xl-1">';
    echo '<span><button class="btn btn-sm btn-light collapsed" id="res_btn_' . $line_no . '" type="button" data-toggle="collapse" ' . 
         'data-target="#res_coll_' . $line_no . '" aria-expanded="false" aria-controls="res_coll_' . $line_no . '">';
    echo '<div id="res_tgl_' . $line_no . '"><i id="res_img_' . $line_no . '" class="fa fa-caret-right"></i></div></button>';
    echo '</div>'; // col
    
    // Result Line Header
    // Method Name
    echo '<div class="col-md-4 col-xl-4">';
    echo '<span class="badge badge-light"><h5>' . htmlentities($line->mth_name) . '</h5></span>';
    echo '</div>'; // col
    
    // Method Rating
    $rating = round($line->mth_rtg_avg, 1);
    echo '<div class="col-md-2 col-xl-2">Bewertung: ';
    if ($rating < 2.2)
    {
        echo '<span class="badge badge-danger">';
    }
    else
    {
        if ($rating < 3.8)
        {
            echo '<span class="badge badge-warning">';
        }
        else
        {
            echo '<span class="badge badge-success">';
        }
    }
    for($num_stars = 1; $num_stars <= $rating; $num_stars++)
    {
        echo '<i class="fa fa-star"></i>';    
    }
    for (; $num_stars <= 5; $num_stars++)
    {
        echo '<i class="fa fa-star-o"></i>';    
    }
    echo '</span>';
    echo '</div>'; // col
    
    echo '<div class="col-md-2 col-xl-2">';
    echo 'Downloads: ';
    echo '<span class="badge badge-secondary">';
    echo $line->mth_dnl_cnt . '</span>';
    echo '</div>'; // col
    
    echo '<div class="col-md-2 col-xl-2">';
    echo 'Erstellt: ';
    echo '<span>' . substr($line->mth_create_tm, 0, 10) . '</span>';
    echo '</div>'; // col
    
    echo '</div>'; // row

    echo '</span></div>';
    
    echo '<div class="collapse" id="res_coll_' . $line_no . '" aria-labelledby="res_line_' . $line_no . '" data-parent="#mth_result">';
    echo '<div class="card-body">';

    echo '<div class="row">';
    
    echo '<table class="table table-sm">';
    echo '<tbody>';

    echo '<tr>';
    echo '<td class="table-secondary">Beschreibung</td><td colspan=5><span>' . htmlentities($line->mth_summary) . '</span></td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td class="table-secondary">Unterrichtsfach / Phase</td>'; 
    echo '<td class="table-secondary">Fachbereich / Sozialform</td>';
    echo '<td class="table-secondary">Vorbereitungszeit</td>'; 
    echo '<td class="table-secondary">Durchf&uuml;hrungszeit</td>';
    echo '<td class="table-secondary">Jahrgang</td>'; 
    echo '<td class="table-secondary">AutorIn</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td><span>' . htmlentities($line->mth_subject_txt) . '</span></td>';
    echo '<td><span>' . htmlentities($line->mth_subj_area_txt) . '</span></td>';
    echo '<td><span>' . htmlentities($line->mth_prep_tm_txt) . '</span></td>';
    echo '<td><span>' . htmlentities($line->mth_exec_tm_txt) . '</span></td>';
    echo '<td><span>' . htmlentities($line->mth_age_grp_txt) . '</span></td>';
    echo '<td><span>' . htmlentities($line->mth_authors_arr[0]) . '</span></td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td>';
    $phase_txt = array('E' => 'Einstieg', 'I' => 'Information', 'S' => 'Sicherung', 'A' => 'Aktivierung');
    foreach($line->mth_phase_arr as $phase)
    {
        echo '<span class="badge badge-secondary badge-pill badge-sm">' . $phase_txt[$phase] . '</span>';
    }
    echo '</td>';
    echo '<td>';
    $soc_txt = array('E' => 'Einzelarbeit', 'P' => 'Partnerarbeit', 'G' => 'Gruppenarbeit', 'K' => 'Klassenplenum');
    foreach($line->mth_soc_form_arr as $soc_form)
    {
        echo '<span class="badge badge-secondary badge-pill badge-sm">' . $soc_txt[$soc_form] . '</span>';
    }
    echo '</td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td>'; 
    
    if ($cur_usr_id > 0)
    {
        echo '<a href="/mbx/ctrl/mth_download.php?mth_id=' . $line->mth_id . '&file_guid=' . $line->mth_file_guid . '" download="' . $line->mth_file_name;
        echo '" class="btn btn-primary btn-sm" role="button">';
        echo 'Download <i class="fa fa-download" aria-hidden="true"></i></a>';
    }
    echo '</td>';
    echo '</tr>';

    echo '</tbody>';
    echo '</table>';
    
    echo '</div>'; // row
    
    
    echo '</div></div></div>';
}

if ($res_view->getNumRemaining() > 0)
{
    echo '<div class="card"><div class="card-header" id="rest">';
    echo $res_view->getNumRemaining() . ' weitere Datens&auml;tze vorhanden &hellip;';
    echo '</div></div>';
}
else
{
    echo '<div class="card"><div class="card-header" id="rest">';
    echo 'Keine weiteren Datens&auml;tze vorhanden &hellip;';
    echo '</div></div>';
}
?>