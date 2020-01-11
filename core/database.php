<?php

class DATABASE {

    //info ku connection k MySQL
    protected $host         = "localhost";
    protected $user         = "***";
    protected $pass         = "***";
    protected $db           = "***";
    protected $conn         = NULL;

    //info k datam MySQL
    protected $table        = "";
    protected $act_site     = 1;
    protected $item_site    = 100;
    protected $expire       = 5; 

    //uvodny connection k MySQL
    public function __construct() {
        
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db);

    }

    //aktivacia editingu
    public function ActiveEdit($session_edit) {

        $result = $this->conn->query("
            UPDATE 
                ".$session_edit['table']." 
            SET 
                updating = NOW()
            WHERE 
                id=".$session_edit['id']
        );
        return TRUE;

    }

    //zrusenie aktivneho editingu
    public function CancelActiveEdit($session_edit) {

        $result = $this->conn->query("
            UPDATE 
                ".$session_edit['table']." 
            SET 
                updating = (NOW() - INTERVAL ".($this->expire + 1)." MINUTE)
            WHERE 
                id=".$session_edit['id']
        );
        return TRUE;

    }

    //overenie ci sme uspesne konektnuty
    public function Connect() {
        
        if($this->conn->connect_error)
            return FALSE;
        return TRUE;

    }

    //ziskanie linky na aktualnu stranku
    public function GetActSiteLink() {
        
        $q = array();
        if($this->act_site > 1)
            $q['site'] = $this->act_site;
        if($this->table != "realty") 
            $q[$this->table] = "";

        $query = http_build_query($q, '', '&amp;');
        $query = substr($query, -1) == "=" ? substr($query, 0, -1) : $query;
        return "/".(empty($query)?"":"?".$query);

    }

    //ziskanie poctu items
    public function GetCountItems() {

        $result = $this->conn->query("SELECT COUNT(id) AS count FROM ".$this->table);
        $row    = $result->fetch_assoc();
        return (int)$row['count'];

    }

    //ziskanie poctu percent ubehnutej expiracie
    public function GetExpirePercent($expire_sec) {

        $expire_all     = $this->expire*60;
        $expire_before  = $expire_all - $expire_sec;
        return $expire_before / $expire_all * 100;

    }

    //ziskanie poctu sekund do expiracie
    public function GetExpireSec($session_edit) {

        $result = $this->conn->query("
            SELECT 
                UNIX_TIMESTAMP(updating + INTERVAL ".$this->expire." MINUTE) - UNIX_TIMESTAMP(NOW()) AS EXPIRE_SEC
            FROM 
                ".$session_edit['table']."
            WHERE
                id=".$session_edit['id']
        );
        $row        = $result->fetch_assoc();
        $expire_sec = (int) $row['EXPIRE_SEC'];
        return $expire_sec > 0 ? $expire_sec : 0;

    }

    //ziskanie danej casti tabulky
    public function GetItems() {

        $result = $this->conn->query("
            SELECT 
                id AS ITEM_ID, 
                name AS ITEM_NAME, 
                description AS ITEM_DESC,
                updating > NOW() - INTERVAL ".$this->expire." MINUTE AS ITEM_EDIT
            FROM 
                ".$this->table." 
            LIMIT 
                ".$this->GetStartedIndex().",".$this->item_site
        );
        return $result->fetch_all(MYSQLI_ASSOC);

    }

    //ziskanie linky na poslednu stranku
    public function GetLastSiteLink() {
        
        $q = array();
        $q['site'] = $this->GetMaxSite();
        if($this->table != "realty") 
            $q[$this->table] = "";
            
        $query = http_build_query($q, '', '&amp;');
        $query = substr($query, -1) == "=" ? substr($query, 0, -1) : $query;
        return "/".(empty($query)?"":"?".$query);

    }

    //ziskanie maximalnej moznej site
    public function GetMaxSite() {

        return ceil($this->GetCountItems() / $this->item_site);

    }

    //ziskanie nasledujucej stranky
    public function GetNextSite() {

        if($this->act_site == $this->GetMaxSite())
            return $this->act_site;
        return $this->act_site + 1;

    }

    //ziskanie linky na nasledujucu stranku
    public function GetNextSiteLink() {
        
        $q = array();
        $q['site'] = $this->GetNextSite();
        if($this->table != "realty") 
            $q[$this->table] = "";
            
        $query = http_build_query($q, '', '&amp;');
        $query = substr($query, -1) == "=" ? substr($query, 0, -1) : $query;
        return "/".(empty($query)?"":"?".$query);

    }

    //ziskanie predoslej stranky
    public function GetPrevSite() {

        if($this->act_site == 1)
            return 1;
        return $this->act_site - 1;

    }

    //ziskanie linky na predoslu stranku
    public function GetPrevSiteLink() {
        
        $q = array();
        if($this->GetPrevSite() > 1)
            $q['site'] = $this->GetPrevSite();
        if($this->table != "realty") 
            $q[$this->table] = "";

        $query = http_build_query($q, '', '&amp;');
        $query = substr($query, -1) == "=" ? substr($query, 0, -1) : $query;
        return "/".(empty($query)?"":"?".$query);

    }

    //ziskanie start pozicie v select dotaze
    public function GetStartedIndex() {

        return ($this->act_site - 1) * $this->item_site;

    }

    //overenie ci je este aktivny editing daneho ID z TAB
    public function IsActiveEdit($session_edit) {

        $result = $this->conn->query("
            SELECT 
                updating > NOW() - INTERVAL ".$this->expire." MINUTE AS ITEM_EDIT
            FROM 
                ".$session_edit['table']."
            WHERE 
                id = ".$session_edit['id']
        );
        $row = $result->fetch_assoc();
        return (int)$row['ITEM_EDIT'] == 1 ? TRUE : FALSE;

    }

    //existuje dane id?
    public function IsExist($id) {

        $result = $this->conn->query("
            SELECT 
                name
            FROM 
                ".$this->table."
            WHERE 
                id = ".$id
        );
        return $result->num_rows == 1 ? TRUE : FALSE;

    }

    //ulozenie noveho popisu
    public function SaveActiveEdit($session_edit, $desc) {

        $desc = $this->conn->escape_string(htmlspecialchars($desc));
        $result = $this->conn->query("
            UPDATE 
                ".$session_edit['table']." 
            SET 
                description = '".$desc."' 
            WHERE 
                id=".$session_edit['id']
        );
        return TRUE;

    }

    //urcenie nad ktorou tabulkou budeme pracovat
    public function SelectTable($table) {

        $this->table = $table;

    }

    //nastavenie stranky ktoru chceme
    public function SetSite($site) {

        $site = (int)$site;

        if($site<1)
            return FALSE;
        if($site>$this->GetMaxSite())
            return FALSE;

        $this->act_site = $site;
        return $this->act_site;

    }

}

?>