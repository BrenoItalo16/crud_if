<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Seguranca {
    private $tabela = null;
    private $campo = null;

    public static function permitir($nivel){
        if (!Seguranca::temPermissao($nivel)){
            $CI =& get_instance();
            print $CI->load->view('sem_permissao', '', true);
            die();
        }
    }

    public static function temPermissao($nivel){
        $model = $_SESSION["model_seguranca"];

        $CI =& get_instance();
        $CI->load->model($model);

        $tbl = $CI->$model->table;
        $field = $CI->$model->secureField;
        $levels = $CI->$model->secureLevels;

        $sql = "select $field from $tbl where id = {$_SESSION['user_id']}";

        #procura a id do nivel passado via parametro
        foreach($levels as $key=>$val){
            if (strtolower($val) == strtolower($nivel)){
                $nivel = $key;#troca o texto pela id
                break;
            }
        }

        if (!is_numeric($nivel)){
            $CI->session->set_flashdata("error","<div class='ui red message'>Houve um erro na checagem do nível do usuário, não foi possível encontrar o nível: \"<b>$nivel</b>\".</div>");
            print $CI->load->view('sem_permissao', "", true);
            die();
        }

        
        $row = R::getRow($sql);
        #compara o nivel
        if ($row == null || $row[$field] > $nivel){
            return false;
        } else {
            return true;
        }
    }



}