<?php

include 'PHPMailer/PHPMailerAutoload.php';

$blast = new SendMail;

$blast->sendMailBlast();



class SendMail 
{
    public $from;
    public $fromName;
    public $to;
    public $toName;
    public $emailTable;
    
    public function sendMailBlast() {
        //require 'PHPMailerAutoload.php';

        $mail = new PHPMailer;

        //$mail->SMTPDebug = 1;
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        
        $mail->Username = 'youremail@gmail.com';                 // SMTP username
        $mail->Password = 'yourpassword';                           // SMTP password

        
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to
   
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "emailblast";
        
        
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
        
        $this->emailTable = "dummy_email";
        
        $sql = "SELECT * FROM " . $this->emailTable . " where sent_date = '0000-00-00 00:00:00'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $this->from = "george.lucas@gmail.com";
                $this->fromName = "George Lucas";
                $this->to = $row["email_address"];
                //$this->toName = $row["email_address"];
                
                $mail->setFrom($this->from, $this->fromName);
                $mail->addAddress($this->to, $this->toName);     // Add a recipient
                //$mail->addReplyTo($to, 'Information');
                //$mail->addCC('cc@example.com');
                //$mail->addBCC('bcc@example.com');
                
                $mail->addAttachment('C:\Files\WORD DOCUMENTS\somedocument.doc');         // Add attachments
                $mail->addAttachment('C:\Files\WORD DOCUMENTS\somedocument.doc');    // Optional name
                $mail->isHTML(true);                                  // Set email format to HTML
                
                $mail->Subject = 'This is the title';
                $mail->Body    = 'Hello<br/><br/> This is a test body';
                $mail->AltBody = 'Hello, This is a test body.';
                
                echo $row["email_address"] . "<br/>";
                if($mail->send()) {
                    $sql = "UPDATE " . $this->emailTable ." set sent_date = now() where id = " . $row['id'];
                    $conn->query($sql);
                    $mail->ClearAddresses();
                    $mail->ClearAttachments();
                } 
            }
        } 

        $conn->close();
    }
    
    
}


class PHPMailer_mine extends PHPMailer { public function get_mail_string() { return $this->MIMEHeader.$this->MIMEBody; }}
