<?php
include_once('db.php');
function home(array $prodotti):string
{
    $prod_string = "";
    $session = session_id();
    foreach ($prodotti as $prodotto) {
        $qta = 1;
        $prezzo= number_format($prodotto['prezzo'],2);
        $prod_string .= "<div class='card p-2 mt-1 me-lg-1'>
                        <div class='mx-auto image'>
                            <img src='img/{$prodotto['image']}' class='card-img-top' alt='arancia'>
                        </div>
                        <div class='card-body'>
                        <form action='acart.php' method='post'>
                        <input type='hidden' name='for' value='product'>
                            <h5 class='card-title border border-2 border-black'>{$prodotto['Nome_prodotto']}</h5>
                            <p class='card-text'>{$prodotto['descrizione']}</p>
                            <div class='mb-2'>
                            <span class='card-text text-center'><span class='text-danger'>Prezzo:</span><span class='fw-bold'>&nbsp;$prezzo €</span></span>
                            </div>
                          <div class='input-group'>
                            <span class='input-group-text' id='qta'>Q.tà:</span>
                            <input type='number' name='qta' min='0' value='$qta' class='form-control' placeholder='Qta' aria-label='qta'
                                   aria-describedby='qta'>
                        </div>
                          <input type='hidden' name='id_prodotto' value='{$prodotto['id_prodotto']}'>
                          <input type='hidden' name='session_id' value='$session'> 
                            <button type='submit' class='btn btn-primary mt-2'>Aggiungi al Carrello</button>
                            </form>
                           </div>
                    </div>";
    }

    return ("<main class='products d-flex flex-wrap justify-content-center'>
                    {$prod_string}
                </main>
    ");
}

function carrello(array $products, array $pagamenti, array $trasporti, array $data):string{
    $session= session_id();
$Products_string="";
$payments="";
$transports="";
$count=1;
$pCount=0;
$tCount=0;
$total=0;
$array_count_products=0;
    foreach ($products as $product){
        $total_article=$product['prezzo']*$product['qta'];
        $total_article_view=number_format($total_article,2);
        $prezzo = number_format($product['prezzo'],2);
        $Products_string .= " <tr>
                                <th scope='row'>$count</th>
                                <td>{$product['Nome_prodotto']}</td>
                                <td>
                                    <input class='cart-qta-item' min='0' type='number' value='{$product['qta']}' aria-label='qta' name='item[$array_count_products][Qta]'
                                           id='Qta' aria-describedby='QtaLabel'>
                                    <input type='hidden' name='item[$array_count_products][id_prodotto]' value='{$product['id_prodotto']}'>
                                    <input type='hidden' name='item[$array_count_products][nome_prodotto]' value='{$product['Nome_prodotto']}'>
                                    <input type='hidden' name='item[$array_count_products][prezzo]' value='{$product['prezzo']}'>
                                    <input type='hidden' name='item[$array_count_products][Total_article]' value='$total_article'>
                                </td>
                                <td>$prezzo €</td>
                                <td>$total_article_view €</td>
                            </tr>";
        $total+=$total_article;
        $count++;
        $array_count_products++;

    }




    foreach ($pagamenti as $pagamento){
        if ($pCount == 0){
            $agg = "checked";
        }else{
            $agg = "";
        }

        $payments .= "<label>
                                        <input name='Metodo_di_Pagamento_id' class='form-check-input mt-0' type='radio' value='{$pagamento['id']}' {$agg}>{$pagamento['Pagamento']}
    </label>";
        $pCount++;
    }

    foreach ($trasporti as $trasporto){
        if ($tCount == 0){
            $agg = "checked";
        }else{
            $agg = "";
        }
        $transports .= "<label>
                                    <input name='Trasporto_a_Mezzo_id' class='form-check-input mt-0' type='radio' value='{$trasporto['id']}' {$agg}> {$trasporto['Tipo']} (+ {$trasporto['Costo']} €)
                                   
    </label>";
        $tCount++;
        $total+=$trasporto['Costo'];
    }
    if (!empty($data)) {
        $nome = $data['nome'];
        $cognome = $data['cognome'];
        $indirizzo = $data['indirizzo'];
        $cap = $data['cap'];
        $citta = $data['citta'];
        $tel = $data['telefono'];
    }else{
        $nome = "";
        $cognome = "";
        $indirizzo = "";
        $cap = "";
        $citta = "";
        $tel = "";
    }
$total_view = number_format($total,2);

    return ("

<main class='carrello d-flex flex-wrap justify-content-center'>
    <div class='d-flex flex-wrap'>
        <form action='acart.php' method='post'>
            <input type='hidden' name='session_id' value='$session'>
            <input type='hidden' name='for' value='cart'>
            <div class='row-cols-3 my-3'>
                <div class='col-5 bg-secondary p-2'>
                    <div class='input-group'>
                        <div class='input-group mb-3'>
                            <span class='input-group-text' id='Nome'>Nome:</span>
                            <input name='Nome' type='text' class='form-control' placeholder='Nome' aria-label='Nome'
                                   aria-describedby='Nome' value='{$nome}'>
                        </div>
                        <div class='input-group mb-3'>
                            <span class='input-group-text' id='Cognome'>Cognome:</span>
                            <input type='text' name='Cognome' class='form-control' placeholder='Cognome'
                                   aria-label='Cognome'
                                   aria-describedby='Cognome' value='{$cognome}'>
                        </div>
                        <div class='input-group mb-3'>
                            <span class='input-group-text' id='indirizzo'>Indirizzo:</span>
                            <input type='text' name='Indirizzo' class='form-control' placeholder='Indirizzo'
                                   aria-label='Indirizzo'
                                   aria-describedby='Indirizzo' value='{$indirizzo}'>
                        </div>
                        <div class='input-group mb-3'>
                            <span class='input-group-text' id='citta'>Città:</span>
                            <input type='text' name='citta' class='form-control' placeholder='Città' aria-label='Città'
                                   aria-describedby='citta' value='{$citta}'>
                        </div>
                        <div class='input-group mb-3'>
                            <span class='input-group-text' id='cap'>CAP:</span>
                            <input type='text' name='CAP' class='form-control' placeholder='CAP' aria-label='CAP'
                                   aria-describedby='cap' value='{$cap}'>
                        </div>
                        <div class='input-group mb-3'>
                            <span class='input-group-text' id='tel'>Telefono:</span>
                            <input type='text' name='tel' class='form-control' placeholder='tel' aria-label='tel'
                                   aria-describedby='tel' value='{$tel}'>
                        </div>

                    </div>


                </div>

            </div>


            <div class='row bg-dark-subtle mt-3 p-3'>
                <div class='col-10'>
                    <div class='input-group'>
                        <table class='table table-responsive table-sm cart-products'>
                            <thead>
                            <tr>
                                <th class='cart-item' scope='col'>#</th>
                                <th class='cart-pr-name' scope='col'>Nome_prodotto</th>
                                <th class='cart-qta' scope='col'><span class='col-form-label' id='QtaLabel'>Q.ta.</span>
                                </th>
                                <th class='cart-price' scope='col'>Prezzo</th>
                                <th>Totale Articolo</th>
                            </tr>
                            </thead>
                            <tbody>
                            {$Products_string}
                            </tbody>
                        </table>
                    </div>
                    <div class='row row-cols-2'>
                        <div class='col card mt-5 bg-secondary p-3' style='width: 15rem;'>
                            <div class='mx-auto'>
                                <table class='table'>
                                    <thead>
                                    <tr>
                                        <th scope='col'>Metodo di Pagamento:</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <div class='input-group'>
                                                {$payments}

                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>


                                </table>


                            </div>
                        </div>

                    </div>
                    <div class='row row-cols-2'>
                        <div class='col card mt-5 bg-secondary p-3' style='width: 15rem;'>
                            <div class='mx-auto'>
                                <table class='table'>
                                    <thead>
                                    <tr>
                                        <th scope='col'>Trasporto a mezzo:</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <div class='input-group'>
                                                {$transports}

                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>


                                </table>


                            </div>
                        </div>

                    </div>
                  <div class='input-group'>
                    <div class='ms-auto my-3'>
                        <table class='table' style='width: 300px'>
                            <tbody>
                            <tr>
                                <th scope='row'>Totale Carrello:</th>
                                <td>$total_view €</td>
                            </tr>
                            </tbody>
                        </table>
                    
                    </div>
</div>

                </div>

            </div>

    


    <div class='d-flex justify-content-end mb-3'>
        <button class='btn btn-success me-2' value='Update' type='submit' formaction='ucart.php'>Aggiorna</button>
        <button class='btn btn-primary' type='submit'>Invia</button>
    </div>
    </form>
    
</main>
    
    ");
}