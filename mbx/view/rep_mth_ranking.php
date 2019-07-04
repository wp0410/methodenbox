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
use koolreport\processes\CalculatedColumn;
use koolreport\widgets\koolphp\Table;
use koolreport\widgets\google\DonutChart;

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
        <title><?php echo GlobalParameter::$applicationConfig['applicationTitle'] . ' - Berichte: Beliebte Unterrichtsmethoden';?></title>
        <?php FormElements::styleSheetRefs(); ?>
    </head>
    <body>
        <?php FormElements::topNavBar('REP.MRNK', $usr_session); ?>
        <?php FormElements::bottomNavBar('REP.MRNK'); ?>
        
        <div class="container-fluid">
            <div class="row row-fluid"><div class="col"><br></div></div>
		
            <div class="row row-fluid">
				<div class="col">
					<div class="alert alert-primary" role="alert"><center><h4>Berichte und Statistiken / Beliebte Unterrichtsmethoden</h4></center></div>
				</div>
			</div> <!-- row -->
			 
			<div class="row row-fluid">
				<div class="col col-sm-6 col-md-6 col-xl-6">
					<div class="alert alert-primary"><center><h4>Downloads: Top 5</h4></center></div>
					<?php
						$cn = DatabaseConnection::get_report_connection();
						Table::create(
							array(
								'dataSource' => (new PdoDataSource($cn))->query('
									SELECT mth.mth_id, mth.mth_name, subj.mth_sub_name, suba.mth_area_name, mth.num_dnl
									FROM   vi_rep_mth_dnl_top AS mth
										   INNER JOIN vi_mth_subjects AS subj ON subj.mth_sub_val = mth.mth_subject
										   INNER JOIN vi_mth_subject_areas AS suba ON suba.mth_area_val = mth.mth_subject_area
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
				</div> <!-- col col-sm-6 ... -->

				<div class="col col-sm-6 col-md-6 col-xl-6">
					<div class="alert alert-primary"><center><h4>Bewertungen: Top 5</h4></center></div>
					<?php
						$cn = DatabaseConnection::get_report_connection();
						Table::create(
							array(
								'dataSource' => (new PdoDataSource($cn))->query('
									SELECT mth.mth_id, mth.mth_name, subj.mth_sub_name, suba.mth_area_name, num_rate, avg_rate, min_rate, max_rate
									FROM   vi_rep_mth_rtg_top AS mth
										   INNER JOIN vi_mth_subjects AS subj ON subj.mth_sub_val = mth.mth_subject
										   INNER JOIN vi_mth_subject_areas AS suba ON suba.mth_area_val = mth.mth_subject_area
								')->pipe(
									new CalculatedColumn(array(
										'rowNum' => '{#}+1',
										'avgVal' => array(
											'exp' => function($data) { return substr($data['avg_rate'] . '.0', 0, 3); },
											'type' => 'string'
										)
									))
								),
								'columns' => array(
									'rowNum' => array('label' => 'Rang'),
									'mth_name' => array('label' => 'Name der Methode'),
									'mth_sub_name' => array('label' => 'Unterrichtsfach'),
									'mth_area_name' => array('label' => 'Fachbereich'),
									'num_rate' => array('label' => 'Anzahl', 'cssStyle' => 'text-align:right'),
									'min_rate' => array('label' => 'Min', 'cssStyle' => 'text-align:right'),
									'max_rate' => array('label' => 'Max', 'cssStyle' => 'text-align:right'),
									'avgVal' => array(
										'label' => 'Mittel', 
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
				</div> <!-- col col-sm-6 ... -->
			</div> <!-- row -->

			<div class="row row-fluid">
				<div class="col col-sm-6 col-md-6 col-xl-6">
					<div class="card">
						<?php
							$cn = DatabaseConnection::get_report_connection();
							
							DonutChart::create(
								array(
									'dataSource' => (new PdoDataSource($cn))->query("
										SELECT mth_id, mth_name, num_dnl
										FROM   vi_rep_mth_dnl_top
										UNION ALL 
										SELECT 0 AS mth_id, 'Rest' mth_name, COUNT(1) AS num_dnl
										FROM   ta_mth_method_download AS dnl
										WHERE  dnl.dnl_mth_id NOT IN ( SELECT mth_id FROM vi_rep_mth_dnl_top )
										ORDER BY mth_id DESC
									"),
									'title' => 'Relativer Anteil der Top 5',
									'columns' => array(
										'mth_name' => array('label' => 'Name'),
										'num_dnl' => array('label' => 'Anzahl')
									)
								)
							);
						?>
					</div> <!-- card -->
				</div> <!-- col col-sm-6 ... -->

				<div class="col col-sm-6 col-md-6 col-xl-6">
					<div class="card">
						<?php
							$cn = DatabaseConnection::get_report_connection();
							
							DonutChart::create(
								array(
									'dataSource' => (new PdoDataSource($cn))->query("
										SELECT rtop.mth_id, rtop.mth_name, rtop.num_rate, rtop.avg_rate
										FROM   vi_rep_mth_rtg_top AS rtop
										UNION ALL 
										SELECT 0 mth_id, 'Rest' mth_name, COUNT(1) num_rate, ROUND(SUM(rtg.rtg_value) / COUNT(1), 1) avg_rate 
										FROM   ta_mth_method_rating AS rtg
										WHERE  rtg.rtg_mth_id NOT IN ( SELECT mth_id FROM vi_rep_mth_rtg_top )
										ORDER BY mth_id DESC
									"),
									'title' => 'Relativer Anteil der Top 5',
									'columns' => array(
										'mth_name' => array('label' => 'Name'),
										'num_rate' => array('label' => 'Anzahl')
									)
								)
							);
						?>
					</div> <!-- card -->
				</div> <!-- col col-sm-6 ... -->
			</div> <!-- row -->

        </div> <!-- container-fluid -->

        <?php FormElements::scriptRefs(); ?>
    </body>
 </html>   
