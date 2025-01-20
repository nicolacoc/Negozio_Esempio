<?php
function connessione(): PDO|string
{
    $ConnDetails = new stdClass();
    $ConnDetails->host = "localhost";
    $ConnDetails->username = "root";
    $ConnDetails->password = "root";
    $ConnDetails->database = "negozio";
    $ConnDetails->commPort = "3306";
    $ConnDetails->options = [];

    try {

        $db = new PDO("mysql: host=$ConnDetails->host; dbname=$ConnDetails->database",
            $ConnDetails->username,
            $ConnDetails->password,
            $ConnDetails->options);

        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;

    } catch (PDOException $exception) {
        return "connection error: " . $exception->getMessage();
    }

}

function in_shop(PDO $db, string|null $category = null): array
{
    $query = "SELECT prodotti.*, Nome_categoria FROM prodotti
inner join categoria on categoria.categoria_id = prodotti.categoria_id";
    if (!empty($category)) {
        $query .= " where categoria.categoria_id =" . $category;
    };
    $options = [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY];
    $statement = $db->prepare($query, $options);
    $statement->execute();
    return $statement->fetchAll();
}

;

function in_category(PDO $db): array
{
    $query = "SELECT * FROM categoria";
    $options = [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY];
    $statement = $db->prepare($query, $options);
    $statement->execute();
    return $statement->fetchAll();
}

function is_to_cart_and_qty(PDO $db, int $id, string $session): array
{
    $query = "SELECT count(*) as count FROM carrello where id_prodotto = ? and session_id = ?";
    $options = [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY];
    $statement = $db->prepare($query, $options);
    $statement->execute([$id, $session]);
    $resultIf = $statement->fetchColumn();
    if ($resultIf > 0) {
        $if = true;
    } else {
        $if = false;
    }
    $query = "SELECT qta FROM carrello where id_prodotto = ? and session_id = ?";
    $statement2 = $db->prepare($query, $options);
    $statement2->execute([$id, $session]);
    $resultQta = $statement2->fetchColumn();

    return ['if' => $if, 'qta' => $resultQta];
}

function in_cart(PDO $db, string $session): array
{
    if (!empty($session)) {
        $query = "SELECT carrello.*, prodotti.Nome_prodotto, prodotti.prezzo FROM carrello 
                  inner join prodotti on carrello.id_prodotto = prodotti.id_prodotto
                  where carrello.session_id = ?";
        $options = [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY];
        $statement = $db->prepare($query, $options);
        $statement->execute([$session]);
        return $statement->fetchAll();
    } else {
        return [];
    }
}

function in_transport(PDO $db): array
{
    $query = "SELECT * FROM trasporto";
    $options = [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY];
    $statement = $db->prepare($query, $options);
    $statement->execute();
    return $statement->fetchAll();
}

function in_payment(PDO $db): array
{
    $query = "SELECT * FROM pagamento";
    $options = [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY];
    $statement = $db->prepare($query, $options);
    $statement->execute();
    return $statement->fetchAll();
}


function addToCart(PDO $db, array $Prodotti): void
{

    if (!empty($Prodotti)) {
        $sessionID = $Prodotti[0]['session_id'];
        if (!empty($sessionID)) {

            foreach ($Prodotti as $Prodotto) {
                $idProdotto = $Prodotto['id_prodotto'];
                $verify = is_to_cart_and_qty($db, $idProdotto, $sessionID);
                if ($verify['if']) {
                    $qta = $verify['qta'] + $Prodotto['qta'];
                    $query = "update carrello set qta=? where id_prodotto=? and session_id=?";
                } else {
                    $qta = $Prodotto['qta'];
                    $query = "INSERT INTO carrello (qta, id_prodotto, session_id) VALUES (?,?,?)";

                }

                $statement = $db->prepare($query);


                $statement->execute([$qta, $Prodotto['id_prodotto'], $sessionID]);

            };
            $statement = null;
        }
    }


}

