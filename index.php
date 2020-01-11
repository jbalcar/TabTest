<?php

session_start();
include("core/template.php");
include("core/database.php");

//inicializacia
$realty_list_class  = "";
$comp_list_class    = "";
$user_list_class    = "";
$act_site           = 1;
$other_table        = 1;
$session_edit       = 0;
$site_editing       = 0;
$expire_sec         = 0;
$expire_min         = 0;

//inicializacia podla typu tabulky
if(isset($_GET['computer'])) {
    $table              = "computer";
    $comp_list_class    = "active";
    $first_site_link    = "/?computer";
} elseif(isset($_GET['user'])) {
    $table              = "user";
    $user_list_class    = "active";
    $first_site_link    = "/?user";
} else {
    $table              = "realty";
    $realty_list_class  = "active";
    $first_site_link    = "/";
    $other_table        = 0;
}

//uvodny connection k MySQL
$data = new DATABASE();
if(!$data->Connect()) {
    echo "Nepodarilo sa pripojit k databaze";
    die();
}

//vyber tabulky
$data->SelectTable($table);

//nastavenie aktualnej strany
if(isset($_GET['site'])) {
    $act_site = $data->SetSite($_GET['site']);
}

//zistenie informacii k stranam
$max_site       = $data->GetMaxSite();
$prev_site_link = $data->GetPrevSiteLink();
$next_site_link = $data->GetNextSiteLink();
$last_site_link = $data->GetLastSiteLink();
$actual_link    = $data->GetActSiteLink();

//vystupne data
$result_table   = $data->GetItems();

//overenie aktivity editacie itemu
if(isset($_SESSION['edit'])) {
    if($_SESSION['edit']['table'] == $table) {
        if($data->IsActiveEdit($_SESSION['edit'])) {
            $session_edit   = $_SESSION['edit']['id'];
            if(array_search($session_edit, array_column($result_table, 'ITEM_ID')) !== FALSE) {
                $site_editing    = 1;
                $expire_sec      = $data->GetExpireSec($_SESSION['edit']);
                $expire_percent  = $data->GetExpirePercent($expire_sec);
                $actual_link     = $actual_link."#item".$session_edit;
            }
        }
    }
}

//nacitanie template
$template = new TEMPLATE("template/index.tpl");
if(!$template->Load()) {
    echo "Nepodarilo sa nacitat template";
    die();
}

//definicia premennych
$template->DefineVar("REALTY_LIST_CLASS", $realty_list_class);
$template->DefineVar("COMP_LIST_CLASS",   $comp_list_class  );
$template->DefineVar("USER_LIST_CLASS",   $user_list_class  );
$template->DefineVar("FIRST_SITE_LINK",   $first_site_link  );
$template->DefineVar("PREV_SITE_LINK",    $prev_site_link   );
$template->DefineVar("ACT_SITE",          $act_site         );
$template->DefineVar("MAX_SITE",          $max_site         );
$template->DefineVar("NEXT_SITE_LINK",    $next_site_link   );
$template->DefineVar("LAST_SITE_LINK",    $last_site_link   );
$template->DefineVar("RESULT_TABLE",      $result_table     );
$template->DefineVar("SESSION_EDIT",      $session_edit     );
$template->DefineVar("TABLE",             $table            );
$template->DefineVar("OTHER_TABLE",       $other_table      );
$template->DefineVar("ACTUAL_LINK",       $actual_link      );
$template->DefineVar("SITE_EDITING",      $site_editing     );
$template->DefineVar("EXPIRE_SEC",        $expire_sec       );
$template->DefineVar("EXPIRE_PERCENT",    $expire_percent   );

//finalne zobrazenie stranky
$template->Execute();

?>