-----------------------------------------------------------------------------------------
--  Copyright (c) 2018, 2019 Walter Pachlinger (walter.pachlinger@gmail.com)
--    
--  Licensed under the Apache License, Version 2.0 (the "License"); you may not use this 
--  file except in compliance with the License. You may obtain a copy of the License at
--      http://www.apache.org/licenses/LICENSE-2.0
--  Unless required by applicable law or agreed to in writing, software distributed under 
--  the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF 
--  ANY KIND, either express or implied. See the License for the specific language 
--  governing permissions and limitations under the License.
------------------------------------------------------------------------------------------

create table ta_usr_account (
    usr_id            int not null auto_increment primary key,
    usr_fst_name      varchar(127) collate utf8_unicode_ci not null,
    usr_lst_name      varchar(127) collate utf8_unicode_ci not null,
    usr_email         varchar(255) collate utf8_unicode_ci not null unique,
    usr_pwd           char(64) collate utf8_unicode_ci not null,
    usr_salt          char(16) collate utf8_unicode_ci not null,
    usr_register_date datetime not null,
    usr_confirm_date  datetime default null,
    usr_login_date    datetime default null,
    usr_fail_count    int not null default 0,
    usr_status        int not null default 0,
    usr_role          int not null default 0,
    usr_challenge     varchar(32) collate utf8_unicode_ci
);

create table ta_usr_permissions (
	per_id            int not null auto_increment primary key,
	per_usr_id        int not null,
	per_permission    varchar(15) collate utf8_unicode_ci not null,

	foreign key fk_per_usr_id (per_usr_id) references ta_usr_account (usr_id) match full on delete cascade on update cascade
);

-- OBSOLETE - see below
create table ta_usr_session (
    ses_id            int not null auto_increment primary key,
    ses_start_time    datetime not null,
    ses_end_time      datetime not null,
    ses_last_change   datetime not null,
    ses_usr_id        int not null,
    ses_usr_grant     int not null,
    ses_salt          char(16) collate utf8_unicode_ci not null,
	ses_permissions   varchar(255) collate utf8_unicode_ci,

    foreign key fk_ses_usr_id (ses_usr_id) references ta_usr_account (usr_id) match full on delete cascade on update cascade
);

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
	CONSTRAINT `fk_ses_usr_id` FOREIGN KEY (`ses_usr_id`) REFERENCES `ta_usr_account` (`usr_id`)
);

create table ta_log_usr_session (
	log_id				int not null auto_increment primary key,
    log_time			datetime not null,
    log_usr_email		varchar(255) collate utf8_unicode_ci not null,
    log_usr_pwd			varchar(255) collate utf8_unicode_ci,
    log_status          int not null default 0,
    log_extra           varchar(1023) collate utf8_unicode_ci
);

create table ta_mth_subject_area (
    mth_sub_seq       int not null,
    mth_sub_val       varchar(15) collate utf8_unicode_ci not null,
    mth_sub_name      varchar(63) collate utf8_unicode_ci not null,
    mth_area_seq      int not null,
    mth_area_val      varchar(15) collate utf8_unicode_ci not null,
    mth_area_name     varchar(63) collate utf8_unicode_ci not null
);

insert into ta_mth_subject_area values(10, 'BW', 'Betriebswirtschaft',   0, 'BW.000', 'Finanzierung');
insert into ta_mth_subject_area values(10, 'BW', 'Betriebswirtschaft',  10, 'BW.010', 'F&uuml;hrung');
insert into ta_mth_subject_area values(10, 'BW', 'Betriebswirtschaft',  20, 'BW.020', 'Investition');
insert into ta_mth_subject_area values(10, 'BW', 'Betriebswirtschaft',  30, 'BW.030', 'Kaufvertrag');
insert into ta_mth_subject_area values(10, 'BW', 'Betriebswirtschaft',  40, 'BW.040', 'Kennzahlen');
insert into ta_mth_subject_area values(10, 'BW', 'Betriebswirtschaft',  50, 'BW.050', 'Management');
insert into ta_mth_subject_area values(10, 'BW', 'Betriebswirtschaft',  60, 'BW.060', 'Marketing');
insert into ta_mth_subject_area values(10, 'BW', 'Betriebswirtschaft',  70, 'BW.070', 'Materialwirtschaft');
insert into ta_mth_subject_area values(10, 'BW', 'Betriebswirtschaft',  80, 'BW.080', '&Ouml;komanagement');
insert into ta_mth_subject_area values(10, 'BW', 'Betriebswirtschaft',  90, 'BW.090', 'Organisation');
insert into ta_mth_subject_area values(10, 'BW', 'Betriebswirtschaft', 100, 'BW.100', 'Personal');
insert into ta_mth_subject_area values(10, 'BW', 'Betriebswirtschaft', 110, 'BW.110', 'Rechtsformen');

