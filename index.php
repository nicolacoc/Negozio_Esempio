<?php
include_once('db.php');
include_once('main.php');
if (empty(session_id())) {
    session_start();
}
$db = connessione();
$err = null;
$prodotti=[];
$categoria=[];



if (is_string($db)) {
    $err = $db;
    $connError= $db;
    $db = null;
} elseif (empty($err)) {
    $prodotti = (array_key_exists('cid', $_GET))?in_shop($db, $_GET['cid']):in_shop($db);
    $categoria = in_category($db);
    if (!empty($_POST['err'])) {
    $err = $_POST['err'];}

}

if (!array_key_exists('main', $_GET)) {
    $main = home($prodotti);
} elseif ($_GET['main'] == 'carrello') {
    if (array_key_exists('data', $_POST)) {
        $data = $_POST['data'];

    }else{
        $data=[];
    }
    if(empty($connError)) {
        $transport = in_transport($db);
        $payment = in_payment($db);
        $Prod_in_cart = in_cart($db, session_id());
    }else{
        $transport = [];
        $payment = [];
        $Prod_in_cart = [];
    }
    $main = carrello($Prod_in_cart, $payment, $transport, $data);
} else {
    $main = home($prodotti);
}


$db = null;


?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Negozio</title>
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container text-center">
    <div class="row">
        <div class="col-12">
            <nav class="navbar navbar-expand-lg bg-body-tertiary">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                            <li class="nav-item me-xl-2" aria-current="page">
                                <a class="nav-link btn-link btn-group bg-secondary text-danger"
                                   href="<?php echo $_SERVER['PHP_SELF'] ?>">Shop</a>
                            </li>
                            <li class="nav-item" aria-current="page">
                                <a class="nav-link btn-link btn-group bg-secondary text-danger"
                                   href="<?php echo $_SERVER['PHP_SELF'] ?>?main=carrello">Carrello</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </div>

    <div class="row row-cols-1 my-2 he1">

        <div class="primo col-xl-2 border border-2 border-black bg-danger d-flex flex-column align-items-center">
            <div class="category d-block  p-0 mt-2 mb-0">
                <div class="p-0">
                    <h2 class="fw-bold">Categoria:</h2>
                </div>
                <ul class="list-group my-2 p-0">
                    <?php foreach ($categoria as $cat): ?>
                    <li class="list-group-item bg-transparent p-0">
                        <a href="<?php echo $_SERVER['PHP_SELF'].'?cid='.$cat['categoria_id']?>"
                           class="link-underline-opacity-0 link-underline-opacity-0-hover text-decoration-none"><?php echo $cat['Nome_categoria']?></a>
                    </li>
                   <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="col-xl-8 bg-secondary d-flex justify-content-center">
            <div class="d-block">
                <?php if (!empty($err)):?>
                <div class="row">
                    <div class="col bg-dark-subtle">
                        <span class="text-danger fw-bold fs-2"><?php echo $err?></span>
                    </div>
                </div>
                <?php endif;?>
                <?php echo $main ?>
            </div>
        </div>
        <div class="col-xl-2 bg-black offerta d-lg-flex justify-content-center flex-wrap d-sm-none">

        </div>
    </div>
    <footer class="row my-2 bg-dark-subtle">
        <div class="col-12">
        <span>  Questo è un esempio di footer!!</span>
        </div>
    </footer>
</div>

<script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
