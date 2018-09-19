select mth.mth_id, mth.mth_name, std.dld_count, std.dld_last_date
from   ta_mth_method mth,
       ( select dld_mth_id, count(1) as dld_count, max(dld_date) as dld_last_date from ta_mth_statistics_download group by dld_mth_id ) std
where  mth.mth_id = std.dld_mth_id;

select rtg_mth_id, count(1) as rtg_count, avg(rtg_rating) as rtg_average from ta_mth_statistics_rating group by rtg_mth_id

select mth.mth_id, mth.mth_name, coalesce(std.dld_count,0) as dld_count, std.dld_last_date, 
       coalesce(str.rtg_count,0) as rtg_count, str.rtg_average
from   ta_mth_method mth 
       left join ( select dld_mth_id, count(1) as dld_count, max(dld_date) as dld_last_date from ta_mth_statistics_download group by dld_mth_id ) std on  mth.mth_id = std.dld_mth_id
       left join ( select rtg_mth_id, count(1) as rtg_count, avg(rtg_rating) as rtg_average from ta_mth_statistics_rating group by rtg_mth_id ) str on mth.mth_id = str.rtg_mth_id;

select mth.mth_id, mth.mth_name, mth.mth_phase, mth.mth_prep_min, mth.mth_prep_max,
       mth.mth_exec_min, mth.mth_exec_max, mth.mth_topic, mth.mth_type, mth.mth_soc_form,
       mth.mth_age_grp, mth.mth_summary, 
       dld.dld_usr_id, dld.dld_count, dld.dld_last_date,
       coalesce(rtg.rtg_count,0) rtg_count
from   ta_mth_method mth 
       inner join ( select dld_usr_id, dld_mth_id, count(1) as dld_count, max(dld_date) as dld_last_date
                    from ta_mth_statistics_download 
                    group by dld_mth_id, dld_usr_id ) dld on mth.mth_id = dld.dld_mth_id
       left join  ( select rtg_mth_id, count(1) as rtg_count 
                    from ta_mth_statistics_rating 
                    group by rtg_mth_id ) rtg on mth.mth_id = rtg.rtg_mth_id
where  dld.dld_usr_id = 6
  and  rtg.rtg_count is null
order by dld_last_date desc
;

select mth.mth_id, mth.mth_name, mth.mth_phase, mth.mth_prep_min, mth.mth_prep_max,
       mth.mth_exec_min, mth.mth_exec_max, mth.mth_topic, mth.mth_type, mth.mth_soc_form,
       mth.mth_age_grp, mth.mth_summary,
       dld.dld_count, dld.dld_last_date,
       coalesce(rtg.rtg_count,0) rtg_count, coalesce(rtg.rtg_sum,0) rtg_sum, 
       coalesce(rtg.rtg_sum,0) / (coalesce(rtg.rtg_count,0) + 1) rtg_sort
from   ta_mth_method mth
       inner join ( select dld_usr_id, dld_mth_id, count(1) as dld_count, max(dld_date) as dld_last_date
                    from ta_mth_statistics_download
                    group by dld_mth_id, dld_usr_id ) dld on mth.mth_id = dld.dld_mth_id
       left join  ( select rtg_mth_id, count(1) as rtg_count, sum(rtg_rating) as rtg_sum
                    from ta_mth_statistics_rating
                    group by rtg_mth_id ) rtg on mth.mth_id = rtg.rtg_mth_id
where  mth.mth_owner_id = 6
order by rtg_sort asc;

//---- Test Method Search
select mth.mth_id, mth.mth_name, mth.mth_phase, mth.mth_prep_min, mth.mth_prep_max,
       mth.mth_exec_min, mth.mth_exec_max, mth.mth_topic, mth.mth_type, mth.mth_soc_form,
       mth.mth_age_grp, mth.mth_summary, rtg.rtg_count, rtg.rtg_sum, 
       mau.mth_auth_name, att.att_guid, att.att_name
from   ta_mth_method mth
       inner join ta_mth_method_attachment att on mth.mth_id = att.att_mth_id
       inner join ( select mth_id, group_concat(mth_auth_name order by mth_seq separator '<br>') as mth_auth_name 
                    from ta_mth_method_author 
                    group by mth_id ) mau on mth.mth_id = mau.mth_id
       left join  ( select rtg_mth_id, count(1) as rtg_count, sum(rtg_rating) as rtg_sum
                    from ta_mth_statistics_rating
                    group by rtg_mth_id ) rtg on mth.mth_id = rtg.rtg_mth_id
where  mth.mth_id >= 0
;    


select mth.mth_id, mth.mth_name, mth.mth_phase, mth.mth_prep_min, mth.mth_prep_max,
       mth.mth_exec_min, mth.mth_exec_max, mth.mth_topic, mth.mth_type, mth.mth_soc_form,
       mth.mth_age_grp, mth.mth_summary,
       dld.dld_count, dld.dld_last_date,
       coalesce(rtg.rtg_count,0) rtg_count, coalesce(rtg.rtg_sum,0) rtg_sum,
       coalesce(rtg.rtg_sum,0) / (coalesce(rtg.rtg_count,0) + 1) rtg_sort
from   ta_mth_method mth
       left join ( select dld_mth_id, count(1) as dld_count, max(dld_date) as dld_last_date
                   from ta_mth_statistics_download
                   group by dld_mth_id ) dld on mth.mth_id = dld.dld_mth_id
       left join  ( select rtg_mth_id, count(1) as rtg_count, sum(rtg_rating) as rtg_sum
                    from ta_mth_statistics_rating
                    group by rtg_mth_id ) rtg on mth.mth_id = rtg.rtg_mth_id
where  mth.mth_status = 0 and mth.mth_owner_id = 1
order by rtg_sort asc;


select rtg.rtg_mth_id, rtg.rtg_usr_id, usr.usr_email, usr.usr_fst_name, usr.usr_lst_name, 
       rtg.rtg_date, rtg.rtg_rating, rtg.rtg_comment
from   ta_mth_statistics_rating rtg,
       ta_sec_user usr
where  rtg.rtg_usr_id = usr.usr_id;