insert into ta_mth_subject_area values(20, 'RW', 'Rechnungswesen', 1, 'RW.01', 'Rechnungswesen Teil 1');
insert into ta_mth_subject_area values(20, 'RW', 'Rechnungswesen', 2, 'RW.02', 'Rechnungswesen Teil 2');
insert into ta_mth_subject_area values(20, 'RW', 'Rechnungswesen', 3, 'RW.03', 'Rechnungswesen Teil 3');

insert into ta_mth_subject_area values(30, 'WI', 'Wirtschaftsinformatik', 1, 'WI.01', 'Kapitel 1');
insert into ta_mth_subject_area values(30, 'WI', 'Wirtschaftsinformatik', 2, 'WI.02', 'Kapitel 2');
insert into ta_mth_subject_area values(30, 'WI', 'Wirtschaftsinformatik', 3, 'WI.03', 'Kapitel 3');
insert into ta_mth_subject_area values(30, 'WI', 'Wirtschaftsinformatik', 4, 'WI.04', 'Kapitel 4');
insert into ta_mth_subject_area values(30, 'WI', 'Wirtschaftsinformatik', 5, 'WI.05', 'Kapitel 5');
insert into ta_mth_subject_area values(30, 'WI', 'Wirtschaftsinformatik', 6, 'WI.06', 'Kapitel 6');

create table ta_mth_selections (
    mth_sel_val         varchar(15) collate utf8_unicode_ci not null,
    mth_opt_seq         int not null,
    mth_opt_val         varchar(15) collate utf8_unicode_ci not null,
    mth_opt_name        varchar(63) collate utf8_unicode_ci not null
);

insert into ta_mth_selections values('JG', 10, '1', 'Jahrgang 1');
insert into ta_mth_selections values('JG', 20, '2', 'Jahrgang 2');
insert into ta_mth_selections values('JG', 30, '3', 'Jahrgang 3');
insert into ta_mth_selections values('JG', 40, '4', 'Jahrgang 4');
insert into ta_mth_selections values('JG', 50, '5', 'Jahrgang 5');

insert into ta_mth_selections values('TM.P', 10, '1', '&lt; 30 Min');
insert into ta_mth_selections values('TM.P', 20, '2', '30-60 Min');
insert into ta_mth_selections values('TM.P', 30, '3', '60-90 Min');
insert into ta_mth_selections values('TM.P', 40, '4', '&gt; 90 Min');

insert into ta_mth_selections values('TM.E', 10, '1', '&lt; 30 Min');
insert into ta_mth_selections values('TM.E', 20, '2', '30-60 Min');
insert into ta_mth_selections values('TM.E', 30, '3', '60-90 Min');
insert into ta_mth_selections values('TM.E', 40, '4', '&gt; 90 Min');

create table ta_mth_method_header (
    mth_id              int not null auto_increment primary key,
    mth_name            varchar(127)  collate utf8_unicode_ci not null,
    mth_summary         varchar(4000) collate utf8_unicode_ci not null,
    mth_subject         varchar(15)   collate utf8_unicode_ci not null,
    mth_subject_area    varchar(15)   collate utf8_unicode_ci not null,
    mth_age_grp         varchar(15)   collate utf8_unicode_ci not null,
    mth_prep_time       varchar(15)   collate utf8_unicode_ci not null,
    mth_exec_time       varchar(15)   collate utf8_unicode_ci not null,
    mth_phase           varchar(15)   collate utf8_unicode_ci not null,
    mth_soc_form        varchar(15)   collate utf8_unicode_ci not null,
    mth_authors         varchar(127)  collate utf8_unicode_ci not null,
    mth_owner_id        int not null,
    mth_create_time     datetime not null,
    
    foreign key fk_owner_id (mth_owner_id) references ta_usr_account (usr_id) match full on delete restrict on update restrict
);

