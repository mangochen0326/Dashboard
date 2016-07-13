<?php

require_once 'Database.php';

if($_SERVER["HTTP_HOST"]==='your.domain.com'){ // 連正式機JDE Schema
    $jde_table_sch = "jde_production.proddta";
    $gcr_table_sch = "Master.dbo";
}else{ // 連測試機JDE Schema
    $jde_table_sch = "jde_crp.crpdta";
    $gcr_table_sch = "TEST.dbo";
}


if (isset($_POST['func']) and $_POST['func'] == 'getRegion')
{
    $DB = Database::getInstance();
    $DBH = $DB->getDBH();
    $SQL = "
        select country_id, country_name 
        from g_region
        where exists (
            select *
            from " . $jde_table_sch . ".F4201 as a
                inner join " . $jde_table_sch . ".F0116 as b on b.ALAN8 = a.SHAN8
            where b.alctr = country_id collate Chinese_Taiwan_Stroke_CI_AS
        )
        order by country_name;
    ";
    $stmt = $DBH->prepare($SQL);
    $stmt->execute();
    $rows = $stmt->fetchAll();
    $data = "";
    foreach ($rows as $row) {
        if($data > '')
            $data .= ',';
        $data .= '{"country_id":"' . $row['country_id'] . '", "country_name":"' . $row['country_name'] . '"}';
    }
    $data = 'var json = [' . $data . "];";
    echo $data;
}

if (isset($_POST['func']) and $_POST['func'] == 'getSI')
{
    $SQL = "select a.aban8 as si_id,
            a.abalph as si_name
        from " . $jde_table_sch . ".F0101 as a
        where a.ABAT1='C' and a.ABAC01='170'
            and exists (select * from " . $jde_table_sch . ".F4201 where aban8 = SHAN8)
        order by si_name;";
    $DB = Database::getInstance();
    $DBH = $DB->getDBH();
    $stmt = $DBH->prepare($SQL);
    $stmt->execute();
    $rows = $stmt->fetchAll();
    $sis = array();
    foreach ($rows as $row) 
    {
        $sis[] = array('si_id' => $row['si_id'], 'si_name' => $row['si_name']);
    }
    echo json_encode($sis);
}

if (isset($_POST['func']) and $_POST['func'] == 'getProduct')
{
 
    $SQL = "
        select distinct product_id, product_name 
        from g_order_item 
        where kind = 'P'
        order by product_name
    ";
   
    $DB = Database::getInstance();
    $DBH = $DB->getDBH();
    $stmt = $DBH->prepare($SQL);
    $stmt->execute();
    $rows = $stmt->fetchAll();
    $products = array();
    foreach ($rows as $row) 
    {
        $products[] = array('product_id' => $row['product_id'], 'product_name' => $row['product_name']);
    }
    echo json_encode($products);
}


function getMonth( $start, $end )
{

    $d1 = new DateTime($start . '-1');
    $d2 = new DateTime($end . '-1');
    $return_arr = [];
    $arr_y = [];
    $arr_m = [];
    $arr_y[] = $d1->format('Y');
    $arr_m[] = $d1->format('m');
    while($d1 < $d2) {
        $d1 = $d1->modify('+1 month');
        $arr_y[] = $d1->format('Y');
        $arr_m[] = $d1->format('m');
    }
    $return_arr[] = $arr_y;
    $return_arr[] = $arr_m;
    return $return_arr;
}

if(isset($_POST['func']) and $_POST['func'] == 'initMonthPicker')
{
    $DB = Database::getInstance();
    $DBH = $DB->getDBH();
    $SQL = "
        select year(min_paydate) as min_year,
            month(min_paydate) as min_month,
            year(max_paydate) as max_year,
            month(max_paydate) as max_month
        from (
            select min(dateadd(year, 
                    100 * cast(left(cast(shtrdj as varchar),1) as int)
                    + 10 * cast(substring(cast(shtrdj as varchar), 2,1) as int)
                    + cast(substring(cast(shtrdj as varchar),3,1) as int), 
                    dateadd(day, cast(substring(cast(shtrdj as varchar), 4, 3) as int)-1, 
                    0))) as min_paydate, 
                    max(dateadd(year, 
                    100 * cast(left(cast(shtrdj as varchar),1) as int)
                    + 10 * cast(substring(cast(shtrdj as varchar), 2,1) as int)
                    + cast(substring(cast(shtrdj as varchar),3,1) as int), 
                    dateadd(day, cast(substring(cast(shtrdj as varchar), 4, 3) as int)-1, 
                    0))) as max_paydate
            from " . $jde_table_sch . ".F4201
        ) as a
    ";
    $stmt = $DBH->prepare($SQL);
    $stmt->execute();
    $rows = $stmt->fetchAll();
    $data = '';
    $data .= '[{"min_year":"' . $rows[0]['min_year'] . '", "min_month":"' . $rows[0]['min_month'] . '", "max_year":"' . $rows[0]['max_year'] . '", "max_month":"' . $rows[0]['max_month'] . '"}];';
    $data = 'var json = ' . $data;
    echo $data;
}

