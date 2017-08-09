<?php

require __DIR__ . '/vendor/autoload.php';

$config = parse_ini_file("config.ini", true);

$mail = new PHPMailer;

$tabMail = array();
$csvFile = fopen("data/CsvPublipostage.csv", "r");

do {
    $tabData = fgetcsv($csvFile, 0, ';');
    if (strlen($tabData[3]) > 0) {
        $tabMail[$tabData[3]] = array (
            'mail' => $tabData[16],
            'prenom' => $tabData[5],
            'nom' => $tabData[4]
        );
    }
} while ($tabData);
fclose($csvFile);

echo "-".count($tabMail)."-\n";

$dir = "data/pdf";

$attachRenseignement = __DIR__ . '/data/RENSEIGNEMENTS_2017_2018.pdf';
$attachNotice        = __DIR__ . '/data/notice_assurance_2017_2018.pdf';

$configSmtp = $config['SMTP'];
$mail->isSMTP();
$mail->Host = 'ssl0.ovh.net';
$mail->SMTPAuth = true;
$mail->Port = 587;
$mail->Username = $configSmtp['Username'];
$mail->Password = $configSmtp['Password'];
$mail->SMTPSecure = 'none';
$mail->SMTPKeepAlive = true;

$mail->setFrom($configSmtp['Username'], 'Secretariat COP Rink-Hockey');
$mail->Subject = 'Rink-Hockey/Roller - Saison 2017-2018 : dossier d\'inscription et informations tarifs et horaires';

// Ouvre un dossier bien connu, et liste tous les fichiers
if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            if (strpos($file, "pdf") !== false) {
                $attachFile = __DIR__ . '/' . $dir . "/" . $file;
                $attachFileDone = __DIR__ . '/' . $dir . "/done/" . $file;
                $tabFileName = explode(" - ", $file);
                if (array_key_exists($tabFileName[0], $tabMail)) {
                    $infoMail = $tabMail[$tabFileName[0]];
                    echo "Licence ".$tabFileName[0]." envoyée à " . $infoMail['mail']." ";

                    $mail->addAddress($infoMail['mail'], $infoMail['prenom'].' '.$infoMail['nom']);
                    // $mail->addAddress('karine_machard@yahoo.fr', $infoMail['prenom'].' '.$infoMail['nom']);
                    $mail->addAttachment($attachFile);
                    $mail->addAttachment($attachRenseignement);
                    $mail->addAttachment($attachNotice);

                    $mail->isHTML(true);
                    $mail->Body    = 'Bonjour,<br/>
<br/>
Comme pr&eacute;vu, voici votre dossier d\'inscription pour la saison 2017-2018. Celui-ci est pr&eacute;-rempli avec les informations connues pour la saison derni&egrave;re. Merci de le compl&eacute;ter (n\'oubliez pas de signer toutes les cases obligatoires) et de modifier en rouge si besoin les informations qui pourraient &ecirc;tre erron&eacute;es.<br/>
<br/>
Petit changement : cette ann&eacute;e les photos et certificats m&eacute;dicaux devront &ecirc;tre d&eacute;pos&eacute;s sur le site de la f&eacute;d&eacute;ration au moment de l\'inscription, aussi pour faciliter les d&eacute;clarations merci de me faire parvenir ces 2 &eacute;l&eacute;ments par mail sur l\'adresse secretariat@coppaceroller.fr ; sans ces &eacute;l&eacute;ments, l\'inscription ne pourra se faire.<br/>
Sinon, le dossier papier est &agrave; me faire parvenir
<ul>
  <li>soit &agrave; mon adresse (au nom de Karine MACHARD, 11 r&eacute;sidence du pont Amelin, 35740 PACE),</li>
  <li>soit au moment du Forum le 09 septembre matin,</li>
  <li>soit lors du tout 1er entrainement.</li>
</ul>
Il devra &ecirc;tre remis au plus tard au 30 septembre. Pass&eacute;e cette date, le joueur ne sera plus accept&eacute; tant que le dossier ne sera pas r&eacute;gularis&eacute;. De m&ecirc;me tout dossier incomplet se verra refus&eacute;.<br/>
<br/>
Vous trouverez &eacute;galement
<ul>
  <li>la notice d\'assurance (&agrave; conserver)</li>
  <li>la fiche de renseignement avec les tarifs et les horaires pour la nouvelle saison. (&agrave; conserver)</li>
</ul>
<br/>
Comme vous le constaterez, il y a bien un changement de cat&eacute;gorie pour la saison prochaine. Nous allons passer en U10, U12, U14, etc.
Du coup, ce sont exactement les m&ecirc;mes &eacute;quipes que cette ann&eacute;e : tout le monde redouble!<br/>
Les U11 passent U12, les U13 passent U14, les U15 passent U16, les U17 passent U18.<br/>
Les cr&eacute;neaux sont donc indiqu&eacute;s avec les nouvelles cat&eacute;gories.<br/>
<br/>
Les horaires indiqu&eacute;s sont sous r&eacute;serve d\'&eacute;ventuels changements au vu de discussions en cours avec la municipalit&eacute; (la mairie souhaite que l\'on lib&egrave;re un de nos cr&eacute;neaux, ce que nous ne souhaitons pas car nous avons besoin de tous nos cr&eacute;neaux. Mais il se peut que nous y soyons contraints)<br/>
<br/>
Je vous souhaite une bonne fin de vacances et vous dis &agrave; tr&egrave;s bient&ocirc;t<br/>
<br/> 
Karine, secr&eacute;taire du CO Pac&eacute; Rink-Hockey';
echo date('h:i:s')." mail->send()"; 
                    if(!$mail->send()) {
                        echo " -> KO !! ";
                        echo $mail->ErrorInfo."\n";
                    } else {
                        echo " -> ok ";
                    }
echo date('h:i:s')."\n";
sleep(60);
                    $mail->clearAddresses();
                    $mail->clearAttachments();
	        } else {
                    echo "Licence ".$tabFileName[0]." PAS de destinataire\n";
                }
            }
        }
        closedir($dh);
    }
}

$mail->SmtpClose();

