-----------------------------------------------------------------------------------------
--  Copyright (c) 2018, 2019 Walter Pachlinger (walter.pachlinger@gmail.com)
--    
--  Licensed under the Apache License, Version 2.0 (the "License"); you may not use this 
--  file except in compliance with the License. You may obtain a copy of the License at
--      http://www.apache.org/licenses/LICENSE-2.0
--  Unless required by applicable law or agreed to in writing, software distributed under 
--  the License is distributed ON an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF 
--  ANY KIND, either express or implied. See the License for the specific language 
--  governing permissions and limitations under the License.
------------------------------------------------------------------------------------------

CREATE TABLE `ta_usr_account` (
	`usr_id` INT(11) NOT NULL AUTO_INCREMENT,
	`usr_fst_name` VARCHAR(127) NOT NULL COLLATE 'utf8_unicode_ci',
	`usr_lst_name` VARCHAR(127) NOT NULL COLLATE 'utf8_unicode_ci',
	`usr_email` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	`usr_pwd` CHAR(64) NOT NULL COLLATE 'utf8_unicode_ci',
	`usr_salt` CHAR(16) NOT NULL COLLATE 'utf8_unicode_ci',
	`usr_register_date` DATETIME NOT NULL,
	`usr_confirm_date` DATETIME NULL DEFAULT NULL,
	`usr_login_date` DATETIME NULL DEFAULT NULL,
	`usr_fail_count` INT(11) NOT NULL DEFAULT 0,
	`usr_status` INT(11) NOT NULL DEFAULT 0,
	`usr_role` INT(11) NOT NULL DEFAULT 0,
	`usr_challenge` VARCHAR(32) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	PRIMARY KEY (`usr_id`),
	UNIQUE INDEX `usr_email` (`usr_email`) )
COLLATE='utf8_unicode_ci';

CREATE TABLE `ta_usr_role` (
	`role_name` VARCHAR(127) NOT NULL COLLATE 'utf8_unicode_ci',
	`role_description` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	`role_symbol` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	`role_seq` INT(3) NOT NULL DEFAULT 0,
	PRIMARY KEY (`role_name`) )
COLLATE='utf8_unicode_ci';

INSERT INTO ta_usr_role( role_name, role_description, role_symbol, role_seq ) VALUES
	('USER',   'Normaler Benutzer',  'fa-user-o', 1),
	('METHOD', 'Methoden Verwalten', 'fa-cloud-upload', 2),
	('ADMIN',  'Administrator',      'fa-cog', 3);

CREATE TABLE `ta_usr_permission` (
	`perm_name` VARCHAR(127) NOT NULL COLLATE 'utf8_unicode_ci',
	`perm_description` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	`perm_authenticated` INT(11) NOT NULL DEFAULT 0,
	`perm_unauthenticated` INT(11) NOT NULL DEFAULT 0,
	`perm_seq` INT(3) NOT NULL DEFAULT 0,
	PRIMARY KEY (`perm_name`) )
COLLATE='utf8_unicode_ci';

INSERT INTO ta_usr_permission ( perm_name, perm_description, perm_authenticated, perm_unauthenticated, perm_seq ) VALUES 
	( 'USR.REG',  'Benutzer registrieren', 0, 1, 12 ),
	( 'USR.CONF', 'Benutzerregistrierung bestätigen', 0, 1, 13 ),
	( 'USR.IN',   'Benutzer anmelden', 0, 1, 11 ),
	( 'USR.OUT',  'Benutzer abmelden', 1, 0, 22 ),
	( 'USR.OPT',  'Benutzer Einstellungen', 1, 0, 21 ),
	( 'MTH.SRCH', 'Methoden suchen', 1, 1, 31 ),
	( 'MTH.NEW',  'Methoden anlegen', 1, 0, 32 ),
	( 'MTH.RATE', 'Methoden bewerten', 1, 0, 33 ),
	( 'MTH.ADM',  'Methoden verwalten', 1, 0, 34 ),
	( 'ADM.USR',  'Benutzer verwalten', 1, 0, 41 ),
	( 'ADM.REQ',  'Anfragen bearbeiten', 1, 0, 42 ),
	( 'REP.MRNK', 'Bericht Methoden Ranking', 1, 1, 51 ),
	( 'REP.MST',  'Bericht Methoden Statistik', 1, 1, 52 );

