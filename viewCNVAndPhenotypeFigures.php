
<script src="https://cdn.plot.ly/plotly-2.12.1.min.js"></script>

<?php
$TITLE = "Soybean MViz";

// include '../header.php';
include '../config.php';
include './php/pdoResultFilter.php';
?>

<!-- Get and process the variables -->
<?php
$chromosome = $_GET['chromosome_1'];
$position_start = $_GET['position_start_1'];
$position_end = $_GET['position_end_1'];
$data_option = $_GET['cnv_data_option_1'];
$phenotype = $_GET['phenotype_1'];
$cn_1 = $_GET['cn_1'];

$chromosome = trim($chromosome);
$position_start = intval(trim($position_start));
$position_end = intval(trim($position_end));
$data_option = trim($data_option);
$phenotype = trim($phenotype);
$cn_1 = trim($cn_1);

if (is_string($cn_1)) {
    $cn_array = preg_split("/[;,\n]+/", trim($cn_1));
} elseif (is_array($cn_1)) {
    $cn_array = $cn_1;
}

?>

<!-- Query phenotype data from database -->
<?php
if(isset($chromosome) && isset($position_start) && isset($position_end) && isset($data_option) && isset($phenotype) && isset($cn_array) && is_array($cn_array) && !empty($cn_array)) {

    $query_str = "SELECT CNV.Chromosome, CNV.Start, CNV.End, CNV.Width, CNV.Strand, AM.SoyKB_Accession AS Accession, CNV.CN, ";
    $query_str = $query_str . "CASE CNV.CN ";
    $query_str = $query_str . "WHEN 'CN0' THEN 'Loss' ";
    $query_str = $query_str . "WHEN 'CN1' THEN 'Loss' ";
    $query_str = $query_str . "WHEN 'CN3' THEN 'Gain' ";
    $query_str = $query_str . "WHEN 'CN4' THEN 'Gain' ";
    $query_str = $query_str . "WHEN 'CN5' THEN 'Gain' ";
    $query_str = $query_str . "WHEN 'CN6' THEN 'Gain' ";
    $query_str = $query_str . "WHEN 'CN7' THEN 'Gain' ";
    $query_str = $query_str . "WHEN 'CN8' THEN 'Gain' ";
    $query_str = $query_str . "ELSE 'Normal' ";
    $query_str = $query_str . "END as Status, ";
    $query_str = $query_str . "G." . $phenotype . " ";
    $query_str = $query_str . "FROM ";
    if ($data_option == "Individual_Hits") {
        $query_str = $query_str . "soykb.mViz_Soybean_CNVS ";
    } else if ($data_option == "Consensus_Regions") {
        $query_str = $query_str . "soykb.mViz_Soybean_CNVR ";
    }
    $query_str = $query_str . "AS CNV ";
    $query_str = $query_str . "LEFT JOIN soykb.mViz_Soybean_Accession_Mapping AS AM ";
    $query_str = $query_str . "ON CNV.Accession = AM.Accession ";
    $query_str = $query_str . "LEFT JOIN soykb.germplasm AS G ";
    $query_str = $query_str . "ON AM.GRIN_Accession = G.ACNO ";
    $query_str = $query_str . "WHERE (CNV.Chromosome = '" . $chromosome . "') ";
    $query_str = $query_str . "AND (CNV.Start BETWEEN " . $position_start . " AND " . $position_end . ") ";
    $query_str = $query_str . "AND (CNV.End BETWEEN " . $position_start . " AND " . $position_end . ") ";
    if (count($cn_array) > 0) {
        $query_str = $query_str . "AND (CNV.CN IN ('";
        for ($i = 0; $i < count($cn_array); $i++) {
            if($i < (count($cn_array)-1)){
                $query_str = $query_str . trim($cn_array[$i]) . "', '";
            } elseif ($i == (count($cn_array)-1)) {
                $query_str = $query_str . trim($cn_array[$i]);
            }
        }
        $query_str = $query_str . "')) ";
    }
    $query_str = $query_str . "AND (G." . $phenotype . " IS NOT NULL) ";
    $query_str = $query_str . "AND (G." . $phenotype . " != '') ";
    $query_str = $query_str . "AND (G." . $phenotype . " != '-') ";
    $query_str = $query_str . "AND (G." . $phenotype . " != '_') ";
    $query_str = $query_str . "AND (G." . $phenotype . " != 'NA') ";
    $query_str = $query_str . "AND (G." . $phenotype . " != 'NA') ";
    $query_str = $query_str . "ORDER BY CNV.CN, CNV.Chromosome, CNV.Start, CNV.End, AM.SoyKB_Accession; ";

    $stmt = $PDO->prepare($query_str);
    $stmt->execute();
    $result = $stmt->fetchAll();

    $result_arr = pdoResultFilter($result);
}

