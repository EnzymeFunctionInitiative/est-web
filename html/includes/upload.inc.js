
function uploadFile() {
    var fd = new FormData();
    var option_selected;
    var uploadHandler = uploadProgressFasta;
    //Option A Selected
    if (document.getElementById('option_selected_a').checked) {
        option_selected = document.getElementById('option_selected_a').value;
        fd.append('blast_input',document.getElementById('blast_input').value);
        fd.append('blast_evalue',document.getElementById('blast_evalue').value);
        fd.append('blast_max_seqs',document.getElementById('blast_max_seqs').value);
    }

    //Option B Selected
    else if (document.getElementById('option_selected_b').checked) {
        option_selected = document.getElementById('option_selected_b').value;
        fd.append('families_input',document.getElementById('families_input').value);
        fd.append('pfam_evalue',document.getElementById('pfam_evalue').value);
        fd.append('pfam_fraction',document.getElementById('pfam_fraction').value);
        fd.append('pfam_domain',document.getElementById('pfam_domain').checked);
        fd.append('program',document.getElementById('option_b_program').value);
    }

    //Option C Selected
    else if (document.getElementById('option_selected_c').checked) {
        option_selected = document.getElementById('option_selected_c').value;
        fd.append("fasta_file", document.getElementById('fasta_file').files[0]);
        fd.append('families_input2',document.getElementById('families_input2').value);
        fd.append('fasta_evalue',document.getElementById('fasta_evalue').value);
        fd.append('fasta_fraction',document.getElementById('fasta_fraction').value);
        fd.append('program',document.getElementById('option_c_program').value);
    }

    //Option D Selected
    else if (document.getElementById('option_selected_d').checked) {
        option_selected = document.getElementById('option_selected_d').value;
        fd.append("accession_file", document.getElementById('accession_file').files[0]);
        fd.append('accession_evalue',document.getElementById('accession_evalue').value);
        fd.append('accession_fraction',document.getElementById('accession_fraction').value);
        fd.append('program',document.getElementById('option_d_program').value);
        uploadHandler = uploadProgressAccession;
    }

    //Option E Selected
    else if (document.getElementById('option_selected_e').checked) {
        option_selected = document.getElementById('option_selected_e').value;
        fd.append("fasta_id_file", document.getElementById('fasta_id_file').files[0]);
        fd.append('fasta_id_evalue',document.getElementById('fasta_id_evalue').value);
        fd.append('fasta_id_fraction',document.getElementById('fasta_id_fraction').value);
        fd.append('program',document.getElementById('option_e_program').value);
        uploadHandler = uploadProgressFastaId;
    }

    //Global Form Options
    fd.append('option_selected',option_selected);
    fd.append('MAX_FILE_SIZE',document.getElementById('MAX_FILE_SIZE').value);
    fd.append('email',document.getElementById('email').value);
    fd.append('submit',document.getElementById('submit').value);
    disableForm();



    //Create http post request to create.php
    var xhr = new XMLHttpRequest();
    xhr.upload.addEventListener("progress", uploadHandler, false);
    xhr.addEventListener("load", uploadComplete, false);
    xhr.addEventListener("error", uploadFailed, false);
    xhr.addEventListener("abort", uploadCanceled, false);
    xhr.open("POST", "create.php",true);
    xhr.send(fd);

    xhr.onreadystatechange  = function(){
        if (xhr.readyState == 4  ) {

            // Javascript function JSON.parse to parse JSON data
            var jsonObj = JSON.parse(xhr.responseText);

            // jsonObj variable now contains the data structure and can
            // be accessed as jsonObj.name and jsonObj.country.
            if (jsonObj.valid) {
                window.location.replace("stepb.php");
            }
            if (jsonObj.message) {
                enableForm();
                document.getElementById("message").innerHTML =  jsonObj.message;
            }

        }
    }

}


function uploadProgressFasta(evt) {
    if (evt.lengthComputable) {
        var percentComplete = Math.round(evt.loaded * 100 / evt.total);
        document.getElementById('progressNumberFasta').innerHTML = "Uploading File: " + percentComplete.toString() + '%';
        var bar = document.getElementById('progress_bar_fasta');
        bar.value = percentComplete;
    }
    else {
        document.getElementById('progressNumberFasta').innerHTML = 'unable to compute';
    }
}

function uploadProgressFastaId(evt) {
    if (evt.lengthComputable) {
        var percentComplete = Math.round(evt.loaded * 100 / evt.total);
        document.getElementById('progressNumberFastaId').innerHTML = "Uploading File: " + percentComplete.toString() + '%';
        var bar = document.getElementById('progress_bar_fasta_id');
        bar.value = percentComplete;
    }
    else {
        document.getElementById('progressNumberFastaId').innerHTML = 'unable to compute';
    }
}
function uploadProgressAccession(evt) {
    if (evt.lengthComputable) {
        var percentComplete = Math.round(evt.loaded * 100 / evt.total);
        document.getElementById('progressNumberAccession').innerHTML = "Uploading File: " + percentComplete.toString() + '%';
        var bar = document.getElementById('progress_bar_accession');
        bar.value = percentComplete;
    }
    else {
        document.getElementById('progressNumberAccession').innerHTML = 'unable to compute';
    }
}

function uploadComplete(evt) {
    /* This event is raised when the server send back a response */
    //alert(evt.target.responseText);
}

function uploadFailed(evt) {
    alert("There was an error attempting to upload the file.");
}

function uploadCanceled(evt) {
    alert("The upload has been canceled by the user or the browser dropped the connection.");
}

function disableForm() {
    document.getElementById("option_a").disabled = true;
    document.getElementById("option_b").disabled = true;
    document.getElementById("option_c").disabled = true;
    document.getElementById("option_d").disabled = true;
    document.getElementById("option_e").disabled = true;
    document.getElementById('email').disabled = true;
    document.getElementById('submit').disabled = true;


}
function enableForm() {
    document.getElementById("option_a").disabled = false;
    document.getElementById("option_b").disabled = false;
    document.getElementById("option_c").disabled = false;
    document.getElementById("option_d").disabled = false;
    document.getElementById("option_e").disabled = false;
    document.getElementById('email').disabled = false;
    document.getElementById('submit').disabled = false;


}
