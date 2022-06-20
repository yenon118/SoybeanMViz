function convertJsonToCsv(jsonObject) {
    let csvString = '';
    let th_keys = Object.keys(jsonObject[0]);
    for (let i = 0; i < th_keys.length; i++) {
        th_keys[i] = "\"" + th_keys[i] + "\"";
    }
    csvString += th_keys.join(',') + '\n';
    for (let i = 0; i < jsonObject.length; i++) {
        let tr_keys = Object.keys(jsonObject[i]);
        for (let j = 0; j < tr_keys.length; j++) {
            csvString += ((jsonObject[i][tr_keys[j]] === null) || (jsonObject[i][tr_keys[j]] === undefined)) ? '\"\"' : "\"" + jsonObject[i][tr_keys[j]] + "\"";
            if (j < (tr_keys.length-1)) {
                csvString += ',';
            }
        }
        csvString += '\n';
    }
    return csvString;
}


function createAndDownloadCsvFile(csvString, filename) {
    let dataStr = "data:text/csv;charset=utf-8," + encodeURI(csvString);
    let downloadAnchorNode = document.createElement('a');
    downloadAnchorNode.setAttribute("href", dataStr);
    downloadAnchorNode.setAttribute("download", filename + ".csv");
    document.body.appendChild(downloadAnchorNode); // required for firefox
    downloadAnchorNode.click();
    downloadAnchorNode.remove();
}


function uncheck_all() {
    let ids = document.querySelectorAll('input[id^=CN],input[id^=chemical_descriptor_],input[id^=disease_descriptor_],input[id^=growth_descriptor_],input[id^=insect_descriptor_],input[id^=morphology_descriptor_],input[id^=other_descriptor_],input[id^=phenology_descriptor_],input[id^=qualifier_],input[id^=stress_descriptor_]');

    for (let i = 0; i < ids.length; i++) {
        if (ids[i].checked) {
            ids[i].checked = false;
        }
    }
}


function check_all() {
    let ids = document.querySelectorAll('input[id^=CN],input[id^=chemical_descriptor_],input[id^=disease_descriptor_],input[id^=growth_descriptor_],input[id^=insect_descriptor_],input[id^=morphology_descriptor_],input[id^=other_descriptor_],input[id^=phenology_descriptor_],input[id^=qualifier_],input[id^=stress_descriptor_]');

    for (let i = 0; i < ids.length; i++) {
        if (!ids[i].checked) {
            ids[i].checked = true;
        }
    }
}


function constructInfoTable(res) {

    // Create table
    let detail_table = document.createElement("table");
    detail_table.setAttribute("style", "text-align:center; border:3px solid #000;");
    let detail_header_tr = document.createElement("tr");

    let header_array = Object.keys(res[0]);
    for (let i = 0; i < header_array.length; i++) {
        var detail_th = document.createElement("th");
        detail_th.setAttribute("style", "border:1px solid black; min-width:80px; height:18.5px;");
        detail_th.innerHTML = header_array[i];
        detail_header_tr.appendChild(detail_th);
    }

    detail_table.appendChild(detail_header_tr);

    for (let i = 0; i < res.length; i++) {
        var detail_tr = document.createElement("tr");
        detail_tr.style.backgroundColor = ((i%2) ? "#FFFFFF" : "#DDFFDD");
        for (let j = 0; j < header_array.length; j++) {
            var detail_td = document.createElement("td");
            detail_td.setAttribute("style", "min-width:80px; height:18.5px;");
            detail_td.innerHTML = res[i][header_array[j]];
            detail_tr.appendChild(detail_td);
        }
        detail_table.appendChild(detail_tr);
    }

    return detail_table;
}


