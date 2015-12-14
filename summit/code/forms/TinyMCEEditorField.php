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

    /**
     * @var int
     */
    private $word_count_limit = 0 ;

    /**
     * @var int
     */
    private $max_char_limit = 0;
    /**
     * @param int $word_count_limit
     * @return $this
     */
    public function setWordCount($word_count_limit){
        $this->word_count_limit = $word_count_limit;
        return $this;
    }

    /**
     * @param int $max_char_limit
     * @return $this
     */
    public function setMaxCharLimit($max_char_limit)
    {
        $this->max_char_limit = $max_char_limit;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasMaxCharLimit()
    {
        return $this->max_char_limit > 0;
    }

    public function FieldHolder($attributes = array ()) {
        Requirements::javascript('//tinymce.cachefly.net/4.3/tinymce.min.js');

        $script = '';
        $plugins = "'paste'";
        $extra_options = "";
        if($this->word_count_limit > 0 ){
            $plugins .=", 'wordcount'";
            $extra_options = ",  wordcount_limit : {$this->word_count_limit}";
        }
        else if($this->max_char_limit > 0)
        {

      $script .=<<<JS

      var max_chars   = {$this->max_char_limit}; //max characters
      var allowed_keys = [8, 13, 16, 17, 18, 20, 33, 34, 35,36, 37, 38, 39, 40, 46];

      function alarmChars(chars_without_html, container_id){
        if(chars_without_html > (max_chars - 10))
        {
            $('#chars_left_'+container_id).css('color','red');
        }
        else
        {
            $('#chars_left_'+container_id).css('color','gray');

        }
      }
JS;

            $extra_options .= <<<JS
 , setup : function(ed) {

    ed.on("KeyDown", function(ed, evt) {
        var chars_without_html = $.trim(tinyMCE.activeEditor.getBody().textContent).length;
        var container_id       = tinyMCE.activeEditor.id;
        var key = ed.keyCode;

        $('#chars_left_'+container_id).html(max_chars - chars_without_html);

        if(allowed_keys.indexOf(key) != -1){
            alarmChars(chars_without_html, container_id);
            return;
        }

        if (chars_without_html > ( max_chars - 1 )){
            ed.stopPropagation();
            ed.preventDefault();
            return false;
        }
         alarmChars(chars_without_html, container_id);
    });

    ed.on("Paste", function(ed, evt, o) {
          setTimeout(function(){
               var txt = tinyMCE.activeEditor.getBody().textContent;
               var chars_without_html = $.trim(txt).length;
               var container_id       = tinyMCE.activeEditor.id;
               if(txt.length > max_chars)
               {
                    txt = txt.substr(0, max_chars);
                    chars_without_html  = max_chars;
               }
               $('#chars_left_'+container_id).html(max_chars - chars_without_html);
               alarmChars(chars_without_html, container_id);
               tinyMCE.activeEditor.setContent(txt.trim());
           },200);
    });
  }
JS;
        }

        $script .= <<<SCRIPT
tinymce.init({
    menubar: false,
    content_css: '//www.openstack.org/summit/css/tinymce.css',
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

        if($this->max_char_limit > 0) {
            $script .= <<<JS

 var chars_without_html = $.trim($("#{$this->ID()}").text().replace(/(<([^>]+)>)/ig,"")).length;
 $('#chars_left_{$this->ID()}').html(max_chars - chars_without_html);
 alarmChars('{$this->ID()}', chars_without_html);
JS;
        }

        Requirements::customScript($script);

        $this->addExtraClass('tinymceeditor');

        return parent::FieldHolder($attributes);

    }
}