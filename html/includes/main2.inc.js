function expand(section)
{
    if (document.getElementById(section).style.display=='none')
    {
        document.getElementById(section).style.display='inline';
    }
    else
    {
        document.getElementById(section).style.display='none';
    }
}
function close_all()
{
    i = 1;
    while (document.getElementById('#' + i))
    {
        document.getElementById('#' + i).style.display='none';
        ++i;
    }
}
function show_all()
{
    i = 1;
    while (document.getElementById('#' + i))
    {
        document.getElementById('#' + i).style.display='inline';
        ++i;
    }
}

function clear_email() {
    var isFirst = true;
    $('input[type=text]').focus(function() {
            if(isFirst){
            $(this).val('');
            isFirst = false;
            }
            });

}

function uploadProgress(evt) {
    if (evt.lengthComputable) {
        var percentComplete = Math.round(evt.loaded * 100 / evt.total);
        document.getElementById('progressNumber').innerHTML = "Uploading File: " + percentComplete.toString() + '%';
        var bar = document.getElementById('progress_bar');
        bar.value = percentComplete;
    }
    else {
        document.getElementById('progressNumber').innerHTML = 'unable to compute';
    }
}

function disable_forms() {
    var option_selected;
    if (document.getElementById('option_selected_a').checked) {
        option_selected = document.getElementById('option_selected_a').value;
    }
    else if (document.getElementById('option_selected_b').checked) {
        option_selected = document.getElementById('option_selected_b').value;
    }
    else if (document.getElementById('option_selected_c').checked) {
        option_selected = document.getElementById('option_selected_c').value;
    }
    else if (document.getElementById('option_selected_d').checked) {
        option_selected = document.getElementById('option_selected_d').value;
    }
    else if (document.getElementById('option_selected_e').checked) {
        option_selected = document.getElementById('option_selected_e').value;
    }
    else if (document.getElementById('option_selected_colorssn').checked) {
        option_selected = document.getElementById('option_selected_colorssn').value;
    }

    if (option_selected == "A") {
        document.getElementById("option_a").disabled = false;
        document.getElementById("option_b").disabled = true;
        document.getElementById("option_c").disabled = true;
        document.getElementById("option_d").disabled = true;
        document.getElementById("option_e").disabled = true;
        document.getElementById("option_colorssn").disabled = true;
    } 
    else if (option_selected == "B") {
        document.getElementById("option_a").disabled = true;
        document.getElementById("option_b").disabled = false;
        document.getElementById("option_c").disabled = true;
        document.getElementById("option_d").disabled = true;
        document.getElementById("option_e").disabled = true;
        document.getElementById("option_colorssn").disabled = true;
    }
    else if (option_selected == "C") {
        document.getElementById("option_a").disabled = true;
        document.getElementById("option_b").disabled = true;
        document.getElementById("option_c").disabled = false;
        document.getElementById("option_d").disabled = true;
        document.getElementById("option_e").disabled = true;
        document.getElementById("option_colorssn").disabled = true;
    }
    else if (option_selected == "D") {
        document.getElementById("option_a").disabled = true;
        document.getElementById("option_b").disabled = true;
        document.getElementById("option_c").disabled = true;
        document.getElementById("option_d").disabled = false;
        document.getElementById("option_e").disabled = true;
        document.getElementById("option_colorssn").disabled = true;
    }
    else if (option_selected == "E") {
        document.getElementById("option_a").disabled = true;
        document.getElementById("option_b").disabled = true;
        document.getElementById("option_c").disabled = true;
        document.getElementById("option_d").disabled = true;
        document.getElementById("option_e").disabled = false;
        document.getElementById("option_colorssn").disabled = true;
    }
    else {
        document.getElementById("option_a").disabled = true;
        document.getElementById("option_b").disabled = true;
        document.getElementById("option_c").disabled = true;
        document.getElementById("option_d").disabled = true;
        document.getElementById("option_e").disabled = true;
        document.getElementById("option_colorssn").disabled = false;
    }
}

function toggleUniref(combo_id, uniref_checkbox) {
    if (uniref_checkbox.checked) {
        document.getElementById(combo_id).disabled = false;
    } else {
        document.getElementById(combo_id).disabled = true;
    }
}

function toggle_blast_advanced() {
    if ( $("#blast_advanced").is(":hidden")) {
        $("#blast_advanced").slideDown("slow");
    }
    else {
        $("#blast_advanced").slideUp("slow");
    }

}

function toggle_pfam_advanced() {
    if ( $("#pfam_advanced").is(":hidden")) {
        $("#pfam_advanced").slideDown("slow");
    }
    else {
        $("#pfam_advanced").slideUp();
    }

}

function toggle_pfam_plus_advanced() {
    if ( $("#pfam_plus_advanced").is(":hidden")) {
        $("#pfam_plus_advanced").slideDown("slow");
    }
    else {
        $("#pfam_plus_advanced").slideUp();
    }

}

function toggle_fasta_advanced() {
    if ( $("#fasta_advanced").is(":hidden")) {
        $("#fasta_advanced").slideDown("slow");
    }
    else {
        $("#fasta_advanced").slideUp();
    }

}

function toggle_accession_advanced() {
    if ( $("#accession_advanced").is(":hidden")) {
        $("#accession_advanced").slideDown("slow");
    }
    else {
        $("#accession_advanced").slideUp();
    }

}

function toggle_fasta_id_advanced() {
    if ( $("#fasta_id_advanced").is(":hidden")) {
        $("#fasta_id_advanced").slideDown("slow");
    }
    else {
        $("#fasta_id_advanced").slideUp();
    }

}

