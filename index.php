<?php
$TITLE = "Soybean mViz";
include '../header.php';
?>

<div>
    <table width="100%" cellspacing="14" cellpadding="14">
        <tr>
            <td><h2>Promoter Search</h2></td>
        </tr>
        <tr>
            <td width="50%" align="center" valign="top" style="border:1px solid #999999; padding:10px; background-color:#f8f8f8; text-align:left;">
                <form action="viewAllMotifByGenes.php" method="get" target="_blank">
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
            <!-- <td width="50%" align="center" valign="top" style="border:1px solid #999999; padding:10px; background-color:#f8f8f8; text-align:left;">
            </td> -->
        </tr>
        <tr>
            <td>
                <br />
                <br />
            </td>
        </tr>
        <tr>
            <td><h2>Copy Number Variation Search</h2></td>
        </tr>
        <tr>
            <td width="50%" align="center" valign="top" style="border:1px solid #999999; padding:10px; background-color:#f8f8f8; text-align:left;">
                <form action="viewAllCNVByChromosomeAndRegion.php" method="get" target="_blank">
                    <h2>Search By Chromosome and Region</h2>
                    <br />
                    <label for="chromosome_1"><b>Chromosome:</b> (eg Chr01)</label>
                    <input type="text" id="chromosome_1" name="chromosome_1" size="60">
                    <br />
                    <br />
                    <br />
                    <br />
                    <label for="position_start_1"><b>Starting Position:</b> (eg 29325001)</label>
                    <input type="text" id="position_start_1" name="position_start_1" size="60">
                    <br />
                    <br />
                    <label for="position_end_1"><b>Ending Position:</b> (eg 29425000)</label>
                    <input type="text" id="position_end_1" name="position_end_1" size="60">
                    <br />
                    <br />
                    <br />
                    <br />
                    <br />
                    <br />
                    <input type="submit" value="Search">
                </form>
            </td>
            <td width="50%" align="center" valign="top" style="border:1px solid #999999; padding:10px; background-color:#f8f8f8; text-align:left;">
                <form action="viewAllCNVByAccessionAndCopyNumber.php" method="get" target="_blank">
                    <h2>Search By Accession and Copy Number</h2>
                    <br />
                    <label for="accession_1"><b>Accession:</b> (eg CRR108616)</label>
                    <input type="text" id="accession_1" name="accession_1" size="60">
                    <br />
                    <br />
                    <label for="copy_number_1"><b>Copy Number:</b> (eg CN0 CN1 CN3 CN4 CN5 CN6 CN7 CN8)</label>
                    <textarea id="copy_number_1" name="copy_number_1" rows="10" cols="50" placeholder="&#10;Please separate each copy number into a new line. &#10;&#10;Example:&#10;CN0&#10;CN1&#10;CN3&#10;&#10;**There is no CN2 as CN2 represents normal."></textarea>
                    <br />
                    <br />
                    <input type="submit" value="Search">
                </form>    
            </td>
        </tr>
    </table>
    <br />
    <br />
</div>


<script type="text/javascript" language="javascript">
</script>

<?php include '../footer.php'; ?>
