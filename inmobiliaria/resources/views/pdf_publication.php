<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Publicacion</title>

    <style>
        /* @font-face {
            font-family: "arial-narrow";
            src: url('/fonts/arialn.ttf');
        } */
        html{
            margin: 0px;
            font-family: 'sans-serif'
        }
        .clearfix:after{
            content: "";
            display: table;
            clear: both;
        }
        .page{
            padding: 40px 5px;
        }
        .page-header{
            text-align: center;
        }
        .page-header .title{
            font-size: 46px;
            font-weight: bold;
        }
        .table1{
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
        }
        
        .table1 thead{
            vertical-align: top;
        }
        .table1 thead th{
            text-align: left;
            font-weight: unset;
            /* font-family: 'arial-narrow' */
        }

        .table2{
            width: 100%;
            border-spacing: 4;
        }
        .table2 thead th{
            width: 33.3%;
            height: 196px;
            background-color: #5a758045;   
            vertical-align: middle;
            border-radius: 5px;
            /* border: 1px solid red; */
            /* overflow: hidden; */
        }
        .table2 thead th.empty{
            background-color: transparent;   
            height: 0px;
        }
        .table2 thead th .image-wrapper{
            max-height: 196px;
            overflow: hidden;
        }
        .table2 thead th .image-wrapper img{
            width: 245px;
        }
        .left{
            float: left;
        }
        .right{
            float: right;
        }
        
        .f-12{
            font-size: 12px;
        }
        .f-13{
            font-size: 13px;
        }
        .f-14{
            font-size: 14px;
        }
        .f-16{
            font-size: 16px;
        }
        .f-bold{
            font-weight: bold;
        }
        .text-center{
            text-align: center;
        }
        .divider{
            border: 1px solid gray;
            margin-top: 5px;
        }
        .r_symbol{
            /* border: 1px solid black; */
            /* border-radius: 5px; */
            font-size: 10px;
            width: 10px;
        }
        .circle-marker{
            width: 32px;
            height: 32px;
            border-radius: 16px;
            border: 1px solid #0064bd20;
            background-color: #0064bd70;
            position: absolute;
            top: 135px;
            left: 185px;
        }
        .pub-photo{
            width: 400px;
            height: 260px;
            overflow: hidden;
            margin-left: 26px;
        }
        .pub-photo img{
            height: 260px;
            width: auto;
        }
    </style>
    <link type="text/css" rel="stylesheet" href="css/quill.core.css">
    <link type="text/css" rel="stylesheet" href="css/quill.bubble.css">
    <link type="text/css" rel="stylesheet" href="css/quill.snow.css">
