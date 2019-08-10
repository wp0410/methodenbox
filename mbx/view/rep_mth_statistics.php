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
include_once '../model/aux_parameter.php';
include_once '../model/sql_connection.php';
include_once '../view/frm_common.php';
include_once '../model/app_warning.php';
include_once '../model/usr_session.php';
include_once '../model/rep_mth_statistics.php';
include_once '../koolreport-4.0.0/koolreport/core/autoload.php';

use koolreport\datasources\PdoDataSource;
use koolreport\processes\CalculatedColumn;
use koolreport\widgets\google\DonutChart;
use koolreport\widgets\koolphp\Card;

set_private_warning_handler();
session_start();

$db_conn = DatabaseConnection::get_connection();
$usr_session = new UserSession($db_conn);

if (! empty($_SESSION) && ! empty($_SESSION['user']))
{
    $res = $usr_session->validateSession($_SESSION['user']);
    if ($res->isOK())
    {
        $_SESSION['user'] = $usr_session->getSessionDescriptor();
    }
}
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <title><?php echo GlobalParameter::$applicationConfig['applicationTitle'] . ' Statistiken: Unterrichtsmethoden';?></title>
        <?php FormElements::styleSheetRefs(); ?>
    </head>
    <body>
        <?php FormElements::topNavBar('REP.MST', $usr_session); ?>
        <?php FormElements::bottomNavBar('REP.MST'); ?>
        
        <div class="container-fluid">
            <div class="row row-fluid"><div class="col"><br></div></div>
		
            <div class="row row-fluid">
				<div class="col">
					<div class="alert alert-primary" role="alert"><center><h4>Berichte und Statistiken / Unterrichtsmethoden nach Kategorien</h4></center></div>
				</div>
			</div> <!-- row -->
			 
			<div class="row row-fluid">
				<div class="col col-sm-12 col-md-12 col-xl-12">
					<div class="alert alert-info"><center><h5>Unterrichtsmethoden nach Fachbereich pro Unterrichtsfach</h5></center></div>
				</div>
			</div>
			<div class="row row-fluid">
			<?php
				$mth_stat = new MethodStatistics($db_conn);
				$subjects = $mth_stat->mth_per_subject();
				
				$subj_count = 0;
				foreach($subjects as $subj)
				{
					if ($subj['num_per_subj'] != 0)
					{
						$subj_count += 1;
					}
				}
				$col_size = 8 - ($subj_count - 1) * 2;
				$col_size = 'col-sm-' . $col_size . ' col-md-' . $col_size . ' col-lg-' . $col_size . ' col-xl-' . $col_size;
				
				foreach($subjects as $subj)
				{
					$cn = DatabaseConnection::get_report_connection();
					if ($subj['num_per_subj'] != 0)
					{
						echo '<div class="col ' . $col_size . '">';

						DonutChart::create(
							array(
								'dataSource' => (new PdoDataSource($cn))->query("
									SELECT sub.mth_subject, sub.mth_subject_area, sub.mth_area_name, sum(sub.num_per_area) as num_per_area
									FROM (
										SELECT subj.mth_sub_val as mth_subject, subj.mth_area_val as mth_subject_area, subj.mth_area_name, 0 as num_per_area
										FROM   ta_mth_subject_area as subj
										UNION ALL
										SELECT mth.mth_subject, mth.mth_subject_area, subj.mth_area_name, COUNT(1) as num_per_area
										FROM   ta_mth_method_header as mth
												INNER JOIN ta_mth_subject_area subj on subj.mth_sub_val = mth.mth_subject and subj.mth_area_val = mth.mth_subject_area
										GROUP BY mth.mth_subject, mth.mth_subject_area, subj.mth_area_name ) sub
									WHERE sub.mth_subject = '" . $subj['mth_subject'] . "'
									GROUP BY sub.mth_subject, sub.mth_subject_area, sub.mth_area_name;
								")->pipe(
									new CalculatedColumn(array(
										'area_name' => array(
											'exp' => function($data){
												return html_entity_decode($data['mth_area_name']);
											},
											'type' => 'string'
										)
									))
								),
								'title' => 'Fachbereiche Unterrichtsfach ' . $subj['mth_sub_name'] . ' (Anzahl: ' . $subj['num_per_subj'] . ')',
								'columns' => array(
									'area_name' => array('label' => 'Fachbereich'),
									'num_per_area' => array('label' => 'Anzahl Gesamt')
								)
							)
						);
						
						echo '</div>';
					}
				}
			?>
			</div> <!-- row row-fluid -->
			
			<div class="row row-fluid">
				<div class="col col-sm-12 col-md-12 col-xl-12">
					<div class="alert alert-info"><center><h5>Unterrichtsmethoden nach Einzeleigenschaften</h5></center></div>
				</div>
			</div>

			<div class="row row-fluid">	
				<div class="col col-sm-4 col-md-4 col-xl-4">
					<?php
						// Anzahl Methoden pro Jahrgang
						/*
						$cn = DatabaseConnection::get_report_connection();
						DonutChart::create(
							array(
								'dataSource' => (new PdoDataSource($cn))->query('
									SELECT sub.mth_age_grp, sub.mth_opt_name, SUM(sub.num_per_age) as num_per_age
									FROM (
										SELECT agrp.mth_opt_val as mth_age_grp, agrp.mth_opt_name, 0 as num_per_age
										FROM   vi_mth_age_groups as agrp
										UNION ALL
										SELECT mth.mth_age_grp, agrp.mth_opt_name, COUNT(1) as num_per_age
										FROM   ta_mth_method_header as mth
												INNER JOIN vi_mth_age_groups as agrp on mth.mth_age_grp = agrp.mth_opt_val
										GROUP BY mth.mth_age_grp, agrp.mth_opt_name
									) as sub 
									group by sub.mth_age_grp, sub.mth_opt_name;
								'),
								'title' => 'Methoden nach Jahrgang',
								'columns' => array(
									'mth_opt_name' => array('label' => 'Jahrgang'),
									'num_per_age' => array('label' => 'Anzahl Gesamt')
								)
							)
						);
						*/
						
						// Anzahl Methoden pro Art
						$cn = DatabaseConnection::get_report_connection();
						DonutChart::create(
							array(
								'dataSource' => (new PdoDataSource($cn))->query('
									SELECT sub.mth_type, sub.mth_type_name, SUM(sub.num_per_type) AS num_per_type
									FROM (
										SELECT mtyp.mth_opt_val AS mth_type, mtyp.mth_opt_name AS mth_type_name, 0 AS num_per_type
										FROM   vi_mth_method_type AS mtyp
										UNION ALL
										SELECT mth.mth_type, mtyp.mth_opt_name AS mth_type_name, COUNT(1) AS num_per_type
										FROM   ta_mth_method_header AS mth
												INNER JOIN vi_mth_method_type AS mtyp ON mth.mth_type = mtyp.mth_opt_val
										GROUP BY mth.mth_type, mtyp.mth_opt_name
									) AS sub
									GROUP BY sub.mth_type, sub.mth_type_name
								')->pipe(
									new CalculatedColumn(array(
										'mth_type_name' => array(
											'exp' => function($data){
												return html_entity_decode($data['mth_type_name']);
											},
											'type' => 'string'
										)
									))
								),
								'title' => 'Anzahl pro Art der Methode',
								'columns' => array(
									'mth_type_name' => array('label' => 'Art der Methode'),
									'num_per_type' => array('label' => 'Anzahl Gesamt')
								)
							)
						);
						
					?>
				</div> <!-- col -->
				
				<div class="col col-sm-4 col-md-4 col-xl-4">
					<?php
						$cn = DatabaseConnection::get_report_connection();
						DonutChart::create(
							array(
								'dataSource' => (new PdoDataSource($cn))->query('
									SELECT sub.mth_prep_time, sub.mth_opt_name, SUM(num_per_pt) as num_per_pt
									FROM (
										SELECT prep.mth_opt_val as mth_prep_time, prep.mth_opt_name, 0 as num_per_pt
										FROM   vi_mth_prep_times as prep
										UNION ALL
										SELECT mth.mth_prep_time, prep.mth_opt_name, COUNT(1) AS num_per_pt
										FROM   ta_mth_method_header as mth 
										       INNER JOIN vi_mth_prep_times prep ON mth.mth_prep_time = prep.mth_opt_val
										GROUP BY mth.mth_prep_time, prep.mth_opt_name
									) AS sub
									GROUP BY sub.mth_prep_time, sub.mth_opt_name;
								')->pipe(
									new CalculatedColumn(
										array(
											'mth_opt_prep' => array(
												'exp' => function($data){
													return html_entity_decode($data['mth_opt_name']);
												},
												'type' => 'string'
											)
										)
									)
								),
								'title' => 'Anzahl pro Vorbereitungszeit',
								'columns' => array(
									'mth_opt_prep' => array('label' => 'Vorbereitungszeit'),
									'num_per_pt' => array('label' => 'Anzahl Gesamt')
								)
							)
						);
					?>
				</div> <!-- col -->
				
				<div class="col col-sm-4 col-md-4 col-xl-4">
					<?php
						$cn = DatabaseConnection::get_report_connection();
						DonutChart::create(
							array(
								'dataSource' => (new PdoDataSource($cn))->query('
									SELECT sub.mth_exec_time, sub.mth_opt_name, SUM(num_per_et) AS num_per_et
									FROM (
										SELECT exec.mth_opt_val as mth_exec_time, exec.mth_opt_name, 0 as num_per_et
										FROM   vi_mth_prep_times as exec
										UNION ALL
										SELECT mth.mth_exec_time, exec.mth_opt_name, COUNT(1) AS num_per_et
										FROM   ta_mth_method_header as mth 
											   INNER JOIN vi_mth_exec_times AS exec ON mth.mth_exec_time = exec.mth_opt_val
										GROUP BY mth.mth_exec_time, exec.mth_opt_name
									) AS sub
									GROUP BY sub.mth_exec_time, sub.mth_opt_name
								')->pipe(
									new CalculatedColumn(
										array(
											'mth_opt_exec' => array(
												'exp' => function($data){
													return html_entity_decode($data['mth_opt_name']);
												},
												'type' => 'string'
											)
										)
									)
								),
								'title' => html_entity_decode('Anzahl pro Durchf&uuml;hrungszeit'),
								'columns' => array(
									'mth_opt_exec' => array('label' => 'Vorbereitungszeit'),
									'num_per_et' => array('label' => 'Anzahl Gesamt')
								)
							)
						);
					?>
				</div> <!-- col -->
			 </div> <!-- row row-fluid -->

 			<div class="row row-fluid">
				<div class="col col-sm-12 col-md-12 col-xl-12">
					<div class="alert alert-info"><center><h5>Unterrichtsmethoden nach Mehrfacheigenschaften</h5></center></div>
				</div>
			</div> <!-- row row-fluid -->

			<div class="row row-fluid">	
				<div class="col col-sm-6 col-md-6 col-xl-6">
					<?php
						$cn = DatabaseConnection::get_report_connection();
						DonutChart::create(
							array(
								'dataSource' => (new PdoDataSource($cn))->query("
									SELECT sub.mth_soc_form, SUM(sub.num_form) AS num_form
									FROM (
										SELECT mth.mth_id, 'Einzelarbeit' AS mth_soc_form, 1 AS num_form
										FROM   ta_mth_method_header AS mth
										WHERE  mth.mth_soc_form LIKE '%E:%'
										UNION ALL 
										SELECT mth.mth_id, 'Gruppenarbeit' AS mth_soc_form, 1 AS num_form
										FROM   ta_mth_method_header AS mth
										WHERE  mth.mth_soc_form LIKE '%G:%'
										UNION ALL 
										SELECT mth.mth_id, 'Partnerarbeit' AS mth_soc_form, 1 AS num_form
										FROM   ta_mth_method_header AS mth
										WHERE  mth.mth_soc_form LIKE '%P:%'
										UNION ALL 
										SELECT mth.mth_id, 'Klassenplenum' AS mth_soc_form, 1 AS num_form
										FROM   ta_mth_method_header AS mth
										WHERE  mth.mth_soc_form LIKE '%K:%' ) AS sub 
									GROUP BY sub.mth_soc_form
									ORDER BY num_form DESC 
								"),
								'title' => 'Methoden nach Sozialform',
								'columns' => array(
									'mth_soc_form' => array('label' => 'Sozialform'),
									'num_form' => array('label' => 'Anzahl')
								)
							)
						);
					?>
				</div> <!-- col -->

				<div class="col col-sm-6 col-md-6 col-xl-6">
					<?php
						/* Deprecated: Method Phase
						$cn = DatabaseConnection::get_report_connection();
						DonutChart::create(
							array(
								'dataSource' => (new PdoDataSource($cn))->query("
									SELECT sub.mth_phase, SUM(sub.phase_num) AS phase_num
									FROM (
										SELECT mth.mth_id, 'Einstieg' AS mth_phase, 1 AS phase_num
										FROM   ta_mth_method_header AS mth
										WHERE  mth.mth_phase LIKE '%E:%'
										UNION ALL 
										SELECT mth.mth_id, 'Information' AS mth_phase, 1 AS phase_num
										FROM   ta_mth_method_header AS mth
										WHERE  mth.mth_phase LIKE '%I:%'
										UNION ALL 
										SELECT mth.mth_id, 'Sicherung' AS mth_phase, 1 AS phase_num
										FROM   ta_mth_method_header AS mth
										WHERE  mth.mth_phase LIKE '%S:%'
										UNION ALL 
										SELECT mth.mth_id, 'Aktivierung' AS mth_phase, 1 AS phase_num
										FROM   ta_mth_method_header AS mth
										WHERE  mth.mth_phase LIKE '%A:%' ) AS sub 
									GROUP BY sub.mth_phase
									ORDER BY phase_num DESC 
								"),
								'title' => 'Methoden nach Unterrichtsphase',
								'columns' => array(
									'mth_phase' => array('label' => 'Unterrichtsphase'),
									'phase_num' => array('label' => 'Anzahl')
								)
							)
						);
						*/
						$cn = DatabaseConnection::get_report_connection();
						DonutChart::create(
							array(
								'dataSource' => (new PdoDataSource($cn))->query("
									SELECT sub.mth_elem, SUM(sub.elem_num) AS elem_num
									FROM (
										SELECT mth.mth_id, 'Anfangen' AS mth_elem, 1 AS elem_num
										FROM   ta_mth_method_header AS mth
										WHERE  mth.mth_elements LIKE '%E:%'
										UNION ALL 
										SELECT mth.mth_id, 'Informieren' AS mth_elem, 1 AS elem_num
										FROM   ta_mth_method_header AS mth
										WHERE  mth.mth_elements LIKE '%I:%'
										UNION ALL 
										SELECT mth.mth_id, 'Sicherung - Wissen Abfragen' AS mth_elem, 1 AS elem_num
										FROM   ta_mth_method_header AS mth
										WHERE  mth.mth_elements LIKE '%S:%'
										UNION ALL 
										SELECT mth.mth_id, 'Sicherung - Wissen Anwenden' AS mth_elem, 1 AS elem_num
										FROM   ta_mth_method_header AS mth
										WHERE  mth.mth_elements LIKE '%W:%'
										UNION ALL 
										SELECT mth.mth_id, 'Auflockerung' AS mth_elem, 1 AS elem_num
										FROM   ta_mth_method_header AS mth
										WHERE  mth.mth_elements LIKE '%A:%'
									) AS sub 
									GROUP BY sub.mth_elem ORDER BY elem_num DESC"
								)->pipe(
									new CalculatedColumn(
										array(
											'mth_elem' => array(
												'exp' => function($data){
													return html_entity_decode($data['mth_elem']);
												},
												'type' => 'string'
											)
										)
									)
								),
								'title' => 'Methoden nach Unterrichtselementen',
								'columns' => array(
									'mth_elem' => array('label' => 'Unterrichtselemente'),
									'elem_num' => array('label' => 'Anzahl')
								)
							)
						);
					?>
				</div> <!-- col -->

			</div> <!-- row row-fluid -->
      </div> <!-- container-fluid -->

        <?php FormElements::scriptRefs(); ?>
    </body>
 </html>   
