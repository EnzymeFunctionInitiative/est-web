
function uploadFile() {
        var fd = new FormData();
	fd.append("fasta_file", document.getElementById('fasta_file').files[0]);
        var xhr = new XMLHttpRequest();
	xhr.upload.addEventListener("progress", uploadProgress, false);
	xhr.addEventListener("load", uploadComplete, false);
	xhr.addEventListener("error", uploadFailed, false);
	xhr.addEventListener("abort", uploadCanceled, false);
	fd.append('email',document.getElementById('email').value);
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

	fd.append('option_selected',option_selected);
	fd.append('MAX_FILE_SIZE',document.getElementById('MAX_FILE_SIZE').value);
	fd.append('families_input',document.getElementById('families_input').value);
	fd.append('families_input2',document.getElementById('families_input2').value);
	fd.append('blast_input',document.getElementById('blast_input').value);
	fd.append('submit',document.getElementById('submit').value);
	disableForm();
        xhr.open("POST", "create.php",true);
        xhr.send(fd);
	xhr.onreadystatechange  = function(){
		if (xhr.readyState == 4  ) {

			// Javascript function JSON.parse to parse JSON data
			var jsonObj = JSON.parse(xhr.responseText);

			// jsonObj variable now contains the data structure and can
			// be accessed as jsonObj.name and jsonObj.country.
			if (jsonObj.valid) {
//				window.location.replace("stepb.php");
			}
			if (jsonObj.message) {
				enableForm();
				document.getElementById("message").innerHTML =  jsonObj.message;
			}
			
		}
	}

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
                document.getElementById('email').disabled = true;
                document.getElementById('submit').disabled = true;

	
}
function enableForm() {
                document.getElementById("option_a").disabled = false;
                document.getElementById("option_b").disabled = false;
                document.getElementById("option_c").disabled = false;
                document.getElementById('email').disabled = false;
                document.getElementById('submit').disabled = false;


}
