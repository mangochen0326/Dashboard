<?php
$time = $_SERVER['REQUEST_TIME'];
$timeout_duration = 1800;
session_start();
if(!isset($_SESSION['login_user']))
{
	header("Location: login.php");
}
else
{
    if (isset($_SESSION['LAST_ACTIVITY']) && ($time - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
      session_unset();     
      session_destroy();
    }
    $_SESSION['LAST_ACTIVITY'] = $time;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>DashBoard</title>
    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/jquery.validate.min.js"></script>
    <script src="./js/map.js"></script>
    <script src="./js/svg1.js"></script>
    <script src="./js/dataloader.js"></script>
    <script src="./js/amcharts.js"></script>
    <script src="./js/serial.js"></script>
    <script src="./js/light.js"></script>
    <script src="./js/jquery.mtz.monthpicker.js"></script>
    <script src="./js/jquery-scrollto.js"></script>
    <script src="./js/SalesReport.js"></script>
    <script src="./js/socket.io.js"></script>

    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous" />
    <link rel="stylesheet" type="text/css" media="screen" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/base/jquery-ui.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="./css/SalesReport.css" />
    <script>
        var total_sales;
        var total_payment;
        var total_outstanding;
        var total_msp;
        var total_smb;
        var total_arpu;
        var total_country;
        $(document).ready(function(){
            $("a.FootPrint, a.Distribution, a.SalesReport").click(function(e){
                e.preventDefault();
                var $this = $(this);
                var $class = $this[0].className;
                if($this.parent().hasClass('active'))
                    return false;
                $('div#' + $class).ScrollTo();
                $(".nav").find(".active").removeClass("active");
                $this.parent().addClass("active");
            });
            $("#FootPrint,#Distribution,#SalesReport").css({"height":$(window).height()});
            var $left = $('#FootPrint').position().left + $('#FootPrint').width()-$('#selector-wrapper').width();
            var $top = $('#FootPrint').position().top + 120;
            $('#selector-wrapper').css({'left':$left + 'px', 'top':$top + 'px'});  
            $(window).resize(function(){
                var $left = $('#FootPrint').position().left + $('#FootPrint').width()-$('#selector-wrapper').width();
                var $top = $('#FootPrint').position().top + 120;
                $('#selector-wrapper').css({'left':$left + 'px', 'top':$top + 'px'});                
            });
        });     
    </script>
    <style type="text/css">
        #selector-wrapper {
           position: absolute;
           white-space: nowrap;
        }
              .selector {
                    position: relative;
                    display: inline-block;
                    width: 350px;
                    height: 90px;
                    line-height: 25px;
                    padding: 10px;
                    background: #FFFFFF;
                    border: #000000 solid 2px;
                    -webkit-border-radius: 10px;
                    -moz-border-radius: 10px;
                    border-radius: 10px;
                    /*top:-535px;
                    left:690px;*/
                    word-spacing: 10px;
                    font-weight: bold;
                    z-index: 1;
                }           

              .selector:after {
                content: "";
                position: absolute;
                top: 12px;
                left: 345px;
                border-style: solid;
                border-width: 15px 0 15px 15px;
                border-color: transparent #FFFFFF;
                display: block;
                width: 0;
                z-index: 1;
            }

              .selector:before {
                content: "";
                position: absolute;
                top: 12px;
                left: 348px;
                border-style: solid;
                border-width: 16px 0 16px 16px;
                border-color: transparent #000000;
                display: block;
                width: 0;
                z-index: 0;
            }
        #selector {
            display: inline-block;
            padding: 10px;
            background: rgba(255, 255, 255, 0.8);
            width: 90px;
            height: 40px;
            line-height: 10px;
            position: relative;
            top:-40px;
            /*top: 100px;
            right: 160px;*/
            border: 4px solid #000000;
            text-align: center;
            font-weight: bold;
        }
 
        .navbar .divider-vertical {
            height: 50px;
            margin: 0 9px;
            border-right: 1px solid #ffffff;
            border-left: 1px solid #f2f2f2;
        }

        .navbar-inverse .divider-vertical {
            border-right-color: #222222;
            border-left-color: #111111;
        }
        
        .amcharts-map-image-GCR {
          -webkit-animation: am-pulsating 1s ease-out infinite;
          animation: am-pulsating 1s ease-out infinite;
        }
        @-webkit-keyframes am-pulsating {
          0% {
            transform: scale(0);
            opacity: 1;
          }
          100% {
            transform: scale(4);
            opacity: 0;
          }
        }

        @keyframes am-pulsating {
          0% {
            transform: scale(0);
            opacity: 1;
          }
          100% {
            transform: scale(2);
            opacity: 0;
          }
        }

        .sales {
            font-size:8pt;
            font-family: Verdana;
            font-weight:bold;
            line-height: 2;
            
        }
        .msp_sub {
            word-spacing: 17px;
        }
        .div {
            width       : 90%;
            height      : 550px;
            font-size   : 11px;
            
            margin: 0px auto;
        }

    </style>
