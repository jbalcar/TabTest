<?php

session_start();
include("../core/database.php");

//uvodny connection k MySQL
$data = new DATABASE();
if(!$data->Connect()) {
    echo "Nepodarilo sa pripojit k databaze";
    die();
}

//vyber tabulky
if(isset($_GET['computer'])) {
    $table = "computer";
} elseif(isset($_GET['user'])) {
    $table = "user";
} else {
    $table = "realty";
}
$data->SelectTable($table);

//spracovanie ID itemu
$id = (int)$_GET['id'];
if(!$data->IsExist($id)) {
    header("Location: /");
    die();
}

$final_id = 0;

//aktivacia upravy
if(!isset($_GET['desc']) && !isset($_GET['cancel'])) {

    //mame aktivnu editaciu od druheho uzivatela?
    if((!isset($_SESSION['edit']) || $_SESSION['edit']['id']!=$id || $_SESSION['edit']['table']!=$table) && $data->IsActiveEdit(array('id'=>$id, 'table'=>$table))) {
        $data->CancelActiveEdit($_SESSION['edit']);
        header("Location: ".$_SERVER['HTTP_REFERER']."#item".$id);
        die();
    } 

    //mame nejaku aktivnu editaciu od aktualneho usera?
    if(isset($_SESSION['edit']) && $data->IsActiveEdit($_SESSION['edit'])) {
        $data->CancelActiveEdit($_SESSION['edit']);
    } 

    //aktivujeme editaciu
    $_SESSION['edit']           = array();
    $_SESSION['edit']['id']     = $id;
    $_SESSION['edit']['table']  = $table;
    $final_id                   = $id;
    $data->ActiveEdit($_SESSION['edit']);

//zrusenie upravy
} elseif(isset($_GET['cancel']) && isset($_SESSION['edit'])) {

    if($data->IsActiveEdit($_SESSION['edit'])) {
        $data->CancelActiveEdit($_SESSION['edit']);
    }
    $final_id = $_SESSION['edit']['id'];
    unset($_SESSION['edit']);

//finalna uprava popisu
} elseif(isset($_GET['desc']) && isset($_SESSION['edit']) && $data->IsActiveEdit($_SESSION['edit'])) {

    $data->SaveActiveEdit($_SESSION['edit'], $_GET['desc']);
    $data->CancelActiveEdit($_SESSION['edit']);
    $final_id = $_SESSION['edit']['id'];
    unset($_SESSION['edit']);

}

header("Location: ".$_SERVER["HTTP_REFERER"]."#item".$final_id);

?>