function qeuryCNVRAndPhenotype() {

    // Clear data appended to the div tags, if there is any
    if (document.getElementById('CNVR_and_Phenotye_detail_table').innerHTML) {
        document.getElementById('CNVR_and_Phenotye_detail_table').innerHTML = null;
    }

    let chromosome_1 = document.getElementById('chromosome_1').value;
    let position_start_1 = document.getElementById('position_start_1').value;
    let position_end_1 = document.getElementById('position_end_1').value;

    let cn_ids = document.querySelectorAll('input[id^=CN]');
    let cn_array = [];

    let phenotype_ids = document.querySelectorAll('input[id^=chemical_descriptor_],input[id^=disease_descriptor_],input[id^=growth_descriptor_],input[id^=insect_descriptor_],input[id^=morphology_descriptor_],input[id^=other_descriptor_],input[id^=phenology_descriptor_],input[id^=qualifier_],input[id^=stress_descriptor_]');
    let phenotype_array = [];

    for (let i = 0; i < cn_ids.length; i++) {
        if (cn_ids[i].checked) {
            cn_array.push(cn_ids[i].value);
        }
    }

    for (let i = 0; i < phenotype_ids.length; i++) {
        if (phenotype_ids[i].checked) {
            phenotype_array.push(phenotype_ids[i].value);
        }
    }


    if (chromosome_1 && position_start_1 && position_end_1 && cn_array.length > 0 && phenotype_array.length > 0) {
        $.ajax({
            url: './php/qeuryCNVRAndPhenotype.php',
            type: 'GET',
            contentType: 'application/json',
            data: {
                Chromosome: chromosome_1,
                Start: position_start_1,
                End: position_end_1,
                CN: cn_array,
                Phenotype: phenotype_array
            },
            success: function (response) {
                res = JSON.parse(response);
                res = res.data;

                if (res.length > 0) {
                    document.getElementById('CNVR_and_Phenotye_detail_table').appendChild(
                        constructInfoTable(res)
                    );
                    document.getElementById('CNVR_and_Phenotye_detail_table').style.overflow = 'scroll';
                } else {
                    let error_message = document.createElement("p");
                    error_message.innerHTML = "Please select CN and phenotype!!!";
                    document.getElementById('CNVR_and_Phenotye_detail_table').appendChild(error_message);
                    document.getElementById('CNVR_and_Phenotye_detail_table').style.overflow = 'visible';
                }
                
            },
            error: function (xhr, status, error) {
                console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
            }
        });
    } else {
        let error_message = document.createElement("p");
        error_message.innerHTML = "Please select CN and phenotype!!!";
        document.getElementById('CNVR_and_Phenotye_detail_table').appendChild(error_message);
        document.getElementById('CNVR_and_Phenotye_detail_table').style.overflow = 'visible';
    }

}


function downloadCNVRAndPhenotype() {

    let chromosome_1 = document.getElementById('chromosome_1').value;
    let position_start_1 = document.getElementById('position_start_1').value;
    let position_end_1 = document.getElementById('position_end_1').value;

    let cn_ids = document.querySelectorAll('input[id^=CN]');
    let cn_array = [];

    let phenotype_ids = document.querySelectorAll('input[id^=chemical_descriptor_],input[id^=disease_descriptor_],input[id^=growth_descriptor_],input[id^=insect_descriptor_],input[id^=morphology_descriptor_],input[id^=other_descriptor_],input[id^=phenology_descriptor_],input[id^=qualifier_],input[id^=stress_descriptor_]');
    let phenotype_array = [];

    for (let i = 0; i < cn_ids.length; i++) {
        if (cn_ids[i].checked) {
            cn_array.push(cn_ids[i].value);
        }
    }

    for (let i = 0; i < phenotype_ids.length; i++) {
        if (phenotype_ids[i].checked) {
            phenotype_array.push(phenotype_ids[i].value);
        }
    }


    if (chromosome_1 && position_start_1 && position_end_1 && cn_array.length > 0 && phenotype_array.length > 0) {
        $.ajax({
            url: './php/qeuryCNVRAndPhenotype.php',
            type: 'GET',
            contentType: 'application/json',
            data: {
                Chromosome: chromosome_1,
                Start: position_start_1,
                End: position_end_1,
                CN: cn_array,
                Phenotype: phenotype_array
            },
            success: function (response) {
                res = JSON.parse(response);
                res = res.data;

                if (res.length > 0) {
                    let csvString = convertJsonToCsv(res);
                    createAndDownloadCsvFile(csvString, String(chromosome_1) + "_" + String(position_start_1) + "_" + String(position_end_1) + "_data");
                    
                } else {
                    alert("Please select CN and phenotype to download data!!!");
                }
                
            },
            error: function (xhr, status, error) {
                console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
            }
        });
    } else {
        alert("Please select CN and phenotype to download data!!!");
    }

}
