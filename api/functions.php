<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../api/vendor/autoload.php';

function job($connect, $data){
    $name = mysqli_real_escape_string($connect, $data['name']);
    $city = mysqli_real_escape_string($connect, $data['city']);
    $type = mysqli_real_escape_string($connect, $data['type']);
    $age = mysqli_real_escape_string($connect, $data['age']);
    $phone = mysqli_real_escape_string($connect, $data['phone']);
    $email = mysqli_real_escape_string($connect, $data['email']);
    $positions = json_decode($data['expirience'], true);
    $startFormattedDateMessage = date('d.m.Y', strtotime($startDate));
    $endFormattedDateMessage = date('d.m.Y', strtotime($endDate));
    
    if(isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK){
        $fileTmpPath = $_FILES['cv']['tmp_name'];
        $fileName = $_FILES['cv']['name'];
        $fileSize = $_FILES['cv']['size'];
        $fileType = $_FILES['cv']['type'];
        
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        $newFileName = uniqid('file_') . '.' . $fileExtension;
        $uploadFileDir = '../api/uploads/'; // Змінена шлях до завантаження
        $dest_path = $uploadFileDir . $newFileName;
        
        if(move_uploaded_file($fileTmpPath, $dest_path)) {
            
        } else {
            
        }
    }
    
    $query = "INSERT INTO job_apply (name, city, type, age, phone, email, company, position, startDate, endDate, comment, img) 
              VALUES ('$name', '$city', '$type', '$age', '$phone', '$email', '$company', '$position', '$startFormattedDate', '$endFormattedDate', '$comment', '$newFileName')";
    mysqli_query($connect, $query);
    
    $to = 'tomashehlysh@gmail.com';
    $subject = '=?UTF-8?B?' . base64_encode('Запит на роботу від ' . $name) . '?=';
    $message = '<p>Доброго дня,</p>';
    $message .= '
                <p>Як тебе звати: ' . $name . '</p>
                <p>Місто в якому ти зараз живеш : ' . $city . '</p>
                <p>В якому напрямку хочеш працювати: ' . $type . '</p>
                <p>Скільки тобі повних років: ' . $age . '</p>
                <p>Номер телефону : ' . $phone . '</p>';
    
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ehlysht@gmail.com';
        $mail->Password = 'lmkx zdkf aggd nyqc';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom($email, mb_encode_mimeheader($name, "UTF-8", "Q"));
        $mail->addAddress($to);
        $mail->addCC($email);

        if (isset($newFileName) && !empty($newFileName)) {
            $file_path = '../api/uploads/' . $newFileName;
            $mail->addAttachment($file_path, basename($file_path));
        }else{
            $message .= '<p>Досвід роботи:</p>';
            foreach ($positions as $position) {
                $positionMessage = '<p>Посада ' . mysqli_real_escape_string($connect, $position['id'] + 1) . ':</p>';
                $positionMessage .= '<div style="margin-left: 24px;">';
                $positionMessage .= '<p>Посада: ' . mysqli_real_escape_string($connect, $position['position']) . '</p>';
                $positionMessage .= '<p>Компанія: ' . mysqli_real_escape_string($connect, $position['company']) . '</p>';
                $positionMessage .= '<p>Початок роботи: ' . mysqli_real_escape_string($connect, $position['startDate']) . '</p>';
                $positionMessage .= '<p>Кінець роботи: ' . mysqli_real_escape_string($connect, $position['endDate']) . '</p>';
                $positionMessage .= '<p>Коментар (за бажанням): ' . mysqli_real_escape_string($connect, $position['comment']) . '</p>';
                $positionMessage .= '</div>';
        
                // Додаємо повідомлення про посаду до загального повідомлення про досвід роботи
                $message .= $positionMessage;
            }
        }
        
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        echo "Email sent successfully to $to";
    } catch (Exception $e) {
        echo "Email sending failed: {$mail->ErrorInfo}";
    }
}

?>
