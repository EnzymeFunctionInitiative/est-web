
function uploadFile() {
    var fd = new FormData();
    var option_selected;
    var uploadHandler = uploadProgressFasta;
    //Option A Selected
    if (document.getElementById('option_selected_a').checked) {
        option_selected = document.getElementById('option_selected_a').value;
        fd.append('blast_input',document.getElementById('blast_input').value);
        fd.append('evalue',document.getElementById('blast_evalue').value);
        fd.append('blast_max_seqs',document.getElementById('blast_max_seqs').value);
    }

    //Option B Selected
    else if (document.getElementById('option_selected_b').checked) {
        option_selected = document.getElementById('option_selected_b').value;
        fd.append('families_input',document.getElementById('families_input').value);
        fd.append('evalue',document.getElementById('pfam_evalue').value);
        fd.append('fraction',document.getElementById('pfam_fraction').value);
        fd.append('pfam_domain',document.getElementById('pfam_domain').checked);
        //TODO: conditionally enable this
        //fd.append('program',document.getElementById('option_b_program').value);
    }

    //Option C Selected
    else if (document.getElementById('option_selected_c').checked) {
        option_selected = document.getElementById('option_selected_c').value;
        fd.append("file", document.getElementById('fasta_file').files[0]);
        fd.append('families_input',document.getElementById('families_input2').value);
        fd.append('evalue',document.getElementById('fasta_evalue').value);
        fd.append('fraction',document.getElementById('fasta_fraction').value);
        // This checkbox replaces Option E
        fd.append('use_fasta_headers', document.getElementById('fasta_use_headers').checked);
        fd.append('fasta_input', document.getElementById('fasta_input').value);
        //TODO: conditionally enable this
        //fd.append('program',document.getElementById('option_c_program').value);
    }

    //Option D Selected
    else if (document.getElementById('option_selected_d').checked) {
        option_selected = document.getElementById('option_selected_d').value;
        fd.append("file", document.getElementById('accession_file').files[0]);
        fd.append('evalue',document.getElementById('accession_evalue').value);
        fd.append('fraction',document.getElementById('accession_fraction').value);
        fd.append('families_input',document.getElementById('families_input4').value);
        fd.append('accession_input', document.getElementById('accession_input').value);
        fd.append('accession_use_uniref', document.getElementById('accession_use_uniref').value);
        //TODO: conditionally enable this
        //fd.append('program',document.getElementById('option_d_program').value);
        uploadHandler = uploadProgressAccession;
    }

    else if (document.getElementById('option_selected_colorssn').checked) {
        option_selected = document.getElementById('option_selected_colorssn').value;
        fd.append("file", document.getElementById('colorssn_file').files[0]);
        //fd.append("cooccurrence", document.getElementById('cooccurrence').value);
        //fd.append("neighborhood_size", document.getElementById('neighbor_size').value);
        uploadHandler = uploadProgressColorSsn;
    }

    //Option E Selected
    else if (document.getElementById('option_selected_e').checked) {
        option_selected = document.getElementById('option_selected_e').value;
        fd.append('families_input',document.getElementById('pfam_plus_families').value);
        fd.append('evalue',document.getElementById('pfam_plus_evalue').value);
        fd.append('fraction',document.getElementById('pfam_plus_fraction').value);
        fd.append('pfam_domain',document.getElementById('pfam_plus_domain').checked);
        fd.append('pfam_seqid',document.getElementById('pfam_plus_seqid').value);
        fd.append('pfam_length_overlap',document.getElementById('pfam_plus_length_overlap').value);
        fd.append('pfam_uniref_version',document.getElementById('pfam_plus_uniref_version').value);
        fd.append('pfam_demux',document.getElementById('pfam_plus_demux').checked);
        //TODO: conditionally enable this
        //fd.append('program',document.getElementById('option_b_program').value);
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
function uploadProgressColorSsn(evt) {
    if (evt.lengthComputable) {
        var percentComplete = Math.round(evt.loaded * 100 / evt.total);
        document.getElementById('progressNumberColorSsn').innerHTML = "Uploading File: " + percentComplete.toString() + '%';
        var bar = document.getElementById('progress_bar_colorssn');
        bar.value = percentComplete;
    }
    else {
        document.getElementById('progressNumberColorSsn').innerHTML = 'unable to compute';
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
    document.getElementById("option_colorssn").disabled = true;
    document.getElementById('email').disabled = true;
    document.getElementById('submit').disabled = true;


}
function enableForm() {
    document.getElementById("option_a").disabled = false;
    document.getElementById("option_b").disabled = false;
    document.getElementById("option_c").disabled = false;
    document.getElementById("option_d").disabled = false;
    document.getElementById("option_colorssn").disabled = false;
    document.getElementById('email').disabled = false;
    document.getElementById('submit').disabled = false;


}