if (isset($_POST['func']) and $_POST['func'] == 'getData')
{
    $d1 = $_POST['f_month'];
    $d2 = $_POST['t_month'];
    $region = $_POST['region'];
    $si = $_POST['si'];
    $product = $_POST['product'];

    $current_start = "{$d1}-1";
    $current_end = date('Y-m-t', strtotime($d2 . '-1'));
    $diff_month = count(getMonth($d1, $d2)[0]);

    $temp1 = new \DateTime($current_start);
    $last_end = $temp1->modify('-1 days');
    $temp1 = strtotime($current_start);
    $last_start = date('Y-m-d', strtotime("-{$diff_month} month", $temp1));
    $last_start = new \DateTime($last_start);

    $arr = getMonth($d1, $d2);
    $years = $arr[0];
    $months = $arr[1];

    $SQL = "select yy, mm, dd, cast(sum(sales) as int) as sales from ( ";
    for($i = 0; $i < count($years); $i++) {
        if($i > 0)
        {
            $SQL .= "union all ";
        }
        $SQL .= "select {$years[$i]} as yy, ";
        $SQL .= "{$months[$i]} as mm, ";
        if($i == count($years) - 1)
            $SQL .= "2 as dd, ";
        else
            $SQL .= "1 as dd, ";
        $SQL .= "case when year(a.paydate) = {$years[$i]} and month(a.paydate) = {$months[$i]} then isnull(sum(e.amount),0) + isnull(sum(d.amount), 0) else 0 end as sales, ";
        $SQL .= "count(a.SHDOCO) as order_count ";
        $SQL .= "from (
                        select dateadd(year, 
                              100 * cast(left(cast(shtrdj as varchar),1) as int)
                                + 10 * cast(substring(cast(shtrdj as varchar), 2,1) as int)
                                + cast(substring(cast(shtrdj as varchar),3,1) as int), 
                                dateadd(day, cast(substring(cast(shtrdj as varchar), 4, 3) as int)-1, 
                                0)) as paydate, SHFAP, SHDOCO, b.ALCTR
                        from " . $jde_table_sch . ".F4201 as a
                            inner join " . $jde_table_sch . ".F0116 as b on b.ALAN8 = a.SHAN8 ";
        if("" != $si)
        {
            $SQL .= "where a.shan8 = '" . $si . "' ";
        }
        $SQL .= "
                    ) as a
                    left outer join " . $gcr_table_sch . ".G_order_title as c on c.order_number_jde=a.SHDOCO
                    left outer join " . $gcr_table_sch . ".G_order_item as e on c.id = e.order_id
                        and e.kind = 'P' ";
        if($product != "")
        {
            $SQL .= "and e.product_id = '{$product}' ";
        }
        $SQL .= "left outer join " . $gcr_table_sch . ".G_billing as d on d.order_id=c.id 
                where a.paydate between '{$current_start}' and '{$current_end}' ";
        if($region != "")
        {
            $SQL .= "and a.alctr = '{$region}' ";
        }
        $SQL .= "group by year(a.paydate), month(a.paydate) ";
    }
    $SQL .= ") as a group by yy, mm, dd;";

    $DB = Database::getInstance();
    $DBH = $DB->getDBH();
    $stmt = $DBH->prepare($SQL);
    $stmt->execute();
    $rows = $stmt->fetchAll();
    $data1 = '';
    foreach($rows as $row)
    {
        if($data1 == '')
        {
            $data1 = '{"date":"' . $row['yy'] . '-' . $row['mm'] . '", "sales":"' . $row['sales'] . '"}';
        }
        else
        {
            $data1 .= ',{"date":"' . $row['yy'] . '-' . $row['mm'] . '", "sales":"' . $row['sales'] . '"}';
        }
    }
    $data1 = 'var json = [' . $data1 . ']';
    echo $data1;
}