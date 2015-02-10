<?php
namespace DriveForm\Form;

class Setup {
    public static function init() {
        global $_STATE;
        if ($_STATE->form_setup) {
            return true;
        }

        
    }
}