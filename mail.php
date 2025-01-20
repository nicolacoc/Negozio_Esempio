<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function email (array $Client, array $products, array $trasp_pag): string|null{
    $mail_products="";
    $total_mail=0;
    foreach ($products as $product){
        $Total_article= number_format($product['Total_article'],2);
        $price= number_format($product['prezzo'],2);
        $mail_products.="   <tr>
                    <td>{$product['id_prodotto']}</td>
                    <td>{$product['nome_prodotto']}</td>
                    <td>{$product['Qta']}</td>
                    <td>{$price} &#8364;</td>
                    <td>{$Total_article} &#8364;</td>
                </tr>";
        $total_mail+= $product['Total_article'];

    }


    $Tipo_pag=$trasp_pag[0]['Pagamento'];
    $trasporto=$trasp_pag[0]['trasporto'];
    $Costo_trasp=number_format($trasp_pag[0]['Costo_trasp'],2);
    $total_mail+= $trasp_pag[0]['Costo_trasp'];

    $total_view_mail=number_format($total_mail,2);

    $bodyHtml="<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <title>Title</title>
    <style>
        .Product td{
            border: 1px solid black;
            text-align: center;
        }
    </style>
</head>
<body>
<table style='width:100%; border: 1px solid black;'>
  <tr style='height:50px'>
      <td style='width:50%'>
          <table style='width:100%; border: 1px solid black;'>
              <tr>
                  <td>{$Client['nome']} {$Client['cognome']}</td>
              </tr>
              <tr>
                  <td>{$Client['indirizzo']}</td>
              </tr>
              <tr>
                  <td>{$Client['citta']}</td>
              </tr>
              <tr>
                  <td>{$Client['cap']}</td>
              </tr>
              <tr>
                  <td>{$Client['telefono']}</td>
              </tr>
          </table>
      </td>

  </tr>
    <tr style='height:50px'>
        <td style='width:50%'>
            <table style='width:100%; border: 1px solid black;' class='Product'>
                <tr>
                    <th>Codice</th>
                    <th>Nome Prodotto</th>
                    <th>Qta</th>
                    <th>Prezzo</th>
                    <th>Totale Prodotto</th>
                </tr>
                {$mail_products}
            </table>
        </td>
    </tr>
    <tr>
        <td>Pagamento: $Tipo_pag </td>
    </tr>
    <tr>
        <td>Spedizione: $trasporto (+ {$Costo_trasp}) </td>
    </tr>
    <tr>
        <td>Totale Carrello: $total_view_mail &#8364;</td>
    </tr>
</table>

</body>
</html>";


//Load Composer's autoloader
    require 'vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.sendgrid.net';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'apikey';                     //SMTP username
        $mail->Password   = '';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('niki@motoimco.it', 'nicola');
        $mail->addAddress('niki@motoimco.it', 'Joe User');     //Add a recipient
        $mail->addReplyTo('niki@motoimco.it', 'nicola');


        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Ordine su sito';
        $mail->Body    = $bodyHtml;
        $mail->AltBody = 'questo è il tuo ordine';

        $mail->send();
        echo 'Message has been sent';

    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    } finally {
        return null;
    }
}

