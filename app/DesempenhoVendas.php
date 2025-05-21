<?php
  // faz requisição da estrutura base da págima do sistema
  include_once './EstruturaPrincipal.php'; include_once './ConnectDB.php'; $_SESSION['posicao'] = 'Detalhes do Pedido'; include_once './RastreadorAtividades.php';
  $sql0 = $connDB->prepare("SELECT PRODUTO, SUM(QTDE_PEDIDO) AS TOTAL FROM pedidos GROUP BY PRODUTO");
  $sql0->execute();

  $sql1 = $connDB->prepare("SELECT CLIENTE, SUM(QTDE_PEDIDO) AS TTLCLIENTE FROM pedidos GROUP BY CLIENTE");
  $sql1->execute();
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
    var data1 = google.visualization.arrayToDataTable([
      ["PRODUTO", "QUANTIDADE", { role: "style" } ], <?php
      while($row0 = $sql0->fetch(PDO::FETCH_ASSOC)){ ?>
        ['<?php echo $row0['PRODUTO'] ?>', <?php echo $row0['TOTAL'] ?>, '' ], <?php  
      } ?>
    ]);

    var view1 = new google.visualization.DataView(data1);
    view1.setColumns([0, 1, { calc: "stringify", sourceColumn: 1, type: "string", role: "annotation" }, 2 ]);

    var options1 = { title: "Produtos", width: 600, height: 400, bar: {groupWidth: "95%"}, legend: { position: "right" }, backgroundColor: 'slategray',};
    var chart = new google.visualization.PieChart(document.getElementById("columnchart1_values"));
    chart.draw(view1, options1);

    var data2 = google.visualization.arrayToDataTable([
      ["CLIENTE", "QUANTIDADE", { role: "style" } ], <?php
      while($row1 = $sql1->fetch(PDO::FETCH_ASSOC)){ ?>
        ['<?php echo $row1['CLIENTE'] ?>', <?php echo $row1['TTLCLIENTE'] ?>, '' ], <?php  
      } ?>
    ]);

    var view2 = new google.visualization.DataView(data2);
    view2.setColumns([0, 1, { calc: "stringify", sourceColumn: 1, type: "string", role: "annotation" }, 2 ]);

    var options2 = { title: "Clientes", width: 600, height: 400, bar: {groupWidth: "95%"}, legend: { position: "right" }, backgroundColor: 'slategray', };
    var chart = new google.visualization.PieChart(document.getElementById("columnchart2_values"));
    chart.draw(view2, options2);
  }
</script>
<div class="main"><br>
  <p style="font-size: 20px; color: whitesmoke">Desempenho das Vendas</p><br>
  <div class="row g-2">
    <div class="col-md-6">
      <div id="columnchart1_values" style="width: 600px; height: 400px;"></div>
    </div>
    <div class="col-md-6">
      <div id="columnchart2_values" style="width: 600px; height: 400px;"></div>
    </div>
    <div class="col-md-7">
      <br>
      <button class="btn btn-primary" onclick="location.href='./MonitorProduto.php'" style="float:inline-end; width:270px;">Voltar para Mapa Geral</button>
    </div>
  </div>

</div>


  