function updateCart(PDO $db, array $Prodotti, string $session): void
{

    if (!empty($Prodotti) && !empty($session)) {
        foreach ($Prodotti as $Prodotto) {
            $idProdotto = $Prodotto['id_prodotto'];
            $qta = $Prodotto['Qta'];
            if ($qta <= 0) {
                deleteCart($db, $session, $idProdotto);
            } else {
                $query = "UPDATE carrello set qta=? where id_prodotto=? and session_id=?";
                $statement = $db->prepare($query);


                $statement->execute([$qta, $idProdotto, $session]);
            }


        };
        $statement = null;
    }
}

function deleteCart(PDO $db, string $session, int|null $id_prodotto=null): void{
    if (!empty($id_prodotto)) {
        $query = "DELETE FROM carrello where id_prodotto=? and session_id=?";
        $statement = $db->prepare($query);


        $statement->execute([$id_prodotto, $session]);
    }else{
        $query = "DELETE FROM carrello where session_id=?";
        $statement = $db->prepare($query);


        $statement->execute([$session]);
    }
}

function verify_client(PDO $db, array $Clients): array
{
    if (!empty($Clients)) {
        $query = "SELECT * FROM clienti where Nome=? and Cognome=? and Indirizzo=? and Cap=?";
        $options = [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY];
        $statement = $db->prepare($query, $options);
        $statement->execute([$Clients['nome'], $Clients['cognome'], $Clients['indirizzo'], $Clients['cap']]);
        return $statement->fetchAll();
    } else {
        return [];
    }
}

function Clients(PDO $db, array $Clients): void
{
    if (!empty($Clients)) {
        $verify = verify_client($db, $Clients);
        if (empty($verify)) {
            $verify = null;
            $query = "INSERT INTO clienti (Nome,Cognome,Indirizzo,Cap,citta,Telefono,Pagamento_id,Trasporto_id) VALUES (?,?,?,?,?,?,?,?)";
            $statement = $db->prepare($query);
            $statement->execute([$Clients['nome'], $Clients['cognome'], $Clients['indirizzo'], $Clients['cap'],$Clients['citta'], $Clients['telefono'], $Clients['pagamento_id'], $Clients['trasporto_id']]);
        }


    }


}

function in_trasp_pag(PDO $db, array $clienti): array{
    $query = "SELECT trasp.Tipo as trasporto, trasp.Costo as Costo_trasp, pag.Pagamento FROM trasporto as trasp, pagamento as pag where trasp.id=? and pag.id=?";
    $options = [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY];
    $statement = $db->prepare($query, $options);
    $statement->execute([$clienti['trasporto_id'],$clienti['pagamento_id']]);
    return $statement->fetchAll();
}


function add_Products_finish(PDO $db, array $Prodotti, string $id_client, string $session): void
{
    if (!empty($Prodotti) and !empty($session) and !empty($id_client)) {
        foreach ($Prodotti as $Prodotto) {
            $idProdotto = $Prodotto['id_prodotto'];
            $qta = $Prodotto['Qta'];
            $total_prodotto = $Prodotto['Total_article'];
            $query = "INSERT INTO prodotti_acquistati (Session_id, id_clienti, id_prodotto, qta, Total_product) VALUES (?,?,?,?,?)";
            $statement = $db->prepare($query);
            $statement->execute([$session, $id_client, $idProdotto, $qta, $total_prodotto]);
        }
    }
}








//function addToForm(PDO $db, object $data): void{
//$query= "INSERT INTO form1 (Nome, Cognome, Societa, Qualifica, Email, Telefono, Data_di_Nascita,Ima)
// VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
//
//$dati = $data;
//
//$statement = $db->prepare($query);
//$statement->execute([$dati->Nome, $dati->Cognome, $dati->Societa, $dati->Qualifica, $dati->Email, $dati->Telefono, $dati->Data_di_Nascita,  $dati->imageName]);
//$statement= null;
//}
