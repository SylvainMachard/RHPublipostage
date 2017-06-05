<?php

require __DIR__ . '/vendor/autoload.php';

$config = parse_ini_file("config.ini", true);

$mail = new PHPMailer;

$tabMail = array();
$csvFile = fopen("data/CsvPublipostage.csv", "r");

do {
    $tabData = fgetcsv($csvFile, 1024, ';');
    if (intval($tabData[3]) > 0) {
        $tabMail[$tabData[3]] = array (
            'mail' => $tabData[15],
            'prenom' => $tabData[5],
            'nom' => $tabData[4]
        );
    }
} while ($tabData);
fclose($csvFile);

$dir = "data/pdf";

$configSmtp = $config['SMTP'];
$mail->isSMTP();
$mail->Host = 'ssl0.ovh.net';
$mail->SMTPAuth = true;
$mail->Port = 587;
$mail->Username = $configSmtp['Username'];
$mail->Password = $configSmtp['Password'];
$mail->SMTPSecure = 'none';

$mail->setFrom($configSmtp['Username'], 'Secretariat COP Rink-Hockey');
$mail->Subject = 'Inscription Rink-Hockey saison 2017-2018';

// Ouvre un dossier bien connu, et liste tous les fichiers
if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            if (strpos($file, "pdf") !== false) {
                $attachFile = __DIR__ . '/' . $dir . "/" . $file;
                $tabFileName = explode(" - ", $file);
                if (array_key_exists($tabFileName[0], $tabMail)) {
                    $infoMail = $tabMail[$tabFileName[0]];
                    echo "Licence ".$tabFileName[0]." envoyée à " . $infoMail['mail'];

                    // $mail->addAddress($infoMail['mail'], $infoMail['prenom'].' '.$infoMail['nom']);
                    $mail->addAddress('sylvain.machard@gmail.com', $infoMail['prenom'].' '.$infoMail['nom']);
                    $mail->addAttachment($attachFile);

                    $mail->isHTML(true);
                    $mail->Body    = 'Bonjour,<br/>'
                        .'<br/>'
                        .'La saison de rink-hockey se termine et il est temps de penser à l\'année prochaine.<br/>'
                        .'Vous trouverez ci-joint le dossier d\'inscription de '.$infoMail['prenom'].', '
                        .'merci de le compléter et de nous le renvoyer pour votre prochaine inscription.<br/>'
                        .'<br/>'
                        .'Karine, secrétaire du CO Pacé Rink-Hockey';

                    if(!$mail->send()) {
                        echo " -> KO !!\n";
                        echo $mail->ErrorInfo."\n";
                    } else {
                        echo " -> ok\n";
                    }

                    $mail->clearAddresses();
                    $mail->clearAttachments();

                }
            }
        }
        closedir($dh);
    }
}
