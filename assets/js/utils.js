document.addEventListener("DOMContentLoaded", function () {
    load();
});

function load() {
    loadActive();
    loadPresets();
}

function textAreaAdjust(textarea) {
    textarea.style.height = "1px";
    textarea.style.height = (3 + textarea.scrollHeight) + "px";
}

function adjustTextAreas() {
    let elements = document.getElementsByTagName('textarea');
    for (var i = 0; i < elements.length; i++) {
        textAreaAdjust(elements[i])
    }
}

function resetInput() {
    document.getElementById('web.0.content').value = "";
    document.getElementById('web.0.content2').value = ""
}