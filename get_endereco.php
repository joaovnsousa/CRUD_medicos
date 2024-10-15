<?php
    if (isset($_POST['cep'])) {
        $cep = $_POST['cep'];
        
        function get_endereco($cep) {
            $cep = preg_replace("/[^0-9]/", "", $cep);
            $url = "http://viacep.com.br/ws/$cep/xml";
        
            $xml = simplexml_load_file($url);
            return $xml;
        }

        $endereco = get_endereco($cep);

        if ($endereco) {
            $response = array(
                "logradouro" => (string)$endereco->logradouro,
                "bairro" => (string)$endereco->bairro,
                "localidade" => (string)$endereco->localidade,
                "uf" => (string)$endereco->uf
            );
            echo json_encode($response);
        } else {
            echo json_encode(null);
        }
    }
?>