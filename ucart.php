<?php
include_once('db.php');
function returnToHome(string|null $err = null): void
{

    if (empty($err)) {
        echo "<script>setTimeout(()=>{window.location.href='index.php?main=carrello'},1000)</script>";
    } else {
        echo "

<form name='fr' action='index.php?main=carrello' method='post'>
<input type='hidden' name='err' value='$err'>
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

    if ($dati['for'] == 'cart' && empty($err)) {
        if (array_key_exists('item', $dati)) {
            $item = $dati['item'];
            if (!empty($item)) {
                updateCart($db, $item, $session_id);
            } else {
                $err = "Carrello vuoto";
            }
        }else{
            $err = "Carrello vuoto";
        }
    }


} else {
    $err = "Errore, for non valido";
}
returnToHome($err);
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



