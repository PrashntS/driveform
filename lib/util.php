<?php
namespace DriveForm\Util;

class Setup {

    public static function Admin() {
        global $_STATE, $_CONFIG;

    }

    public static function Database() {
        global $_STATE;

        if (isset($_STATE->table_setup) && $_STATE->table_setup) {
            return false;
        } else {
            $Handle = new \DriveForm\Database\Client();
            $Handle->query(
                "CREATE TABLE workshop_registrations (
                    ID INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                    Time_Stamp varchar(128),
                    Workshop varchar(128),
                    Name varchar(128),
                    Email varchar(128),
                    Contact varchar(128),
                    College varchar(128),
                    Course varchar(128),
                    DD varchar(128),
                    Bank varchar(128),
                    Confirmed varchar(128),
                    DD_URI varchar(128));");
            $_STATE->table_setup = true;
            return true;
        }
    }
}

class Validate {
    function Alphanumeric($Candidate, $LengthMin = 0, $LengthMax = 0) {
        $PatternTail = "";
        
        if ($LengthMin >= 0 && $LengthMax > $LengthMin) $PatternTail = "{".$LengthMin.",".$LengthMax."}";
        if ($LengthMin == 0 && $LengthMax == 0) $PatternTail = "*"; 
        if ($LengthMin > 0 && $LengthMax == 0) $PatternTail = "{".$LengthMin.",}";
        
        $Pattern = "/^[A-Za-z0-9_~\.\- ]$PatternTail$/";

        return (bool)(preg_match($Pattern, $Candidate));
    }

    function Email($Candidate, $_Override = True) {
        /**
         * EMail RegEx pattern. Modified to span in multiple lines.
         * @copyright Michael Rushton 2009-10 http://squiloople.com/
         * @link    http://lxr.php.net/xref/PHP_5_4/ext/filter/logical_filters.c#501
         */

        $PatternEMail = 
            '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?))'.
            '{255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x'.
            '22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\'.
            'x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B'.
            '\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27'.
            '\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x0'.
            '8\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F])'.
            ')*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*'.
            '\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]'.
            '+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:'.
            '.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]\{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f'.
            '0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::['.
            'a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]'.
            '{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5'.
            '])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:'.
            '2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
        
        $Result = (bool)(preg_match($PatternEMail, $Candidate));

        if($_Override)
            return $Result ? $Candidate : False;
        else
            return $Result;
    }
}

