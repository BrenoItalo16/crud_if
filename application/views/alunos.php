<?php include 'layout/header.php' ?>

<div class="ui grid">

<form class="ui form column stackable grid" 
	action="<?=site_url()?>/alunos/salvar" method="post">


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


<div class="field">
	<label>Matrícula
		<input type="text" name="matricula" value="<?=val($dados,'matricula')?>">
		<?=error('matricula')?>
	</label>
</div>


<div class="field">
	<label>Período
		<input type="text" name="periodo" value="<?=val($dados,'periodo')?>">
		<?=error('periodo')?>
	</label>
</div>

<?php if (val($dados,'id') != ""): ?>

<div class="field">
	<label>Adicionar aluno
		<?=form_dropdown("alunos_id", $alunos);?>
	</label>
</div>

<div class="field">
<table class="ui celled table">
	<thead>
		<tr>
			<th colspan="3">Alunos na disciplina</th>
		</tr>
		<tr>
			<th>Nome</th>
			<th>Matrícula</th>
			<th>Remover aluno</th>
		</tr>
	</thead>
	<tbody>
	
<?php


foreach($dados->ownDisciplinasalunosList as $da){
	
	$aluno = $da->alunos;

	print "<tr>";
	
	print "<td><a href='".site_url()."/disciplinas/index/{$aluno->id}'> {$aluno->nome} </a></td>";

	print "<td>{$aluno->matricula}</td>";
	
	print "<td><a  onclick='confirmDelete(\"".site_url()."/disciplinas/remover_aluno/{$dados['id']}/{$da->id}\")' > Remover </a></td>";
	
	print "</tr>";
}
?>
</tbody>
</table>
</div>

<?php endif; ?>


<div class="field">
	<button  class="ui blue button" type="submit">Salvar</button>
	<a class="ui button" href="<?=site_url()?>/disciplinas">Novo</a>
</div>

</form>


</div>




<form class="ui form column stackable grid" action="<?=site_url()?>/disciplinas" method="GET">
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
			<th>Matrícula</th>
			<th>Período</th>
			<th>Deletar</th>
		</tr>
	</thead>
	<tbody>
	
<?php

foreach($listaPaginada["data"] as $ln){
	
	print "<tr>";
	
	print "<td><a href='".site_url()."/alunos/index/{$ln->id}'> Editar </a></td>";
	
	print "<td>{$ln->nome}</td>";
	
	print "<td>{$ln->matricula}</td>";
	
	print "<td>{$ln->periodo}</td>";

	print "<td><a onclick='confirmDelete(\"".site_url()."/alunos/deletar/{$ln->id}\")'> Deletar </a></td>";
	
	print "</tr>";
}

#paginacao
$this->pagination->initialize($listaPaginada);
?>

	<tfoot>
	<tr>
		<th colspan="6">
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