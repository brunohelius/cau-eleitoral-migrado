<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app('translator')->getLocale()) }}">
<head>
<link href="http://fonts.cdnfonts.com/css/dax" rel="stylesheet">
    <style>
        @import url('http://fonts.cdnfonts.com/css/dax');
        body{
            font-family: Arial, Helvetica, sans-serif;;
            font-style: normal;
            font-weight: 400;
            font-size: 10px;
            margin: 0;
        }

        #cabecalho {
            width: 100%;
            margin-top: -5%;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            margin-bottom: -5%;
            height: 150px;
        }
        .watermark {
            position: fixed;
            top: -7%;
            margin-left: 0%;
            text-align: center;
            opacity: .6;
            z-index: -1000;
        }

        .diploma {
            line-height: 1.5;
            font-size: 14px;
        }

        .tituloDiploma{
            margin-top: 0;
            font-size: 28px;
        }

        .codigoAutenticador{
            font-size: 10px;
            color: gray;
            text-align:left;
            margin-top: 0%;
        }
        
        .imgAssinatura{
            width: 100px;
        }
    </style>
</head>

<body style="text-align: center;">
    <div class="watermark">
        <img src="images/marca_dagua.jpg" width="1000">
    </div>
    {!! $html !!}
</body>
</html>