create table ta_mth_method_file (
    file_mth_id         int not null primary key,
    file_guid           varchar(32)  collate utf8_unicode_ci not null,
    file_type           varchar(16)  collate utf8_unicode_ci not null,
    file_name           varchar(127) collate utf8_unicode_ci not null,
    file_data           mediumblob,
    
    foreign key fk_file_mth_id (file_mth_id) references ta_mth_method_header (mth_id) match full on delete cascade on update cascade
);

create table ta_mth_method_rating (
    rtg_id              int not null auto_increment primary key,
    rtg_mth_id          int not null,
    rtg_usr_id          int,
    rtg_date            datetime not null,
    rtg_value           decimal not null,
    rtg_summary         varchar(400) collate utf8_unicode_ci,
    
    foreign key fk_rtg_mth_id (rtg_mth_id) references ta_mth_method_header (mth_id) match full on delete cascade on update cascade,
    foreign key fk_rtg_usr_id (rtg_usr_id) references ta_usr_account (usr_id) match full on delete set null on update set null
);

create table ta_mth_method_download (
    dnl_id              int not null auto_increment primary key,
    dnl_mth_id          int not null,
    dnl_usr_id          int,
    dnl_date            datetime not null,
    
    foreign key fk_dnl_mth_id (dnl_mth_id) references ta_mth_method_header (mth_id) match full on delete cascade on update cascade,
    foreign key fk_dnl_usr_id (dnl_usr_id) references ta_usr_account (usr_id) match full on delete set null on update set null
);

create table ta_aux_cache (
    cch_obj_id          varchar(32) collate utf8_unicode_ci not null primary key,
    cch_owner_id        int not null,
    cch_obj_data        varchar(4000) collate utf8_unicode_ci not null,
    cch_lines_pp		int not null default 5,
    cch_store_date      datetime not null,
    cch_expiry_date     datetime not null
);

create or replace view vi_mth_method_download as
    select dnl_mth_id, 
           count(1) as dnl_cnt, 
           min(dnl_date) as dnl_first_tm, 
           max(dnl_date) as dnl_last_tm
    from   ta_mth_method_download
    group by dnl_mth_id;

create or replace view vi_mth_method_rates as
    select rtg_mth_id,
           count(1) as rtg_cnt,
           min(rtg_date) as rtg_first_tm,
           max(rtg_date) as rtg_last_tm,
           min(rtg_value) as rtg_min_val,
           max(rtg_value) as rtg_max_val,
           avg(rtg_value) as rtg_avg_val
    from   ta_mth_method_rating
    group by rtg_mth_id;
    
create or replace view vi_mth_subjects as select distinct mth_sub_val, mth_sub_name from ta_mth_subject_area;
create or replace view vi_mth_subject_areas as select distinct mth_area_val, mth_area_name from ta_mth_subject_area;
create or replace view vi_mth_age_groups as select distinct mth_opt_val, mth_opt_name from ta_mth_selections where mth_sel_val='JG';
create or replace view vi_mth_prep_times as select distinct mth_opt_val, mth_opt_name from ta_mth_selections where mth_sel_val='TM.P';
create or replace view vi_mth_exec_times as select distinct mth_opt_val, mth_opt_name from ta_mth_selections where mth_sel_val='TM.E';

create or replace view vi_mth_method_result as
    select mth.mth_id, mth.mth_name, mth.mth_summary, 
           mth.mth_subject, subj.mth_sub_name as mth_subject_text, 
           mth.mth_subject_area, area.mth_area_name as mth_subject_area_text,
           mth.mth_age_grp, age.mth_opt_name as mth_age_grp_text, 
           mth.mth_prep_time, prep.mth_opt_name as mth_prep_time_text,
           mth.mth_exec_time, extm.mth_opt_name as mth_exec_time_text, 
           mth.mth_phase, mth.mth_soc_form, mth.mth_authors, 
           mth.mth_owner_id, mth_create_time, 
           mtf.file_guid, mtf.file_name, 
           dnl.dnl_cnt, dnl.dnl_first_tm, dnl.dnl_last_tm, '' as dnl_usr_id, 
           rtg.rtg_cnt, rtg.rtg_first_tm, rtg.rtg_last_tm, rtg.rtg_min_val, rtg.rtg_max_val, rtg.rtg_avg_val
    from   ta_mth_method_header mth
              inner join ta_mth_method_file mtf on mtf.file_mth_id = mth.mth_id
              inner join vi_mth_subjects subj on subj.mth_sub_val = mth.mth_subject
              inner join vi_mth_subject_areas area on area.mth_area_val = mth.mth_subject_area
              inner join vi_mth_age_groups age on age.mth_opt_val = mth.mth_age_grp
              inner join vi_mth_prep_times prep on prep.mth_opt_val = mth.mth_prep_time
              inner join vi_mth_exec_times extm on extm.mth_opt_val = mth.mth_exec_time
              left join vi_mth_method_download dnl on dnl.dnl_mth_id = mth.mth_id
              left join vi_mth_method_rates rtg on rtg.rtg_mth_id = mth.mth_id;

