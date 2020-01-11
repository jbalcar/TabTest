<?php

class TEMPLATE {

    protected $file     = "";
    protected $template = "";
    protected $vars     = array();

    //definovanie template
    public function __construct($file) {

        $this->file = $file;

    }

    //nacitanie template
    public function Load() {

        if(!is_file($this->file))
            return FALSE;
        if(!is_readable($this->file))
            return FALSE;

        $this->template = file_get_contents($this->file);
        return TRUE;

    }

    //definicia premennych
    public function DefineVar($name, $value) {

        $this->vars[$name] = $value;
        return TRUE;

    }

    //zobraz vyslednu stranku
    public function Execute() {

        $this->ExecuteCycles();
        $this->ExecuteVars();
        $this->ExecuteConditions();

        echo $this->template;

    }

    //uprava template po cycles
    private function ExecuteCycles() {

        $temp = "";
        $regex = '/(.*)\{CYCLE:\{([^\}]+)\}\}(.*)\{END_CYCLE\}(.*)/sim';
        preg_match_all($regex, $this->template, $matches); 

        for($i=0; $i<count($matches[2]); $i++) { //najdeme vsetky cykly
            $temp .= $matches[1][$i];
            foreach($this->vars[$matches[2][$i]] as $vars) { //pre kazdy cyklus opakujeme patricny pocet krat
                $text = $matches[3][$i];
                foreach($vars as $key=>$val) { //nahradime kazdu premennu zodpovedajucu danemu cyklu
                    $text = str_replace('{'.$key.'}', $val, $text);
                }
                $temp .= $text;
            }
        }

        $temp .= $matches[4][$i-1];
        $this->template = $temp;

    }

    //uprava template po nahradeni premennych
    private function ExecuteVars() {

        foreach($this->vars as $key=>$var) {
            $this->template = str_replace('{'.$key.'}', $var, $this->template);
        }

    }

    //uprava template po podmienkach
    private function ExecuteConditions() {

        $temp   = "";
        $regex  = "/([^\{]*)";
        $regex .= '\{IF:([^\}]+)\}'; //keyword
        $regex .= '(((?!\{ELSEIF:([^\}]+)\})(?!\{ELSE\})(?!\{END_IF\}).)*)'; //obsah
        $regex .= '(\{ELSEIF:([^\}]+)\})?'; //keyword
        $regex .= '(((?!\{ELSE\})(?!\{END_IF\}).)*)'; //obsah
        $regex .= '(\{ELSE\})?'; //keyword
        $regex .= '(((?!\{END_IF\}).)*)'; //obsah
        $regex .= '\{END_IF\}([^\{]*)/sim'; //keyword
        preg_match_all($regex, $this->template, $matches);

        $before_if   = $matches[1][0];
        $temp       .= $before_if;
        for($i=0; $i<count($matches[2]); $i++) {

            //definicia patricnych casti
            $if_cond        = $matches[2][$i];
            $if_value       = $matches[3][$i];
            $elseif_cond    = $matches[7][$i];
            $elseif_value   = $matches[8][$i];
            $else_value     = $matches[11][$i];
            $after_end_if   = $matches[13][$i];

            //execute potrebnych podmienok
            $if_cond        = $this->ExecuteIS($if_cond);
            $if_cond        = $this->ExecuteAND($if_cond);
            $elseif_cond    = $this->ExecuteIS($elseif_cond);
            $elseif_cond    = $this->ExecuteAND($elseif_cond);

            //execute patricnych casti
            if($if_cond == "1") {
                $temp .= $if_value;
            } elseif($elseif_cond == "1") {
                $temp .= $elseif_value;
            } else {
                $temp .= $else_value;
            }
            
            $temp .= $after_end_if;
        }

        $this->template = $temp;

    }

    //vykonanie logického členu: Porovnanie
    private function ExecuteIS($text) {

        while(strpos($text, ' IS ') !== false) {
            $text = preg_replace_callback("/([^ ]+) IS ([^ ]+)/sim", function($matches){
                return $matches[1] == $matches[2] ? "1" : "0";
            }, $text);
        }
        return $text;

    }

    //vykonanie logického členu: AND
    private function ExecuteAND($text) {

        while(strpos($text, ' AND ') !== false) {
            $text = preg_replace_callback("/([^ ]+) AND ([^ ]+)/sim", function($matches){
                return $matches[1] == 1 && $matches[2] == 1 ? "1" : "0";
            }, $text);
        }
        return $text;

    }

}

?>