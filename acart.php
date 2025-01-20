<?php
include_once('db.php');
include_once('mail.php');


function returnToHome(string|null $err = null, string|null $for = 'product', array|null $dati= null): void
{
    if ($for == 'product') {
        $to = 'index.php';
        $data="";
    } elseif ($for == 'cart') {
        $to = 'index.php?main=carrello';
        $data = "<input type='hidden' name='data[nome]' value='{$dati['nome']}'>";
        $data .= "<input type='hidden' name='data[cognome]' value='{$dati['cognome']}'>";
        $data .= "<input type='hidden' name='data[indirizzo]' value='{$dati['indirizzo']}'>";
        $data .= "<input type='hidden' name='data[cap]' value='{$dati['cap']}'>";
        $data .= "<input type='hidden' name='data[citta]' value='{$dati['citta']}'>";
        $data .= "<input type='hidden' name='data[telefono]' value='{$dati['telefono']}'>";

        var_dump($data);

    } else {
        $to = 'index.php';
        $data="";
    }

    if (empty($err)) {
        echo "<script>setTimeout(()=>{window.location.href='$to'},1000)</script>";
    }
    else {
        echo "

<form name='fr' action='$to' method='post'>
<input type='hidden' name='err' value='$err'>
{$data}
</form>
<script type='text/javascript'>
     document.fr.submit();
</script>
";
    }


}

$dati = $_POST;
$err = null;
if (array_key_exists('for', $dati)) {
    $for = $dati['for'];
    $db = connessione();
    if (array_key_exists('session_id', $dati)) {
        $session_id = $dati['session_id'];
    } else {
        $session_id = null;
    }
    if (empty($session_id)) {
        $err = "Errore, session id non valido";
    }
    if (is_string($db)) {
        header("Location:" . "index.php");
    }

//---------------------------------------------------------------------------------

    if ($for == 'product') {
        $ArrayData = [$dati];
        if (array_key_exists('qta', $dati)) {
            $qta = $dati['qta'];
        } else {
            $qta = 0;
        }

        if ($qta <= 0) {
            $err = "Errore, Quantità minima prodotto insufficiente";
        }

        if (empty($err)) {
            addToCart($db, $ArrayData);
        }

    } elseif ($for == 'cart') {
        var_dump($dati);
        $nome = $dati['Nome'];
        $cognome = $dati['Cognome'];
        $indirizzo = $dati['Indirizzo'];
        $cap = $dati['CAP'];
        $citta = $dati['citta'];
        $tel = $dati['tel'];
        $trasporto_id = $dati['Trasporto_a_Mezzo_id'];
        $pagamento_id = $dati['Metodo_di_Pagamento_id'];
        $array_clienti = ['nome' => $nome, 'cognome' => $cognome, 'indirizzo' => $indirizzo, 'cap' => $cap,'citta' => $citta, 'telefono' => $tel, 'trasporto_id' => $trasporto_id, 'pagamento_id' => $pagamento_id];

        if (empty($nome) or empty($cognome) or empty($indirizzo) or empty($cap) or empty($tel) or empty($citta)) {
            $err = 'Manca un campo obbligatorio';
        } else {

            if (!is_numeric($cap)) {
                $err = 'Cap non valido';
            }
        }

        if (empty($err)) {

            if (array_key_exists('item', $dati)) {
                $item = $dati['item'];
                if (!empty($item)) {
                    try {
                        Clients($db, $array_clienti);

                    } finally {
                        $client = verify_client($db, $array_clienti);
                        if (!empty($client)) {
                            add_Products_finish($db, $item, $client[0]['id_clienti'], $session_id);
                            $trasp_pag = in_trasp_pag($db, $array_clienti);
                            deleteCart($db, $session_id);
                            $email = email($array_clienti, $item, $trasp_pag);
                        }
                        if (!empty($email)) {
                            $err=$email;
                        }

                    }






                } else {
                    $err = "Carrello vuoto";
                }
            } else {
                $err = "Carrello vuoto";
            }
        }


    }
} else {
    $err = "Errore, for non valido";
    $for = 'product';
}

if (!empty($array_clienti)) {
    returnToHome($err, $for, $array_clienti);
}else{
    returnToHome($err, $for);
}

?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Negozio</title>
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <?php

    if (!empty($err)) {
        echo "<h1 class='text-center text-danger'>$err</h1>";
    }


    ?>
</div>
</body>
</html>




