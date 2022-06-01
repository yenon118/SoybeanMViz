<?php
$TITLE = "Soybean mViz";
include '../header.php';
?>

<div>
    <table width="100%" cellspacing="14" cellpadding="14">
        <tr>
            <td width="50%" align="center" valign="top" style="border:1px solid #999999; padding:10px; background-color:#f8f8f8; text-align:left;">
                <form action="viewAllByGenes.php" method="get" target="_blank">
                    <h2>Search By Gene IDs</h2>
                    <br />
                    <label for="gene_name_1"><b>Gene IDs:</b> (eg Glyma.01G049100 Glyma.01G049200 Glyma.01G049300)</label>
                    <textarea id="gene_name_1" name="gene_name_1" rows="8" cols="50" placeholder="&#10;Please separate each gene into a new line. &#10;&#10;Example:&#10;Glyma.01g049100&#10;Glyma.01g049200&#10;Glyma.01g049300"></textarea>
                    <br />
                    <br />
                    <label for="upstream_length_1"><b>Upstream length (bp):</b> (eg 2000)</label>
                    <input type="text" id="upstream_length_1" name="upstream_length_1" size="60">
                    <br />
                    <br />
                    <input type="submit" value="Search">
                </form>
            </td>
            <td width="50%" align="center" valign="top" style="border:1px solid #999999; padding:10px; background-color:#f8f8f8; text-align:left;">
            </td>
        </tr>
        <tr>
        </tr>
    </table>
    <br />
    <br />
</div>


<script type="text/javascript" language="javascript">
</script>

<?php include '../footer.php'; ?>
