/*
Loading functions
 */

function loadActive() {
    let request = new XMLHttpRequest();

    request.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("active").innerHTML = this.responseText;
            adjustTextAreas();
        }
    };
    request.open("GET", "xmlhttp/xmlhttpAushang.php?action=load", true);
    request.send();
}

function loadPresets() {
    let request = new XMLHttpRequest();

    request.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("presets").innerHTML = this.responseText;
            adjustTextAreas();
        }
    };
    request.open("GET", "xmlhttp/xmlhttpAushang.php?action=presets", true);
    request.send();
}

/*
Add functions

 */

function addElementFromWeb() {
    let request;
    let content;
    let content2;
    let colorSelector;
    let color;
    let json;

    content = document.getElementById('web.0.content').value;
    content2 = document.getElementById('web.0.content2').value;

    if (content == "" || content == null) {
        $(document.getElementById('web.0.content')).notify("Inhalt eingeben");
        return
    }

    colorSelector = document.getElementById("web.0.color");
    color = colorSelector.options[colorSelector.selectedIndex].value;
    if (color == "none") {
        $(colorSelector).notify("Farbe auswählen");
        return
    }

    request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            load();
            $.notify("Erstellt", "success");
        }
    };

    request.open("POST", "xmlhttp/xmlhttpAushang.php?action=create");
    json = JSON.stringify({"content1": content, "content2": content2, "color": color});
    console.log(json);
    request.setRequestHeader("Content-Type", "application/json");
    request.send(json);

    $.notify("Bitte warten", "info");
    resetInput();
}

function addElementFromPresets(id) {
    let content;
    let content2;
    let colorSelector;
    let color;
    let request;

    content = document.getElementById('textarea.' + id + '.content').value;
    content2 = "";

    if (document.getElementById('textarea.' + id + '.content2') != null && document.getElementById('textarea.' + id + '.content2') != "") {
        content2 = document.getElementById('textarea.' + id + '.content2').value;
    }
    colorSelector = document.getElementById("color." + id);
    color = colorSelector.options[colorSelector.selectedIndex].value;


    request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            load();
        }
    };

    request.open("POST", "xmlhttp/xmlhttpAushang.php?action=create");
    let json = JSON.stringify({"id": id, "content1": content, "content2": content2, "color": color});
    request.setRequestHeader("Content-Type", "application/json");
    request.send(json);
}

function addElementToPresets(id) {
    let content;
    let content2 = "";
    let colorSelector;
    let color;
    let request;
    let json;

    content = document.getElementById('textarea.' + id + '.content').value;
    if (document.getElementById('textarea.' + id + '.content2') != null && document.getElementById('textarea.' + id + '.content2') != "") {
        let content2 = document.getElementById('textarea.' + id + '.content2').value;
    }

    colorSelector = document.getElementById("color." + id);
    color = colorSelector.options[colorSelector.selectedIndex].value;

    request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            load();
        }
    };

    request.open("POST", "xmlhttp/xmlhttpAushang.php?action=createPreset");
    json = JSON.stringify({"id": id, "content1": content, "content2": content2, "color": color});
    request.setRequestHeader("Content-Type", "application/json");
    request.send(json);
}

/*
Manage Functions

 */


function editElementFromApi(id) {
    let row;

    row = document.getElementById('row.' + id);

    document.getElementById("textarea." + id + ".content").disabled = false;
    if (!!document.getElementById("textarea." + id + ".content2")) {
        document.getElementById("textarea." + id + ".content2").disabled = false;
    }

    document.getElementById('color.' + id).disabled = false;

    document.getElementById('edit.' + id).style.display = "none";
    document.getElementById('save.' + id).style.display = "block";

    $(row).notify("Bearbeitung aktiv", "success");

}

function updateToApi(id) {
    let content;
    let content2;
    let colorSelector;
    let request;
    let color;
    let json;

    content = document.getElementById('textarea.' + id + '.content').value;
    if (!!document.getElementById('textarea.' + id + '.content2')) {
        content2 = document.getElementById('textarea.' + id + '.content2').value
    }
    colorSelector = document.getElementById('color.' + id);
    color = colorSelector.options[colorSelector.selectedIndex].value;

    request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            load();
            $(row).notify("Gespeichert");
        }
    };

    request.open("POST", "xmlhttp/xmlhttpAushang.php?action=update");
    request.setRequestHeader("Content-Type", "application/json");
    if (content2 != null) {
        json = JSON.stringify({"id": id, "content1": content, "content2": content2, "color": color});

    } else {
        json = JSON.stringify({"id": id, "content1": content, "color": color});

    }
    request.send(json);
    console.log(json);
}

function moveElement(id, direction, type) {
    let request;

    request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            load();
        }
    };
    if (type == "1") {
        request.open("POST", "xmlhttp/xmlhttpAushang.php?action=move");
    } else {
        request.open("POST", "xmlhttp/xmlhttpAushang.php?action=movePreset");
    }

    request.setRequestHeader("Content-Type", "application/json");
    request.send(JSON.stringify({"id": id, "direction": direction}));
}

/*
remove Functions

 */

function removeElementFromApi(id) {
    let request;

    request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            load();
            $.notify("Gelöscht", "success");
        }
    };
    request.open("POST", "xmlhttp/xmlhttpAushang.php?action=delete");
    request.setRequestHeader("Content-Type", "application/json");
    request.send(JSON.stringify({"id": id}));
}


