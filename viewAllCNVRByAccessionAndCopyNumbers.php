<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<?php
$TITLE = "Soybean MViz";

// include '../header.php';
include '../config.php';
include './php/pdoResultFilter.php';
?>

<!-- Get and process the variables -->
<?php
$accession_1 = $_GET['accession_1'];
$copy_number_1 = $_GET['copy_number_1'];

$accession_1 = trim($accession_1);

if (is_string($copy_number_1)) {
    $copy_number_arr = preg_split("/[;, \n]+/", trim($copy_number_1));
    for ($i = 0; $i < count($copy_number_arr); $i++) {
        $copy_number_arr[$i] = trim($copy_number_arr[$i]);
    }
} elseif (is_array($copy_number_1)) {
    $copy_number_arr = $copy_number_1;
    for ($i = 0; $i < count($copy_number_arr); $i++) {
        $copy_number_arr[$i] = trim($copy_number_arr[$i]);
    }
} else {
    exit(0);
}

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
$query_str = $query_str . "WHERE ((AM.SoyKB_Accession = '" . $accession_1 . "') OR (AM.GRIN_Accession = '" . $accession_1 . "')) AND (CNVR.CN IN ('";
for ($i = 0; $i < count($copy_number_arr); $i++) {
    if($i < (count($copy_number_arr)-1)){
        $query_str = $query_str . trim($copy_number_arr[$i]) . "', '";
    } elseif ($i == (count($copy_number_arr)-1)) {
        $query_str = $query_str . trim($copy_number_arr[$i]);
    }
}
$query_str = $query_str . "')) ";
$query_str = $query_str . "ORDER BY CNVR.CN, CNVR.Chromosome, CNVR.Start, CNVR.End; ";

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
