<?php

/**
 * Creates a TinyMCE rich text editor field.
 *
 * This is just a placeholder for now.
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 */
class TinyMCEEditorField extends TextareaField
{

    public function FieldHolder($attributes = array ()) {
        Requirements::javascript('//tinymce.cachefly.net/4.1/tinymce.min.js');        
        Requirements::customScript('
tinymce.init({
    menubar: false,
    content_css: document.querySelector("base").href + "summit/css/tinymce.css",
    selector: "textarea.tinymceeditor",
    plugins: [
        "paste"
    ],
    paste_auto_cleanup_on_paste : true,
    paste_remove_styles: true,
    paste_remove_styles_if_webkit: true,
    paste_strip_class_attributes: true, 
    paste_retain_style_properties: "font-size, font-style, color",   
    toolbar: "bold italic | alignleft aligncenter | bullist numlist | paste"
});
');

        $this->addExtraClass('tinymceeditor');

        return parent::FieldHolder($attributes);

    }
}