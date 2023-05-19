function showSubmitButton(e) {
    if (e.id == "serial-num-field") {
        var inputText = e.value;

        if (inputText != '')
            submitButton.style.display = "block";
        else
            submitButton.style.display = "none";
    }
    else if (e.id == "serial-all-box") {
        if (e.checked == true) {
            e.value = "true";
            serialNumInput.value = "";
            serialNumInput.disabled = true;
            submitButton.style.display = "block";
        }
        else if (e.checked == false) {
            e.value = "false";
            serialNumInput.disabled = false;
            submitButton.style.display = "none";
        }
    }

}

function showInsert(e) {
    if (e.value == "add-new") {
        if (e.id == "type-drop") {
            document.getElementById("insert-new-type").style.display = "block";
            document.getElementById("new-type-field").disabled = false;
            document.getElementById("new-type-status-drop").disabled = false;
        }
        else if (e.id == "manu-drop") {
            document.getElementById("insert-new-manu").style.display = "block";
            document.getElementById("new-manu-field").disabled = false;
            document.getElementById("new-manu-status-drop").disabled = false;
        }
    }
    else {
        if (e.id == "type-drop") {
            document.getElementById("insert-new-type").style.display = "none";
            document.getElementById("new-type-field").disabled = true;
            document.getElementById("new-type-status-drop").disabled = true;
        }
        else if (e.id == "manu-drop") {
            document.getElementById("insert-new-manu").style.display = "none";
            document.getElementById("new-manu-field").disabled = true;
            document.getElementById("new-manu-status-drop").disabled = true;
        }
    }
}


/*********** MAY OR MY NOT USE ***********/

function selectBoxSelected(e) {
    var selected = e.options[e.selectedIndex].value;
    if (selected === '')
        submitButton.style.display = "none";
    else 
        submitButton.style.display = "block";
    
}

function inputFieldFilled(e) {
    var inputText = e.value;

    if (inputText != '')
        submitButton.style.display = "block";
    else
        submitButton.style.display = "none";
}

function checkBoxChecked(e) {
    if (e.checked == true) {
        e.value = "true";
        serialNumInput.disabled = true;
        submitButton.style.display = "block";
    }
    else if (e.checked == false) {
        e.value = "false";
        serialNumInput.disabled = false;
        submitButton.style.display = "none";
    }
}


