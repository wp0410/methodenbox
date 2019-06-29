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
include_once '../koolreport-4.0.0/koolreport/core/autoload.php';

use koolreport\datasources\PdoDataSource;
use \koolreport\processes\CalculatedColumn;
use koolreport\widgets\koolphp\Table;
use koolreport\widgets\google\DonutChart;

set_private_warning_handler();
session_start();

$db_conn = DatabaseConnection::get_connection();
$usr_session = new UserSession($db_conn);
$usr_name = '';

if (! empty($_SESSION) && ! empty($_SESSION['user']))
{
    $res = $usr_session->validateSession($_SESSION['user']);
    if ($res->isOK())
    {
        $_SESSION['user'] = $usr_session->getSessionDescriptor();
        $usr_name = $usr_session->ses_usr_email;
    }
}
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <title><?php echo GlobalParameter::$applicationConfig['applicationTitle'] . ' - Berichte und Statistiken / &Uuml;bersicht';?></title>
        <?php FormElements::styleSheetRefs(); ?>
    </head>
    <body>
        <?php FormElements::persTopNavigationBar('REP.OVW', $usr_session->isAuthenticated(), $usr_name, $usr_session->getPermissions()); ?>
        <?php FormElements::bottomNavigationBar('REP.OVW'); ?>
        
        <div class="container-fluid">
             <div class="row row-fluid"><div class="col"><br></div></div>
		
             <div class="row row-fluid">
				<div class="col">
					<div class="alert alert-primary" role="alert"><center><h4>Berichte und Statistiken / Methoden&uuml;bersicht</h4></center></div>
				</div>
			 </div> <!-- row -->
			 
			 <div class="row row-fluid">
				<div class="col col-sm-6 col-md-6 col-xl-6">
					<div class="card">
						<div class="card-header text-center"><h4>Downloads: Top 5</h4></div>
						<div class="card-body">
							<?php
							$cn = DatabaseConnection::get_report_connection();
							Table::create(
								array(
									'dataSource' => (new PdoDataSource($cn))->query('
										 SELECT mth.mth_id, mth.mth_name, subj.mth_sub_name, area.mth_area_name, count(1) num_dnl
										 FROM   ta_mth_method_header mth
												INNER JOIN vi_mth_subjects subj on subj.mth_sub_val = mth.mth_subject
												INNER JOIN vi_mth_subject_areas area on area.mth_area_val = mth.mth_subject_area
												INNER JOIN ta_mth_method_download dnl on dnl.dnl_mth_id = mth.mth_id
										 GROUP BY mth.mth_id, mth.mth_name, subj.mth_sub_name, area.mth_area_name HAVING COUNT(1) > 0
										 ORDER BY num_dnl DESC LIMIT 5
									')->pipe(
										new CalculatedColumn(array(
											'rowNum' => '{#}+1'
										))
									),
									'columns' => array(
										'rowNum' => array('label' => 'Rang'),
										'mth_name' => array('label' => 'Name der Methode'),
										'mth_sub_name' => array('label' => 'Unterrichtsfach'),
										'mth_area_name' => array('label' => 'Fachbereich'),
										'num_dnl' => array('label' => 'Anzahl Downloads', 'cssStyle' => 'text-align:right')
									),
									'cssClass' => array(
										'table' => "table table-striped",
										'th' => "table-dark"
									)
								)
							);
							?>
						</div>
					</div> <!-- card -->
				</div> <!-- col -->

				<div class="col col-sm-6 col-md-6 col-xl-6">
					<div class="card">
						<div class="card-header text-center"><h4>Bewertungen: Top 5</h4></div>
						<div class="card-body">
							<?php
							$cn = DatabaseConnection::get_report_connection();
							Table::create(
								array(
									'dataSource' => (new PdoDataSource($cn))->query('
										 SELECT mth.mth_id, mth.mth_name, subj.mth_sub_name, area.mth_area_name, 
												COUNT(1) num_rate, ROUND(SUM(rate.rtg_value) / COUNT(1), 1) avg_rate
										 FROM   ta_mth_method_header mth
												INNER JOIN vi_mth_subjects subj on subj.mth_sub_val = mth.mth_subject
												INNER JOIN vi_mth_subject_areas area on area.mth_area_val = mth.mth_subject_area
												INNER JOIN ta_mth_method_rating rate on rate.rtg_mth_id = mth.mth_id
										 GROUP BY mth.mth_id, mth.mth_name, subj.mth_sub_name, area.mth_area_name HAVING COUNT(1) > 0
										 ORDER BY avg_rate DESC LIMIT 5
									')->pipe(
										new CalculatedColumn(array(
											'rowNum' => '{#}+1'
										))
									),
									'columns' => array(
										'rowNum' => array('label' => 'Rang'),
										'mth_name' => array('label' => 'Name der Methode'),
										'mth_sub_name' => array('label' => 'Unterrichtsfach'),
										'mth_area_name' => array('label' => 'Fachbereich'),
										'num_rate' => array('label' => 'Anzahl', 'cssStyle' => 'text-align:right'),
										'avg_rate' => array(
											'label' => 'Durchschnitt', 
											'type' => 'decimal',
											'cssStyle' => 'text-align:right' )
									),
									'cssClass' => array(
										'table' => "table table-striped",
										'th' => "table-dark"
									)
								)
							);
							?>
						</div> <!-- card-body -->
					</div> <!-- card -->
				</div> <!-- col -->
			 </div> <!-- row -->
			 
			 <div class="row row-fluid"><div class="col"><br></div></div>
			 
			 <div class="row row-fluid">
				<div class="col col-sm-4 col-md-4 col-xl-4">
					<?php
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
									WHERE sub.mth_subject = 'BW'
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
								'title' => 'Methoden pro Fachbereich Betriebswirtschaft',
								'columns' => array(
									'area_name' => array('label' => 'Fachbereich'),
									'num_per_area' => array('label' => 'Anzahl Gesamt')
								)
							)
						);
					?>
				</div> <!-- col -->

				<div class="col col-sm-4 col-md-4 col-xl-4">
					<?php
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
								'title' => 'Methoden pro Jahrgang',
								'columns' => array(
									'mth_opt_name' => array('label' => 'Jahrgang'),
									'num_per_age' => array('label' => 'Anzahl Gesamt')
								)
							)
						);
					?>
				</div> <!-- col -->
			 </div> <!-- row -->

        </div> <!-- container-fluid -->

        <?php FormElements::scriptRefs(); ?>
    </body>
 </html>   
