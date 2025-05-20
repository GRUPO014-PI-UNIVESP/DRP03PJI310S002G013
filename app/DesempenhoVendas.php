<?php
  // faz requisição da estrutura base da págima do sistema
  include_once './EstruturaPrincipal.php'; include_once './ConnectDB.php'; $_SESSION['posicao'] = 'Detalhes do Pedido'; include_once './RastreadorAtividades.php';
  $sql0 = $connDB->prepare("SELECT PRODUTO, SUM(QTDE_PEDIDO) AS TOTAL FROM pedidos GROUP BY PRODUTO");
  $sql0->execute();
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
      while($row = $sql0->fetch(PDO::FETCH_ASSOC)){ ?>
        ['<?php echo $row['PRODUTO'] ?>', <?php echo $row['TOTAL'] ?>, 'red' ], <?php  
      } ?>
    ]);

    var view1 = new google.visualization.DataView(data1);
    view1.setColumns([0, 1,
      { calc: "stringify",
        sourceColumn: 1,
        type: "string",
        role: "annotation" }, 2
    ]);


    var options1 = {
      title: "Vendas",
      width: 600,
      height: 400,
      bar: {groupWidth: "95%"},
      legend: { position: "none" },
    };
 
    var chart = new google.visualization.PieChart(document.getElementById("columnchart1_values"));
      chart.draw(view1, options1);
  }
</script>
<div class="main"><br>
  <p style="font-size: 20px; color: whitesmoke">Desempenho das Vendas</p><br>
  <div class="row g-2">
    <div class="col-md-6">
      <div id="columnchart1_values" style="width: 800px; height: 300px;"></div>
    </div>
    <div class="col-md-6">

    </div> 

  </div>

</div>


  