</head>
<body>
<div class="row">
<nav class="navbar navbar-default navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header" style="background: url('/images/logo.png') center left no-repeat; padding-left:100px;">
      <div class="navbar-brand">DashBoard</div>
    </div>
    <ul class="nav navbar-nav">
        <li role="separator" class="divider-vertical"></li>
        <li class="active"><a href="#" class="FootPrint">Foot Print</a></li>
        <li role="separator" class="divider-vertical"></li>
        <li><a href="#" class="Distribution">Distribution</a></li>
        <li role="separator" class="divider-vertical"></li>
        <li><a href="#" class="SalesReport">Sales Report</a></li>
        <li role="separator" class="divider-vertical"></li>
    </ul>
    <ul class="nav navbar-nav navbar-right">
        <li role="separator" class="divider-vertical"></li>
        <li>
            <?php
                if(isset($_SESSION['login_user']))
                {
                    echo '<a href="logout.php" id="logout">';
                    echo '<span class="glyphicon glyphicon-log-out"></span>'; 
                    //echo ' ' . $_SESSION['login_name'] . ' ';
                    echo ' Logout';
                    echo '</a>';
                }
                else
                {
                    echo '<a href="login.php" id="login>';
                    echo '<span class="glyphicon glyphicon-log-in"></span>';
                    echo ' Login';
                    echo '</a>';
                }
            ?>
        </li>
    </ul>
  </div>
</nav>
</div>
<div class="row">
<div class="container">
    <!-- div: Foot Print -->
    <div class="div" id="FootPrint" style="padding-top:80px;">
    
    </div>
    <div id="selector-wrapper">
        <div class="selector"></div>
        <div id="selector">In total</div>
    </div>
    <!-- div: ABCDE -->
    <div id="Distribution" style="padding-top:80px;">
        <div id="menu_list" style="left:30px;top:30px;z-index:100;">
             <ul class="nav nav-tabs navbar-right">
            <li class="active">
              <a href="#ISV" data-anchor="ISV" data-toggle="tab" onclick="setData(0);">ISV Distribution</a>
            </li>
            <li>
              <a href="#ISVline" data-anchor="ISVline" data-toggle="tab" onclick="setData(1);">ISV to GCR</a>
            </li>
            <li>
              <a href="#MSPline" data-anchor="MSPline" data-toggle="tab" onclick="setData(2);">DC to MSP</a>
            </li>
          </ul>
        </div> 
        <div id="isvtogcr" class="div" style="left:30px;top:60px;z-index:80;"></div>  
    </div>

    <!-- div: Sales Report -->      
    <div id="SalesReport" style="padding-top:80px;">
        <b><div class="text-center" style="font-weight:bolder;font-size:24px;">Sales Report</div></b>
        <div class="col-lg-4">
            <form class="form-horizontal" id="salesReportForm">
                <div class="form-group">
                    <label for="f_month">From</label>
                    <input type="text" id="f_month" name="f_month" value="" class="form-control" required />
                </div>
                <div class="form-group">
                    <label for="t_month">To</label>
                    <input type="text" id="t_month" name="t_month" value="" class="form-control" required />
                </div>
                <div class="form-group">
                    <label for="region">Country</label>
                    <select name="region" id="region" class="form-control">
                    </select>
                </div>
                <div class="form-group">
                    <label for="product">Product</label>
                    <select name="product" id="product" class="form-control"></select>
                </div>
                <div class="form-group">
                    <label for="SI">SI</label>
                    <select name="SI" id="SI" class="form-control"></select>
                </div>
                <input type="button" id="btn" value="Query" class="btn btn-primary" />
            </form>
        </div>
        <div class="col-lg-8">
            <div id="chartdiv"></div>
        </div>
    </div>
</div>
</div>

<script>
var isvimages = [];
var mspimages = [];
var areasdata  = [];
var isvlines  = [];
var gcrlines  = [];
var sales_data =[];



