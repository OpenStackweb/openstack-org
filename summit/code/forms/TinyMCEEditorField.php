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

    private $word_count_limit = 0 ;

    /**
     * @param int $word_count_limit
     */
    public function setWordCount($word_count_limit){
        $this->word_count_limit = $word_count_limit;
        return $this;
    }

    public function FieldHolder($attributes = array ()) {
        Requirements::javascript('//tinymce.cachefly.net/4.1/tinymce.min.js');

        $plugins = "'paste'";
        $extra_options = "";
        if($this->word_count_limit > 0 ){
            $plugins .=", 'wordcount'";
            $extra_options = ",  wordcount_limit : {$this->word_count_limit}";
        }

        $script = <<<SCRIPT
tinymce.init({
    menubar: false,
    content_css: document.querySelector('base').href + 'summit/css/tinymce.css',
    selector: 'textarea.tinymceeditor',
    plugins: [
        {$plugins}
    ],
    paste_auto_cleanup_on_paste : true,
    paste_remove_styles: true,
    paste_remove_styles_if_webkit: true,
    paste_strip_class_attributes: true,
    paste_retain_style_properties: 'font-size, font-style, color',
    toolbar: 'bold italic | alignleft aligncenter | bullist numlist | paste',
    statusbar : false
    {$extra_options}
});
SCRIPT;

        Requirements::customScript($script);

        $this->addExtraClass('tinymceeditor');

        return parent::FieldHolder($attributes);

    }
}