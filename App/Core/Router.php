<?php

namespace App\Core;

class Router{

    private $controller;

    private $httpMethod = "GET";

    private $controllerMethod;

    private $params = [];

    function __construct(){

        //configurando cors e conteúdo do response
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header("Access-Control-Allow-Headers: Content-Type");
        header("content-type: application/json");

        //recuperar a url que está sendo acessada
        $url = $this->parseURL();

        //se o controller existir dentro da pasta de controllers
        if(isset($url[1]) && file_exists("../App/Controller/" . $url[1] . ".php")){

            $this->controller = $url[1];
            unset($url[1]);
        }elseif(empty($url[1])){

            //setamos o controller padrão da aplicação (produtos)
            $this->controller = "produtos";
        }else{

            //se não existir e houver um controller na url
            //exibimos página não encontrada
            $this->controller = "erro404";
        }

        //importamos o controller
        require_once "../App/Controller/" . $this->controller . ".php";

        //instancia o controller
        $this->controller = new $this->controller;

        //pegando o HTTP Method
        $this->httpMethod = $_SERVER["REQUEST_METHOD"];

        //pegando o método do controller baseando-se no http method
        switch($this->httpMethod){

            case "GET":
                if(!isset($url[2])){
                    $this->controllerMethod = "index";
                }elseif(is_numeric($url[2])){
                    $this->controllerMethod = "find";
                    $this->params = [$url[2]];
                }else{
                    http_response_code(400);
                    echo json_encode(["erro" => "Parâmetro inválido"], 
                        JSON_UNESCAPED_UNICODE);
                    exit;
                }
               
                break;

            case "POST":
                $this->controllerMethod = "store";
                break;

            case "PUT":
                $this->controllerMethod = "update";
                $this->getParams($url);
                break;

            case "DELETE":
                $this->controllerMethod = "delete";
                $this->getParams($url);
                break;

            default:
                echo "Método não habilitado";
                exit;

        }

        //executamos o método dentro do controller, passando os parametros
        call_user_func_array([$this->controller, $this->controllerMethod], $this->params);

    }

    //recuperar a URL e retornar os parametros
    private function parseURL(){
        return explode("/", $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
    }

    private function getParams($url){
        if(isset($url[2]) && is_numeric($url[2])){
            $this->params = [$url[2]];
        }else{
            http_response_code(400); //400 bad request
            echo json_encode(["erro" => "Parâmetro inválido"], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

}