</head>
<body>
    <?php
        $currency = $pubInfo->IdTipoMoneda;
        if($currency == '1') $currency = 'S/';
        else if ($currency == '2') $currency = 'US$/';
        else if ($currency == '3') $currency = '€/';
        else $currency = 'S/';
    ?>
    <div class="page clearfix" style="page-break-after: always">
        <div class="page-header clearfix">
            <div class="title">FICHA TECNICA</div>
            <div style="font-size: 26px"><?php echo $pubInfo->Des_Titulo ?></div>
            <div style="font-size: 22px"><?php echo $pubInfo->Des_Subtitulo ?></div>
        </div>

        <div class="page-content">
            <table class="table1">
                <thead>
                <tr>
                    <th style="width: 40%;">
                        <div style="padding: 24px 5px 5px">
                            <div class="f-14">
                                <span class="right clearfix">ID: <?php echo $pubInfo->IdPubCabecera ?></span>    
                                <span>DATOS DE LA PROPIEDAD</span>
                            </div>
                            <div class="divider"></div>

                            <div>
                                <ul class="f-12">
                                    <li>Tipo: <?php echo $pubInfo->tipo ?></li>
                                    <li>Precio De Alquiler: <?php echo $currency.' '.number_format(floatval($pubInfo->Num_Precio)) ?></li>
                                    <li>Superficie De Terreno: <?php echo $pubInfo->Num_AreaTotal ?> M2</li>
                                    <li>Superficie De Construcción: <?php echo $pubInfo->Num_AreaTechado ?> M2</li>
                                    <li>Dormitorios: <?php echo $pubInfo->Num_Habitaciones ?></li>
                                    <li>Baños Completos: <?php echo $pubInfo->Num_Banios ?></li>
                                    <li>Baños Servicio: <?php echo $pubInfo->Num_BaniosVisita ?></li>
                                    <li>Cochera: <?php echo $pubInfo->Num_Cochera ?></li>
                                    <li>Antiguedad: <?php echo $pubInfo->Num_Antiguedad ?> Años</li>
                                </ul>
                            </div>

                            <div>
                                <div class="f-14">DESCRIPCIÓN:</div>
                            </div>
                            <div class="divider"></div>

                            <div style="padding: 20px 0; font-size: 12px; max-height: 400px; overflow: hidden">
                                <div class="wql-content">
                                    <?php echo $pubInfo->Des_Subtitulo2 ?>
                                </div>
                            </div>

                            <div class="text-center f-13">
                                <div style="margin-top: 20px; margin-bottom: 10px">
                                    <img src="<?php echo $person->Img_Personal ? base_path().'/public'.$person->Img_Personal : 'img/dummy_user.jpg'; ?>" alt="" style="width: 120px">
                                </div>
                                <div>FOTO DEL USUARIO</div>
                                <div>Agente: <?php echo $person->Des_NombreCompleto ?></div>
                                <div>Correo: <?php echo $person->Des_Correo1 ?></div>
                                <div>Telefono: <?php echo $person->Des_Telefono1 ?></div>
                            </div>
                        </div>
                    </th>
                    <th style="width: 60%;">
                        <div class="f-14" style="padding: 50px 15px">
                            <div class="text-center">
                                <div class="pub-photo">
                                    <img src="<?php echo count($images) > 0 ? base_path().'/public'.$images[0]->Des_url : 'img/dummy.jpg'; ?>" alt="">
                                </div>
                                <br>
                                <div>UBICACIÓN DEL INMUEBLE</div>
                                <div><?php echo $pubInfo->ubicacion ?></div>
                            </div>

                            <div>
                                <div style="margin-top: 50px; margin-left: 26px; position: relative; ">
                                    <div style="">
                                        <img src="<?php echo $mapUrl; ?>" alt="" style="width: 400px">
                                    </div>
                                    <?php if(!$isMarker){ ?>
                                        <div class="circle-marker"></div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
    
    <?php
        $p_count = count($images);
        $pages = ceil($p_count/12);

        if($pages > 0){
            for($p = 0; $p < $pages; $p++){
    ?>
        <div class="page">
            <div class="page-header clearfix">
                <div class="title">FICHA TECNICA</div>
                <div style="font-size: 26px;"><?php echo $pubInfo->Des_Titulo ?></div>
                <div style="font-size: 22px;"><?php echo $pubInfo->Des_Subtitulo ?></div>
            </div>

            <div style="padding: 10px">
                <div class="clearfix" style="margin: 20px 20px 20px 0px">
                    <span class="right clearfix">ID: <?php echo $pubInfo->IdPubCabecera ?></span>    
                    <span>CATÁLOGO DE PUBLICACIÓN</span>
                </div>
                <table class="table2">
                    <thead>
                        <?php
                            for($i = 0; $i < 4; $i++){
                        ?>
                            <tr>
                        <?php
                                for($j = 0; $j < 3; $j++){
                                    $num = $p*12 + $i * 3 + $j;
                                    if($num < $p_count){
                        ?>
                                        <th>
                                            <div class="image-wrapper">
                                                <img src="<?php echo base_path().'/public'.$images[$num]->Des_url; ?>" alt="">
                                            </div>
                                        </th>
                        <?php
                                    }else{
                        ?>
                                        <th class="empty"></th>              
                        <?php
                                    }
                                }
                        ?>
                            </tr>
                        <?php
                            }
                        ?>
                    </thead>
                </table>
            </div>
        </div>
    <?php
            } 
        }
    ?>
</body>
</html>
