<?php
namespace DriveForm\Util;

class Setup {
    public static function Database() {
        $Handle = new \DriveForm\Database\Client();
        $Handle->query(
            "CREATE TABLE workshop_registrations (
                Id int AUTO_INCREMENT PRIMARY KEY,
                Workshop varchar(128),
                Name varchar(128),
                Email varchar(128),
                Contact varchar(128),
                College varchar(128),
                Course varchar(128),
                Year varchar(128),
                DD varchar(128),
                Bank varchar(128),
                DD_URI varchar(128));");
    }
}