<?php
namespace DriveForm\Action;

class Model {
    public static function register() {

        $query = "INSERT INTO workshop_registrations SET(Workshop, Name, Email, Contact, College, Course, Year, DD, Bank, DD_URI) VALUES(:Workshop, :Name, :Email, :Contact, :College, :Course, :Year, :DD, :Bank, :DD_URI)";

        # TODO: Check if slot available.

        # Validate.
        $Valid = [];
        $Valid['Workshop'] = in_array($_POST['Workshop'], ["3D1", "3D2", "RB1", "RB2"]);
        $Valid['Name'] = \DriveForm\Util\Validate::Alphanumeric($_POST['Name'], 2);
        $Valid['Email'] = \DriveForm\Util\Validate::Email($_POST['Email']);
        $Valid['Contact'] = \DriveForm\Util\Validate::Alphanumeric($_POST['Contact'], 8, 12);
        $Valid['College'] = \DriveForm\Util\Validate::Alphanumeric($_POST['College'], 2);
        $Valid['Course'] = \DriveForm\Util\Validate::Alphanumeric($_POST['Course'], 2);
        $Valid['Year'] = \DriveForm\Util\Validate::Alphanumeric($_POST['Year'], 2);
        $Valid['DD'] = \DriveForm\Util\Validate::Alphanumeric($_POST['DD'], 3);
        $Valid['Bank'] = \DriveForm\Util\Validate::Alphanumeric($_POST['Bank'], 3);

        # Upload the file.
        
        $return = ["error" => false, "error_field" => []];

        foreach ($Valid as $field => $value) {
            if (!$value) {
                $return["error"] = true;
                array_push($return["error_field"], $field);
            }
        }

        if ($return["error"]) {
            return $return;
        }

        $upload = self::upload("DD_Img");

        if ($upload[0]) {
            $return["error"] = true;
            $return["error_field"] = ["DD_Img"];
        }

        # Okay. Insert.
        
        $Handle = new \DriveForm\Database\Client();

        $success = $Handle->query($query, array(
            'Workshop' => $_POST['Workshop'],
            'Name' => $_POST['Name'],
            'Email' => $_POST['Email'],
            'Contact' => $_POST['Contact'],
            'College' => $_POST['College'],
            'Course' => $_POST['Course'],
            'Year' => $_POST['Year'],
            'DD' => $_POST['DD'],
            'Bank' => $_POST['Bank'],
            'DD_URI' => $upload[0]))->errorCode() === 0;

        if ($success) {
            return $return;
        } else {
            $return["error"] = true;
            $return["error_field"] = ["Server Error"];
        }
    }

    public static function upload($id) {
        global $_CONFIG;
        # See if ID exists. If yes, check if it is a valid img. If it is, okay.
        $allowed = ["image/jpeg", "image/png", "image/gif", "image/tiff"];
        if (isset($_FILES[$id]) &&
            $_FILES[$id]["error"] === 0 &&
            in_array($_FILES[$id]["type"], $allowed) &&
            $_FILES[$id]["size"] < 8000 * 1000) {
            # ALL OK!
            $file = file_get_contents($_FILES[$id]["tmp_name"]);
            $file_name = uniqid("upload_".time()."_").'.'.explode('/', $_FILES[$id]["type"])[1];
            $file_loc = realpath($_CONFIG->config_dir() . '/' . $_CONFIG->user_uploads) . '/' . $file_name; 
            echo $file_loc;
            file_put_contents($file_loc, $file);
            return [false, $file_name];
        } else return [true];
    }
}
