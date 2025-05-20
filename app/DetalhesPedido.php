<?php
  // faz requisição da estrutura base da págima do sistema
  include_once './EstruturaPrincipal.php'; $_SESSION['posicao'] = 'Detalhes do Pedido'; include_once './RastreadorAtividades.php';
  //verifica o numero de pedido selecionado para mostrar detalhes
  if(!empty($_GET['id'])){ $id = $_GET['id'];
    $pedi = array(); $comp = array(); $rece = array(); $anaM = array(); $fabr = array(); $anaP = array(); $entr = array(); $tota = array();

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
 <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load("current", {packages:['corechart']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
      var data = google.visualization.arrayToDataTable([
        ["Element", "Density", { role: "style" } ],
        ["<?php echo  date('d/m/Y H:i',strtotime($rowHistorico['INICIO'])) ?>" ,
          <?php echo $rowHistorico['PEDIDO'] ?>, "limegreen"],

        ["<?php echo  date('d/m/Y H:i',strtotime($rowHistorico['T_COMPRA'])) ?>",
          <?php echo $rowHistorico['COMPRA'] ?>, "limegreen"],

        ["<?php echo  date('d/m/Y H:i',strtotime($rowHistorico['T_RECEBE'])) ?>",
          <?php echo $rowHistorico['RECEBIMENTO'] ?>, "limegreen"],

        ["<?php echo  date('d/m/Y H:i',strtotime($rowHistorico['T_ANAMAT'])) ?>",
          <?php echo $rowHistorico['ANALISE_MATERIAL'] ?>, "limegreen"],

        ["<?php echo  date('d/m/Y H:i',strtotime($rowHistorico['T_FABRI'])) ?>",
          <?php echo $rowHistorico['FABRICACAO'] ?>, "limegreen"],

        ["<?php echo  date('d/m/Y H:i',strtotime($rowHistorico['T_ANAPRO'])) ?>",
          <?php echo $rowHistorico['ANALISE_PRODUTO'] ?>, "limegreen"],

        ["<?php echo  date('d/m/Y H:i',strtotime($rowHistorico['T_ENTREGA'])) ?>",
          <?php echo $rowHistorico['ENTREGA'] ?>, "limegreen"]
      ]);

      var view = new google.visualization.DataView(data);
      view.setColumns([0, 1, { calc: "stringify", sourceColumn: 1, type: "string", role: "annotation" }, 2]);

      var options = { title: "Histórico cronológico do pedido", width: 1080, height: 200, bar: {groupWidth: "100%"}, legend: { position: "none" },
      };
      var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values"));
      chart.draw(view, options);
  }
  </script>
<div class="main"><br>
  <div class="row g-2">
    <div class="col-md-6">
      <p style="font-size: 20px; color: whitesmoke">Relatório de Produção de Pedido</p>
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
    <div class="col-md-12">
      <div id="columnchart_values" style="width: 1200px; height: 200px;"></div>
    </div>
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
          <tbody style="height: 75%; font-size: 12px;">
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
  </div>
</div>


  