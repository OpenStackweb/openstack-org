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
     * @var bool
     */
    private $required;

    /**
     * @param boo $required
     * @return $this
     */
    public function setRequired($required)
    {
        $this->required = $required;
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
        $extra_options = ",setup : function(ed) {";
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



JS;
        }

        if($this->required){

            $extra_options.= <<<JS

                ed.on("Change", function(ed){
                    tinyMCE.triggerSave();
                    console.log('change');
                });
JS;
        }

        $extra_options .= ' }';
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
    statusbar : false,
    valid_elements : "@[id|class|style|title"
        + "a[name|href|target|title|class],strong/b,em/i,strike,u,"
        + "#p,-ol[type|compact],-ul[type|compact],-li,br,"
        + "img[src|border|alt=|title|width|height|align],"
        + "-blockquote,-table[border=0|cellspacing|cellpadding|width|frame|rules|"
        + "height|align|summary|bgcolor|background|bordercolor],-tr[rowspan|width|"
        + "height|align|valign|bgcolor|background|bordercolor],tbody,thead,tfoot,"
        + "#td[colspan|rowspan|width|height|align|valign|bgcolor|background|bordercolor"
        + "|scope],#th[colspan|rowspan|width|height|align|valign|scope],caption,"
        + "-span,-code,-pre,-h1,-h2,-h3,-h4,-h5,-h6,hr[size|noshade],-font[face"
        + "|size|color],dd,dl,dt,cite,abbr,acronym,del[datetime|cite],ins[datetime|cite],"
        + "col[align|char|charoff|span|valign|width],colgroup[align|char|charoff|span|"
        + "valign|width],fieldset,label[for],legend,q[cite],small,"
        + "textarea[cols|rows|disabled|name|readonly],big"
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

        if($this->required)
        {
            $form_id = $this->getForm()->FormName();
            $script .= <<<JS
                $(document).ready(function(){

                    var form = $('#{$form_id}');

                    if(form.length > 0)
                    {

                        $( "#{$this->ID()}" ).rules( "add", {
                          required: true,
                          messages: {
                            required: '{$this->Name} field is required.'
                          }
                        });


                        form.submit(function(e){
                             tinyMCE.triggerSave();
                             var is_valid = form.valid();
                             if(!is_valid)
                             {
                                return false;
                             }
                             return true;
                        });
                    }
                });
JS;

        }

        Requirements::customScript($script);

        $this->addExtraClass('tinymceeditor');

        return parent::FieldHolder($attributes);

    }
}