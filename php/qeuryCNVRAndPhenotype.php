<?php

include '../../config.php';
include 'pdoResultFilter.php';

$chromosome = $_GET['Chromosome'];
$position_start = $_GET['Start'];
$position_end = $_GET['End'];
$cn = $_GET['CN'];
$phenotype = $_GET['Phenotype'];


if (is_string($cn)) {
    $cn_array = preg_split("/[;, \n]+/", $cn);
    for ($i = 0; $i < count($cn_array); $i++) {
        $cn_array[$i] = trim($cn_array[$i]);
    }
} elseif (is_array($cn)) {
    $cn_array = $cn;
    for ($i = 0; $i < count($cn_array); $i++) {
        $cn_array[$i] = trim($cn_array[$i]);
    }
} else {
    exit(0);
}

if (is_string($phenotype)) {
    $phenotype_array = preg_split("/[;, \n]+/", $phenotype);
    for ($i = 0; $i < count($phenotype_array); $i++) {
        $phenotype_array[$i] = trim($phenotype_array[$i]);
    }
} elseif (is_array($phenotype)) {
    $phenotype_array = $phenotype;
    for ($i = 0; $i < count($phenotype_array); $i++) {
        $phenotype_array[$i] = trim($phenotype_array[$i]);
    }
} else {
    exit(0);
}


// Construct query string
$query_str = "SELECT CNVR.Chromosome, CNVR.Start, CNVR.End, CNVR.Width, CNVR.Strand, AM.SoyKB_Accession AS Accession, CNVR.CN ";
for ($i = 0; $i < count($phenotype_array); $i++) {
    $query_str = $query_str . ", G." . $phenotype_array[$i] . " ";
}
$query_str = $query_str . "FROM soykb.mViz_Soybean_CNVR AS CNVR ";
$query_str = $query_str . "LEFT JOIN soykb.mViz_Soybean_Accession_Mapping AS AM ";
$query_str = $query_str . "ON CNVR.Accession = AM.Accession ";
$query_str = $query_str . "LEFT JOIN soykb.germplasm AS G ";
$query_str = $query_str . "ON AM.GRIN_Accession = G.ACNO ";
$query_str = $query_str . "WHERE (CNVR.Chromosome = '" . $chromosome . "') ";
$query_str = $query_str . "AND (CNVR.Start BETWEEN " . $position_start . " AND " . $position_end . ") ";
$query_str = $query_str . "AND (CNVR.End BETWEEN " . $position_start . " AND " . $position_end . ") ";
if (count($cn_array) > 0) {
    $query_str = $query_str . "AND (CNVR.CN IN ('";
    for ($i = 0; $i < count($cn_array); $i++) {
        if($i < (count($cn_array)-1)){
            $query_str = $query_str . trim($cn_array[$i]) . "', '";
        } elseif ($i == (count($cn_array)-1)) {
            $query_str = $query_str . trim($cn_array[$i]);
        }
    }
    $query_str = $query_str . "')) ";
}
$query_str = $query_str . "ORDER BY CNVR.CN, CNVR.Chromosome, CNVR.Start, CNVR.End, AM.SoyKB_Accession; ";


$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$result_arr = pdoResultFilter($result);

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);


?>