function myFileBrowser (field_name, url, type, win) {
    var filebrowser = "tinymce4_filebrowser.php";
    filebrowser += (filebrowser.indexOf("?") < 0) ? "?type=" + type : "&type=" + type;
    tinymce.activeEditor.windowManager.open({
        title: "File Browser",
        width: 520,
        height: 400,
        url: filebrowser
    }, {
        window: win,
        input: field_name
    });
    return false;
  }
