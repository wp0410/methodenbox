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

class MethodStatistics
{
    private $db_conn;

    public function __construct($db_cn)
    {
        $this->db_conn = $db_cn;
	}
	
	public function mth_per_subject(): array
	{
		$res = array();
		
		$sql_stmt =
			"SELECT sub.mth_subject, MAX(sub.mth_sub_name) AS mth_sub_name, SUM(sub.num_per_subj) AS num_per_subj
             FROM   (
             	SELECT mth.mth_subject, '' as mth_sub_name, COUNT(1) AS num_per_subj
            	FROM   ta_mth_method_header AS mth
            	GROUP BY mth.mth_subject
            	UNION ALL 
            	SELECT subj.mth_sub_val AS mth_subject, subj.mth_sub_name,  0 AS num_per_subj
            	FROM   vi_mth_subjects AS subj ) sub
             GROUP BY sub.mth_subject 
             ORDER BY num_per_subj DESC";
			
		$stm_st1 = $this->db_conn->prepare($sql_stmt);
		if ($stm_st1->execute())
		{
			$mth_subject = '';
			$mth_sub_name = '';
			$num_per_subj = 0;
			
			$stm_st1->bind_result($mth_subject, $mth_sub_name, $num_per_subj);
			while($stm_st1->fetch())
			{
				$res[] = array(
							'mth_subject' => $mth_subject, 
							'mth_sub_name' => $mth_sub_name,
							'num_per_subj' => $num_per_subj
						);
			}
		}
		$stm_st1->close();
		
		return $res;
	}
}

?>