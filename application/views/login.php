<?php include 'layout/header.php' ?>

<div class="ui middle aligned center aligned grid">
  <div class="column">
    <h2 class="ui teal image header">
      <img src="<?=base_url()?>/static/logo.png" class="image">
      <div class="content">
        Login do sistema
      </div>
    </h2>
    <form class="ui large form" action="<?=site_url()?>/login/login/" method="POST">
      <div class="ui stacked segment">
        <div class="field">
          <div class="ui left icon input">
            <i class="user icon"></i>
            <input type="text" name="email" placeholder="Digite seu e-mail">
          </div>
          <?=error('email')?>
        </div>
        <div class="field">
          <div class="ui left icon input">
            <i class="lock icon"></i>
            <input type="password" name="senha" placeholder="Digite sua senha">
          </div>
          <?=error('senha')?>
        </div>
        <button class="ui fluid large teal submit button">Entrar</button>
      </div>

      <?php if ($this->session->flashdata('message')): ?>
        <div class="ui red message">
          <?=$this->session->flashdata('message');?>
        </div>
      <?php endif; ?>


    </form>

    <div class="ui message">
      Novo usuário? <a href="<?=site_url()?>/login/cria_usuario/">Cadastre-se</a>
    </div>
  </div>
</div>




<?php include 'layout/bottom.php' ?>