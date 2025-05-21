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
    function deslogar() { <?php $_SESSION['posicao'] = 'Encerrado por inatividade'; include_once './RastreadorAtividades.php'; ?>
      window.location.href = 'LogOut.php';
     }
    function resetTimer() { clearTimeout(time); time = setTimeout(deslogar, 69900000);  }
  }; inactivityTime();
</script>
<style> .tabela{ width: 100%; height: 680px; overflow-y: scroll;} </style>
<!-- Área Principal -->
<div class="main">
  <div class="row g-1">
    <div class="col md-6">
      <br>
      <p style="font-size: 25px; color:cyan">Monitor da Execução dos Pedidos</p>
    </div>
    <div class="col md-6" style="text-align:center;">
      <br>
      <img src="./legenda de cores.jpg" />
    </div>
  </div>
  <div class="tabela">
    <table class="table table-dark table-hover">
      <thead style="font-size: 10px">
        <tr>
          <th scope="col" style="width: 5%; text-align:right"><?php echo 'Data' . '<br>' . 'Pedido No.'; ?></th>
          <th scope="col" style="width: 20%"><?php echo 'Produto' . '<br>' . 'Cliente'; ?></th>
          <th scope="col" style="width: 5%; text-align:right"><?php echo 'Quantidade' . '<br>' . 'Entrega'; ?></th>
          <th scope="col" style="width: 60%; text-align: center">Progresso</th>
          <th scope="col" style="width: 10%; text-align: center">Detalhes</th>
        </tr>
      </thead>
      <?php $buscaLinhaTempo = $connDB->prepare("SELECT * FROM historico_tempo WHERE ETAPA_PROCESS < 7 AND NUMERO_PEDIDO > 0 ORDER BY NUMERO_PEDIDO DESC"); $buscaLinhaTempo->execute(); ?>
      <tbody style="height: 75%; font-size: 11px;"><?php $clear1 = ''; $clear2 = ''; $clear3 = ''; $clear4 = ''; $clear5 = ''; $clear6 = ''; 
        while($rowTempo = $buscaLinhaTempo->fetch(PDO::FETCH_ASSOC)){

          //busca registro do tempo de referência da atividade do produto
          $buscaReferencia = $connDB->prepare("SELECT * FROM historico_tempo WHERE ID_PRODUTO = :idProd AND NUMERO_PEDIDO = 0");
          $buscaReferencia->bindParam('idProd', $rowTempo['ID_PRODUTO'], PDO::PARAM_INT);
          $buscaReferencia->execute(); $rowReferencia = $buscaReferencia->fetch(PDO::FETCH_ASSOC);

          if($rowTempo['T_COMPRA'] != null || $rowTempo['T_COMPRA'] > 0 ){
            if($rowTempo['COMPRA'] <= $rowReferencia['COMPRA']){ $clear1 = 'font-size:12px; text-align:center; color:black; background-color:limegreen;';}
            if($rowTempo['COMPRA'] > $rowReferencia['COMPRA']) { $clear1 = 'font-size:12px; text-align:center; color:black; background-color:orange;'   ;}
          }
          if($rowTempo['RECEBIMENTO'] != null || $rowTempo['RECEBIMENTO'] > 0 ){
            if($rowTempo['RECEBIMENTO'] <= $rowReferencia['RECEBIMENTO']){ $clear2 = 'font-size:12px; text-align:center; color:black; background-color:limegreen;';}
            if($rowTempo['RECEBIMENTO'] > $rowReferencia['RECEBIMENTO']) { $clear2 = 'font-size:12px; text-align:center; color:black; background-color:orange;'   ;}
          }
          if($rowTempo['ANALISE_MATERIAL'] != null || $rowTempo['ANALISE_MATERIAL'] > 0 ){
            if($rowTempo['ANALISE_MATERIAL'] <= $rowReferencia['ANALISE_MATERIAL']){ $clear3 = 'font-size:12px; text-align:center; color:black; background-color:limegreen;';}
            if($rowTempo['ANALISE_MATERIAL'] > $rowReferencia['ANALISE_MATERIAL'] ){ $clear3 = 'font-size:12px; text-align:center; color:black; background-color:orange;'   ;}
          }
          if($rowTempo['FABRICACAO'] != null || $rowTempo['FABRICACAO'] > 0 ){
            if($rowTempo['FABRICACAO'] <= $rowReferencia['FABRICACAO']){ $clear4 = 'font-size:12px; text-align:center; color:black; background-color:limegreen;';}
            if($rowTempo['FABRICACAO'] > $rowReferencia['FABRICACAO']) { $clear4 = 'font-size:12px; text-align:center; color:black; background-color:orange;'   ;}
          } 
          if($rowTempo['ANALISE_PRODUTO'] != null || $rowTempo['ANALISE_PRODUTO'] > 0 ){
            if($rowTempo['ANALISE_PRODUTO'] <= $rowReferencia['ANALISE_PRODUTO']){ $clear5 = 'font-size:12px; text-align:center; color:black; background-color:limegreen;';}
            if($rowTempo['ANALISE_PRODUTO'] > $rowReferencia['ANALISE_PRODUTO']) { $clear5 = 'font-size:12px; text-align:center; color:black; background-color:orange;'   ;}
          }
          if($rowTempo['ENTREGA'] != null || $rowTempo['ENTREGA'] > 0 ){
            if($rowTempo['ENTREGA'] <= $rowReferencia['ENTREGA']){ $clear6 = 'font-size:12px; text-align:center; color:black; background-color:limegreen;';}
            if($rowTempo['ENTREGA'] > $rowReferencia['ENTREGA']) { $clear6 = 'font-size:12px; text-align:center; color:black; background-color:orange;'   ;}
          }  
          $exec  = 'font-size:12px; text-align:center; color:black     ; background-color:dodgerblue    ;';
          $wait  = 'font-size:12px; text-align:center; color:whitesmoke; background-color:lightslategrey;';

          if($rowTempo['ETAPA_PROCESS'] == 0){ $a = $exec  ; $b = $wait  ; $c = $wait  ; $d = $wait  ; $e = $wait  ; $f = $wait  ; }
          if($rowTempo['ETAPA_PROCESS'] == 1){ $a = $clear1; $b = $exec  ; $c = $wait  ; $d = $wait  ; $e = $wait  ; $f = $wait  ; } 
          if($rowTempo['ETAPA_PROCESS'] == 2){ $a = $clear1; $b = $clear2; $c = $exec  ; $d = $wait  ; $e = $wait  ; $f = $wait  ; } 
          if($rowTempo['ETAPA_PROCESS'] == 3){ $a = $clear1; $b = $clear2; $c = $clear3; $d = $exec  ; $e = $wait  ; $f = $wait  ; }
          if($rowTempo['ETAPA_PROCESS'] == 4){ $a = $clear1; $b = $clear2; $c = $clear3; $d = $clear4; $e = $exec  ; $f = $wait  ; }
          if($rowTempo['ETAPA_PROCESS'] == 5){ $a = $clear1; $b = $clear2; $c = $clear3; $d = $clear4; $e = $clear5; $f = $exec  ; }
          if($rowTempo['ETAPA_PROCESS'] == 6){ $a = $clear1; $b = $clear2; $c = $clear3; $d = $clear4; $e = $clear5; $f = $clear6; } ?>
          <tr>
            <td scope="col" style="width: 5%; text-align:right;"><?php
            $buscaPedido = $connDB->prepare("SELECT * FROM pedidos WHERE NUMERO_PEDIDO = :numPedido");
            $buscaPedido->bindParam(':numPedido', $rowTempo['NUMERO_PEDIDO'], PDO::PARAM_INT);
            $buscaPedido->execute(); $rowPedido = $buscaPedido->fetch(PDO::FETCH_ASSOC); 
            echo '<br>' . date('d/m/Y', strtotime($rowTempo['INICIO'])) . '<br>' . $rowPedido['NUMERO_PEDIDO'] ; ?></td>

            <td scope="col" style="width: 20%;"><?php echo '<br>' . $rowPedido['PRODUTO'] . '<br>' . $rowPedido['CLIENTE']; ?></td>

            <td scope="col" style="width: 5%; text-align:right;"><?php 
              echo '<br>' . number_format($rowPedido['QTDE_PEDIDO'],0,',','.') . ' '    . $rowPedido['UNIDADE'] . 
              '<br>' . date('d/m/Y',strtotime($rowPedido['DATA_ENTREGA'])); ?></td>

            <td scope="col" style="width: 60%;">
              <div class="row g-2">
                <div class="col md-6" style="font-size: 11px; color:aqua;">Matéria Prima</div>
                <div class="col md-6" style="font-size: 11px; color:aqua;">Produto Final</div>
              </div>
              <div class="row g-0">
                <div class="col md-2" style="<?php echo $a ?>;border: 1px solid white;"><?php echo 'COMPRA'     . '<br>' . 'Administrativo' ?></div>
                <div class="col md-2" style="<?php echo $b ?>;border: 1px solid white;"><?php echo 'RECEBIDA'   . '<br>' . 'Logística' ?></div>
                <div class="col md-2" style="<?php echo $c ?>;border: 1px solid white;"><?php echo 'ANÁLISE'    . '<br>' . 'Ctl.Quali' ?></div>
                <div class="col md-2" style="<?php echo $d ?>;border: 1px solid white;"><?php echo 'FABRICAÇÃO' . '<br>' . 'Produção' ?></div>
                <div class="col md-2" style="<?php echo $e ?>;border: 1px solid white;"><?php echo 'ANÁLISE'    . '<br>' . 'Ctl.Quali' ?></div>
                <div class="col md-2" style="<?php echo $f ?>;border: 1px solid white;"><?php echo 'ENTREGA'    . '<br>' . 'Logística' ?></div>
              </div>
            </td>
            
            <td scope="col" style="width: 10%;">
              <br><?php if($rowPedido['ETAPA_PROCESS'] >= 6){ $desabilita = 'enabled';} else { $desabilita = 'disabled';}  ?>
              <button class="btn btn-outline-primary" onclick="location.href='./DetalhesPedido.php?id=<?php echo $rowPedido['NUMERO_PEDIDO'] ?>'" <?php echo $desabilita ?>>Detalhes</button>
            </td>
          </tr><?php
        } ?>                    
      </tbody>
    </table>
  </div>
</div>