var targetSVG ="M9,15.93 c-3.83,0-6.93-3.1-6.93-6.93S5.17,2.07,9,2.07s6.93,3.1,6.93,6.93S12.83,15.93,9,15.93 M12.5,9c0,1.933-1.567,3.5-3.5,3.5S5.5,10.933,5.5,9S7.067,5.5,9,5.5 S12.5,7.067,12.5,9z";


var check_host=location.host;

if(check_host.match('test')!=null) 
{
    var websocket_url="host1.domain.com";
} else {
    var websocket_url="host2.domain.com";
}

var socket = io.connect(websocket_url+":3000");

function arrayObjectIndexOf(myArray, searchTerm, property) {
    for(var i = 0, len = myArray.length; i < len; i++) {
        if (myArray[i][property] === searchTerm) return i;
    }
    return -1;
}

AmCharts.ready(function() {
   
    
    socket.on('data_update', function(socket_data) {
        check_areas_ary=[];
        for (var i in areasdata) {
            check_areas_ary.push({id:areasdata[i].id,customData:areasdata[i].customData}); 
        }
        //console.log(check_areas_ary);
        for (var i in socket_data['data']) {
            push_data=socket_data['data'][i];  
            switch (i) {
                case "AREASDATA":
                    for (var j in push_data) {
                        var chk_id=push_data[j].id;
                        var chk_customData=push_data[j].customData;

                        var rs=(check_areas_ary, chk_id, "id");
                        var rs1=arrayObjectIndexOf(check_areas_ary, chk_customData, "customData");
                        if (rs==-1 || rs1==-1) {
                            sales_data.push({id:chk_id,customData:chk_customData,color:'#FF0000'});
                        } else {
                            sales_data.push({id:chk_id,customData:chk_customData});
                        }
                    }
                    if (sales_data.length > 1) {
                        map.dataProvider.areas=sales_data;
                        map.validateData();
                    }
                    break;
                case "ISVPOINT":
                    for (var j in push_data) {
                        var tmp_data=push_data[j];
                        tmp_data["svgPath"]=window[tmp_data["svgPath"]]; 
                        isvimages.push(tmp_data);        
                    }
                    break;
                case "ISVLINETO":
                    for (var j in push_data) {
                         var tmp_data=push_data[j];
                         isvlines.push(tmp_data);
                    }
                    break;
                case "MSPPOINT":
                   for (var j in push_data) {
                        var tmp_data=push_data[j];
                        tmp_data["svgPath"]=window[tmp_data["svgPath"]]; 
                        mspimages.push(tmp_data);        
                    }
                    break;
                case "MSPLINETO":
                    for (var j in push_data) {
                         var tmp_data=push_data[j];
                         gcrlines.push(tmp_data);
                    }
                    break;
                //AWS機房異動機會少，可以評估是否不用更新
                case "AWS":
                    for (var j in push_data) {
                         var tmp_data=push_data[j];
                         var a_data={"title":j,"svgPath":targetSVG,"latitude":tmp_data['latitude'],"longitude":tmp_data['longitude'],"scale":"1","color":"#000000"};
                         mspimages.push(a_data);
                    }
                    break;
                // case "TOTAL_SALES":
                //     total_sales=push_data;
                //     break;
                case "TOTAL_OUTSTANDING":
                    total_outstanding=push_data;
                    break;
                 case "TOTAL_PAYMENT":
                    total_payment=push_data;
                    break;
                case "TOTAL_MSP":
                    total_msp=push_data;
                    break;
                case "TOTAL_SMB":
                    total_smb=push_data;
                    break;
                case "TOTAL_COUNTRY":
                    total_country=push_data;
                    break;
                case "TOTAL_ARPU":
                    total_arpu=push_data;
                    break;
            }
        }
        $(".selector").html("<span style='word-spacing:normal;'>Payment received: </span>"+total_payment+" ARPU:"+total_arpu+"<BR><span style='word-spacing:normal;'>Outstanding balance: </span>"+total_outstanding+"<BR>MSP:"+total_msp+"  subscribers:"+total_smb+"  Country:"+total_country);
    });


    AmCharts.loadFile("data_local.php", {}, function(json) {
        /**
         * Parse data and create map data
         */
        var data = AmCharts.parseJSON(json); 
        //console.log(data);
        for (var i in data) {
            push_data=data[i];  
            switch (i) {
                case "AREASDATA":
                    for (var j in push_data) {
                        areasdata.push(push_data[j]);    
                    }
                    break;
                case "ISVPOINT":
                    for (var j in push_data) {
                        var tmp_data=push_data[j];
                        tmp_data["svgPath"]=window[tmp_data["svgPath"]]; 
                        isvimages.push(tmp_data);        
                    }
                    break;
                case "ISVLINETO":
                    for (var j in push_data) {
                         var tmp_data=push_data[j];
                         isvlines.push(tmp_data);
                    }
                    break;
                case "MSPPOINT":
                   for (var j in push_data) {
                        var tmp_data=push_data[j];
                        tmp_data["svgPath"]=window[tmp_data["svgPath"]]; 
                        mspimages.push(tmp_data);        
                    }
                    break;
                case "MSPLINETO":
                    for (var j in push_data) {
                         var tmp_data=push_data[j];
                         gcrlines.push(tmp_data);
                    }
                    break;
                case "AWS":
                    for (var j in push_data) {
                         var tmp_data=push_data[j];
                         var a_data={"title":j,"svgPath":targetSVG,"latitude":tmp_data['latitude'],"longitude":tmp_data['longitude'],"scale":"1","color":"#000000"};
                         //console.log(a_data);
                         mspimages.push(a_data);
                    }
                    break;
                // case "TOTAL_SALES":
                //     total_sales=push_data;
                //     break;
                case "TOTAL_OUTSTANDING":
                    total_outstanding=push_data;
                    break;
                 case "TOTAL_PAYMENT":
                    total_payment=push_data;
                    break;
                case "TOTAL_MSP":
                    total_msp=push_data;
                    break;
                case "TOTAL_SMB":
                    total_smb=push_data;
                    break;
                case "TOTAL_COUNTRY":
                    total_country=push_data;
                    break;
                case "TOTAL_ARPU":
                    total_arpu=push_data;
                    break;
            }
        }   

        $(".selector").html("<span style='word-spacing:normal;'>Payment received: </span>"+total_payment+" ARPU:"+total_arpu+"<BR><span style='word-spacing:normal;'>Outstanding balance: </span>"+total_outstanding+"<BR>MSP:"+total_msp+"  subscribers:"+total_smb+"  Country:"+total_country);


        //console.log(mspimages);
        map = AmCharts.makeChart("FootPrint", {
            "type": "map",
            "theme": "light",
            "mouseWheelZoomEnabled":true,
            "balloon": {
                "maxWidth":240,
                "cornerRadius": 7,
                "horizontalPadding":30,
                "fadeOutDuration":0.1
            },
            "titles": [
                {
                    "text": "Foot Print",
                    "size": 24
                }
            ],
            "dataProvider": {
                "map": "worldHigh",
                "zoomLevel": 1,
                "areas":areasdata,
            },
            "areasSettings": {
                "balloonText": "[[customData]]",
                "selectedColor": "#CC0000",
                "rollOverColor": "#911fad",
                "alpha": 0.3,
                "color": "#3c5bdc",
            },
            "zoomControl":{
               "minZoomLevel":1,
               "maxZoomLevel":14,
               "zoomControlEnabled":true
            },
            backgroundZoomsToTop: false
        });


        map1 = AmCharts.makeChart("isvtogcr", { 
            "type": "map",
            "theme": "light",
            "addClassNames": true,
            "mouseWheelZoomEnabled":true,
            "titles": [
                {
                    "text": "Global ISV Distribution",
                    "size": 24
                }
            ],
            dataProvider: {
                "map": "worldHigh",
                "zoomLevel": 1,
                "zoomLatitude": 40.21205,
                "zoomLongitude": 24.84225,
                "images":isvimages, 
                "lines":[]
                
            }, 
            imagesSettings: {
                color: "#CC0000",
                rollOverColor: "#CC0000"
            },

            linesSettings: {
                arc: -0.8, // this makes lines curved. Use value from -1 to 1
                //arrow: "middle",
                color: "#CC0000",
                alpha: 0.4,
                arrowAlpha: 1
                //arrowSize: 4
            },
            zoomControl:{
               minZoomLevel:1,
               maxZoomLevel:14,
               zoomControlEnabled:true
            },
             
            backgroundZoomsToTop: true,
            linesAboveImages: true
        });
    });
});


function setData(index) {
    switch (index) {
        case 0:
            map1.dataProvider.lines = [];
            map1.dataProvider.images = isvimages;
            map1.validateData();
            break;
        case 1:
            map1.dataProvider.images = isvimages;
            map1.dataProvider.lines = isvlines;
            map1.validateData();
            break;
        case 2:
            //console.log($aws_images);
            
            map1.dataProvider.images = mspimages;
            map1.dataProvider.lines = gcrlines;
            
            map1.validateData();
            break;
    }   
}

</script>
</body>
</html>