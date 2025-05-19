<?php
// inclusão do banco de dados e estrutura base da página web
include_once './ConnectDB.php'; include_once './EstruturaPrincipal.php'; $_SESSION['posicao'] = 'Monitor de Produtos'; include_once './RastreadorAtividades.php';

//atribui usuário como responsável por registro de entrada do material ou cadastramento
$responsavel = $_SESSION['nome_func'];

?>
<script>
  // verifica inatividade da página e fecha sessão
  let inactivityTime = function () {
    let time; window.onload = resetTimer; document.onmousemove = resetTimer; document.onkeypress  = resetTimer;
    function deslogar() { <?php $_SESSION['posicao'] = 'Encerrado por inatividade'; include_once './RastreadorAtividades.php'; ?> window.location.href = 'LogOut.php'; }
    function resetTimer() { clearTimeout(time); time = setTimeout(deslogar, 69900000);  }
  }; inactivityTime();
</script>
<style> .tabela{ width: 100%; height: 480px; overflow-y: scroll;} </style>
<!-- Área Principal -->
<div class="main">
  <?php
    $sql0 = $connDB->prepare("SELECT * FROM historico_tempo WHERE NUMERO_PEDIDO > 0 AND ETAPA_PROCESS = 6");
    $sql0->execute(); $pedido = 0;
    while($rowTempo = $sql0->fetch(PDO::FETCH_ASSOC)){ $t = 0;
      $sql1 = $connDB->prepare("SELECT FABRICACAO FROM historico_tempo WHERE NUMERO_PEDIDO = 0 AND ID_PRODUTO = :nProd");
      $sql1->bindParam(':nProd', $rowTempo['ID_PRODUTO'], PDO::PARAM_INT); $sql1->execute();
      $rowF = $sql1->fetch(PDO::FETCH_ASSOC);

      $sql2 = $connDB->prepare("SELECT QTDE_PEDIDO FROM pedidos WHERE NUMERO_PEDIDO = :numPedido");
      $sql2->bindParam(':numPedido', $rowTempo['NUMERO_PEDIDO'], PDO::PARAM_INT); $sql2->execute();
      $numPedido = $sql2->fetch(PDO::FETCH_ASSOC); $capa = $numPedido['QTDE_PEDIDO'] / $rowF['FABRICACAO'];
      
      $a = new datetime($rowTempo['INICIO']);
      $b = new datetime($rowTempo['T_COMPRA']);
      $c = new datetime($rowTempo['T_RECEBE']);
      $d = new datetime($rowTempo['T_ANAMAT']);
      $e = new datetime($rowTempo['T_FABRI']);
      $f = new datetime($rowTempo['T_ANAPRO']);
      $g = new datetime($rowTempo['T_ENTREGA']);
      $compra = ($b->getTimestamp() - $a->getTimestamp()) / 60; $t = $t + $compra;
      $recebe = ($c->getTimestamp() - $b->getTimestamp()) / 60; $t = $t + $recebe;
      $anamat = ($d->getTimestamp() - $c->getTimestamp()) / 60; $t = $t + $anamat;
      $fabric = (($e->getTimestamp() - $d->getTimestamp()) / 60) / $capa; $t = $t + (($e->getTimestamp() - $d->getTimestamp()) / 60);
      $anapro = ($f->getTimestamp() - $e->getTimestamp()) / 60; $t = $t + $anapro;
      $entreg = ($g->getTimestamp() - $f->getTimestamp()) / 60; $t = $t + $entreg;
      echo '<br> Pedido: ' . $rowTempo['NUMERO_PEDIDO'] . ' ' . $compra . ' minutos ' . $recebe . ' minutos ' . $anamat . ' minutos ' . $fabric . ' minutos ' . $anapro . ' minutos ' . $entreg . ' minutos ';
      $sql3 = $connDB->prepare("UPDATE historico_tempo SET PEDIDO = :pedido, COMPRA = :compra, RECEBIMENTO = :recebe, ANALISE_MATERIAL = :anamat, FABRICACAO = :fabric,
                                       ANALISE_PRODUTO = :anapro, ENTREGA = :entreg, TOTAL = :totalT WHERE NUMERO_PEDIDO = :numPedido AND ETAPA_PROCESS = 6");
      $sql3->bindParam('numPedido', $rowTempo['NUMERO_PEDIDO'], PDO::PARAM_INT);
      $sql3->bindParam(':pedido'  , $pedido                   , PDO::PARAM_INT);
      $sql3->bindParam(':compra'  , $compra                   , PDO::PARAM_INT);
      $sql3->bindParam(':recebe'  , $recebe                   , PDO::PARAM_INT);
      $sql3->bindParam(':anamat'  , $anamat                   , PDO::PARAM_INT);
      $sql3->bindParam(':fabric'  , $fabric                   , PDO::PARAM_INT);
      $sql3->bindParam(':anapro'  , $anapro                   , PDO::PARAM_INT);
      $sql3->bindParam(':entreg'  , $entreg                   , PDO::PARAM_INT);
      $sql3->bindParam(':totalT'  , $t                        , PDO::PARAM_INT);
      $sql3->execute();
    }
  ?>
</div>