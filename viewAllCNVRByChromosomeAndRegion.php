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

<!-- Query data from database -->
<?php

// Check GRIN accession mapping
$query_str = "SELECT CNVR.Chromosome, CNVR.Start, CNVR.End, CNVR.Width, CNVR.Strand, AM.SoyKB_Accession AS Accession, CNVR.CN ";
$query_str = $query_str . "FROM soykb.mViz_Soybean_CNVR AS CNVR ";
$query_str = $query_str . "LEFT JOIN soykb.mViz_Soybean_Accession_Mapping AS AM ";
$query_str = $query_str . "ON CNVR.Accession = AM.Accession ";
$query_str = $query_str . "WHERE (CNVR.Chromosome = '" . $chromosome_1 . "') ";
$query_str = $query_str . "AND (CNVR.Start BETWEEN " . $position_start_1 . " AND " . $position_end_1 . ") ";
$query_str = $query_str . "AND (CNVR.End BETWEEN " . $position_start_1 . " AND " . $position_end_1 . ") ";
$query_str = $query_str . "ORDER BY AM.SoyKB_Accession, CNVR.CN, CNVR.Chromosome, CNVR.Start, CNVR.End; ";

$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

if (count($result) > 0) {
    $result_arr = pdoResultFilter($result);
} else {
    echo "No data found!!!";
}

?>


<!-- Render the table -->
<?php

    if (count($result_arr) > 0) {
        echo "<div style='width:auto; height:auto; border:3px solid #000; overflow:scroll; max-height:1000px; display:inline-block;'>";
        echo "<table style='text-align:center;'>";

        // Table header
        echo "<tr>";
        foreach ($result_arr[0] as $key => $value) {
            echo "<th style=\"border:1px solid black;\">" . strval($key) . "</th>";
        }
        echo "</tr>";

        // Table body
        for ($i = 0; $i < count($result_arr); $i++) {
            $tr_bgcolor = ($i % 2 ? "#FFFFFF" : "#DDFFDD");

            echo "<tr bgcolor=\"" . $tr_bgcolor . "\">";
            foreach ($result_arr[$i] as $key => $value) {
                echo "<td style=\"border:1px solid black;min-width:120px;\">" . $value . "</td>";
            }
            echo "</tr>";
        }

        echo "</table>";
        echo "</div>";
    }

?>


<?php include '../footer.php'; ?>
