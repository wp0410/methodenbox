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

create table ta_sec_user_session (
    sess_id varchar(16) collate utf8_unicode_ci not null,
    sess_start datetime not null,
    sess_end datetime not null,
    sess_usr_id int not null,
    sess_usr_name varchar(255) collate utf8_unicode_ci not null
);


