</div> <!-- ui text container -->
<div id="rodape"></div>

<?php if (ENVIRONMENT == "development"): ?>
  <div class="ui yellow message debugger">
    <i class="close icon" onclick="$(this).closest('.message').transition('fade');"></i>
    <div class="header">
      Debugger
    </div>
  <?php

  #mostra os erros do PHP
  foreach($_SESSION["php_errors"] as $err){
    print "<p class='ui red message'>$err</p>";
  }


  #mostra as validações que não foram mostradas no formulário
  $errors = errors();
  foreach($errors as $val=>$err){
    print "<p class='ui red message'>
    O campo \"<b>$val</b>\" não existe no formulário, a validação do <b>controller</b> não está permitindo que o formulário seja salvo sem esse campo.</p>";
  }
  clear_errors();



  $logs = val($_SESSION,'logs');
  if ($logs != ""){
    foreach($logs as $log){
      print "<p>$log</p>";
    }
    $_SESSION["logs"] = [];
  }
  $logs = R::getDatabaseAdapter()->getDatabase()->getLogger()->getLogs();
  foreach($logs as $log){
    print "<p>$log</p>";
  }
  ?>
  <script>
  $('p:contains("SELECT"),p:contains("select"),p:contains("PRAGMA")').addClass("ui blue message").next().addClass("ui blue message");
  $('p:contains("UPDATE"),p:contains("update")').addClass("ui green message").next().addClass("ui green message");
  $('p:contains("INSERT"),p:contains("insert")').addClass("ui green message").next().addClass("ui green message");
  $('p:contains("DELETE"),p:contains("delete")').addClass("ui green message").next().addClass("ui green message");
  $('p:contains("SQLSTATE")').removeClass("blue green").addClass("ui red message").prev().addClass("ui red message");
  </script>
  </div>
<?php endif; ?>

<div class="ui basic mini modal">
  <div class="ui icon header">
    <i class="trash icon"></i>Confirmação</div>
  <div class="content center">
    <p>Você realmente deseja deletar esse registro?</p>
  </div>
  <div class="actions">
    <div class="ui red basic cancel inverted button">
      <i class="remove icon"></i>No
    </div>
    <div id="confirmDeleteBtn" class="ui green ok inverted button">
      <i class="checkmark icon"></i>Yes
    </div>
  </div>
</div>


</body>
</html>