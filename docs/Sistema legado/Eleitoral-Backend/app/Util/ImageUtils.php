<?php
/*
 * ImageUtils.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Util;

/**
 * Classe utilitária da aplicação para manipulação de Imagens.
 *
 * @package App\Util
 * @author Squadra Tecnologia
 */
class ImageUtils
{

    const DATA_IMAGE = 'data:image/';

    const BASE_64 = ';base64,';

    /**
     * Construtor privado para garantir o Singleton.
     */
    private function __construct()
    {
    }

    /**
     * Retorna o valor em BASE64 referente à imagem informada.
     *
     * @param $path
     * @return string|null
     */
    public static function getImageBase64($path)
    {
        $base64 = null;
   
        if ((!empty($path)) && file_exists($path)) {
            $data = file_get_contents($path);
            $type = pathinfo($path, PATHINFO_EXTENSION);

            $base64 = self::DATA_IMAGE . $type . self::BASE_64 . base64_encode($data);
        }

        return $base64;
    }

}
