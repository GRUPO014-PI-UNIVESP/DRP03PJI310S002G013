<?php
  // faz requisição da estrutura base da págima do sistema
  include_once './EstruturaPrincipal.php'; include_once './ConnectDB.php'; $_SESSION['posicao'] = 'Detalhes do Pedido'; include_once './RastreadorAtividades.php';

?>
<!-- Carregar a API do google -->
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<!-- Preparar a geracao do grafico -->
<script type="text/javascript">
  // Carregar a API de visualizacao e os pacotes necessarios.
  google.load('visualization', '1.0', {'packages':['corechart']});
  // Especificar um callback para ser executado quando a API for carregada.
  google.setOnLoadCallback(desenharGrafico);
/*
* Funcao que preenche os dados do grafico
*/
</script>
<script>
  // verifica inatividade da página e fecha sessão
  let inactivityTime = function () {
    let time; window.onload = resetTimer; document.onmousemove = resetTimer; document.onkeypress  = resetTimer;
    function deslogar() { <?php $_SESSION['posicao'] = 'Encerrado por inatividade'; include_once './RastreadorAtividades.php'; ?> window.location.href = 'LogOut.php'; }
    function resetTimer() { clearTimeout(time); time = setTimeout(deslogar, 69900000); }
  }; inactivityTime();
</script>
<div class="main"><br>
  <p style="font-size: 20px; color: whitesmoke">Desempenho das Vendas</p><br>
  <?php
    $sql0 = $connDB->prepare("SELECT PRODUTO, SUM(QTDE_PEDIDO) AS TOTAL FROM pedidos GROUP BY PRODUTO");
    $sql0->execute(); 
    while($rowTotal = $sql0->fetch(PDO::FETCH_ASSOC)){
      echo $rowTotal['PRODUTO'] . ' => ' . $rowTotal['TOTAL'] . '<br>'; ?>
      <script>
        function desenharGrafico() {
          // Montar os dados usados pelo grafico
          var dados = new google.visualization.DataTable();
          dados.addColumn('string', 'Gênero');
          dados.addColumn('number', 'Quantidades');
          dados.addRows([[<?php echo $rowTotal['PRODUTO'] ?>, <?php $rowTotal['TOTAL'] ?>]]);

          // Configuracoes do grafico
          var config = {'title':'Vendas por Produto','width':800,'height':600};

          // Instanciar o objeto de geracao de graficos de pizza,
          // informando o elemento HTML onde o grafico sera desenhado.
          var chart = new google.visualization.PieChart(document.getElementById('area_grafico'));

          // Desenhar o grafico (usando os dados e as configuracoes criadas)
          chart.draw(dados, config);
        }
      </script><?php
    }
  ?>
  <div id="area_grafico"></div>
</div>


  