?>

<!-- Query information -->
<?php
echo "<h3>Queried CNV:</h3>";
echo "<div style='width:auto; height:auto; overflow:visible; max-height:1000px;'>";
echo "<table style='text-align:center; border:3px solid #000;'>";
echo "<tr>";
echo "<th style=\"border:1px solid black; min-width:80px;\">Chromsome</th>";
echo "<th style=\"border:1px solid black; min-width:80px;\">Position Start</th>";
echo "<th style=\"border:1px solid black; min-width:80px;\">Position End</th>";
if (isset($result_arr) && is_array($result_arr) && !empty($result_arr)) {
    echo "<th style=\"border:1px solid black; min-width:80px;\">Width</th>";
    echo "<th style=\"border:1px solid black; min-width:80px;\">Strand</th>";
}
echo "<th style=\"border:1px solid black; min-width:80px;\">Data Option</th>";
echo "<th style=\"border:1px solid black; min-width:80px;\">CN</th>";
echo "</tr>";
echo "<tr bgcolor=\"#DDFFDD\">";
echo "<td style=\"border:1px solid black; min-width:80px;\">" . $chromosome . "</td>";
echo "<td style=\"border:1px solid black; min-width:80px;\">" . $position_start . "</td>";
echo "<td style=\"border:1px solid black; min-width:80px;\">" . $position_end . "</td>";
if (isset($result_arr) && is_array($result_arr) && !empty($result_arr)) {
    echo "<td style=\"border:1px solid black; min-width:80px;\">" . $result_arr[0]["Width"] . "</td>";
    echo "<td style=\"border:1px solid black; min-width:80px;\">" . $result_arr[0]["Strand"] . "</td>";
}
echo "<td style=\"border:1px solid black; min-width:80px;\">" . $data_option . "</td>";
echo "<td style=\"border:1px solid black; min-width:80px;\">" . implode(',', $cn_array) . "</td>";
echo "</tr>";
echo "</table>";
echo "<br /><br />";
?>

<h3>Figures:</h3>
<div id="cn_figure_div"></div>
<div id="status_figure_div"></div>


<script type="text/javascript" language="javascript" src="./js/viewCNVAndPhenotypeFigures.js"></script>

<script type="text/javascript" language="javascript">

    var phenotype = <?php if(isset($phenotype)) {echo json_encode($phenotype, JSON_INVALID_UTF8_IGNORE);} else {echo "";}?>;
    var queried_data = <?php if(isset($cn_array) && is_array($cn_array) && !empty($cn_array)) {echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);} else {echo "";} ?>;

    if (queried_data != "" && phenotype != "") {
        var result_arr = processQueriedData(queried_data['data'], phenotype);

        var cnData = collectDataForFigure(result_arr, phenotype, 'CN');
        var statusData = collectDataForFigure(result_arr, phenotype, 'Status');

        plotFigure(cnData, 'CN', 'cn_figure_div')
        plotFigure(statusData, 'Status', 'status_figure_div')
    }

</script>
