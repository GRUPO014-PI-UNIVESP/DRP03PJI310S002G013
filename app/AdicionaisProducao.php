<?php
// inclusão do banco de dados e estrutura base da página web
include_once './ConnectDB.php'; include_once './EstruturaPrincipal.php'; $_SESSION['posicao'] = 'Monitor de Produtos'; include_once './RastreadorAtividades.php';

//atribui usuário como responsável por registro de entrada do material ou cadastramento
$responsavel = $_SESSION['nome_func'];
$nPedido = '';  
 ?>
<!-- Área Principal -->
<div class="main">
  <br><p style="font-size: 20px; color: whitesmoke">INSERÇÃO DE DADOS OPERACIONAIS DA PRODUÇÃO</p><br>
  <form id="addProd" method="POST">
    <div class="row g-1">
      <div class="col-md-2">
        <label for="nPedido" class="form-label" style="font-size: 10px; color:aqua">Número do Pedido</label>
        <input style="font-weight: bold; font-size: 18px; background: rgba(0,0,0,0.3); text-align: center; width: 160px;" 
               type="number" class="form-control" id="nPedido" name="nPedido" value="" onclick="submeterFormulario()" required autofocus>
      </div>
    </div><br><br>   
  </form><?php
  try{
    $connDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if(isset($_POST['nPedido'])){
      $_SESSION['nPedido'] = $_POST['nPedido'];
      $sql0 = $connDB->prepare("SELECT NUMERO_PEDIDO FROM pedidos WHERE NUMERO_PEDIDO = :numPedido");
      $sql0->bindParam(':numPedido', $_SESSION['nPedido'], PDO::PARAM_INT); $sql0->execute();
      $rowPedido = $sql0->fetch(PDO::FETCH_ASSOC); $contador =  $sql0->rowCount();
      if($contador > 0){
        header('Location: ./AdicionaisProducao2.php');
      } else { ?><button class="btn btn-warning" onclick="location.href='./AdicionaisProducao.php'">Número de Pedido inexistente! Clique e tente novamente.</button> <?php }
    }
  }
  catch(PDOException $e){
    echo 'Ocorreu algum problema durante a criação da tabela, comunique o responsável do TI.' . $e; ?>
    <div>
      <br><br>
      <button class="btn btn-danger" onclick="location.href='./ConfigurarEstrutura.php'">Reiniciar</button>
    </div><?php
  }
?>
</div>
<script>
  // verifica inatividade da página e fecha sessão
  let inactivityTime = function () {
    let time; window.onload = resetTimer; document.onmousemove = resetTimer; document.onkeypress  = resetTimer;
    function deslogar() { <?php $_SESSION['posicao'] = 'Encerrado por inatividade'; include_once './RastreadorAtividades.php'; ?> window.location.href = 'LogOut.php'; }
    function resetTimer() { clearTimeout(time); time = setTimeout(deslogar, 69900000);  }
  }; inactivityTime();
  // atribui função de enviar formulário
  function submeterFormulario() { document.getElementById("enviar").submit(); }
</script>
<style>  .tabela{ width: 100%; height: 480px; overflow-y: scroll;} </style>
  