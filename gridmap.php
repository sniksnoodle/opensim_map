<?php

/*
# To use this script replace the 2nd, 3rd & 4th parameters in the
# mysqli_connect below to match your database user, password & name.
# Place your map images inside the ./gridmap/images folder, all the
# images should be in the format map-1-x_cord-y_cord-objects.jpg

# This script is licenced under the CC0 1.0 Universal (CC0 1.0) 
# Public Domain Dedication by snik snoodle on 18 August 2018.
*/

$db = mysqli_connect('localhost','db_user','db_user_password','db_name')
or die('Error connecting to MySQL server.');

$simulators = array();

$query = "SELECT regionname,locx,locy FROM regions ORDER BY locx, locy";
mysqli_query($db, $query) or die('Error querying database.');

$result = mysqli_query($db, $query);

while ($row = mysqli_fetch_array($result)) { $simulators[] = $row;}

mysqli_close($db);

$loc_x = array_column($simulators,1); $loc_y = array_column($simulators,2);

$arr_x_mod = array_map( function($val) { return $val / 256; }, $loc_x);
$arr_y_mod = array_map( function($val) { return $val / 256; }, $loc_y);

$x_replace = array_replace($loc_x, $arr_x_mod); $y_replace = array_replace($loc_y, $arr_y_mod);

$sim_name = array_column($simulators,0);

$sim_data = array_map(function ($sim_name, $x_replace, $y_replace) { return "$sim_name, $x_replace, $y_replace"; }, $sim_name, $x_replace, $y_replace );

$x_value = $loc_x[0]; $row_count=0;
while ($x_value == $loc_x[$row_count]) { $row_count=$row_count +1; }

$new_column_array = array_fill(0, 49, '</p></div>');

$result = array_map(function($sim_data, $new_column_array) {
    return $sim_data . $new_column_array;
}, $sim_data,$new_column_array);

$new_img_tag_array = array_map(function($x_replace, $y_replace) {
    return '<img src="./images/map-1-' . $x_replace . '-' . $y_replace . '-objects.jpg" alt="[IMG]"><p class="text">';
}, $x_replace, $y_replace);

$add_image_tag_array = array_map(function($new_img_tag_array, $result) {
return $new_img_tag_array . $result;
}, $new_img_tag_array,$result);

$new_column_array = array_fill(0, 49, '<div class="grid-item">');

$pushtostart = array_map(function($new_column_array, $add_image_tag_array) {
    return $new_column_array . $add_image_tag_array;
}, $new_column_array,$add_image_tag_array);

$gridmap = implode(" ",$pushtostart);

$head_string = "<html>
<head>

<title>Your Grid Names Map</title>

<style>
 
body { font-family: \"Arial\", sans-serif;}

.grid-container { 
 float:left; 
 display: grid; 
 grid-template-columns: repeat($row_count, 1fr); 
 transform: rotate(-90deg); 
 grid-column-gap: 1px; 
 grid-row-gap: 1px;
}

.text { 
 color: white;
 margin: 0;
 padding: 5px;
 position:absolute;
 left:0;
 bottom:0;
}

img {
display: block;
width: 100%;
height: auto;
}

.grid-item {
  border: 0px;
  padding: 0px;
  font-size: 12px;
  text-align: center;
  justify-self: center;
  transform: rotate(90deg)
}

</style> 
</head>

 <body>
 <center><h3>Your Grid Names Map</h3></center>";

echo $head_string;
echo '<div class="grid-container">';
echo "$gridmap";
echo '</div>';
?>

</body>
</html>