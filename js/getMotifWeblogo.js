
function getMotifWeblogo(motif, gene, chromosome, motif_start, motif_end, motif_sequence) {

    // Clear data appended to the div tags, if there is any
    if (document.getElementById(gene+"_b").innerHTML) {
        document.getElementById(gene+"_b").innerHTML = null;
    }
    if (document.getElementById(gene+"_weblogo").innerHTML) {
        document.getElementById(gene+"_weblogo").innerHTML = null;
    }
    if (document.getElementById(gene+"_detail_table").innerHTML) {
        document.getElementById(gene+"_detail_table").innerHTML = null;
    }

    // Create b tag for motif
    var motif_b = document.createElement("b");
    motif_b.innerHTML = "Selected Motif: " + motif;
    document.getElementById(gene+"_b").appendChild(motif_b);

    // Load Ceqlogo / Weblogo image
    var weblogo = document.createElement("img");
    weblogo.setAttribute("src", "assets/motif_weblogos/"+motif+".png");
    document.getElementById(gene+"_weblogo").appendChild(weblogo);

    $.ajax({
        url: 'php/query_chrom_pos_ref_alt.php',
        type: 'GET',
        contentType: 'application/json',
        data: {
            Chromosome: chromosome,
            Start: motif_start,
            End: motif_end
        },
        success: function (response) {
            let res = JSON.parse(response);
            res = res.data;

            // Create a dictionary to store chrom pos ref alt 
            let chrom_pos_ref_alt_dict = {}
            for (let i = 0; i < res.length; i++) {
                chrom_pos_ref_alt_dict[res[i]['Position']] = res[i];
            }

            // Create motif sequence table
            let detail_table = document.createElement("table");
            detail_table.setAttribute("style", "text-align:center; border:3px solid #000;");
            let detail_tr_index = document.createElement("tr");
            let detail_tr_position = document.createElement("tr");
            let detail_tr_nucleotide = document.createElement("tr");
            let detail_tr_ref_alt = document.createElement("tr");

            for (let i = 0; i < (motif_end-motif_start+1); i++) {
                var ref_alt = '';

                var detail_th = document.createElement("th");
                detail_th.setAttribute("style", "border:1px solid black; min-width:80px;");
                detail_th.innerHTML = Number(i)+1;
                detail_tr_index.appendChild(detail_th);

                var detail_th = document.createElement("th");
                detail_th.setAttribute("style", "border:1px solid black; min-width:80px;");
                detail_th.innerHTML = Number(motif_start)+Number(i);
                detail_tr_position.appendChild(detail_th);

                var detail_td = document.createElement("td");
                detail_td.setAttribute("style", "border:1px solid black; min-width:80px;");
                detail_td.innerHTML = motif_sequence[i];
                if (Object.keys(chrom_pos_ref_alt_dict).includes(String(Number(motif_start)+Number(i)))) {
                    let position = String(Number(motif_start)+Number(i));
                    let reference_allele = chrom_pos_ref_alt_dict[position]['Reference_Allele'];
                    let alternate_allele_array = String(chrom_pos_ref_alt_dict[position]['Alternate_Allele']).split(",");

                    if (motif_sequence[i] == reference_allele) {
                        detail_td.style.backgroundColor = '#9EE85C';
                        ref_alt = 'Ref';
                    } else if (alternate_allele_array.includes(motif_sequence[i])) {
                        detail_td.style.backgroundColor = '#F26A55';
                        ref_alt = 'Alt';
                    }
                }
                detail_tr_nucleotide.appendChild(detail_td);

                var detail_td = document.createElement("td");
                detail_td.setAttribute("style", "border:1px solid black; min-width:80px;");
                if (ref_alt == 'Ref') {
                    detail_td.innerHTML = 'Reference (Wm82.a2.v1)';
                } else if (ref_alt == 'Alt') {
                    detail_td.innerHTML = 'Alternate (Wm82.a2.v1)';
                } else {
                    detail_td.innerHTML = '';
                }
                ref_alt = ''
                detail_tr_ref_alt.appendChild(detail_td);
            }

            detail_table.appendChild(detail_tr_index);
            detail_table.appendChild(detail_tr_position);
            detail_table.appendChild(detail_tr_nucleotide);
            detail_table.appendChild(detail_tr_ref_alt);

            document.getElementById(gene+"_detail_table").appendChild(detail_table);
        },
        error: function (xhr, status, error) {
            console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
        }
    });

    // Change the overflow style of the div to scroll
    document.getElementById(gene+"_b").style.overflow = 'scroll';
    document.getElementById(gene+"_weblogo").style.overflow = 'scroll';
    document.getElementById(gene+"_detail_table").style.overflow = 'scroll';
}
