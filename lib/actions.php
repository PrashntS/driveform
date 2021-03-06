<?php
namespace DriveForm\Action;

class Model {
    public static function register() {

        $query = "INSERT INTO workshop_registrations (Workshop, Time_Stamp, Name, Email, Contact, College, Course, DD, Bank, DD_URI, Confirmed) VALUES(:Workshop, :Time_Stamp, :Name, :Email, :Contact, :College, :Course, :DD, :Bank, :DD_URI, :Confirmed)";

        $Valid = [];
        $Valid['Workshop'] = in_array($_POST['Workshop'], ["3D1", "3D2", "RB1", "RB2"]);
        $Valid['Registration_Filled'] = self::count_registrations($_POST['Workshop'])[2];
        $Valid['Name'] = \DriveForm\Util\Validate::Alphanumeric($_POST['Name'], 2);
        $Valid['Email'] = \DriveForm\Util\Validate::Email($_POST['Email']);
        $Valid['Email_Exists'] = self::check_email($_POST['Email']);
        $Valid['Contact'] = \DriveForm\Util\Validate::Alphanumeric($_POST['Contact'], 8, 12);
        $Valid['College'] = \DriveForm\Util\Validate::Alphanumeric($_POST['College'], 2);
        $Valid['Course'] = \DriveForm\Util\Validate::Alphanumeric($_POST['Course'], 2);
        $Valid['DD'] = \DriveForm\Util\Validate::Alphanumeric($_POST['DD'], 3);
        $Valid['Bank'] = \DriveForm\Util\Validate::Alphanumeric($_POST['Bank'], 3);

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

        if (!$upload[0]) {
            $return["error"] = true;
            $return["error_field"] = ["DD_Img"];
            return $return;
        }

        # Okay. Insert.
        
        $Handle = new \DriveForm\Database\Client();

        $insert = $Handle->query($query, [
            'Workshop' => $_POST['Workshop'],
            'Time_Stamp' => time(),
            'Name' => $_POST['Name'],
            'Email' => $_POST['Email'],
            'Contact' => $_POST['Contact'],
            'College' => $_POST['College'],
            'Course' => $_POST['Course'],
            'DD' => $_POST['DD'],
            'Bank' => $_POST['Bank'],
            'DD_URI' => $upload[1]]);

        if ((int)$insert->errorCode()[0] == 0) {
            $return["reg_id"] = $_POST['Workshop']."-".substr(md5($_POST['Name'].$_POST['Email']), 0, 5);
            $return["file_id"] = $upload[1];
            return $return;
        } else {
            $return["error"] = true;
            $return["error_field"] = ["Server_Error"];
            return $return;
        }
    }

    public static function count_registrations($id) {
        $query = "SELECT count(*) AS reg_count FROM workshop_registrations WHERE Workshop = :Workshop";
        $Handle = new \DriveForm\Database\Client();
        $result = $Handle->query($query, ['Workshop' => $id])->fetch(\PDO::FETCH_ASSOC);
        $count = (int)$result['reg_count'];
        $max_avail = ($id == "3D1" || $id == "3D2") ? 20 : (($id == "RB1" || $id == "RB2") ? 40 : 0);
        return [$count, $max_avail - $count, $count < $max_avail];
    }

    public static function check_email($email) {
        $query = "SELECT count(*) AS email_count FROM workshop_registrations WHERE Email = :Email";
        $Handle = new \DriveForm\Database\Client();
        $result = $Handle->query($query, ['Email' => $email])->fetch(\PDO::FETCH_ASSOC);
        $count = (int)$result['email_count'];
        return $count === 0;
    }

    public static function upload($id) {
        global $_CONFIG;
        $allowed = ["image/jpeg", "image/png", "image/gif", "image/tiff", "image/jpg"];
        if (isset($_FILES[$id]) &&
            $_FILES[$id]["error"] === 0 &&
            in_array($_FILES[$id]["type"], $allowed) &&
            $_FILES[$id]["size"] < 8000 * 1000) {
            # ALL OK!
            $file = file_get_contents($_FILES[$id]["tmp_name"]);
            $file_name = uniqid("upload_".time()."_").'.'.explode('/', $_FILES[$id]["type"])[1];
            $file_loc = realpath($_CONFIG->config_dir() . '/' . $_CONFIG->user_uploads) . '/' . $file_name; 
            file_put_contents($file_loc, $file);
            return [true, $file_name];
        } else return [false];
    }
}

class Email {
    public static function acknowledge($q) {

        $data = $_POST;
        $data['reg_id'] = $q['reg_id'];

        $message.= "<p>Dear {$data['Name']},<br>This email is to acknowledge that we've successfully received your registration.<br>";
        $message.= "The following is the information you've provided to us. In case any of it is wrong, please reply back to this Email within next 24 hours. Please note that furnishing incorrect information may lead to cancellation of your registration.</p>";
        $message.= "<p><strong>Name</strong>: &nbsp;{$data['Name']}<br>";
        $message.= "<strong>Email</strong>: &nbsp;{$data['Email']}<br>";
        $message.= "<strong>Contact Number</strong>: &nbsp;{$data['Contact']}<br>";
        $message.= "<strong>College/Affiliation</strong>: &nbsp;{$data['College']}<br>";
        $message.= "<strong>Course/Specialization</strong>: &nbsp;{$data['Course']}<br>";
        $message.= "<strong>Workshop Code</strong>: &nbsp;{$data['Workshop']}<br>";
        $message.= "<strong>DD Number</strong>: &nbsp;{$data['DD']}<br>";
        $message.= "<strong>Bank</strong>: &nbsp;{$data['Bank']}<br>";
        $message.= "Your registration ID is: <strong>#{$data['reg_id']}</strong>. Please mention this ID while contacting us.</p>";
        $message.= "<br>The Demand Draft that you've uploaded can be reviewed at: http://backstage.ducic.ac.in/uploaded_dd/".$q['file_id'];
        $message.= "<br>";
        $message.= "<p>Thank you for your interest in the Workshops offered by Cluster Innovation Centre, and CSEC. You may learn more about us at our website: https://ducic.ac.in.<br>";
        $message.= "Thank You.</p>";
        $message.= "<p>PS: This is an Autogenerated Email. If you think that the information present in this email is incorrect, please reply back to this email with a correction ticket.</p>";

        $subject = "Registration #{$data['reg_id']} | Acknowledgmement | Cluster Innovation Centre, University of Delhi";

        $Mailer = new \PHPMailer();

        $Mailer->isSMTP();
        $Mailer->SMTPDebug = 0;
        $Mailer->Host = "smtp.gmail.com";
        $Mailer->Port = "587";
        $Mailer->SMTPSecure = 'tls';
        $Mailer->SMTPAuth = true;
        $Mailer->Username = "autonomiregs@gmail.com";
        $Mailer->Password = "EMAIL";

        $Mailer->setFrom("autonomiregs@gmail.com", 'Autonomi Registrations');
        $Mailer->addReplyTo("autonomiregs@gmail.com", 'Autonomi Registrations');
        $Mailer->addBCC("autonomiregs@gmail.com", 'Autonomi Registrations');
        $Mailer->addAddress($_POST['Email'], $_POST['Name']);

        $Mailer->Subject = $subject;
        $Mailer->msgHTML($message);

        $Mailer->send();
    }
}