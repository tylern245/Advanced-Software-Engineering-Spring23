function revealEdit(e) {
    const editCell = e.children[2];
    const edit = editCell.children[0];

    // console.log(edit);

    edit.style.display = "";
}

function hideEdit(e) {
    const editCell = e.children[2];
    const edit = editCell.children[0];

    // console.log(edit);

    edit.style.display = "none";
}

function editType(id) {
    window.location.href = "modify.php?modify=type&action=edit&id=" + id;
}

function addType() {
    window.location.href = "modify.php?modify=type&action=add";
}

function cancelType() {
    window.location.href = "modify.php?modify=type";
}

function editManu(id) {
    window.location.href = "modify.php?modify=manu&action=edit&id=" + id;
}

function addManu() {
    window.location.href = "modify.php?modify=manu&action=add";
}

function cancelManu() {
    window.location.href = "modify.php?modify=manu";
}