<?php
  // faz requisição da estrutura base da págima do sistema
  include_once './EstruturaPrincipal.php'; $_SESSION['posicao'] = 'Detalhes do Pedido'; include_once './RastreadorAtividades.php';
  //verifica o numero de pedido selecionado para mostrar detalhes
  if(!empty($_GET['id'])){ $id = $_GET['id'];
    $sql0 = $connDB->prepare("SELECT * FROM pedidos WHERE NUMERO_PEDIDO = :numPedido");
    $sql0->bindParam(':numPedido', $id, PDO::PARAM_INT); $sql0->execute();
    $rowPedido = $sql0->fetch(PDO::FETCH_ASSOC);

    $sql1 = $connDB->prepare("SELECT * FROM historico_tempo WHERE NUMERO_PEDIDO = :numPedido");
    $sql1->bindParam(':numPedido', $id, PDO::PARAM_INT); $sql1->execute();
    $rowHistorico = $sql1->fetch(PDO::FETCH_ASSOC);

    $sql2 = $connDB->prepare("SELECT * FROM historico_tempo WHERE NUMERO_PEDIDO = 0 AND ID_PRODUTO = :nProd");
    $sql2->bindParam(':nProd', $rowPedido['N_PRODUTO'], PDO::PARAM_INT); $sql2->execute();
    $rowBaseT = $sql2->fetch(PDO::FETCH_ASSOC);

    $sql3 = $connDB->prepare("SELECT CAPAC_PROCESS FROM produtos WHERE N_PRODUTO = :nProd");
    $sql3->bindParam(':nProd', $rowPedido['N_PRODUTO'], PDO::PARAM_INT); $sql3->execute();
    $rowProduto = $sql3->fetch(PDO::FETCH_ASSOC);
  }
?>
<script>
  // verifica inatividade da página e fecha sessão
  let inactivityTime = function () {
    let time; window.onload = resetTimer; document.onmousemove = resetTimer; document.onkeypress  = resetTimer;
    function deslogar() { <?php $_SESSION['posicao'] = 'Encerrado por inatividade'; include_once './RastreadorAtividades.php'; ?> window.location.href = 'LogOut.php'; }
    function resetTimer() { clearTimeout(time); time = setTimeout(deslogar, 69900000); }
  }; inactivityTime();
