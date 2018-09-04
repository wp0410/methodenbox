create table ta_jnl_mth_method (
    jnl_id int not null auto_increment primary key,
    jnl_created datetime not null,
    jnl_mth_id int default null,
    jnl_action varchar(16) collate utf8_unicode_ci not null,
    jnl_text varchar(255) collate utf8_unicode_ci default null,
    jnl_usr_id int not null,
    jnl_old_mth_data varchar(1023) collate utf8_unicode_ci default null
);



