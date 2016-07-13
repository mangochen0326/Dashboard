
<?php
    
    define("master_latitude", "25.050135");
    define("master_longitude","121.579285");
    /*
    require_once 'Database.php';

    
    $jde_table_sch = "jde_crp.crpdta";
    $mssql_table_sch = "TEST.dbo";
    

    $DATA=array();
    $DB = Database::getInstance();
    $DBH = $DB->getDBH();

    //ISV Distribution County Data
    $SQL="select c.latitude, c.longitude, c.position_name
    from " . $jde_table_sch . ".F0101 as a
    left join " . $jde_table_sch . ".F0116 as b on b.ALAN8=a.ABAN8
    left outer join g_lat_long as c on b.alctr = c.position_id collate Chinese_Taiwan_Stroke_CI_AS
    where a.ABAC01='180' and b.alctr > ''
    group by c.latitude,c.longitude,c.position_name";

    $query = $DBH->prepare($SQL);
    $query->execute();
    $result = $query->fetchAll();

   
    $DATA["ISVPOINT"][0]["id"]="Home"; //名稱
    $DATA["ISVPOINT"][0]["title"]="Home"; //名稱
    $DATA["ISVPOINT"][0]["type"]="circle"; //名稱
    $DATA["ISVPOINT"][0]["svgPath"]="targetSVG"; //SVG圖
    $DATA["ISVPOINT"][0]["color"]="#FF0000"; //SVG圖
    $DATA["ISVPOINT"][0]["latitude"]=master_latitude; //緯度
    $DATA["ISVPOINT"][0]["longitude"]=master_longitude;//經度
    $DATA["ISVPOINT"][0]["scale"]=0.7; //點大小

    $ii=1;
    foreach ($result as $row) {  
        $DATA["ISVPOINT"][$ii]["title"]=trim($row['position_name']); //名稱
        $DATA["ISVPOINT"][$ii]["svgPath"]="targetSVG"; //SVG圖
        $DATA["ISVPOINT"][$ii]["latitude"]=trim($row['latitude']); //緯度
        $DATA["ISVPOINT"][$ii]["longitude"]=trim($row['longitude']);//經度
        $DATA["ISVPOINT"][$ii]["scale"]=0.6; //點大小
        $ii++;
    }
    


    //MSP,SMB count
    // $SQL="select count(a.email) as total_amount,
    // b.ALCTR as country_code,a.site,e.position_name,e.latitude,e.longitude
    // from ".$mssql_table_sch.".G_login as a
    // left join ".$jde_table_sch.".F0116 as b on b.ALAN8=a.user_id
    // left join ".$mssql_table_sch.".g_lat_long as e on b.ALCTR=e.position_id
    //     collate Chinese_Taiwan_Stroke_CI_AS
    // where a.status=1 and b.ALEFTB=0 and a.site in ('1','2')
    // group by b.ALCTR,a.site,e.latitude,e.longitude,e.position_name
    // order by b.ALCTR";
   

    $SQL="select count(distinct a.user_id) as total_amount, 
    b.ALCTR as country_code,a.site,e.position_name,e.latitude,e.longitude
    from ".$mssql_table_sch.".G_login as a
    left join ".$jde_table_sch.".F0116 as b on b.ALAN8=a.user_id
    left join ".$mssql_table_sch.".g_lat_long as e on b.ALCTR=e.position_id
    collate Chinese_Taiwan_Stroke_CI_AS
    where a.status=1 and b.ALEFTB=0 and a.site in ('1','2')
    group by b.ALCTR,a.site,e.latitude,e.longitude,e.position_name
    order by b.ALCTR";
    //echo $SQL;

    //die;

    $query = $DBH->prepare($SQL);
    $query->execute();
    $result = $query->fetchAll();
    $k=0;
    $total_msp=0;
    $total_smb=0;
    foreach ($result as $idx =>$row) {  
        //$TMP_DATA[trim($row['country_code'])][trim($row['site'])]=trim($row['total_amount']);
        if (trim($row['site'])=='1') {
            $TMP_DATA[trim($row['country_code'])]["MSP"]=trim($row['total_amount']);
            $TMP_DATA[trim($row['country_code'])]["latitude"]=trim($row['latitude']);
            $TMP_DATA[trim($row['country_code'])]["longitude"]=trim($row['longitude']);

            $DATA["MSPPOINT"][$k]["title"]=trim($row['position_name']); //名稱
            $DATA["MSPPOINT"][$k]["svgPath"]="targetSVG"; //SVG圖
            $DATA["MSPPOINT"][$k]["latitude"]=trim($row['latitude']); //緯度
            $DATA["MSPPOINT"][$k]["longitude"]=trim($row['longitude']);//經度
            $DATA["MSPPOINT"][$k]["scale"]=0.6; //點大小
            $k++;
            $total_msp+=trim($row['total_amount']);
       
        } else {
            $TMP_DATA[trim($row['country_code'])]["SMB"]=trim($row['total_amount']);
            $total_smb+=trim($row['total_amount']);
        }
        
    }
    $DATA["TOTAL_MSP"]=$total_msp;
    $DATA["TOTAL_SMB"]=$total_smb;


    //Sales Count

    // $SQL="select isnull(sum(a.SHFAP)/10000,0)+isnull(sum(d.amount),0) as total_amount,
    // b.ALCTR as country_code,e.position_name,e.latitude,e.longitude
    // from ".$jde_table_sch.".F4201 as a
    // left join ".$jde_table_sch.".F0116 as b on b.ALAN8=a.SHAN8
    // left join ".$mssql_table_sch.".G_order_title as c on c.order_number_jde=a.SHDOCO
    // left join ".$mssql_table_sch.".G_billing as d on d.order_id=c.id
    // left join ".$mssql_table_sch.".g_lat_long as e on b.ALCTR=e.position_id
    // collate Chinese_Taiwan_Stroke_CI_AS
    // where b.ALEFTB=0 
    // group by b.ALCTR,e.position_name,e.latitude,e.longitude";
    //echo $SQL;

    $SQL="select isnull(sum(a.SHFAP)/10000,0)+isnull(sum(d.amount),0) as total_amount,
    b.ALCTR as country_code, isnull(sum(d.amount),0) as billingTotal,
    isnull(sum(case when d.jde_status='1' then d.amount else 0 end),0) as billingPaid,
    isnull(sum(case when d.jde_status='0' then d.amount else 0 end),0) as billingUnpaid,
    e.position_name,e.latitude,e.longitude
    from ".$jde_table_sch.".F4201 as a
    left join ".$jde_table_sch.".F0116 as b on b.ALAN8=a.SHAN8
    left join ".$mssql_table_sch.".G_order_title as c on c.order_number_jde=a.SHDOCO
    left join ".$mssql_table_sch.".G_billing as d on d.order_id=c.id
    left join ".$mssql_table_sch.".g_lat_long as e on b.ALCTR=e.position_id
    collate Chinese_Taiwan_Stroke_CI_AS
    where b.ALEFTB=0  group by b.ALCTR,e.position_name,e.latitude,e.longitude";

    //echo $SQL;


    $stmt = $DBH->prepare($SQL);
    $stmt->execute();
    $rows = $stmt->fetchAll();


    $total_sales=0;
    $total_payment=0;
    $total_Outstanding=0;
    foreach ($rows as $idx=>$row) {  
        $MSP_DATA=@$TMP_DATA[trim($row['country_code'])]['MSP'];
        $SMB_DATA=@$TMP_DATA[trim($row['country_code'])]['SMB'];
        ($MSP_DATA=="") ? $MSP_DATA=0:$MSP_DATA;
        ($SMB_DATA=="") ? $SMB_DATA=0:$SMB_DATA;

        $firsttime_charge=$row['total_amount']-$row['billingTotal'];
        //已收金額含firstTime charge押金等
        $Payment_received=floatval($row['billingPaid'])+floatval($firsttime_charge);
        //未收金額為尚未實現的billingPlan
        $Outstanding_balance=$row['billingUnpaid'];

        $customdata ="<p class='sales'>".$row['position_name']."<br>";
        $customdata.="Payment received:$".number_format($Payment_received)." <br>";
        $customdata.="Outstanding balance:$".number_format($Outstanding_balance)."<br>";
        $customdata.="<span class='msp_sub'>MSP:".$MSP_DATA." ";
        $customdata.="subscribers:".$SMB_DATA."</span></p>";
        $DATA["AREASDATA"][$idx]['id']=trim($row['country_code']);
        $DATA["AREASDATA"][$idx]['customData']=$customdata;

        $total_sales+=trim($row['total_amount']);
        $total_payment+=$Payment_received;
        $total_Outstanding+=$Outstanding_balance;
        //$total_msp+=$MSP_DATA;

        //$total_smb+=$SMB_DATA;
      
    }
    $DATA["TOTAL_SALES"]='$'.number_format($total_sales);
    $DATA["TOTAL_PAYMENT"]='$'.number_format(round($total_payment));
    $DATA["TOTAL_OUTSTANDING"]='$'.number_format(round($total_Outstanding));
    $DATA["TOTAL_COUNTRY"]=count($DATA["AREASDATA"]);
    $DATA["TOTAL_ARPU"]='$'.number_format(round($total_sales/$total_smb));
    
    //ISV DATA
    $DATA["ISVLINETO"]=array();
    foreach ($DATA["ISVPOINT"] as $key => $value) { 
        $tmp_lat="";
        $tmp_log="";
        foreach ($value as $idx => $detial) {
            // $$idx=$TMP_DATA[$key][$idx];
            switch (trim($idx)) {
                case "latitude":
                    $tmp_lat=array("latitudes"=>array($detial,master_latitude));
                    break;
                case "longitude":
                    $tmp_lon=array("longitudes"=>array($detial,master_longitude,));
                    break;           }
       }
       if ($tmp_lat!="" and $tmp_lon!="") {
            $DATA["ISVLINETO"][]=$tmp_lat+$tmp_lon;     
       } 
    }

    //AWS DATA

    $SQL="select a.position_name as aws_name,a.latitude as aws_latitude,a.longitude as aws_longitude,
        b.country_id, b.country_name,c.latitude as country_latitude,c.longitude as country_longitude
        from ".$mssql_table_sch.".g_lat_long as a
        inner join ".$mssql_table_sch.".g_region as b on a.position_id = cast(b.oss_region_id as varchar)
        inner join ".$mssql_table_sch.".g_lat_long as c on b.country_id = c.position_id";

    $query = $DBH->prepare($SQL);
    $query->execute();
    $rs = $query->fetchAll();
    foreach ($rs as $row_data) {  
        $CHECK_AWS[trim($row_data['country_id'])]=trim($row_data['aws_name']);
        $AWS["latitude"][trim($row_data['aws_name'])]=trim($row_data['aws_latitude']);
        $AWS["longitude"][trim($row_data['aws_name'])]=trim($row_data['aws_longitude']);
    }
    

    foreach ($TMP_DATA as $country_id => $value) {
        $aws_lat="";
        $tmp_lon="";
        if (@$TMP_DATA[$country_id]['latitude']!="") {
            if (array_key_exists($country_id, $CHECK_AWS)) {

                //$tmp_lat=array("latitudes"=>array(master_latitude,$$TMP_DATA[$country_id)]["latitude"]));

                $aws_name=$CHECK_AWS[$country_id];

                $aws_lat=$AWS["latitude"][$aws_name];
                $aws_lon=$AWS["longitude"][$aws_name];

                $tmp_lat=array("latitudes"=>array($aws_lat,$TMP_DATA[$country_id]["latitude"]));

                $tmp_lon=array("longitudes"=>array($aws_lon,$TMP_DATA[$country_id]["longitude"]));

                $DATA['AWS'][$aws_name]['latitude']=$aws_lat;
                $DATA['AWS'][$aws_name]['longitude']=$aws_lon;


                $DATA["MSPLINETO"][]=$tmp_lat+$tmp_lon;      
            }
        }
    }

*/
$DATA["AREASDATA"][0]["id"]         ="US";
$DATA["AREASDATA"][0]["customData"] ="<p class='sales'>Sales:$10,000<br><span class='msp_sub'>MSP:5 subscribers:10</span></p>";


