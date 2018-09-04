/* ----------------------------------------------------------------------------
 * TA_SEC_USER: Table containing information about registered users
 * 
 * 2017-09-01      WPa / Created
 * 2018-08-23      WPa / Revised
 * ----------------------------------------------------------------------------
 */
create table ta_sec_user (
    usr_id int not null auto_increment primary key,
    usr_fst_name varchar(127) collate utf8_unicode_ci not null,
    usr_lst_name varchar(127) collate utf8_unicode_ci not null,
    usr_email varchar(255) collate utf8_unicode_ci not null unique,
    usr_pwd char(64) collate utf8_unicode_ci not null,
    usr_salt char(16) collate utf8_unicode_ci not null,
    usr_registered datetime not null,
    usr_lastlogin datetime default null,
    usr_numinvlogin int not null default 0,
    usr_locked int not null default 0,
    usr_pin varchar(32) collate utf8_unicode_ci default null,
    usr_role int not null default 0
);

/* ----------------------------------------------------------------------------
 * TA_SEC_USER_SESSION: Table containing information about active user 
 *                      session
 *
 * 2017-09-01      WPa / Created
 * 2018-08-23      WPa / Revised
 * ----------------------------------------------------------------------------
 */
create table ta_sec_user_session (
    sess_id varchar(16) collate utf8_unicode_ci not null,
    sess_start datetime not null,
    sess_end datetime not null,
    sess_usr_id int not null,
    sess_usr_name varchar(255) collate utf8_unicode_ci not null,
	sess_type int default 0 not null
);

/* ----------------------------------------------------------------------------
 * TA_JNL_JOURNAL: Table storing information about (critical) user activities
 *
 * 2017-09-01      WPa / Created
 * 2018-08-23      WPa / Revised
 * ----------------------------------------------------------------------------
 */
create table ta_jnl_journal (
    jnl_id int not null auto_increment primary key,
    jnl_ip varchar(32) collate utf8_unicode_ci default null,
    jnl_time datetime not null,
    jnl_usr_id int not null default 0,
    jnl_usr_name varchar(255) collate utf8_unicode_ci default null,
    jnl_action varchar(31) collate utf8_unicode_ci not null,
    jnl_result int not null default 0, 
    jnl_result_txt varchar(255) collate utf8_unicode_ci default null,
    jnl_data varchar(2047) collate utf8_unicode_ci default null,
    jnl_comment varchar(255) collate utf8_unicode_ci default null
);

/* ----------------------------------------------------------------------------
 * TA_MTH_METHOD: Table of defined teaching methods
 *
 * 2017-09-01      WPa / Created
 * 2018-08-23      WPa / Added elements "summary", "age group", "owner"
 * ----------------------------------------------------------------------------
 */
create table ta_mth_method (
    mth_id int not null auto_increment primary key,
    mth_name varchar(127) collate utf8_unicode_ci not null, 
    mth_phase varchar(16) collate utf8_unicode_ci default null,
    mth_prep_min int default 0 not null,
    mth_prep_max int default 0 not null,
    mth_exec_min int default 0 not null,
    mth_exec_max int default 0 not null,
    mth_topic varchar(127) collate utf8_unicode_ci,
    mth_type varchar(16) collate utf8_unicode_ci default null,
    mth_soc_form varchar(16) collate utf8_unicode_ci default null,
    mth_summary varchar(300) collate utf8_unicode_ci default null,
    mth_age_grp int default null,
    mth_owner_id int not null,
    mth_status int default 0 not null,
    
    foreign key fk_mth_method_owner (mth_owner_id) references ta_sec_user (usr_id) match full on delete restrict on update restrict
);

/* ----------------------------------------------------------------------------
 * TA_MTH_METHOD_AUTHOR: List of authors of a teaching method
 *
 * 2017-09-01      WPa / Created
 * 2018-08-23      WPa / Revised
 * ----------------------------------------------------------------------------
 */
create table ta_mth_method_author (
    mth_id int not null,
    mth_seq int not null,
    mth_auth_name varchar(127) collate utf8_unicode_ci not null,
    
    primary key pk_mth_owner (mth_id, mth_seq),
    foreign key fk_mth_method_id (mth_id) references ta_mth_method (mth_id) match full on delete restrict on update restrict
);

/* ----------------------------------------------------------------------------
 * TA_MTH_METHOD_ATTACHMENT: Files attached to teaching methods
 *
 * 2017-09-01      WPa / Created
 * 2018-08-23      WPa / Revised
 * ----------------------------------------------------------------------------
 */
create table ta_mth_method_attachment (
    att_mth_id int not null,
    att_name varchar(127) not null,
    att_type varchar(16) collate utf8_unicode_ci not null,
    att_guid varchar(32) collate utf8_unicode_ci not null,
    att_data mediumblob,
    
    primary key pk_mth_attachment (att_mth_id),
    foreign key fk_att_method_id (att_mth_id) references ta_mth_method (mth_id) match full on delete restrict on update restrict
);

/* ----------------------------------------------------------------------------
 * TA_MTH_STATISTICS_DOWNLOAD: Base data for dowload statistics per user and
 *                             teaching method
 *
 * 2017-09-01      WPa / Created
 * 2018-08-23      WPa / Revised
 * ----------------------------------------------------------------------------
 */
create table ta_mth_statistics_download (
    dld_mth_id int not null,
    dld_usr_id int not null,
    dld_date datetime not null,
    
    foreign key fk_dld_method_id (dld_mth_id) references ta_mth_method (mth_id) match full on delete restrict on update restrict,
    foreign key fk_dld_user_id (dld_usr_id) references ta_sec_user (usr_id) match full on delete restrict on update restrict
);

/* ----------------------------------------------------------------------------
 * TA_MTH_STATISTICS_RATING: Ratings for teaching methods
 *
 * 2017-09-01      WPa / Created
 * 2018-08-23      WPa / Revised
 * ----------------------------------------------------------------------------
 */
create table ta_mth_statistics_rating (
    rtg_mth_id int not null,
    rtg_usr_id int not null,
    rtg_date datetime not null,
    rtg_rating int not null,
    rtg_comment varchar(511) collate utf8_unicode_ci,

    foreign key fk_rtg_method_id (rtg_mth_id) references ta_mth_method (mth_id) match full on delete restrict on update restrict,
    foreign key fk_rtg_user_id (rtg_usr_id) references ta_sec_user (usr_id) match full on delete restrict on update restrict
);

