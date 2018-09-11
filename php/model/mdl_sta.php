<?php
//---------------------------------------------------------------------------------------
//  Copyright (c) 2017 Walter Pachlinger (walter.pachlinger@gmx.at)
//    
//  Licensed under the Apache License, Version 2.0 (the "License"); you may not use this 
//  file except in compliance with the License. You may obtain a copy of the License at
//      http://www.apache.org/licenses/LICENSE-2.0
//  Unless required by applicable law or agreed to in writing, software distributed under 
//  the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF 
//  ANY KIND, either express or implied. See the License for the specific language 
//  governing permissions and limitations under the License.
//----------------------------------------------------------------------------------------

/**
 * DownloadStatistics        Entry that describes a successful download of a teaching method
 * 
 * @package   DownloadStatistics
 * @author    Walter Pachlinger (walter.pachlinger@gmx.at)
 * @version   $Revision: 1.0 $
 * @access    public
 */
class DownloadStatistics 
{
    private $db_conn;
    private $dld_usr_id;
    private $dld_mth_id;
    private $dld_date;
    private $is_stored;
    
    /**
     * Constructor
     * 
     * @access     public
     * @param      mysqli    $db_cn    Database connection
     * @param      integer   $usr_id   Identifier of an authenticated user
     * @param      integer   $mth_id   Identifier of the downloaded method
     * @return     An initialized DownloadStatistics object
     */
    public function __construct($db_cn, $usr_id, $mth_id)
    {
        $this->db_conn = $db_cn;
        $this->dld_usr_id = $usr_id;
        $this->dld_mth_id = $mth_id;
        $this->dld_date = strftime('%Y-%m-%d %H:%M:%S', time());
        $this->is_stored = false;
    }
    
    /**
     * Stores the DownloadStatistics object in the database
     * 
     * @access     public
     * @return     TRUE      object successfully stored
     * @return     FALSE     error storing the object
     */
    public function save()
    {
        $sql_stmt = 'insert into ta_mth_statistics_download( dld_mth_id, dld_usr_id, dld_date ) values ( ?, ?, ? ); ';
        $stm9 = $this->db_conn->prepare($sql_stmt);
        $stm9->bind_param('iis', $this->dld_mth_id, $this->dld_usr_id, $this->dld_date);
        $this->is_stored = $stm9->execute();
        $stm9->close();
        
        return $this->is_stored;
    }
}

/**
 * RatingStatistics          Entry that describes a successful rating of a teaching method
 * 
 * @package   RatingStatistics
 * @author    Walter Pachlinger (walter.pachlinger@gmx.at)
 * @version   $Revision: 1.0 $
 * @access    public
 */
class RatingStatistics
{
    private $db_conn;
    private $rtg_usr_id;
    private $rtg_mth_id;
    private $rtg_date;
    private $rtg_rating;
    public  $rtg_comment;
    private $is_stored;
    
    /**
     * Constructor
     * 
     * @access     public
     * @param      mysqli    $db_cn    Database connection
     * @param      integer   $usr_id   Identification of the rating user
     * @param      integer   $mth_id   Identification of the rated method
     * @param      integer   $rate     Rating value
     */
    public function __construct($db_cn, $usr_id, $mth_id, $rate)
    {
        $this->db_conn = $db_cn;
        $this->rtg_usr_id = $usr_id;
        $this->rtg_mth_id = $mth_id;
        $this->rtg_date = strftime('%Y-%m-%d %H:%M:%S', time());
        $this->rtg_rating = $rate;
        $this->rtg_comment = '';
        $this->is_stored = false;
    }
    
    /**
     * Stores the RatingStatistics object in the database
     * 
     * @access     public
     * @return     TRUE      Object stored successfully
     * @return     FALSE     Error storing the object
     */
    public function save()
    {
        $sql_stmt = 'insert into ta_mth_statistics_rating( rtg_mth_id, rtg_usr_id, rtg_date, rtg_rating, rtg_comment ) values ( ?, ?, ?, ?, ? ); ';
        $stm9 = $this->db_conn->prepare($sql_stmt);
        $stm9->bind_param('iisis', $this->rtg_mth_id, $this->rtg_usr_id, $this->rtg_date, $this->rtg_rating, $this->rtg_comment);
        $this->is_stored = $stm9->execute();
        $stm9->close();
        
        return $this->is_stored;
    }
}

?>