create or replace view vi_mth_dnl_and_rtg as
    select dnl.dnl_mth_id, dnl.dnl_usr_id, rtg.rtg_usr_id, min(dnl.dnl_date) as dnl_first_tm, max(dnl.dnl_date) as dnl_last_tm
    from   ta_mth_method_download dnl
           left join ta_mth_method_rating rtg on rtg.rtg_mth_id = dnl.dnl_mth_id and rtg.rtg_usr_id = dnl.dnl_usr_id
    group by dnl.dnl_mth_id, dnl.dnl_usr_id, rtg.rtg_usr_id;
    
create or replace view vi_mth_dnl_no_rtg as
    select dnl_mth_id, dnl_usr_id, rtg_usr_id, dnl_first_tm, dnl_last_tm
    from   vi_mth_dnl_and_rtg
    where  dnl_usr_id is not null
      and  rtg_usr_id is null;

create or replace view vi_mth_method_rating as 
    select mth.mth_id, mth.mth_name, mth.mth_summary, 
           mth.mth_subject, subj.mth_sub_name as mth_subject_text, 
           mth.mth_subject_area, area.mth_area_name as mth_subject_area_text,
           mth.mth_age_grp, age.mth_opt_name as mth_age_grp_text, 
           mth.mth_prep_time, prep.mth_opt_name as mth_prep_time_text,
           mth.mth_exec_time, extm.mth_opt_name as mth_exec_time_text, 
           mth.mth_phase, mth.mth_soc_form, mth.mth_authors, 
           mth.mth_owner_id, mth_create_time, 
           '' as file_guid, '' as file_name, 
           0 as dnl_cnt, dnl.dnl_first_tm, dnl.dnl_last_tm, dnl.dnl_usr_id, 
           0 as rtg_cnt, '' as rtg_first_tm, '' as rtg_last_tm, '' as rtg_min_val, '' as rtg_max_val, '' as rtg_avg_val
    from   ta_mth_method_header mth
              inner join vi_mth_subjects subj on subj.mth_sub_val = mth.mth_subject
              inner join vi_mth_subject_areas area on area.mth_area_val = mth.mth_subject_area
              inner join vi_mth_age_groups age on age.mth_opt_val = mth.mth_age_grp
              inner join vi_mth_prep_times prep on prep.mth_opt_val = mth.mth_prep_time
              inner join vi_mth_exec_times extm on extm.mth_opt_val = mth.mth_exec_time
              inner join vi_mth_dnl_no_rtg dnl on dnl.dnl_mth_id = mth.mth_id;

create or replace view vi_usr_roles as
select usr_id, max(per_role_client) as per_role_client, max(per_role_upload) as per_role_upload, max(per_role_admin) as per_role_admin
from (
	select usr_id, 1 per_role_client, 0 per_role_upload, 0 per_role_admin
	from   ta_usr_account
	union all
	select usr.usr_id, 0 per_role_client, 1 per_role_upload, 0 per_role_admin
	from   ta_usr_account usr
		inner join ta_usr_permissions usp on usp.per_usr_id = usr.usr_id
	where  usp.per_permission in ('MTH.NEW','MTH.ADM')
	group by usr.usr_id having count(1) = 2
	union all
	select usr.usr_id, 0 per_role_client, 0 per_role_upload, 1 per_role_admin
	from   ta_usr_account usr
		inner join ta_usr_permissions usp on usp.per_usr_id = usr.usr_id
	where  usp.per_permission in ('ADM.USR')
	group by usr.usr_id having count(1) = 1
) tab1 group by tab1.usr_id;

CREATE OR REPLACE VIEW vi_rep_mth_dnl_top AS 
SELECT mth.mth_id, mth.mth_name, COUNT(1) AS num_dnl
FROM   ta_mth_method_header AS mth
       INNER JOIN ta_mth_method_download AS dnl ON dnl.dnl_mth_id = mth.mth_id
GROUP BY mth.mth_id, mth.mth_name
ORDER BY num_dnl DESC LIMIT 5;

