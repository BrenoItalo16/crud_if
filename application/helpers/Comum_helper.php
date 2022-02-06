<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists("checked")){
    function checked($val, $arr, $field){
        if (isset($arr[$field])){
            if (is_array($arr[$field]) && in_array($val, $arr[$field])){
                return "checked";
            } else
            if ($arr[$field] == $val){
                return "checked";
            }
        } else {
            return "";
        }
    }



    function toOptions($arr){
        $arr2 = [];
        foreach($arr as $val){
            $arr2[$val["id"]] = $val["nome"];
        }
        return $arr2;
    }

    function error($field){
        if (ENVIRONMENT == "development") {
            if (!isset($_SESSION["erros_apresendados"])){
                $_SESSION["erros_apresendados"] = [];
            } else {
                array_push($_SESSION["erros_apresendados"], $field);
            }
        }

        return form_error($field);
    }


    function validation_errors_array($prefix = '', $suffix = '') {
        if (FALSE === ($OBJ = & _get_validation_object())) {
        return '';
        }

        return $OBJ->error_array($prefix, $suffix);
    }

    function errors(){
        $arr = validation_errors_array();
        if (isset($_SESSION["erros_apresendados"])){
            foreach($_SESSION["erros_apresendados"] as $val){
                unset($arr[$val]);
            }
        }
        return $arr;
    }

    function clear_errors(){
        $_SESSION["erros_apresendados"] = [];
        $_SESSION["php_errors"] = [];
    }


    function errorManager($errNo, $errStr, $errFile, $errLine, $a){
        if (error_reporting() == 0) {
            return;
        }
        if (!isset($_SESSION["php_errors"])){
            $_SESSION["php_errors"] = [];
        }
        array_push($_SESSION["php_errors"], "Erro:$errStr <br/>Arquivo:$errFile<br/>Linha:$errLine");
    }

    set_error_handler("errorManager");

}