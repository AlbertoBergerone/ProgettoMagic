<?php
  $conn = null;

  /*Connessione al db carte.db*/
  try{
    $conn = new PDO(
        'sqlite:carte.db',
        null,
        null,
        array(PDO::ATTR_PERSISTENT => true)
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
  }catch(PDOException $ex){
      echo("Connessinoe fallita.".$ex->getMessage());
  }
  
  /* rilevamento richiesta dal client */
	if(is_ajax()){
		$arr["nome"] = $_GET["nome"];
		$arr["tipo"] = $_GET["tipo"];
		$arr["colore"] = $_GET["colore"];
		$arr["espansione"] = $_GET["espansione"];

		$jsonResults = ricerca($arr);
	}

  /*Ricerca gli elenchi degli attributi delle carte, nasce per riempire i campi di ricerca*/
  /*Non utilizzo la bind perchè il parametro viene passato dal programmatore, non dall'utonto*/
  /*Restituisce i dati in formato json*/
  function ricercaAttirbuti($tabella){
    try{
      global $conn;
      $stmSql = $conn->prepare("SELECT * FROM $tabella");
      $stmSql->execute();
      $json = codificaRisultati($stmSql);
    }catch(PDOException $ex){
      echo("Errore nella query!".$ex->getMessage());
    }

    return $json;
  }

  /*Restituisce i risultati di una query in formato json*/
  function codificaRisultati($stmExecuted){
      $results = $stmExecuted->fetchAll(PDO::FETCH_ASSOC);
      $json=json_encode($results);

      return $json;
  }



  /*Ricerca le carte, riceve un vettore contente i campi su cui si vuole effettuare la ricerca.*/
  /*Formato dati [nome, tipo, colore, espansione, rarita]*/
  /*Se il campo non è stato valorizzato dall'utente non viene incluso nella ricerca*/
  function ricerca($request){
    global $conn;
    $binds= [];
    $query = "SELECT nome, tipi.tipo, colore, testo, link as link_immagine FROM ";
    $tabelle = "carte, colori, tipi";
    $condizioni = "carte.colore_id=colori.id AND carte.tipo=tipi.id ";
    if($request["nome"] != ""){
      $condizioni = $condizioni."AND nome LIKE ? ";
      $binds[] = "%".$request["nome"]."%";
    }
    if($request["tipo"] != ""){
      $condizioni = $condizioni."AND tipi.tipo = ? ";
      $binds[] = $request["tipo"];
    }
    if($request["colore"] != ""){
	  $condizioni = $condizioni."AND colore = ? ";
      $binds[] = $request["colore"];
    }

    $query = $query.$tabelle." WHERE ".$condizioni;
    //echo($query);
    $stm = $conn->prepare($query);

    for($i=1; $i <= count($binds) ; $i=$i+1){
      $stm->bindParam($i, $binds[$i-1]);
    }
    // foreach ($binds as $campo) {
    //   echo("$i >> *$campo*<br>");
    //   $stm->bindParam($i, $campo);
    //   $i = $i+1;
    // }


    try{
        $stm->execute();
    }catch(PDOException $e){
      echo($e->getMessage());
    }

    return codificaRisultati($stm);
  }
  
  
  // $arr["nome"] = "Orco";
  // $arr["tipo"] = "4";
  // $arr["colore"] = "";
  // $arr["espansione"] = "";
  // $arr["rarita"] = "3";
  //
  // $jsonResults = ricerca($arr);


  /*$jsonResults = ricercaAttirbuti("espansioni");

  /*Leggo ricorsivamente l'array json, utile per gli array annidati*/
  // $jsonIterator = new RecursiveIteratorIterator(
  //   new RecursiveArrayIterator(json_decode($jsonResults, TRUE)),
  //   RecursiveIteratorIterator::SELF_FIRST);
  //
  // foreach ($jsonIterator as $key => $val) {
  //   if(is_array($val)) {
  //       echo "$key:\n";
  //   } else {
  //       echo "$key => $val</br>";
  //   }
  // }


/*
  while($row = $x->fetch()){
    $colore = $row["espansione"];
    echo("$colore");
  }
/*
  try{
    $stmSql = $conn->prepare("SELECT * FROM colori");
    $stmSql->execute();
  }catch(PDOException $exc){
    echo("Albertooooooooooo!".$ex->getMessage());
  }

  while($row = $stmSql->fetch()){
      $colore = $row["colori_id"];
      echo("$colore");
  }*/

  //echo("Fine!");


 ?>