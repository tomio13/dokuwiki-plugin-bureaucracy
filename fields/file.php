<?php

/**
 * File upload field
 */
class syntax_plugin_bureaucracy_field_file extends syntax_plugin_bureaucracy_field {

    /**
     * Arguments:
     *  - cmd
     *  - label
     *  - ^ (optional)
     *
     * @param array $args The tokenized definition, only split at spaces
     */
    function __construct($args) {
        parent::__construct($args);

        $attr = array();
        if(!isset($this->opt['optional'])) {
            $attr['required'] = 'required';
        }

        $this->tpl = form_makeFileField('@@NAME@@', '@@DISPLAY@@', '@@ID@@', '@@CLASS@@', $attr);
    }

    /**
     * Handle a post to the field
     *
     * Accepts and validates a posted value.
     *
     * @param array $value The passed value or array or null if none given
     * @param syntax_plugin_bureaucracy_field[] $fields (reference) form fields (POST handled upto $this field)
     * @param int    $index  index number of field in form
     * @param int    $formid unique identifier of the form which contains this field
     * @return bool Whether the passed filename is valid
     */
    public function handle_post($value, &$fields, $index, $formid) {
        $this->opt['file'] = $value;

        return parent::handle_post($value['name'], $fields, $index, $formid);
    }

    /**
     * @throws Exception max size, required or upload error
     */
    protected function _validate() {
        global $lang;
        parent::_validate();
        
        $file = $this->getParam('file');
        if($file['error'] == 1 || $file['error'] == 2) {
            throw new Exception(sprintf($lang['uploadsize'],filesize_h(php_to_byte(ini_get('upload_max_filesize')))));
        } else if($file['error'] == 4) {
            if(!isset($this->opt['optional'])) {
                throw new Exception(sprintf($this->getLang('e_required'),hsc($this->opt['label'])));
            }
        } else if( $file['error'] || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception(hsc($this->opt['label']) .' '. $lang['uploadfail'] . ' (' .$file['error'] . ')' );
        }
    }
}
