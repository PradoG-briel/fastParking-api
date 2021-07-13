<?php

use App\Core\Controller;

class Precos extends Controller
{

    public function index()
    {
        $precoModel = $this->model("Preco");
        $preco = $precoModel->getUltimoInserido();

        if (!$preco) {
           http_response_code(204);
           exit;
        }
         echo json_encode($preco, JSON_UNESCAPED_UNICODE);
    }

    public function find($id)
    {
        $precoModel = $this->model("Preco");
        $preco = $precoModel->buscarPorId($id);

        if ($preco) {

            echo json_encode($preco, JSON_UNESCAPED_UNICODE);
        } else {

            http_response_code(404);
            echo json_encode(["ERRO" => "Preço não encontrado"]);
        }
    }
    public function store(){
        $json =  file_get_contents("php://input");

        $novoPreco = json_decode($json);

        $precoModel = $this->model("Preco");

        $precoModel->primeira_hora = $novoPreco->primeira_hora;
        $precoModel->demais_hora = $novoPreco->demais_hora;
     
       $entradaModel = $precoModel->inserir();
       if ($precoModel) {

         http_response_code(201);
         echo json_encode($precoModel, JSON_UNESCAPED_UNICODE);
     } else {

         http_response_code(500);
         echo json_encode(["ERRO" => "Problemas ao inserir preço"]);
     }
    }
}