<meta charset="utf-8">
<title>TraDiary</title>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="shortcut icon" href="image/favicon.png">

<!-- jQuery -->
<script type="text/javascript" src="library/jquery-3.4.1/jquery.min.js"></script>

<!-- Bootstrap -->
<script type="text/javascript" src="library/bootstrap-4.4.1/js/bootstrap.min.js"></script>
<link href="library/bootstrap-4.4.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

<!-- Fontawsome -->
<script type="text/javascript" src="library/fontawesome-5.10.2/js/all.min.js"></script>
<link href="library/fontawesome-5.10.2/css/all.min.css" rel="stylesheet">

<!-- ChartJS -->
<script type="text/javascript" src="library/chartjs-2.9.3/Chart.min.js"></script>
<link href="library/chartjs-2.9.3/Chart.min.css" rel="stylesheet">

<!-- Arquivos padres -->
<script type="text/javascript" src="javascript/default/loading.js"></script>
<script type="text/javascript" src="javascript/default/service.js"></script>

<!-- Estilo dos elementos -->
<?php
$files = scandir(__DIR__."/../style/css/element");
foreach($files as $file){
    if(substr($file, -4) === ".css" && strpos($file, ".map.") === false){
        echo "<link href='style/css/element/{$file}' rel='stylesheet'>";
    }
}
?>

<!-- Arquivos da view -->
<?php
$view = current_view();
if(file_exists(__DIR__."/../javascript/view/{$view}.js")){
    echo "<script type='text/javascript' src='javascript/view/{$view}.js'></script>";
}
?>