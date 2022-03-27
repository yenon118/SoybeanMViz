
function getMotifWeblogo(motif, gene, motif_start, motif_end, motif_sequence) {

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

    // Load Ceqlogo / Weblogo image
    var weblogo = document.createElement("img");
    weblogo.setAttribute("src", "assets/motif_weblogos/"+motif+".png");

    // Create motif sequence table
    var detail_table = document.createElement("table");
    detail_table.setAttribute("style", "text-align:center; border:3px solid #000;");
    var detail_tr_index = document.createElement("tr");
    var detail_tr_position = document.createElement("tr");
    var detail_tr_nucleotide = document.createElement("tr");
    for (let i = 0; i < (motif_end-motif_start+1); i++) {
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
        detail_tr_nucleotide.appendChild(detail_td);
    }
    detail_table.appendChild(detail_tr_index);
    detail_table.appendChild(detail_tr_position);
    detail_table.appendChild(detail_tr_nucleotide);

    // Append motif, weblogo, and motif sequence table to the div tags
    document.getElementById(gene+"_b").appendChild(motif_b);
    document.getElementById(gene+"_weblogo").appendChild(weblogo);
    document.getElementById(gene+"_detail_table").appendChild(detail_table);

}
