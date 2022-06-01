<?php

include '../../config.php';
include 'pdoResultFilter.php';

$chromosome = $_GET['Chromosome'];
$start = $_GET['Start'];
$end = $_GET['End'];

$query_str = "
    SELECT * 
    FROM soykb.mViz_Soy2828_" . $chromosome . "_chrom_pos_ref_alt 
    WHERE (Chromosome = '" . $chromosome . "')
    AND (Position BETWEEN " . $start . " AND " . $end . ") 
    ORDER BY Chromosome, Position;
";

$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$result_arr = pdoResultFilter($result);

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);

?>