</script>
<div class="main"><br>
<p style="font-size: 20px; color: whitesmoke">Relatório de Produção de Pedido</p>
<button class="btn btn-primary" onclick="location.href='./MonitorProduto.php'">Voltar</button>
  <div class="row g-1">
    <div class="col-md-1">
        <label for="" class="form-label" style="font-size: 10px; color:aqua">Número do Pedido</label>
        <input style="font-weight: bold; font-size: 13px; background: rgba(0,0,0,0.3); text-align: center; width: 100px;" type="number" class="form-control" id="" name="" value="<?php echo $rowPedido['NUMERO_PEDIDO']; ?>" disabled>
    </div>
    <div class="col-md-1">
      <label for="" class="form-label" style="font-size: 10px; color:aqua">Número do Lote</label>
      <input style="font-weight: bold; font-size: 13px; background: rgba(0,0,0,0.3); text-align: center; width: 100px;" type="text" class="form-control" id="" name="" value="<?php echo $rowPedido['NUMERO_LOTE']; ?>" disabled>
    </div>
    <div class="col-md-5">
      <label for="" class="form-label" style="font-size: 10px; color:aqua">Produto</label>
      <input style="font-weight: bold; font-size: 13px; background: rgba(0,0,0,0.3);" type="text" class="form-control" id="" name="" value="<?php echo $rowPedido['PRODUTO']; ?>" disabled>
    </div>
    <div class="col-md-2">
      <label for="" class="form-label" style="font-size: 10px; color:aqua">Quantidade</label>
      <input style="font-weight: bold; font-size: 13px; background: rgba(0,0,0,0.3); text-align: right; width: 140px;" type="text" class="form-control" id="" name="" 
        value="<?php echo number_format($rowPedido['QTDE_PEDIDO'],2,',','.') . ' ' . $rowPedido['UNIDADE']; ?>" disabled>
    </div>
  </div><BR></BR>
  <div class="row g-1">
    <div class="col-md-6" >
      <p style="color:bisque;">Histórico Cronológico do Pedido</p>
      <br>
      <div class="row g-3">
        <div class="col-md-3" style="font-size: 11px;">
          <p style="text-align: center; border-bottom: solid 1px;">Etapa da Produção</p><br>
          <p style="color:aqua; text-align: right;">Pedido</p>
          <p style="color:aqua; text-align: right;">Compra de Materiais</p>
          <p style="color:aqua; text-align: right;">Recebimento dos Materiais</p>
          <p style="color:aqua; text-align: right;">Análise dos Materiais</p>
          <p style="color:aqua; text-align: right;">Fabricação do Produto</p>
          <p style="color:aqua; text-align: right;">Análise do Produto</p>
          <p style="color:aqua; text-align: right;">Entrega do Produto</p>
        </div>
        <div class="col-md-2" style="font-size: 11px;">
          <p style="text-align: center; border-bottom: solid 1px;">Data e Hora</p><br>
          <p><?php echo date('d/m/Y H:i', strtotime($rowHistorico['INICIO'])); ?></p>
          <p><?php echo date('d/m/Y H:i', strtotime($rowHistorico['T_COMPRA'])); ?></p>
          <p><?php echo date('d/m/Y H:i', strtotime($rowHistorico['T_RECEBE'])); ?></p>
          <p><?php echo date('d/m/Y H:i', strtotime($rowHistorico['T_ANAMAT'])); ?></p>
          <p><?php echo date('d/m/Y H:i', strtotime($rowHistorico['T_FABRI'])); ?></p>
          <p><?php echo date('d/m/Y H:i', strtotime($rowHistorico['T_ANAPRO'])); ?></p>
          <p><?php echo date('d/m/Y H:i', strtotime($rowHistorico['T_ENTREGA'])); ?></p>
        </div>
        <div class="col-md-2" style="font-size: 11px;">
          <p style="text-align: center; border-bottom: solid 1px;">T.Acumulado</p><br>
          <p style="text-align: right;"><?php echo 'inicio'?></p>
          <p style="text-align: right;"><?php $soma = $rowHistorico['PEDIDO'] + $rowHistorico['COMPRA']; echo number_format($soma,0,',','.') . ' minutos' ?></p>
          <p style="text-align: right;"><?php $soma = $soma + $rowHistorico['RECEBIMENTO']             ; echo number_format($soma,0,',','.') . ' minutos' ?></p>
          <p style="text-align: right;"><?php $soma = $soma + $rowHistorico['ANALISE_MATERIAL']        ; echo number_format($soma,0,',','.') . ' minutos' ?></p>
          <p style="text-align: right;"><?php $soma = $soma + $rowHistorico['FABRICACAO']              ; echo number_format($soma,0,',','.') . ' minutos' ?></p>
          <p style="text-align: right;"><?php $soma = $soma + $rowHistorico['ANALISE_PRODUTO']         ; echo number_format($soma,0,',','.') . ' minutos' ?></p>
          <p style="text-align: right;"><?php $soma = $soma + $rowHistorico['ENTREGA']                 ; echo number_format($soma,0,',','.') . ' minutos' ?></p>
        </div>
        <div class="col-md-2" style="font-size: 11px;">
          <p style="text-align: center; border-bottom: solid 1px;">Tempo Base</p><br>
          <p style="text-align: right;"><?php $soma = 0                                    ; echo number_format($soma,0,',','.') . ' minutos' ?></p>
          <p style="text-align: right;"><?php $soma = $soma + $rowBaseT['COMPRA']          ; echo number_format($soma,0,',','.') . ' minutos' ?></p>
          <p style="text-align: right;"><?php $soma = $soma + $rowBaseT['RECEBIMENTO']     ; echo number_format($soma,0,',','.') . ' minutos' ?></p>
          <p style="text-align: right;"><?php $soma = $soma + $rowBaseT['ANALISE_MATERIAL']; echo number_format($soma,0,',','.') . ' minutos' ?></p>
          <p style="text-align: right;"><?php $soma = $soma + (($rowPedido['QTDE_PEDIDO'] / $rowProduto['CAPAC_PROCESS']) * 60); echo number_format($soma,0,',','.') . ' minutos' ?></p>
          <p style="text-align: right;"><?php $soma = $soma + $rowBaseT['ANALISE_PRODUTO'] ; echo number_format($soma,0,',','.') . ' minutos' ?></p>
          <p style="text-align: right;"><?php $soma = $soma + $rowBaseT['ENTREGA']         ; echo number_format($soma,0,',','.') . ' minutos' ?></p>
        </div>
        <div class="col-md-2" style="font-size: 11px;">
          <p style="text-align: center; border-bottom: solid 1px;">Atraso</p><br>
          <p style="text-align: right;"><?php $soma = $rowBaseT['PEDIDO']; echo number_format($soma,0,',','.') . ' minutos' ?></p>
          <p style="text-align: right;"><?php $soma = $soma + (($rowHistorico['PEDIDO']           + $rowHistorico['COMPRA'])           - ($rowBaseT['PEDIDO']           + $rowBaseT['COMPRA']))          ; echo number_format($soma,0,',','.') . ' minutos' ?></p>
          <p style="text-align: right;"><?php $soma = $soma + (($rowHistorico['COMPRA']           + $rowHistorico['RECEBIMENTO'])      - ($rowBaseT['COMPRA']           + $rowBaseT['RECEBIMENTO']))     ; echo number_format($soma,0,',','.') . ' minutos' ?></p>
          <p style="text-align: right;"><?php $soma = $soma + (($rowHistorico['RECEBIMENTO']      + $rowHistorico['ANALISE_MATERIAL']) - ($rowBaseT['RECEBIMENTO']      + $rowBaseT['ANALISE_MATERIAL'])); echo number_format($soma,0,',','.') . ' minutos' ?></p>
          <p style="text-align: right;"><?php $soma = $soma + (($rowHistorico['ANALISE_MATERIAL'] + $rowHistorico['FABRICACAO'])       - ($rowBaseT['ANALISE_MATERIAL'] + $rowBaseT['FABRICACAO']))      ; echo number_format($soma,0,',','.') . ' minutos' ?></p>
          <p style="text-align: right;"><?php $soma = $soma + (($rowHistorico['FABRICACAO']       + $rowHistorico['ANALISE_PRODUTO'])  - ($rowBaseT['FABRICACAO']       + $rowBaseT['ANALISE_PRODUTO'])) ; echo number_format($soma,0,',','.') . ' minutos' ?></p>
          <p style="text-align: right;"><?php $soma = $soma + (($rowHistorico['ANALISE_PRODUTO']  + $rowHistorico['ENTREGA'])          - ($rowBaseT['ANALISE_PRODUTO']  + $rowBaseT['ENTREGA']))         ; echo number_format($soma,0,',','.') . ' minutos' ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      Aqui vai um gráfico
    </div>
  </div>
</div>


  