$DATA["AREASDATA"][1]["id"]         ="CN";
$DATA["AREASDATA"][1]["customData"] ="<p class='sales'>Sales:$13,000<br><span class='msp_sub'>MSP:5 subscribers:120</span></p>";


$DATA["AREASDATA"][2]["id"]         ="RU";
$DATA["AREASDATA"][2]["customData"] ="<p class='sales'>Sales:$3,000<br><span class='msp_sub'>MSP:5 subscribers:120</span></p>"; 

// 
// $DATA["ISVPOINT"][0]["title"]="Home"; //名稱
//     $DATA["ISVPOINT"][0]["id"]="Home"; //名稱
//     $DATA["ISVPOINT"][0]["type"]="circle"; //名稱
//     $DATA["ISVPOINT"][0]["svgPath"]="targetSVG"; //SVG圖
//     $DATA["ISVPOINT"][0]["color"]="#FF0000"; //SVG圖
//     $DATA["ISVPOINT"][0]["latitude"]=master_latitude; //緯度
//     $DATA["ISVPOINT"][0]["longitude"]=master_longitude;//經度
//     $DATA["ISVPOINT"][0]["scale"]=0.7; //點大小


// $DATA["ISVPOINT"][1]["title"]="Shanghai"; //名稱
// $DATA["ISVPOINT"][1]["svgPath"]="targetSVG"; //SVG圖
// $DATA["ISVPOINT"][1]["latitude"]="31.22934"; //緯度
// $DATA["ISVPOINT"][1]["longitude"]="121.47521";//經度
// $DATA["ISVPOINT"][1]["scale"]=0.5; //點大小

// $DATA["ISVPOINT"][2]["title"]="Singapore"; //名稱
// $DATA["ISVPOINT"][2]["svgPath"]="targetSVG"; //SVG圖
// $DATA["ISVPOINT"][2]["latitude"]="1.35208"; //緯度
// $DATA["ISVPOINT"][2]["longitude"]="103.81984";//經度
// $DATA["ISVPOINT"][2]["scale"]=0.5; //點大小


// $DATA["ISVLINETO"][]=array("latitudes"=>array("31.22934","1.35208"),"longitudes"=>array("121.47521","103.81984"));


// $DATA["MASTERLINETO"][]=array("latitudes"=>array("31.22934","1.35208"),"longitudes"=>array("121.47521","103.81984"));

  

echo json_encode($DATA);

?>