<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<?php
$TITLE = "Soybean mViz";

include '../config.php';
include './php/pdoResultFilter.php';
?>

<!-- Get and process the variables -->
<?php
$gene_name_1 = $_GET['gene_name_1'];
$upstream_length_1 = $_GET['upstream_length_1'];

if (is_string($gene_name_1)) {
    $gene_arr = preg_split("/[;, \n]+/", $gene_name_1);
    for ($i = 0; $i < count($gene_arr); $i++) {
        $gene_arr[$i] = trim($gene_arr[$i]);
    }
} elseif (is_array($gene_name_1)) {
    $gene_arr = $gene_name_1;
    for ($i = 0; $i < count($gene_arr); $i++) {
        $gene_arr[$i] = trim($gene_arr[$i]);
    }
} else {
    exit(0);
}

if (is_string($upstream_length_1)) {
    $upstream_length = intval(trim($upstream_length_1));
} elseif (is_int($upstream_length_1)) {
    $upstream_length = upstream_length_1;
} elseif (is_float(upstream_length_1)) {
    $upstream_length = intval($upstream_length_1);
}
?>

<!-- Back button -->
<a href="/SoybeanMViz/"><button> &lt; Back </button></a>

<br />
<br />

<!-- Query gene regions from database -->
<?php
$query_str = "SELECT * FROM mViz_Soybean_GFF";
$query_str = $query_str . " WHERE (Name IN ('";
for ($i = 0; $i < count($gene_arr); $i++) {
    if ($i < (count($gene_arr)-1)){
        $query_str = $query_str . $gene_arr[$i] . "', '";
    } else {
        $query_str = $query_str . $gene_arr[$i];
    }
}
$query_str = $query_str . "'));";

$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$gene_result_arr = pdoResultFilter($result);
?>

<!-- Calculate promoter start and end -->
<?php
for ($i = 0; $i < count($gene_result_arr); $i++) {
    if ($gene_result_arr[$i]['Strand'] == '+') {
        $gene_result_arr[$i]['Promoter_End'] = $gene_result_arr[$i]['Start']-1;
        $gene_result_arr[$i]['Promoter_Start'] = ((($gene_result_arr[$i]['Promoter_End']-$upstream_length) > 0) ? ($gene_result_arr[$i]['Promoter_End']-$upstream_length) : 1);
    } elseif ($gene_result_arr[$i]['Strand'] == '-') {
        $gene_result_arr[$i]['Promoter_Start'] = $gene_result_arr[$i]['End']+1;
        $gene_result_arr[$i]['Promoter_End'] = $gene_result_arr[$i]['Promoter_Start'] + $upstream_length;
    }
}
?>

<!-- Get motifs -->
<?php
for ($i = 0; $i < count($gene_result_arr); $i++) {
    // Display gene, gene region, and promoter region
    echo "<p><b>" . $gene_result_arr[$i]['Name'] . "</b> (" . $gene_result_arr[$i]['Chromosome'] . ": " . $gene_result_arr[$i]['Start'] . " - " . $gene_result_arr[$i]['End'] . ") (" . $gene_result_arr[$i]['Strand'] . ")</p>";
    echo "<p><b>Promoter Region: </b>" . $gene_result_arr[$i]['Promoter_Start'] . " - " . $gene_result_arr[$i]['Promoter_End'] . "</p>";
    echo "<br />";

    // Get motifs
    $query_str = "
    SELECT MS.Chromosome, MS.Start, MS.End, MS.Strand, MS.Name AS Motif, TF.TF_Family, MS.Sequence, M.Gene FROM (
        SELECT Motif, Gene FROM mViz_Soybean_Motif WHERE Gene = '" . $gene_result_arr[$i]['Name'] . "'
    ) AS M
    INNER JOIN (
        SELECT Chromosome, Start, End, Strand, Name, Sequence FROM mViz_Soybean_Motif_Sequence 
        WHERE (Chromosome = '" . $gene_result_arr[$i]['Chromosome'] . "') AND (Strand = '" . $gene_result_arr[$i]['Strand'] . "') 
        AND ((Start BETWEEN " . $gene_result_arr[$i]['Promoter_Start'] . " AND " . $gene_result_arr[$i]['Promoter_End'] . " ) OR (End BETWEEN " . $gene_result_arr[$i]['Promoter_Start'] . " AND " . $gene_result_arr[$i]['Promoter_End'] . "))
    ) AS MS
    ON M.Motif = MS.Name
    LEFT JOIN mViz_Soybean_TF AS TF ON MS.Name = TF.TF
    ORDER BY Start, End;
    ";

    $stmt = $PDO->prepare($query_str);
    $stmt->execute();
    $result = $stmt->fetchAll();

    $motif_result_arr = pdoResultFilter($result);

    if (isset($motif_result_arr) && !empty($motif_result_arr)){
        echo "<div style='width:auto; height:auto; overflow:scroll; max-height:1000px;'>";
        echo "<table style='text-align:center; border:3px solid #000;'>";
        
        // Table header
        echo "<tr>";
        foreach ($motif_result_arr[0] as $key => $value) {
            echo "<th style=\"border:1px solid black; min-width:80px;\">" . $key . "</th>";
        }
        echo "</tr>";

        // Table body
        for ($j = 0; $j < count($motif_result_arr); $j++) {
            $tr_bgcolor = ($j % 2 ? "#FFFFFF" : "#DDFFDD");

            echo "<tr bgcolor=\"" . $tr_bgcolor . "\">";
            foreach ($motif_result_arr[$j] as $key => $value) {
                if ($key == "Motif") {
                    echo "<td style=\"border:1px solid black; min-width:80px;\"><a href=\"javascript:void(0);\" onclick=\"getMotifWeblogo('" . $value . "', '" . $gene_result_arr[$i]['Name'] . "', '" . $gene_result_arr[$i]['Chromosome'] . "', '" . $motif_result_arr[$j]['Start'] . "', '" . $motif_result_arr[$j]['End'] . "', '" . $motif_result_arr[$j]['Sequence'] . "')\">" . $value . "</a></td>";
                } else {
                    echo "<td style=\"border:1px solid black; min-width:80px;\">" . $value . "</td>";
                }
            }
            echo "</tr>";
        }

        echo "</table>";
        echo "</div>";

        echo "<br />";

        // Div tags for selected motif, weblogo, and motif sequence table
        echo "<div id=\"" . $gene_result_arr[$i]['Name'] . "_b\" style='width:auto; height:auto; overflow:visible; max-height:1000px;'></div>";
        echo "<div id=\"" . $gene_result_arr[$i]['Name'] . "_weblogo\" style='width:auto; height:auto; overflow:visible; max-height:1000px;'></div>";
        echo "<div id=\"" . $gene_result_arr[$i]['Name'] . "_detail_table\" style='width:auto; height:auto; overflow:visible; max-height:1000px;'></div>";

        echo "<br />";
        echo "<br />";
    } else {
        // Display no motif message if none is found
        echo "<div style='width:auto; height:auto; overflow:visible; max-height:1000px;'>";
        echo "No motif found!!!";
        echo "</div>";

        echo "<br />";
        echo "<br />";
    }


}
?>

<script type="text/javascript" language="javascript" src="./js/getMotifWeblogo.js"></script>

<?php include '../footer.php'; ?>
