

function getFamilyCounts(familyInputId, countOutputId) {
    var family = document.getElementById(familyInputId).value;

    if (family.length > 0) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {

                var counts = this.responseText.split(",");
                var table = document.getElementById(countOutputId);
                var newBody = document.createElement('tbody');
                for (var i = 0; i < counts.length; i++) {
                    var data = counts[i].split("=");

                    var row = newBody.insertRow(-1);
                    var familyCell = row.insertCell(0);
                    familyCell.innerHTML = data[0];
                    var countCell = row.insertCell(1);
                    countCell.innerHTML = data[1];
                }
                table.parentNode.replaceChild(newBody, table);
                newBody.id = countOutputId;
            }
        };
        family_query = family.replace(/\n/g, " ").replace(/\r/g, " ");
        xmlhttp.open("GET", "get_family_counts.php?families=" + family_query, true);
        xmlhttp.send();
    }
}


