<?php
include("../externals/pchart/class/pDraw.class.php");
include("../externals/pchart/class/pImage.class.php");
include("../externals/pchart/class/pData.class.php");

include("funcs.inc.php");

$myData = new pData();

$db = new db();

$sql = "select * from (select sum(status=0) as success,sum(status=1) as failures,sum(status=2) as timeouts,count(*) as total,date(builddate) as day from results group by date(builddate) order by builddate desc limit 30) as foo order by day;";

$ret = $db->query($sql);
if ($ret == FALSE) {
  echo "Cannot retrieve statistics<br/>";
  bab_footer();
  exit;
}

$dates_data = array();
$success_data = array();
$failures_data = array();
$timeouts_data = array();
$total_data = array();

while($current = mysql_fetch_object($ret)) {
  array_push($dates_data, $current->day);
  array_push($success_data, $current->success);
  array_push($failures_data, $current->failures);
  array_push($timeouts_data, $current->timeouts);
  array_push($total_data, $current->total);
}

/* Add data in your dataset */
$myData->addPoints($success_data, "success");
$myData->addPoints($failures_data, "failure");
$myData->addPoints($timeouts_data, "timeout");
$myData->addPoints($total_data, "total");
$myData->setAxisName(0,"Number of builds");

$myData->addPoints($dates_data, "Labels");
$myData->setSerieDescription("Labels","Dates");
$myData->setAbscissa("Labels");

/* Create a pChart object and associate your dataset */
$myPicture = new pImage(700,500,$myData);

/* Choose a nice font */
$myPicture->setFontProperties(array("FontName"=>"../externals/pchart/fonts/verdana.ttf","FontSize"=>8));

/* Define the boundaries of the graph area */
$myPicture->setGraphArea(70,50,650,400);

$myPicture->drawScale(array("LabelRotation" => 90));

$myPicture->drawLegend(20,20,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

$myPicture->drawLineChart();

$myPicture->Stroke();
?>