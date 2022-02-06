<?php include 'layout/header.php' ?>

<div class="ui grid">

<form class="ui form column stackable grid" 
	action="<?=site_url()?>/desenvolvedores/salvar" method="post">


<?=$this->session->flashdata('error')?>
<?=$this->session->flashdata('success')?>
<?=$this->session->flashdata('warning')?>

<input type="hidden" name="id" value="<?=val($dados,'id')?>">

<div class="field">
	<label>Nome
		<input type="text" name="nome" value="<?=val($dados,'nome')?>">
		<?=error('nome')?>
	</label>
</div>


<?php if (val($dados,'id') != ""): ?>

<div class="field">
	<label>Adicionar Projeto
		<?=form_dropdown("projetos_id", $projetos);?>
	</label>
</div>

<div class="field">
<table class="ui celled table">
	<thead>
		<tr>
			<th colspan="3">Projetos em desenvolvedores</th>
		</tr>
		<tr>
			<th>Nome</th>
			<th>Linguagem de Programação</th>
		</tr>
	</thead>
	<tbody>
	
<?php


foreach($dados->ownDesenvolvedoresprojetosList as $da){
	
	$projeto = $da->projetos;

	print "<tr>";
	
	print "<td><a href='".site_url()."/projetos/index/{$projeto->id}'> {$projeto->nome} </a></td>";
	
	print "<td><a  onclick='confirmDelete(\"".site_url()."/desenvolvedores/remover_projeto/{$dados['id']}/{$da->id}\")' > Remover </a></td>";
	
	print "</tr>";
}
?>
</tbody>
</table>
</div>

<?php endif; ?>


<div class="field">
	<button  class="ui blue button" type="submit">Salvar</button>
	<a class="ui button" href="<?=site_url()?>/desenvolvedores">Novo</a>
</div>

</form>


</div>




<form class="ui form column stackable grid" action="<?=site_url()?>/desenvolvedores" method="GET">
	<div class="fields">
		<input name="busca" placeholder="Pesquisar."  value="<?=val($_GET,"busca")?>" />
		<button  class="ui blue button" type="submit">Pesquisar</button>
	</div>
</form>

<table class="ui celled table">
	<thead>
		<tr>
			<th>Editar</th>
			<th>Nome</th>
			<th>Deletar</th>
		</tr>
	</thead>
	<tbody>
	
<?php

foreach($listaPaginada["data"] as $ln){
	
	print "<tr>";
	
	print "<td><a href='".site_url()."/desenvolvedores/index/{$ln->id}'> Editar </a></td>";
	
	print "<td>{$ln->nome}</td>";

	print "<td><a onclick='confirmDelete(\"".site_url()."/desenvolvedores/deletar/{$ln->id}\")'> Deletar </a></td>";
	
	print "</tr>";
}

#paginacao
$this->pagination->initialize($listaPaginada);
?>

	<tfoot>
	<tr>
		<th colspan="5">
		<span class="ui label">
			Total: <?=$listaPaginada["total_rows"]?>
		</span>
		<?php if ($listaPaginada["page_max"] > 1): ?>
			<div class="ui right floated pagination menu">
				<?=$this->pagination->create_links()?>
			</div>
		<?php endif; ?>
		</th>
	</tr>
	</tfoot>
</tbody>
</table>

<?php
include 'layout/bottom.php';
?>