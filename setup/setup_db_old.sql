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
    usr_pin varchar(32) collate utf8_unicode_ci default null
);

create table ta_jnl_sec_user (
    jnl_id int not null auto_increment primary key,
    jnl_created datetime not null,
    jnl_usr_id int default null,
    jnl_usr_name varchar(255) collate utf8_unicode_ci default null,
    jnl_action varchar(16) collate utf8_unicode_ci not null,
    jnl_result int not null default 0,
    jnl_text varchar(255) collate utf8_unicode_ci
);

create table ta_sec_user_session (
    sess_id varchar(16) collate utf8_unicode_ci not null,
    sess_start datetime not null,
    sess_end datetime not null,
    sess_usr_id int not null,
    sess_usr_name varchar(255) collate utf8_unicode_ci not null
);

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
    mth_soc_form varchar(8) collate utf8_unicode_ci default null
);

create table ta_mth_method_attachment (
    att_mth_id int not null,
    att_name varchar(127) not null,
    att_type varchar(16) collate utf8_unicode_ci not null,
    att_guid varchar(32) collate utf8_unicode_ci not null,
    att_data mediumblob,
    
    primary key pk_mth_attachment (att_mth_id),
    foreign key fk_att_methode_id (att_mth_id) references ta_mth_method (mth_id) match full on delete restrict on update restrict
);

create table ta_mth_method_author (
    mth_id int not null,
    mth_seq int not null,
    mth_auth_name varchar(127) collate utf8_unicode_ci not null,
    
    primary key pk_mth_owner (mth_id, mth_seq),
    foreign key fk_mth_methode_id (mth_id) references ta_mth_method (mth_id) match full on delete restrict on update restrict
);

create table ta_jnl_mth_method (
    jnl_id int not null auto_increment primary key,
    jnl_created datetime not null,
    jnl_mth_id int default null,
    jnl_action varchar(16) collate utf8_unicode_ci not null,
    jnl_text varchar(255) collate utf8_unicode_ci default null,
    jnl_usr_id int not null,
    jnl_old_mth_data varchar(1023) collate utf8_unicode_ci default null
);



