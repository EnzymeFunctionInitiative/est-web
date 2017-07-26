<?php
require_once 'includes/main.inc.php';
require_once '../libs/input.class.inc.php';

$result = "";

$query_string = str_replace("\n", ",", $_GET["families"]);
$query_string = str_replace("\r", ",", $query_string);
$query_string = str_replace(" ", ",", $query_string);
$families = explode(",", $query_string);

foreach ($families as $family) {
    $family = functions::sanitize_family($family);
    if (!$family)
        continue;

    $family_type = functions::get_family_type($family);
    if (!$family_type)
        continue;

    $sql = "SELECT * FROM family_counts WHERE family_type='$family_type' AND family='$family'";
    $sql .= " LIMIT 1";
    $db_result = $db->query($sql);
    if ($db_result) {
        if ($result)
            $result .= ",";
        $result .= strtoupper($family) . "=" . $db_result[0]['num_members'];
    }
}

echo $result;

//echo json_encode(
//    'id'=>$result['id'],
//    'message'=>$result['MESSAGE']));


?>
