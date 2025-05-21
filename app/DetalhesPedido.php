<?php
  // faz requisição da estrutura base da págima do sistema
  include_once './EstruturaPrincipal.php'; $_SESSION['posicao'] = 'Detalhes do Pedido'; include_once './RastreadorAtividades.php';
  //verifica o numero de pedido selecionado para mostrar detalhes
  if(!empty($_GET['id'])){ $id = $_GET['id']; $pedi = array(); $comp = array(); $rece = array(); $anaM = array(); $fabr = array(); $anaP = array(); $entr = array(); $tota = array();
    $sql0 = $connDB->prepare("SELECT * FROM pedidos WHERE NUMERO_PEDIDO = :numPedido"); $sql0->bindParam(':numPedido', $id, PDO::PARAM_INT); $sql0->execute(); $rowPedido = $sql0->fetch(PDO::FETCH_ASSOC); $baseFabr = (($rowPedido['QTDE_PEDIDO'] / $rowPedido['CAPAC_PROCESS']) * 60);
    $sql1 = $connDB->prepare("SELECT * FROM historico_tempo WHERE NUMERO_PEDIDO = :numPedido"); $sql1->bindParam(':numPedido', $id, PDO::PARAM_INT); $sql1->execute(); $rowHistorico = $sql1->fetch(PDO::FETCH_ASSOC);
    $sql2 = $connDB->prepare("SELECT * FROM historico_tempo WHERE NUMERO_PEDIDO = 0 AND ID_PRODUTO = :nProd"); $sql2->bindParam(':nProd', $rowPedido['N_PRODUTO'], PDO::PARAM_INT); $sql2->execute(); $rowBaseT = $sql2->fetch(PDO::FETCH_ASSOC);
    $sql3 = $connDB->prepare("SELECT CAPAC_PROCESS FROM produtos WHERE N_PRODUTO = :nProd"); $sql3->bindParam(':nProd', $rowPedido['N_PRODUTO'], PDO::PARAM_INT); $sql3->execute(); $rowProduto = $sql3->fetch(PDO::FETCH_ASSOC);
    $col1 =0; $exc1 = 0;
    $col2 = $rowBaseT['COMPRA'] / 60          ; $exc2 = ($rowHistorico['COMPRA']           - $rowBaseT['COMPRA']) / 60          ; if($exc2 <= 0){$exc2 = 0;}
    $col3 = $rowBaseT['RECEBIMENTO'] / 60     ; $exc3 = ($rowHistorico['RECEBIMENTO']      - $rowBaseT['RECEBIMENTO']) / 60     ; if($exc3 <= 0){$exc3 = 0;} 
    $col4 = $rowBaseT['ANALISE_MATERIAL'] / 60; $exc4 = ($rowHistorico['ANALISE_MATERIAL'] - $rowBaseT['ANALISE_MATERIAL']) / 60; if($exc4 <= 0){$exc4 = 0;} 
    $col5 = $baseFabr / 60                    ; $exc5 = ($rowHistorico['FABRICACAO']       - $baseFabr) / 60                    ; if($exc5 <= 0){$exc5 = 0;}
    $col6 = $rowBaseT['ANALISE_PRODUTO'] / 60 ; $exc6 = ($rowHistorico['ANALISE_PRODUTO']  - $rowBaseT['ANALISE_PRODUTO']) / 60 ; if($exc6 <= 0){$exc6 = 0;}
    $col7 = $rowBaseT['ENTREGA'] / 60         ; $exc7 = ($rowHistorico['ENTREGA']          - $rowBaseT['ENTREGA']) / 60         ; if($exc7 <= 0){$exc7 = 0;}

    $acu1 = 0; $exc11 = 0;
    $acu2 = $acu1 + $rowBaseT['COMPRA']          ; $exc22 = $rowHistorico['COMPRA']           - $rowBaseT['COMPRA']          ; if($exc22 <= 0){$exc22 = 0;}
    $acu3 = $acu2 + $rowBaseT['RECEBIMENTO']     ; $exc33 = $rowHistorico['RECEBIMENTO']      - $rowBaseT['RECEBIMENTO']     ; if($exc33 <= 0){$exc33 = 0;}
    $acu4 = $acu3 + $rowBaseT['ANALISE_MATERIAL']; $exc44 = $rowHistorico['ANALISE_MATERIAL'] - $rowBaseT['ANALISE_MATERIAL']; if($exc44 <= 0){$exc44 = 0;}
    $acu5 = $acu4 + $baseFabr                    ; $exc55 = $rowHistorico['FABRICACAO']       - $baseFabr                    ; if($exc55 <= 0){$exc55 = 0;}
    $acu6 = $acu5 + $rowBaseT['ANALISE_PRODUTO'] ; $exc66 = $rowHistorico['ANALISE_PRODUTO']  - $rowBaseT['ANALISE_PRODUTO'] ; if($exc66 <= 0){$exc66 = 0;}
    $acu7 = $acu6 + $rowBaseT['ENTREGA']         ; $exc77 = $rowHistorico['ENTREGA']          - $rowBaseT['ENTREGA']         ; if($exc77 <= 0){$exc77 = 0;}
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
 <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load("current", {packages:['corechart']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart(){
      // primeiro gráfico com valores de cada processo
      var data1 = google.visualization.arrayToDataTable([
        ["Processo", "Custo em horas", "Tempo Excedente", { role: "style" }],
        ['Início'                   , <?php echo $col1 ?>, <?php echo $exc1 ?>, ""],
        ['Compra dos Materiais'     , <?php echo $col2 ?>, <?php echo $exc2 ?>, ""],
        ['Recebimento dos Materiais', <?php echo $col3 ?>, <?php echo $exc3 ?>, ""],
        ['Análise dos Materiais'    , <?php echo $col4 ?>, <?php echo $exc4 ?>, ""],
        ['Fabricação do Produto'    , <?php echo $col5 ?>, <?php echo $exc5 ?>, ""],
        ['Análise dos Produto'      , <?php echo $col6 ?>, <?php echo $exc6 ?>, ""],
        ['Entrega do Produto'       , <?php echo $col7 ?>, <?php echo $exc7 ?>, ""]
      ]);
      var view1 = new google.visualization.DataView(data1);
      view1.setColumns([0, 1, { calc: "stringify", sourceColumn: 1, type: "string", role: "annotation" }, 2]);
      var options1 = { title: "Histórico cronológico do pedido (Horas)", backgroundColor: 'slategray', width: 1180, height: 300, bar: {groupWidth: "98%"}, legend: {position: 'top'}, isStacked: true, colors: ['green', 'red'] };
      var chart = new google.visualization.ColumnChart(document.getElementById("custoEtapa")); chart.draw(view1, options1);

      // segundo grafico com valores acumulados
      var data2 = google.visualization.arrayToDataTable([
        ["Processo", "Custo em minutos", "Tempo Excedente", { role: "style" }],
        ['Início'                   , <?php echo $acu1 ?>, <?php echo $exc11 ?>, ""],
        ['Compra dos Materiais'     , <?php echo $acu2 ?>, <?php echo $exc22 ?>, ""],
        ['Recebimento dos Materiais', <?php echo $acu3 ?>, <?php echo $exc33 ?>, ""],
        ['Análise dos Materiais'    , <?php echo $acu4 ?>, <?php echo $exc44 ?>, ""],
        ['Fabricação do Produto'    , <?php echo $acu5 ?>, <?php echo $exc55 ?>, ""],
        ['Análise dos Produto'      , <?php echo $acu6 ?>, <?php echo $exc66 ?>, ""],
        ['Entrega do Produto'       , <?php echo $acu7 ?>, <?php echo $exc77 ?>, ""]
      ]);
      var view2 = new google.visualization.DataView(data2);
      view2.setColumns([0, 1, { calc: "stringify", sourceColumn: 1, type: "string", role: "annotation" }, 2]);
      var options2 = { title: "Histórico cronológico do pedido (acumulado em Minutos)", backgroundColor: 'slategray', width: 1180, height: 300, bar: {groupWidth: "100%"}, legend: {position: 'top'}, isStacked: true, colors: ['green', 'red'] };
      var chart = new google.visualization.ColumnChart(document.getElementById("acumulado")); chart.draw(view2, options2);
    } 
  </script>
<div class="main"><br>
  <div class="row g-2">
    <div class="col-md-6">
      <p style="font-size: 20px; color:whitesmoke">Relatório de Produção de Pedido</p>
    </div>
    <div class="col-md-6">
      <button class="btn btn-primary" style="float:inline-end" onclick="location.href='./MonitorProduto.php'">Voltar para Monitor</button>
    </div>
  </div>
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
  </div><br>
  <div class="row g-1">
    <div class="col-md-11" >
      <p style="color:bisque;">Histórico Cronológico do Pedido</p>
      <div class="tabela">
        <table class="table table-dark table-hover table-bordered">
          <thead style="font-size: 10px">
            <tr>
              <th scope="col" style="text-align: right">Tipo de Dado</th>
              <th scope="col" style="text-align: right">Pedido</th>
              <th scope="col" style="text-align: right">Compra dos Materiais</th>
              <th scope="col" style="text-align: right">Recebimento dos Materiais</th>
              <th scope="col" style="text-align: right">Análise dos Materiais</th>
              <th scope="col" style="text-align: right">Fabricação do Produto</th>
              <th scope="col" style="text-align: right">Análise do Produto</th>
              <th scope="col" style="text-align: right">Entrega do Produto</th>
            </tr>
          </thead>
          <tbody style="font-size: 12px;">
            <tr style="font-weight:bold; color:yellow">
              <td scope="col" style="text-align: right"><?php echo 'Data e Hora' . '<br>' . 'da Execução'; ?></td>
              <td scope="col" style="text-align: right"><?php echo date('d/m/Y', strtotime($rowHistorico['INICIO']))
                . '<br>' . date('H:i', strtotime($rowHistorico['INICIO'])) ?></td>
              <td scope="col" style="text-align: right"><?php echo date('d/m/Y', strtotime($rowHistorico['T_COMPRA']))
                . '<br>' . date('H:i', strtotime($rowHistorico['T_COMPRA'])) ?></td>
              <td scope="col" style="text-align: right"><?php echo date('d/m/Y', strtotime($rowHistorico['T_RECEBE']))
                . '<br>' . date('H:i', strtotime($rowHistorico['T_RECEBE'])) ?></td>
              <td scope="col" style="text-align: right"><?php echo date('d/m/Y', strtotime($rowHistorico['T_ANAMAT']))
                . '<br>' . date('H:i', strtotime($rowHistorico['T_ANAMAT'])) ?></td>
              <td scope="col" style="text-align: right"><?php echo date('d/m/Y', strtotime($rowHistorico['T_FABRI']))
                . '<br>' . date('H:i', strtotime($rowHistorico['T_FABRI'])) ?></td>
              <td scope="col" style="text-align: right"><?php echo date('d/m/Y', strtotime($rowHistorico['T_ANAPRO']))
                . '<br>' . date('H:i', strtotime($rowHistorico['T_ANAPRO'])) ?></td>
              <td scope="col" style="text-align: right"><?php echo date('d/m/Y', strtotime($rowHistorico['T_ENTREGA']))
                . '<br>' . date('H:i', strtotime($rowHistorico['T_ENTREGA'])) ?></td>
            </tr>
            <tr>
              <td scope="col" style="text-align: right">Base Estimada</td>
              <td scope="col" style="text-align: right"><?php echo number_format($rowBaseT['PEDIDO'], 0, ',', '.') . ' minutos' ?></td>
              <td scope="col" style="text-align: right"><?php echo number_format($rowBaseT['COMPRA'], 0, ',', '.') . ' minutos' ?></td>
              <td scope="col" style="text-align: right"><?php echo number_format($rowBaseT['RECEBIMENTO'], 0, ',', '.') . ' minutos' ?></td>
              <td scope="col" style="text-align: right"><?php echo number_format($rowBaseT['ANALISE_MATERIAL'], 0, ',', '.') . ' minutos' ?></td>
              <td scope="col" style="text-align: right"><?php $f = (($rowPedido['QTDE_PEDIDO'] / $rowPedido['CAPAC_PROCESS']) * 60);
                echo number_format($f, 0, ',', '.') . ' minutos' ?></td>
              <td scope="col" style="text-align: right"><?php echo number_format($rowBaseT['ANALISE_PRODUTO'], 0, ',', '.') . ' minutos' ?></td>
              <td scope="col" style="text-align: right"><?php echo number_format($rowBaseT['ENTREGA'], 0, ',', '.') . ' minutos' ?></td>
            </tr>
            <tr style="font-weight:bold; color:yellow">
              <td scope="col" style="text-align: right">Custo po Etapa</td>
              <td scope="col" style="text-align: right"><?php echo number_format($rowHistorico['PEDIDO'], 0, ',', '.') . ' minutos' ?></td>
              <td scope="col" style="text-align: right"><?php echo number_format($rowHistorico['COMPRA'], 0, ',', '.') . ' minutos' ?></td>
              <td scope="col" style="text-align: right"><?php echo number_format($rowHistorico['RECEBIMENTO'], 0, ',', '.') . ' minutos' ?></td>
              <td scope="col" style="text-align: right"><?php echo number_format($rowHistorico['ANALISE_MATERIAL'], 0, ',', '.') . ' minutos' ?></td>
              <td scope="col" style="text-align: right"><?php echo number_format($rowHistorico['FABRICACAO'], 0, ',', '.') . ' minutos' ?></td>
              <td scope="col" style="text-align: right"><?php echo number_format($rowHistorico['ANALISE_PRODUTO'], 0, ',', '.') . ' minutos' ?></td>
              <td scope="col" style="text-align: right"><?php echo number_format($rowHistorico['ENTREGA'], 0, ',', '.') . ' minutos' ?></td>
            </tr>    
            <tr  style="font-weight:bold;">
              <td scope="col" style="text-align: right">Diferença por Etapa</td>
              <?php $dif = $rowHistorico['PEDIDO'] - $rowBaseT['PEDIDO'];
                if($dif <= 0){ ?>
                  <td scope="col" style="text-align: right; color:limegreen;"><?php 
                    echo number_format($dif, 0, ',', '.') . ' minutos' ?></td><?php 
                } else { ?>
                  <td scope="col" style="text-align: right; color:red;"><?php 
                    echo number_format($dif, 0, ',', '.') . ' minutos' ?></td>
                   <?php 
                }
              ?>
              <?php $dif = $rowHistorico['COMPRA'] - $rowBaseT['COMPRA'];
                if($dif < 0){ ?>
                  <td scope="col" style="text-align: right; color:limegreen;"><?php 
                    echo number_format($dif, 0, ',', '.') . ' minutos' ?></td><?php 
                } else { ?>
                  <td scope="col" style="text-align: right; color:red;"><?php 
                    echo number_format($dif, 0, ',', '.') . ' minutos' ?></td>
                   <?php 
                }
              ?>
              <?php $dif = $rowHistorico['RECEBIMENTO'] - $rowBaseT['RECEBIMENTO'];
                if($dif < 0){ ?>
                  <td scope="col" style="text-align: right; color:limegreen;"><?php 
                    echo number_format($dif, 0, ',', '.') . ' minutos' ?></td><?php 
                } else { ?>
                  <td scope="col" style="text-align: right; color:red;"><?php 
                    echo number_format($dif, 0, ',', '.') . ' minutos' ?></td>
                   <?php 
                }
              ?>
              <?php $dif = $rowHistorico['ANALISE_MATERIAL'] - $rowBaseT['ANALISE_MATERIAL'];
                if($dif < 0){ ?>
                  <td scope="col" style="text-align: right; color:limegreen;"><?php 
                    echo number_format($dif, 0, ',', '.') . ' minutos' ?></td><?php 
                } else { ?>
                  <td scope="col" style="text-align: right; color:red;"><?php 
                    echo number_format($dif, 0, ',', '.') . ' minutos' ?></td>
                   <?php 
                }
              ?>
              <?php $dif = $rowHistorico['FABRICACAO'] - (($rowPedido['QTDE_PEDIDO'] / $rowPedido['CAPAC_PROCESS']) * 60);
                if($dif < 0){ ?>
                  <td scope="col" style="text-align: right; color:limegreen;"><?php 
                    echo number_format($dif, 0, ',', '.') . ' minutos' ?></td><?php 
                } else { ?>
                  <td scope="col" style="text-align: right; color:red;"><?php 
                    echo number_format($dif, 0, ',', '.') . ' minutos' ?></td>
                   <?php 
                }
              ?>
              <?php $dif = $rowHistorico['ANALISE_PRODUTO'] - $rowBaseT['ANALISE_PRODUTO'];
                if($dif < 0){ ?>
                  <td scope="col" style="text-align: right; color:limegreen;"><?php 
                    echo number_format($dif, 0, ',', '.') . 'minutos' ?></td><?php 
                } else { ?>
                  <td scope="col" style="text-align: right; color:red;"><?php 
                    echo number_format($dif, 0, ',', '.') . 'minutos' ?></td>
                   <?php 
                }
              ?>
              <?php $dif = $rowHistorico['ENTREGA'] - $rowBaseT['ENTREGA'];
                if($dif < 0){ ?>
                  <td scope="col" style="text-align: right; color:limegreen;"><?php 
                    echo number_format($dif, 0, ',', '.') . 'minutos' ?></td><?php 
                } else { ?>
                  <td scope="col" style="text-align: right; color:red;"><?php 
                    echo number_format($dif, 0, ',', '.') . 'minutos' ?></td>
                   <?php 
                }
              ?>
            </tr>
            <tr style="font-weight:bold; color:yellow">
              <td scope="col" style="text-align: right">Custo Acumulado</td><?php $acumulado = 0; ?>
              <td scope="col" style="text-align: right"><?php $acumulado1 = $acumulado + $rowHistorico['PEDIDO'];
                echo number_format($acumulado1, 0, ',', '.') . ' minutos' ?></td>
              <td scope="col" style="text-align: right"><?php $acumulado2 = $acumulado1 + $rowHistorico['COMPRA'];
                echo number_format($acumulado2, 0, ',', '.') . ' minutos' ?></td>
              <td scope="col" style="text-align: right"><?php $acumulado3 = $acumulado2 + $rowHistorico['RECEBIMENTO'];
                echo number_format($acumulado3, 0, ',', '.') . ' minutos' ?></td>
              <td scope="col" style="text-align: right"><?php $acumulado4 = $acumulado3 + $rowHistorico['ANALISE_MATERIAL'];
                echo number_format($acumulado4, 0, ',', '.') . ' minutos' ?></td>
              <td scope="col" style="text-align: right"><?php $acumulado5 = $acumulado4 + $rowHistorico['FABRICACAO'];
                echo number_format($acumulado5, 0, ',', '.') . ' minutos' ?></td>
              <td scope="col" style="text-align: right"><?php $acumulado6 = $acumulado5 + $rowHistorico['ANALISE_PRODUTO'];
                echo number_format($acumulado6, 0, ',', '.') . ' minutos' ?></td>
              <td scope="col" style="text-align: right"><?php $acumulado7 = $acumulado6 + $rowHistorico['ENTREGA'];
                echo number_format($acumulado7, 0, ',', '.') . ' minutos' ?></td>
            </tr>
            <tr>
              <td scope="col" style="text-align: right">Estimativa Acumulada</td><?php $acumulado = 0; ?>
              <td scope="col" style="text-align: right"><?php $acumulado11 = $acumulado + $rowBaseT['PEDIDO'];
                echo number_format($acumulado11, 0, ',', '.') . ' minutos' ?></td>
              <td scope="col" style="text-align: right"><?php $acumulado22 = $acumulado11 + $rowBaseT['COMPRA'];
                echo number_format($acumulado22, 0, ',', '.') . ' minutos' ?></td>
              <td scope="col" style="text-align: right"><?php $acumulado33 = $acumulado22 + $rowBaseT['RECEBIMENTO'];
                echo number_format($acumulado33, 0, ',', '.') . ' minutos' ?></td>
              <td scope="col" style="text-align: right"><?php $acumulado44 = $acumulado33 + $rowBaseT['ANALISE_MATERIAL'];
                echo number_format($acumulado44, 0, ',', '.') . ' minutos' ?></td>
              <td scope="col" style="text-align: right"><?php $acumulado55 = $acumulado44 + (($rowPedido['QTDE_PEDIDO'] / $rowPedido['CAPAC_PROCESS']) * 60);
                echo number_format($acumulado55, 0, ',', '.') . ' minutos' ?></td>
              <td scope="col" style="text-align: right"><?php $acumulado66 = $acumulado55 + $rowBaseT['ANALISE_PRODUTO'];
                echo number_format($acumulado66, 0, ',', '.') . ' minutos' ?></td>
              <td scope="col" style="text-align: right"><?php $acumulado77 = $acumulado66 + $rowBaseT['ENTREGA'];
                echo number_format($acumulado77, 0, ',', '.') . ' minutos' ?></td>
            </tr>
            <tr style="font-weight:bold;">
              <td scope="col" style="text-align: right">Diferença Acumulada</td><?php $estimativa = 0; 
                $estimativa1 = $estimativa + $rowBaseT['PEDIDO'];
                $dif1 = $acumulado1 - $estimativa1;
                if($dif1 <= 0){ ?> <td scope="col" style="text-align: right; color:limegreen"><?php echo number_format($dif1,0,',','.') . ' minutos'; ?></td><?php } 
                if($dif1 > 0){ ?> <td scope="col" style="text-align: right; color:red"><?php echo number_format($dif1,0,',','.') . ' minutos'; ?></td><?php }

                $estimativa2 = $estimativa1 + $rowBaseT['COMPRA'];
                $dif2 = $acumulado2 - $estimativa2;
                if($dif2 <= 0){ ?> <td scope="col" style="text-align: right; color:limegreen"><?php echo number_format($dif2,0,',','.') . ' minutos'; ?></td><?php } 
                if($dif2 > 0){ ?> <td scope="col" style="text-align: right; color:red"><?php echo number_format($dif2,0,',','.') . ' minutos'; ?></td><?php }

                $estimativa3 = $estimativa2 + $rowBaseT['RECEBIMENTO'];
                $dif3 = $acumulado3 - $estimativa3;
                if($dif3 <= 0){ ?> <td scope="col" style="text-align: right; color:limegreen"><?php echo number_format($dif3,0,',','.') . ' minutos'; ?></td><?php } 
                if($dif3 > 0){ ?> <td scope="col" style="text-align: right; color:red"><?php echo number_format($dif3,0,',','.') . ' minutos'; ?></td><?php }

                $estimativa4 = $estimativa3 + $rowBaseT['ANALISE_MATERIAL'];
                $dif4 = $acumulado4 - $estimativa4;
                if($dif4 <= 0){ ?> <td scope="col" style="text-align: right; color:limegreen"><?php echo number_format($dif4,0,',','.') . ' minutos'; ?></td><?php } 
                if($dif4 > 0){ ?> <td scope="col" style="text-align: right; color:red"><?php echo number_format($dif4,0,',','.') . ' minutos'; ?></td><?php }

                $estimativa5 = $estimativa4 + (($rowPedido['QTDE_PEDIDO'] / $rowPedido['CAPAC_PROCESS']) * 60);
                $dif5 = $acumulado5 - $estimativa5;
                if($dif5 <= 0){ ?> <td scope="col" style="text-align: right; color:limegreen"><?php echo number_format($dif5,0,',','.') . ' minutos'; ?></td><?php } 
                if($dif5 > 0){ ?> <td scope="col" style="text-align: right; color:red"><?php echo number_format($dif5,0,',','.') . ' minutos'; ?></td><?php }

                $estimativa6 = $estimativa5 + $rowBaseT['ANALISE_PRODUTO'];
                $dif6 = $acumulado6 - $estimativa6;
                if($dif6 <= 0){ ?> <td scope="col" style="text-align: right; color:limegreen"><?php echo number_format($dif6,0,',','.') . ' minutos'; ?></td><?php } 
                if($dif6 > 0){ ?> <td scope="col" style="text-align: right; color:red"><?php echo number_format($dif6,0,',','.') . ' minutos'; ?></td><?php }

                $estimativa7 = $estimativa6 + $rowBaseT['ENTREGA'];
                $dif7 = $acumulado7 - $estimativa7;
                if($dif7 <= 0){ ?> <td scope="col" style="text-align: right; color:limegreen"><?php echo number_format($dif7,0,',','.') . ' minutos'; ?></td><?php } 
                if($dif7 > 0){ ?> <td scope="col" style="text-align: right; color:red"><?php echo number_format($dif7,0,',','.') . ' minutos'; ?></td><?php } ?>
            </tr>                
          </tbody>
        </table>
      </div>
    </div>
    <div class="col-md-12"><br>
      <div id="custoEtapa" style="width: 1100px; height: 300px;"></div>
    </div>
    <div class="col-md-12"><br>
      <div id="acumulado" style="width: 1100px; height: 300px;"></div>
    </div>
  </div>
</div>


  