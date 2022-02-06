<?php include 'layout/header.php' ?>

<h2>Você não tem permissão de acesso.</h2>

<?=$this->session->flashdata('error')?>

<?php
include 'layout/bottom.php';
?>