<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<?php
$TITLE = "Soybean MViz";

// include '../header.php';
include '../config.php';
include './php/pdoResultFilter.php';
?>

<!-- Get and process the variables -->
<?php
$chromosome_1 = $_GET['chromosome_1'];
$position_start_1 = $_GET['position_start_1'];
$position_end_1 = $_GET['position_end_1'];

$chromosome_1 = trim($chromosome_1);
$position_start_1 = intval(trim($position_start_1))-1;
$position_end_1 = intval(trim($position_end_1))+1;

?>

<!-- Back button -->
<a href="/SoybeanMViz/"><button> &lt; Back </button></a>

<br />
<br />

<!-- Query cnvr accession count data from database -->
<?php

$query_str = "SELECT CNVR.Chromosome, CNVR.Start, CNVR.End, CNVR.Width, CNVR.Strand, ";
$query_str = $query_str . "COUNT(IF(CNVR.CN = 'CN0', 1, null)) AS CN0, ";
$query_str = $query_str . "COUNT(IF(CNVR.CN = 'CN1', 1, null)) AS CN1, ";
$query_str = $query_str . "COUNT(IF(CNVR.CN = 'CN2', 1, null)) AS CN2, ";
$query_str = $query_str . "COUNT(IF(CNVR.CN = 'CN3', 1, null)) AS CN3, ";
$query_str = $query_str . "COUNT(IF(CNVR.CN = 'CN4', 1, null)) AS CN4, ";
$query_str = $query_str . "COUNT(IF(CNVR.CN = 'CN5', 1, null)) AS CN5, ";
$query_str = $query_str . "COUNT(IF(CNVR.CN = 'CN6', 1, null)) AS CN6, ";
$query_str = $query_str . "COUNT(IF(CNVR.CN = 'CN7', 1, null)) AS CN7, ";
$query_str = $query_str . "COUNT(IF(CNVR.CN = 'CN8', 1, null)) AS CN8 ";
$query_str = $query_str . "FROM soykb.mViz_Soybean_CNVR AS CNVR ";
$query_str = $query_str . "WHERE (CNVR.Chromosome = '" . $chromosome_1 . "') ";
$query_str = $query_str . "AND (CNVR.Start BETWEEN " . $position_start_1 . " AND " . $position_end_1 . ") ";
$query_str = $query_str . "AND (CNVR.End BETWEEN " . $position_start_1 . " AND " . $position_end_1 . ") ";
$query_str = $query_str . "GROUP BY CNVR.Chromosome, CNVR.Start, CNVR.End, CNVR.Width, CNVR.Strand ";
$query_str = $query_str . "ORDER BY CNVR.Chromosome, CNVR.Start, CNVR.End; ";

$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

if (count($result) > 0) {
    $cnvr_accession_count_result_arr = pdoResultFilter($result);
} else {
    echo "No data found!!!";
}

?>


<!-- Render the table -->
<?php

    if (isset($cnvr_accession_count_result_arr) && is_array($cnvr_accession_count_result_arr) && !empty($cnvr_accession_count_result_arr) && count($cnvr_accession_count_result_arr) > 0) {
        echo "<div style='width:auto; height:auto; border:3px solid #000; overflow:scroll; max-height:1000px; display:inline-block;'>";
        echo "<table style='text-align:center;'>";

        // Table header
        echo "<tr>";
        foreach ($cnvr_accession_count_result_arr[0] as $key => $value) {
            echo "<th style=\"border:1px solid black;\">" . strval($key) . "</th>";
        }
        echo "</tr>";

        // Table body
        for ($i = 0; $i < count($cnvr_accession_count_result_arr); $i++) {
            $tr_bgcolor = ($i % 2 ? "#FFFFFF" : "#DDFFDD");

            echo "<tr bgcolor=\"" . $tr_bgcolor . "\">";
            foreach ($cnvr_accession_count_result_arr[$i] as $key => $value) {
                echo "<td style=\"border:1px solid black;min-width:80px;\">" . $value . "</td>";
            }
            echo "</tr>";
        }

        echo "</table>";
        echo "</div>";

        echo "<br /><br />";
    }

?>


<!-- Query data from database and render the data -->
<?php

for ($i = 0; $i < count($cnvr_accession_count_result_arr); $i++) {

    // Check GRIN accession mapping
    $query_str = "SELECT CNVR.Chromosome, CNVR.Start, CNVR.End, CNVR.Width, CNVR.Strand, AM.SoyKB_Accession AS Accession, CNVR.CN ";
    $query_str = $query_str . "FROM soykb.mViz_Soybean_CNVR AS CNVR ";
    $query_str = $query_str . "LEFT JOIN soykb.mViz_Soybean_Accession_Mapping AS AM ";
    $query_str = $query_str . "ON CNVR.Accession = AM.Accession ";
    $query_str = $query_str . "WHERE (CNVR.Chromosome = '" . $cnvr_accession_count_result_arr[$i]["Chromosome"] . "') ";
    $query_str = $query_str . "AND (CNVR.Start BETWEEN " . $cnvr_accession_count_result_arr[$i]["Start"] . " AND " . $cnvr_accession_count_result_arr[$i]["End"] . ") ";
    $query_str = $query_str . "AND (CNVR.End BETWEEN " . $cnvr_accession_count_result_arr[$i]["Start"] . " AND " . $cnvr_accession_count_result_arr[$i]["End"] . ") ";
    $query_str = $query_str . "ORDER BY CNVR.CN, AM.SoyKB_Accession; ";

    $stmt = $PDO->prepare($query_str);
    $stmt->execute();
    $result = $stmt->fetchAll();

    $result_arr = pdoResultFilter($result);


    // Render the data
    if (isset($result_arr) && is_array($result_arr) && !empty($result_arr) && count($result_arr) > 0) {
        echo "<div style='width:auto; height:auto; border:3px solid #000; overflow:scroll; max-height:1000px; display:inline-block;'>";
        echo "<table style='text-align:center;'>";

        // Table header
        echo "<tr>";
        foreach ($result_arr[0] as $key => $value) {
            echo "<th style=\"border:1px solid black;\">" . strval($key) . "</th>";
        }
        echo "</tr>";

        // Table body
        for ($j = 0; $j < count($result_arr); $j++) {
            $tr_bgcolor = ($j % 2 ? "#FFFFFF" : "#DDFFDD");

            echo "<tr bgcolor=\"" . $tr_bgcolor . "\">";
            foreach ($result_arr[$j] as $key => $value) {
                echo "<td style=\"border:1px solid black;min-width:120px;\">" . $value . "</td>";
            }
            echo "</tr>";
        }

        echo "</table>";
        echo "</div>";

        echo "<br /><br />";
    }

}

?>


<?php include '../footer.php'; ?>
