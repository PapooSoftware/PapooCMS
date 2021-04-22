// Tiny MCE für Bilder
(function () {
    tinyMCE.init({
        mode: "specific_textareas",
        language: "de",
        editor_selector: "feld-bild",
        content_css: '../plugins/fixemodule/css/tiny.css',
        theme: "advanced",
        plugins: "advimage,advlink",
        theme_advanced_buttons1: "image",
        theme_advanced_buttons2: "",
        theme_advanced_buttons3: "",
        convert_urls: false,
        valid_elements: "img[src|alt|title|width|height]",
        external_image_list_url: "./example_image_list.php?tinymce_lang_id={$tinymce_lang_id}",
        external_link_list_url: "./example_link_list.php?tinymce_lang_id={$tinymce_lang_id}",
        file_browser_callback: 'myFileBrowser',
        force_br_newlines: false,
        force_p_newlines: false,
        forced_root_block: ''
    });
})();

// Tiny MCE für HTML
(function () {
    tinyMCE.init({
        mode: "specific_textareas",
        language: "de",
        editor_selector: "feld-html",
        theme: "advanced",
        content_css: '../plugins/fixemodule/css/tiny.css',
        plugins: "advimage,advlink,codemagic",
        theme_advanced_buttons1: "formatselect,bold,italic,bullist,numlist,link,unlink,image,codemagic",
        //theme_advanced_buttons2: "",
        //theme_advanced_buttons3: "",
        convert_urls: false,
        //valid_elements: "p,img[src|alt|title|width|height],a[href|title|target],span[style],strong,em,-h1,-h2,-h3,-h4,-h5,-h6,-ol,-ul-li",
        external_image_list_url: "./example_image_list.php?tinymce_lang_id={$tinymce_lang_id}",
        external_link_list_url: "./example_link_list.php?tinymce_lang_id={$tinymce_lang_id}",
        file_browser_callback: 'myFileBrowser',
        force_br_newlines: false,
        force_p_newlines: false,
        forced_root_block: ''
    });
})();

// SlideToggle für die Variablen
(function () {
    $(".click").click(function () {
        $(this).siblings("ul.show").slideToggle();
    });
})();

(function () {
    var editor = CodeMirror.fromTextArea(document.getElementById("htmltextarea"), {
        lineNumbers: true,
        mode: "text/html",
        matchBrackets: true
    });
})();
