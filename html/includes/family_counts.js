

function getFamilyCountsRaw(familyInputId, countOutputId, handler) {
    var family = document.getElementById(familyInputId).value;

    if (family.length >= 7) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                handler(this.responseText, countOutputId);
            }
        };
        family_query = family.replace(/\n/g, " ").replace(/\r/g, " ");
        xmlhttp.open("GET", "get_family_counts.php?families=" + family_query, true);
        xmlhttp.send();
    }
}

function getFamilyCountsTableHandler(responseText, countOutputId) {
    var sumCounts = 0;
    var counts = responseText.split(",");
    var table = document.getElementById(countOutputId);
    var newBody = document.createElement('tbody');
    for (var i = 0; i < counts.length; i++) {
        var data = counts[i].split("=");

        var row = newBody.insertRow(-1);
        var familyCell = row.insertCell(0);
        familyCell.innerHTML = data[0];

        var countVal = data[1];
        var countCell = row.insertCell(1);
        countCell.innerHTML = commaFormatted(countVal);
        countCell.style.textAlign = "right";
        sumCounts += parseInt(countVal);
    }

    var row = newBody.insertRow(-1);
    var total1 = row.insertCell(0);
    total1.innerHTML = "Total:";
    total1.style.textAlign = "right";

    var total2 = row.insertCell(1);
    total2.innerHTML = commaFormatted(sumCounts.toString());
    total2.style.textAlign = "right";

    table.parentNode.replaceChild(newBody, table);
    newBody.id = countOutputId;

    return sumCounts;
}

function commaFormatted(num) {

    if (num.length <= 3)
        return num;

    var formatted = "";

    while (num.length > 3) {
        var part = num.substring(num.length - 3, num.length);
        formatted = part + "," + formatted;
        num = num.substring(0, num.length - 3);
    }
    
    if (num.length > 0)
        formatted = num + "," + formatted;
    formatted = formatted.substring(0, formatted.length - 1);

    return formatted;
}

function getFamilyCounts(familyInputId, countOutputId) {
    getFamilyCountsRaw(familyInputId, countOutputId, getFamilyCountsTableHandler);
}

function checkFamilyInput(familyInputId, containerOutputId, countOutputId, warningId, warningThreshold) {
    var input = document.getElementById(familyInputId).value;
    var container = document.getElementById(containerOutputId);
    var warning = document.getElementById(warningId);

    if (input.length < 7) {
        warning.style.color = "black";
        container.style.display = "none";
        return;
    }

    var handleResponse = function(responseText, countOutputId) {
        var sumCounts = getFamilyCountsTableHandler(responseText, countOutputId);
        if (sumCounts > warningThreshold)
            warning.style.color = "red";
        else
            warning.style.color = "black";
        container.style.display = "block";
    };

    getFamilyCountsRaw(familyInputId, countOutputId, handleResponse);

    /*
    var output = document.getElementById(countOutputId);
    var famType = input.substring(0, 2).toLowerCase();
    
    outputSize = function(responseText, countOutputId) {
        var parts = responseText.split("=");
        if (parts.length == 2)
        {
            output.innerHTML = parts[0] + " size: " + parts[1];
            if (parseInt(parts[1]) > warningThreshold)
                document.getElementById(warningId).style.color = "red";
            else
                document.getElementById(warningId).style.color = "black";
        }
        else
        {
            output.innerHTML = "";
            document.getElementById(warningId).style.color = "black";
        }
    };

    if ((famType == "pf" && input.length == 7) ||
        (famType == "ip" && input.length == 9))
        getFamilyCountsRaw(familyInputId, countOutputId, outputSize);
    else
    {
        output.innerHTML = "";
        document.getElementById(warningId).style.color = "black";
    }
    */
}

