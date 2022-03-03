<?php include 'layout/header.php' ?>

<div class="ui grid">


<form class="ui form column stackable grid" action="<?=site_url()?>/agenda/salvar"
	method="post" enctype="multipart/form-data">

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
	<label>E-mail
		<input type="text" name="email" value="<?=val($dados,'email')?>">
		<?=error('email')?>
	</label>
</div>

<div class="field">
	<label>Senha
		<input type="password" name="senha" value="">
		<?=error('senha')?>
	</label>
</div>

<div class="field">
	<label>Confirmação da senha
		<input type="password" name="senhaConfirm" value="">
		<?=error('senhaConfirm')?>
	</label>
</div>

<?php
#verifica se tem permissao para escolher o nível do usuário
if (Seguranca::temPermissao("Admin")): ?>
<div class="field">
	<label>Nível de acesso
		<?=form_dropdown("nivel", $niveis, val($dados,"nivel"));?>
		<?=error('nivel')?>
	</label>
</div>
<?php endif; ?>

<div class="field">
	<label>Setor
	<?=form_dropdown("setores_id", $setores, val($dados,"setores_id"));?>
	<?=error('setores_id')?>
	</label>
</div>

<?php if (val($dados,'id') != ""): ?>

<div class="field">
	<label>Adicionar grupo
		<?=form_dropdown("grupo_id", $grupos);?>
	</label>
</div>

<div class="field">
<table class="ui celled table">
	<thead>
		<tr>
			<th colspan="3">Grupos do usuário</th>
		</tr>
		<tr>
			<th>Nome</th>
			<th>Remover grupo</th>
		</tr>
	</thead>
	<tbody>
	
<?php


foreach($dados->ownGruposagendaList as $gruposagenda){
	
	$grupo = $gruposagenda->grupos;

	print "<tr>";
	
	print "<td><a href='".site_url()."/grupos/index/{$grupo->id}'> {$grupo->nome} </a></td>";

	#para remover o usuario eu preciso passar a id_usuario
	#depois a id do registro da tabela gruposusuarios
	print "<td><a onclick='confirmDelete(\"".site_url()."/agenda/remover_grupo/{$dados['id']}/{$gruposagenda->id}\")' > Remover </a></td>";
	
	print "</tr>";
}
?>
</tbody>
</table>
</div>


<?php endif; ?>

<div class="field">
	<label>Tipo de usuário</label>
	<div class="six fields">
		<?php
		foreach($tiposAgendas as $key=>$tipo){
			
			print "<label class='field'>";
			print form_radio("tipo",$key, checked($key, $dados,"tipo") );
			print "$tipo</label>";
		}
		?>
		<?=error('tipo')?>
	</div>
</div>


<div class="field">
	<label>Área de atuação</label>
	<div class="five fields">
		<input type="hidden" name="area[]">
		<?php
		foreach($areasAgenda as $key=>$tipo){			
			print "<label class='field'>";
			print form_checkbox("area[]",$key, checked($key, $dados,"area"));
			print "$tipo</label>";
		}
		?>
		<?=error('area[]')?>
	</div>
</div>

<div class="field">
	<label>Foto
		<input type="file" name="foto">
		<?=error('foto')?>
	</label>
	<?php
	if (val($dados,"foto") != ""){
		print "<img class='ui tiny circular image' src='".base_url()."uploads/{$dados['foto']}' />";
	} ?>
</div>

<div class="field">
	<button  class="ui blue button" type="submit">Salvar</button>
	<a class="ui button" href="<?=site_url()?>/agenda">Novo</a>
</div>


</form>
</div>


<form class="ui form column stackable grid" action="<?=site_url()?>/agenda" method="GET">
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
			<th>E-mail</th>
			<th>Setor</th>
			<th>Deletar</th>
		</tr>
	</thead>
	<tbody>
	
<?php


foreach($listaPaginada["data"] as $ln){
	
	print "<tr>";
	
	print "<td><a href='".site_url()."/agenda/index/{$ln->id}'> Editar </a></td>";
	
	print "<td>{$ln->nome}</td>";
	print "<td>{$ln->email}</td>";
	#link para abrir diretamente o setor já em modo de edição
	@print "<td><a href='".site_url()."/setores/index/{$ln->setores->id}'> {$ln->setores->nome} </a> </td>";
	
	print "<td><a onclick='confirmDelete(\"".site_url()."/agenda/deletar/{$ln->id}\")'> Deletar </a></td>";
	
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