CREATE TABLE `ta_usr_role_permission` (
	`role_name` VARCHAR(127) NOT NULL COLLATE 'utf8_unicode_ci',
	`perm_name` VARCHAR(127) NOT NULL COLLATE 'utf8_unicode_ci',
	PRIMARY KEY (`role_name`, `perm_name`),
	INDEX `fk_rp_role` (`role_name`),
	INDEX `fk_rp_perm` (`perm_name`),
	CONSTRAINT `fk_rp_perm` FOREIGN KEY (`perm_name`) REFERENCES `ta_usr_permission` (`perm_name`),
	CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_name`) REFERENCES `ta_usr_role` (`role_name`) )
COLLATE='utf8_unicode_ci';

INSERT INTO ta_usr_role_permission( role_name, perm_name ) VALUES 
	( 'USER', 'MTH.SRCH' ),
	( 'USER', 'MTH.RATE' ),
	( 'USER', 'REP.MRNK' ),
	( 'USER', 'REP.MST'  ),
	( 'USER', 'USR.OPT'  ),
	( 'USER', 'USR.OUT'  ),
	( 'METHOD', 'MTH.SRCH' ),
	( 'METHOD', 'MTH.RATE' ),
	( 'METHOD', 'REP.MRNK' ),
	( 'METHOD', 'REP.MST'  ),
	( 'METHOD', 'USR.OPT'  ),
	( 'METHOD', 'USR.OUT'  ),
	( 'METHOD', 'MTH.NEW'  ),
	( 'ADMIN', 'MTH.SRCH' ),
	( 'ADMIN', 'MTH.RATE' ),
	( 'ADMIN', 'REP.MRNK' ),
	( 'ADMIN', 'REP.MST'  ),
	( 'ADMIN', 'USR.OPT'  ),
	( 'ADMIN', 'USR.OUT'  ),
	( 'ADMIN', 'MTH.NEW'  ),
	( 'ADMIN', 'MTH.ADM' ),
	( 'ADMIN', 'ADM.USR' ),
	( 'ADMIN', 'ADM.REQ' );

CREATE TABLE `ta_usr_account_role` (
	`rl_usr_id` INT(11) NOT NULL,
	`rl_role_name` VARCHAR(127) NOT NULL COLLATE 'utf8_unicode_ci',
	PRIMARY KEY (`rl_usr_id`, `rl_role_name`),
	INDEX `fk_acc_usr` (`rl_usr_id`),
	INDEX `fk_acc_role` (`rl_role_name`),
	CONSTRAINT `fk_acc_role` FOREIGN KEY (`rl_role_name`) REFERENCES `ta_usr_role` (`role_name`),
	CONSTRAINT `fk_acc_usr` FOREIGN KEY (`rl_usr_id`) REFERENCES `ta_usr_account` (`usr_id`) )
COLLATE='utf8_unicode_ci';

CREATE TABLE `ta_usr_session` (
	`ses_id` INT(11) NOT NULL AUTO_INCREMENT,
	`ses_start_time` DATETIME NOT NULL,
	`ses_end_time` DATETIME NOT NULL,
	`ses_last_change` DATETIME NOT NULL,
	`ses_usr_id` INT(11) NOT NULL,
	`ses_usr_email` VARCHAR(127) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`ses_usr_full_name` VARCHAR(127) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`ses_salt` CHAR(16) NOT NULL COLLATE 'utf8_unicode_ci',
	`ses_permissions` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	PRIMARY KEY (`ses_id`),
	INDEX `fk_ses_usr_id` (`ses_usr_id`),
	CONSTRAINT `fk_ses_usr_id` FOREIGN KEY (`ses_usr_id`) REFERENCES `ta_usr_account` (`usr_id`) ) 
COLLATE='utf8_unicode_ci';

CREATE TABLE `ta_log_security` (
	`log_id` INT(11) NOT NULL AUTO_INCREMENT,
	`log_timestamp` DATETIME NOT NULL,
	`log_client_id` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`log_remote_ip` VARCHAR(127) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`log_remote_host` VARCHAR(127) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`log_detail` VARCHAR(2047) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	PRIMARY KEY (`log_id`)
)
COLLATE='utf8_unicode_ci';

CREATE TABLE `ta_mth_subject_area` (
	`mth_sub_seq` INT(11) NOT NULL,
	`mth_sub_val` VARCHAR(15) NOT NULL COLLATE 'utf8_unicode_ci',
	`mth_sub_name` VARCHAR(63) NOT NULL COLLATE 'utf8_unicode_ci',
	`mth_area_seq` INT(11) NOT NULL,
	`mth_area_val` VARCHAR(15) NOT NULL COLLATE 'utf8_unicode_ci',
	`mth_area_name` VARCHAR(63) NOT NULL COLLATE 'utf8_unicode_ci'
) COLLATE='utf8_unicode_ci';

INSERT INTO ta_mth_subject_area ( mth_sub_seq, mth_sub_val, mth_sub_name, mth_area_seq, mth_area_val, mth_area_name )
VALUES
	(10, 'BW', 'Betriebswirtschaft',   0, 'BW.000', 'Finanzierung'),
    (10, 'BW', 'Betriebswirtschaft',  10, 'BW.010', 'F&uuml;hrung'),
    (10, 'BW', 'Betriebswirtschaft',  20, 'BW.020', 'Investition'),
    (10, 'BW', 'Betriebswirtschaft',  30, 'BW.030', 'Kaufvertrag'),
	(10, 'BW', 'Betriebswirtschaft',  40, 'BW.040', 'Kennzahlen'),
	(10, 'BW', 'Betriebswirtschaft',  50, 'BW.050', 'Management'),
	(10, 'BW', 'Betriebswirtschaft',  60, 'BW.060', 'Marketing'),
	(10, 'BW', 'Betriebswirtschaft',  70, 'BW.070', 'Materialwirtschaft'),
	(10, 'BW', 'Betriebswirtschaft',  80, 'BW.080', '&Ouml;komanagement'),
	(10, 'BW', 'Betriebswirtschaft',  90, 'BW.090', 'Organisation'),
	(10, 'BW', 'Betriebswirtschaft', 100, 'BW.100', 'Personal'),
	(10, 'BW', 'Betriebswirtschaft', 110, 'BW.110', 'Rechtsformen'),
	(20, 'RW', 'Rechnungswesen', 1, 'RW.01', 'Rechnungswesen Teil 1'),
	(20, 'RW', 'Rechnungswesen', 2, 'RW.02', 'Rechnungswesen Teil 2'),
	(20, 'RW', 'Rechnungswesen', 3, 'RW.03', 'Rechnungswesen Teil 3'),
	(30, 'WI', 'Wirtschaftsinformatik', 1, 'WI.01', 'Kapitel 1'),
	(30, 'WI', 'Wirtschaftsinformatik', 2, 'WI.02', 'Kapitel 2'),
	(30, 'WI', 'Wirtschaftsinformatik', 3, 'WI.03', 'Kapitel 3'),
	(30, 'WI', 'Wirtschaftsinformatik', 4, 'WI.04', 'Kapitel 4'),
	(30, 'WI', 'Wirtschaftsinformatik', 5, 'WI.05', 'Kapitel 5'),
	(30, 'WI', 'Wirtschaftsinformatik', 6, 'WI.06', 'Kapitel 6');

CREATE TABLE `ta_mth_selections` (
	`mth_sel_val` VARCHAR(15) NOT NULL COLLATE 'utf8_unicode_ci',
	`mth_opt_seq` INT(11) NOT NULL,
	`mth_opt_val` VARCHAR(15) NOT NULL COLLATE 'utf8_unicode_ci',
	`mth_opt_name` VARCHAR(63) NOT NULL COLLATE 'utf8_unicode_ci' )
COLLATE='utf8_unicode_ci';

INSERT INTO ta_mth_selections ( mth_sel_val, mth_opt_seq, mth_opt_val, mth_opt_name )
VALUES
	('JG', 10, '1', 'Jahrgang 1'),
	('JG', 20, '2', 'Jahrgang 2'),
	('JG', 30, '3', 'Jahrgang 3'),
	('JG', 40, '4', 'Jahrgang 4'),
	('JG', 50, '5', 'Jahrgang 5'),
	('TM.P', 10, '1', '&lt; 30 Min'),
	('TM.P', 20, '2', '30-60 Min'),
	('TM.P', 30, '3', '60-90 Min'),
	('TM.P', 40, '4', '&gt; 90 Min'),
	('TM.E', 10, '1', '&lt; 30 Min'),
	('TM.E', 20, '2', '30-60 Min'),
	('TM.E', 30, '3', '60-90 Min'),
	('TM.E', 40, '4', '&gt; 90 Min');

CREATE TABLE `ta_mth_method_header` (
	`mth_id` INT(11) NOT NULL AUTO_INCREMENT,
	`mth_name` VARCHAR(127) NOT NULL COLLATE 'utf8_unicode_ci',
	`mth_summary` VARCHAR(4000) NOT NULL COLLATE 'utf8_unicode_ci',
	`mth_subject` VARCHAR(15) NOT NULL COLLATE 'utf8_unicode_ci',
	`mth_subject_area` VARCHAR(15) NOT NULL COLLATE 'utf8_unicode_ci',
	`mth_age_grp` VARCHAR(15) NOT NULL COLLATE 'utf8_unicode_ci',
	`mth_prep_time` VARCHAR(15) NOT NULL COLLATE 'utf8_unicode_ci',
	`mth_exec_time` VARCHAR(15) NOT NULL COLLATE 'utf8_unicode_ci',
	`mth_phase` VARCHAR(15) NOT NULL COLLATE 'utf8_unicode_ci',
	`mth_soc_form` VARCHAR(15) NOT NULL COLLATE 'utf8_unicode_ci',
	`mth_authors` VARCHAR(127) NOT NULL COLLATE 'utf8_unicode_ci',
	`mth_owner_id` INT(11) NOT NULL,
	`mth_create_time` DATETIME NOT NULL,
	PRIMARY KEY (`mth_id`),
	INDEX `fk_owner_id` (`mth_owner_id`),
	CONSTRAINT `fk_owner_id` FOREIGN KEY (`mth_owner_id`) REFERENCES `ta_usr_account` (`usr_id`) )
COLLATE='utf8_unicode_ci';

CREATE TABLE `ta_mth_method_file` (
	`file_mth_id` INT(11) NOT NULL,
	`file_guid` VARCHAR(32) NOT NULL COLLATE 'utf8_unicode_ci',
	`file_type` VARCHAR(16) NOT NULL COLLATE 'utf8_unicode_ci',
	`file_name` VARCHAR(127) NOT NULL COLLATE 'utf8_unicode_ci',
	`file_data` MEDIUMBLOB NULL DEFAULT NULL,
	PRIMARY KEY (`file_mth_id`),
	CONSTRAINT `fk_file_mth_id` FOREIGN KEY (`file_mth_id`) REFERENCES `ta_mth_method_header` (`mth_id`) )
COLLATE='utf8_unicode_ci';

CREATE TABLE `ta_mth_method_rating` (
	`rtg_id` INT(11) NOT NULL AUTO_INCREMENT,
	`rtg_mth_id` INT(11) NOT NULL,
	`rtg_usr_id` INT(11) NULL DEFAULT NULL,
	`rtg_date` DATETIME NOT NULL,
	`rtg_value` DECIMAL(10,0) NOT NULL,
	`rtg_summary` VARCHAR(400) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	PRIMARY KEY (`rtg_id`),
	INDEX `fk_rtg_mth_id` (`rtg_mth_id`),
	INDEX `fk_rtg_usr_id` (`rtg_usr_id`),
	CONSTRAINT `fk_rtg_mth_id` FOREIGN KEY (`rtg_mth_id`) REFERENCES `ta_mth_method_header` (`mth_id`),
	CONSTRAINT `fk_rtg_usr_id` FOREIGN KEY (`rtg_usr_id`) REFERENCES `ta_usr_account` (`usr_id`) )
COLLATE='utf8_unicode_ci';

CREATE TABLE `ta_mth_method_download` (
	`dnl_id` INT(11) NOT NULL AUTO_INCREMENT,
	`dnl_mth_id` INT(11) NOT NULL,
	`dnl_usr_id` INT(11) NULL DEFAULT NULL,
	`dnl_date` DATETIME NOT NULL,
	PRIMARY KEY (`dnl_id`),
	INDEX `fk_dnl_mth_id` (`dnl_mth_id`),
	INDEX `fk_dnl_usr_id` (`dnl_usr_id`),
	CONSTRAINT `fk_dnl_mth_id` FOREIGN KEY (`dnl_mth_id`) REFERENCES `ta_mth_method_header` (`mth_id`),
	CONSTRAINT `fk_dnl_usr_id` FOREIGN KEY (`dnl_usr_id`) REFERENCES `ta_usr_account` (`usr_id`) )
COLLATE='utf8_unicode_ci';

CREATE TABLE `ta_aux_cache` (
	`cch_obj_id` VARCHAR(32) NOT NULL COLLATE 'utf8_unicode_ci',
	`cch_owner_id` INT(11) NOT NULL,
	`cch_obj_data` VARCHAR(4000) NOT NULL COLLATE 'utf8_unicode_ci',
	`cch_lines_pp` INT(11) NOT NULL DEFAULT 5,
	`cch_store_date` DATETIME NOT NULL,
	`cch_expiry_date` DATETIME NOT NULL,
	PRIMARY KEY (`cch_obj_id`) )
COLLATE='utf8_unicode_ci';

CREATE TABLE `ta_aux_contact_request` (
	`req_id` INT(11) NOT NULL AUTO_INCREMENT,
	`usr_addr_form` VARCHAR(127) NOT NULL COLLATE 'utf8_unicode_ci',
	`usr_fst_name` VARCHAR(127) NOT NULL COLLATE 'utf8_unicode_ci',
	`usr_lst_name` VARCHAR(127) NOT NULL COLLATE 'utf8_unicode_ci',
	`usr_email` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	`req_type` VARCHAR(7) NOT NULL COLLATE 'utf8_unicode_ci',
	`req_text` VARCHAR(4000) NOT NULL COLLATE 'utf8_unicode_ci',
	`req_create_time` DATETIME NOT NULL,
	`req_close_time` DATETIME NULL DEFAULT NULL,
	`req_close_usr_id` INT(11) NULL DEFAULT NULL,
	`req_answer` VARCHAR(4000) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	PRIMARY KEY (`req_id`) )
COLLATE='utf8_unicode_ci';

CREATE OR REPLACE VIEW vi_mth_method_download AS
	SELECT dnl_mth_id, COUNT(1) AS dnl_cnt, MIN(dnl_date) AS dnl_first_tm, MAX(dnl_date) AS dnl_last_tm
	FROM ta_mth_method_download
	GROUP BY dnl_mth_id;

CREATE OR REPLACE VIEW vi_mth_method_rates AS
	SELECT rtg_mth_id, COUNT(1) AS rtg_cnt, MIN(rtg_date) AS rtg_first_tm, MAX(rtg_date) AS rtg_last_tm, 
		   MIN(rtg_value) AS rtg_min_val, MAX(rtg_value) AS rtg_max_val, AVG(rtg_value) AS rtg_avg_val
	FROM ta_mth_method_rating
	GROUP BY rtg_mth_id;
    
CREATE OR REPLACE VIEW vi_mth_subjects AS 
	SELECT DISTINCT mth_sub_val, mth_sub_name
	FROM ta_mth_subject_area;

CREATE OR REPLACE VIEW vi_mth_subject_areas AS
	SELECT DISTINCT mth_area_val, mth_area_name
	FROM ta_mth_subject_area;

CREATE OR REPLACE VIEW vi_mth_age_groups AS 
	SELECT DISTINCT mth_opt_val, mth_opt_name
	FROM ta_mth_selections
	WHERE mth_sel_val='JG';

CREATE OR REPLACE VIEW vi_mth_prep_times AS 
	SELECT DISTINCT mth_opt_val, mth_opt_name
	FROM ta_mth_selections
	WHERE mth_sel_val='TM.P';

CREATE OR REPLACE VIEW vi_mth_exec_times AS
	SELECT DISTINCT mth_opt_val, mth_opt_name
	FROM ta_mth_selections
	WHERE mth_sel_val='TM.E';

CREATE OR REPLACE VIEW vi_mth_method_result AS
    SELECT mth.mth_id, mth.mth_name, mth.mth_summary, 
           mth.mth_subject, subj.mth_sub_name AS mth_subject_text, 
           mth.mth_subject_area, area.mth_area_name AS mth_subject_area_text,
           mth.mth_age_grp, age.mth_opt_name AS mth_age_grp_text, 
           mth.mth_prep_time, prep.mth_opt_name AS mth_prep_time_text,
           mth.mth_exec_time, extm.mth_opt_name AS mth_exec_time_text, 
           mth.mth_phase, mth.mth_soc_form, mth.mth_authors, 
           mth.mth_owner_id, mth_create_time, 
           mtf.file_guid, mtf.file_name, 
           dnl.dnl_cnt, dnl.dnl_first_tm, dnl.dnl_last_tm, '' AS dnl_usr_id, 
           rtg.rtg_cnt, rtg.rtg_first_tm, rtg.rtg_last_tm, rtg.rtg_min_val, rtg.rtg_max_val, rtg.rtg_avg_val
    FROM   ta_mth_method_header mth
              INNER JOIN ta_mth_method_file mtf ON mtf.file_mth_id = mth.mth_id
              INNER JOIN vi_mth_subjects subj ON subj.mth_sub_val = mth.mth_subject
              INNER JOIN vi_mth_subject_areas area ON area.mth_area_val = mth.mth_subject_area
              INNER JOIN vi_mth_age_groups age ON age.mth_opt_val = mth.mth_age_grp
              INNER JOIN vi_mth_prep_times prep ON prep.mth_opt_val = mth.mth_prep_time
              INNER JOIN vi_mth_exec_times extm ON extm.mth_opt_val = mth.mth_exec_time
              LEFT JOIN vi_mth_method_download dnl ON dnl.dnl_mth_id = mth.mth_id
              LEFT JOIN vi_mth_method_rates rtg ON rtg.rtg_mth_id = mth.mth_id;

CREATE OR REPLACE VIEW vi_mth_dnl_and_rtg AS
    SELECT dnl.dnl_mth_id, dnl.dnl_usr_id, rtg.rtg_usr_id, MIN(dnl.dnl_date) AS dnl_first_tm, MAX(dnl.dnl_date) AS dnl_last_tm
    FROM   ta_mth_method_download dnl
           LEFT JOIN ta_mth_method_rating rtg ON rtg.rtg_mth_id = dnl.dnl_mth_id and rtg.rtg_usr_id = dnl.dnl_usr_id
    GROUP BY dnl.dnl_mth_id, dnl.dnl_usr_id, rtg.rtg_usr_id;
    
CREATE OR REPLACE VIEW vi_mth_dnl_no_rtg AS
    SELECT dnl_mth_id, dnl_usr_id, rtg_usr_id, dnl_first_tm, dnl_last_tm
    FROM   vi_mth_dnl_and_rtg
    WHERE  dnl_usr_id IS NOT NULL
      AND  rtg_usr_id IS NULL;

CREATE OR REPLACE VIEW vi_mth_method_rating AS 
    SELECT mth.mth_id, mth.mth_name, mth.mth_summary, 
           mth.mth_subject, subj.mth_sub_name AS mth_subject_text, 
           mth.mth_subject_area, area.mth_area_name AS mth_subject_area_text,
           mth.mth_age_grp, age.mth_opt_name AS mth_age_grp_text, 
           mth.mth_prep_time, prep.mth_opt_name AS mth_prep_time_text,
           mth.mth_exec_time, extm.mth_opt_name AS mth_exec_time_text, 
           mth.mth_phase, mth.mth_soc_form, mth.mth_authors, 
           mth.mth_owner_id, mth_create_time, 
           '' AS file_guid, '' AS file_name, 
           0 AS dnl_cnt, dnl.dnl_first_tm, dnl.dnl_last_tm, dnl.dnl_usr_id, 
           0 AS rtg_cnt, '' AS rtg_first_tm, '' AS rtg_last_tm, '' AS rtg_min_val, '' AS rtg_max_val, '' AS rtg_avg_val
    FROM   ta_mth_method_header mth
              INNER JOIN vi_mth_subjects subj ON subj.mth_sub_val = mth.mth_subject
              INNER JOIN vi_mth_subject_areas area ON area.mth_area_val = mth.mth_subject_area
              INNER JOIN vi_mth_age_groups age ON age.mth_opt_val = mth.mth_age_grp
              INNER JOIN vi_mth_prep_times prep ON prep.mth_opt_val = mth.mth_prep_time
              INNER JOIN vi_mth_exec_times extm ON extm.mth_opt_val = mth.mth_exec_time
              INNER JOIN vi_mth_dnl_no_rtg dnl ON dnl.dnl_mth_id = mth.mth_id;

CREATE OR REPLACE VIEW vi_rep_mth_dnl_top AS 
	SELECT mth.mth_id, mth.mth_name, mth.mth_subject, mth.mth_subject_area, COUNT(1) AS num_dnl
	FROM   ta_mth_method_header AS mth
		   INNER JOIN ta_mth_method_download AS dnl ON dnl.dnl_mth_id = mth.mth_id
	GROUP BY mth.mth_id, mth.mth_name, mth.mth_subject, mth.mth_subject_area
	ORDER BY num_dnl DESC LIMIT 5;

CREATE OR REPLACE VIEW vi_rep_mth_rtg_top AS 
	SELECT mth.mth_id, mth.mth_name, mth.mth_subject, mth.mth_subject_area, 
		 COUNT(1) num_rate, ROUND(SUM(rate.rtg_value) / COUNT(1), 1) avg_rate,
		 MIN(rate.rtg_value) min_rate, MAX(rate.rtg_value) max_rate
	FROM  ta_mth_method_header AS mth 
		  INNER JOIN ta_mth_method_rating AS rate ON rate.rtg_mth_id = mth.mth_id
	GROUP BY mth.mth_id, mth.mth_name, mth.mth_subject, mth.mth_subject_area
	ORDER BY avg_rate DESC LIMIT 5;

 
