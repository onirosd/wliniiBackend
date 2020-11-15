<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>AMC PDF</title>

    <style>
        /* @font-face {
            font-family: "arial-narrow";
            src: url('/fonts/arialn.ttf');
        } */
        html{
            /* margin: 0px; */
            font-family: 'sans-serif'
        }
        .clearfix:after{
            content: "";
            display: table;
            clear: both;
        }
        .page{
            padding: 30px 20px;
        }
        .page-header{
            text-align: center;
            margin-bottom: 32px;
        }
        .page-content{
            margin-top: 20px;
        }
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td, th {
            border: 1px solid #dddddd;
            text-align: center;
            padding: 8px;
            font-size: 14px;
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
        .f-18{
            font-size: 18px;
        }
        .f-bold{
            font-weight: bold;
        }
        .text-center{
            text-align: center;
        }
        .indent-36{
            text-indent: 36px;
        }
        .mt-32{
            margin-top: 32px;
        }
        .page-footer{
            position: fixed;
            bottom: 40px;
            text-align: center;
            width: 88%;
            padding-top: 10px;
            border-top: 1px solid gray;
            color: gray;
            font-size: 12px;
        }
    </style>
</head>
<body>

    <div class="page clearfix">
        <div class="page-header">
            <div class="title f-16 f-bold">ANALISIS DE MERCADO COMPARATIVO</div>
        </div>

        <div class="clearfix f-14">
            <div class="left">
                <div>Propietario: <?php echo isset($client) ? $client : '' ?> </div>
                <div>Dirección: <?php echo isset($address) ? $address : '' ?></div>
                <div>M2: <?php echo isset($totalarea) ? $totalarea : '' ?></div>
                <div>Tipo de Propiedad: <?php echo isset($inmueble) ? $inmueble : '' ?></div>
            </div>
        </div>
        <div class="clearfix f-14">
            <div class="right">
                <div>Lima  <?php echo $day ?>  de  <?php echo $month ?>  de  2020</div>
            </div>
        </div>

        <div class="page-content">
            <div class="description">
                <p class="indent-36 f-14">Referente al estudio de Análisis de Mercado Comparativo (AMC) realizado a su inmueble, damos a conocer los resultados que arrojó el estudio según nuestro conocimiento en el mercado, el valor de la propiedad para <strong>(<?php echo isset($operation) ? $operation : '' ?>)</strong></p>
                <p class="indent-36 f-14">Se realizó un AMC tomando como referencia para los efectos de valoración de su inmueble, otras propiedades actualmente en promoción, así como operaciones inmobiliarias cerradas tanto por su ubicación, antigüedad, usos y mantenimiento las cuales eran comparables, aun cuando la superficie varia, obteniendo un resultado de rango de precio determinado, que va desde un máximo a un mínimo.</p>
                <p class="indent-36 f-14">Tomando en cuenta todas las características de su propiedad, con el propósito de tener un precio competitivo de mercado que nos permita negociar su inmueble con la mayor efectividad y en el menor tiempo posible se recomienda el siguiente precio basado en un rango de precios:</p>
            </div>

            <div>
                <h2 class="text-center f-18">Precio Sugerido: <?php echo $curSymbol.'/ '.$finalPrice.' '.$currency ?></h2>
            </div>

            <div style="padding: 0 50px">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 50%;">RANGO DE PRECIOS</th>
                            <th style="width: 50%;"><?php echo $curSymbol.'/'.$currency ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Valor Máximo ofertado</td>
                            <td><?php echo $offer_max ?></td>
                        </tr>
                        <tr>
                            <td>Valor mínimo ofertado</td>
                            <td><?php echo $offer_min ?></td>
                        </tr>
                        <tr>
                            <td>Valor Máximo vendido</td>
                            <td><?php echo $sold_max ?></td>
                        </tr>
                        <tr>
                            <td>Valor mínimo vendido</td>
                            <td><?php echo $sold_min ?></td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-32 f-13">Agradeciendo la oportunidad de servirle, se despide</div>
            </div>

            <div class="text-center mt-32 f-13">
                <div>Atentamente</div>
                <br><br><br><br>
                <div class="mt-32">
                    <span style="border-top: 1px solid black"><?php echo $name ?></span></div>
                <br>
                <div><?php echo trim($type) == "1" ? 'Agente Inmobiliario' : 'Asesor Inmobiliario'; ?></div>
                <div>Telf. <?php echo $phone; ?></div>
                <div>Correo: <?php echo $email; ?></div>
            </div>
            
            <!-- <div class="page-footer">Lima Perú</div> -->
        </div>
    </div>
</body>
</html>