CREATE OR REPLACE VIEW vi_rep_mth_rtg_top AS 
SELECT mth.mth_id, mth.mth_name, mth.mth_subject, mth.mth_subject_area, 
   	 COUNT(1) num_rate, ROUND(SUM(rate.rtg_value) / COUNT(1), 1) avg_rate,
   	 MIN(rate.rtg_value) min_rate, MAX(rate.rtg_value) max_rate
FROM   ta_mth_method_header AS mth INNER JOIN ta_mth_method_rating AS rate on rate.rtg_mth_id = mth.mth_id
GROUP BY mth.mth_id, mth.mth_name, mth.mth_subject, mth.mth_subject_area
ORDER BY avg_rate DESC LIMIT 5

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
	PRIMARY KEY (`req_id`)
);

-- PRELIMINARY - DO NOT INSTALL
CREATE TABLE ta_usr_role (
	role_name VARCHAR(127) COLLATE UTF8_UNICODE_CI NOT NULL,
	role_description VARCHAR(255) COLLATE UTF8_UNICODE_CI NOT NULL,
	role_img VARCHAR(255) COLLATE UTF8_UNICODE_CI NOT NULL,
	PRIMARY KEY (role_name) 
);
INSERT INTO ta_usr_role( role_name, role_description, role_img ) VALUES
	('USER',   'Normaler Benutzer',  '-'),
	('METHOD', 'Methoden Verwalten', '-'),
	('ADMIN',  'Administrator',      '-');
CREATE TABLE ta_usr_permission(
	perm_name VARCHAR(127) COLLATE UTF8_UNICODE_CI NOT NULL,
	perm_description VARCHAR(255) COLLATE UTF8_UNICODE_CI NOT NULL,
	perm_authenticated INT NOT NULL DEFAULT 0,
	perm_unauthenticated INT NOT NULL DEFAULT 0, 
	PRIMARY KEY (perm_name) 
);
INSERT INTO ta_usr_permission ( perm_name, perm_description, perm_authenticated, perm_unauthenticated ) VALUES 
	( 'USR.REG',  'Benutzer registrieren', 0, 1 ),
	( 'USR.CONF', 'Benutzerregistrierung bestätigen', 0, 1),
	( 'USR.IN',   'Benutzer anmelden', 0, 1 ),
	( 'USR.OUT',  'Benutzer abmelden', 1, 0 ),
	( 'USR.OPT',  'Benutzer Einstellungen', 1, 0 ),
	( 'MTH.SRCH', 'Methoden suchen', 1, 1 ),
	( 'MTH.NEW',  'Methoden anlegen', 1, 0 ),
	( 'MTH.RATE', 'Methoden bewerten', 1, 0 ),
	( 'MTH.ADM',  'Methoden verwalten', 1, 0 ),
	( 'ADM.USR',  'Benutzer verwalten', 1, 0 ),
	( 'ADM.REQ',  'Anfragen bearbeiten', 1, 0 ),
	( 'REP.MRNK', 'Bericht Methoden Ranking', 1, 1 ),
	( 'REP.MST',  'Bericht Methoden Statistik', 1, 1 );
CREATE TABLE ta_usr_role_permission(
	role_name VARCHAR(127) COLLATE UTF8_UNICODE_CI NOT NULL,
	perm_name VARCHAR(127) COLLATE UTF8_UNICODE_CI NOT NULL,
	PRIMARY KEY (role_name, perm_name),
	INDEX fk_rp_role (role_name),
	INDEX fk_rp_perm (perm_name),
	CONSTRAINT fk_rp_role FOREIGN KEY (role_name) REFERENCES ta_usr_role (role_name),
	CONSTRAINT fk_rp_perm FOREIGN KEY (perm_name) REFERENCES ta_usr_permission (perm_name)
);
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
CREATE TABLE ta_usr_account_role (
	rl_usr_id INT NOT NULL,
	rl_role_name VARCHAR(127) COLLATE UTF8_UNICODE_CI NOT NULL,
	PRIMARY KEY (rl_usr_id, rl_role_name),
	INDEX fk_acc_usr (rl_usr_id),
	INDEX fk_acc_role (rl_role_name),
	CONSTRAINT fk_acc_usr FOREIGN KEY (rl_usr_id) REFERENCES ta_usr_account (usr_id),
	CONSTRAINT fk_acc_role FOREIGN KEY (rl_role_name) REFERENCES ta_usr_role (role_name)
);	
 
