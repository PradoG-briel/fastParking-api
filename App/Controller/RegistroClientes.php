<?php
use App\Core\Controller;

class RegistroClientes extends Controller
{


    public function index()
    {

        $entradaModel = $this->model("RegistroCliente");
        $registroClientes = $entradaModel->listarTodas();
        echo json_encode($registroClientes, JSON_UNESCAPED_UNICODE);
    }

    public function find($id){

        $entradaModel = $this->model("RegistroCliente");

        $registroCliente = $entradaModel->buscarPorId($id);

        if($registroCliente){
            echo json_encode($registroCliente, JSON_UNESCAPED_UNICODE);
        }else{
            http_response_code(404);
            echo json_encode(["erro" => "Registro não encontrado"]);
        }
}
        public function store(){
           $novoPreco = $this->getRequestBody();
           

           $entradaModel = $this->model("RegistroCliente");

           $entradaModel->nome = $novoPreco->nome;
           $entradaModel->placa = $novoPreco->placa;
           $entradaModel->data_hora_entrada = $novoPreco->data_hora_entrada;
        
           $precoModel = $this->model("Preco");
           $ultimoPreco = $precoModel->getUltimoInserido();

           $entradaModel->precoId = $ultimoPreco->id;
           $precoModel = $entradaModel->inserir();

           if ($precoModel) {
               http_response_code(201);
               echo json_encode($precoModel);
           } else {
               http_response_code(500);
               echo json_encode(["erro" => "Problemas ao inserir preço"]);
           }

        }
        public function update($id)
    {
        $json = file_get_contents("php://input");
        $atualizarCliente = json_decode($json);

        $registroEditar = $this->getRequestBody();

        $entradaModel = $this->model("RegistroCliente");

        $entradaModel = $entradaModel->buscarPorId($id);

        if (!$entradaModel) {

            http_response_code(404);
            echo json_encode(["ERRO" => "Registro não encontrado"]);
            exit;
        }

        $entradaModel->nome = $atualizarCliente->nome;
        $entradaModel->placa = $atualizarCliente->placa;

        if ($entradaModel->editar()) {
            http_response_code(204);
        } else {

            http_response_code(500);
            echo json_encode(["erro" => "Problemas ao editar registro"]);
        }
    }
    public function delete($id){

        $entradaModel = $this->model("RegistroCliente");
       
         
        $entradaModel = $entradaModel->buscarPorId($id);
        if(!$entradaModel) {
            http_response_code(404);
            echo json_encode(["erro" => "cliente não encontrado"]);

            exit;
        }

        $registroModel = $this->calculaValor($entradaModel);

        $entradaModel->atualizar();
        echo json_encode($entradaModel, JSON_UNESCAPED_UNICODE);
    }

    private function calculaValor($entradaModel){
        $dataEntrada = DateTime::createFromFormat("Y-m-d H:i:s", $entradaModel->data_hora_entrada);

        $dataSaida = new DateTime();

        $intervalo = $dataSaida->diff($dataEntrada);

        $horas = 0;

        if($intervalo->d > 0){
            $horas = $horas + $intervalo->d * 24;
        }
        $horas = $horas + $intervalo->h;

        //tolerancia de 10 min

        if($intervalo->i > 10){
            $horas += 1;
        }
      
        $precoModel = $this->model("Preco");

        $precoModel = $precoModel->buscarPorId($entradaModel->precoId);

        $entradaModel->valor_total = $precoModel->primeira_hora;
        $horas--;

        if($horas > 0){
            $entradaModel->valor_total += $precoModel->demais_hora * $horas;
        }
        $entradaModel->data_hora_saida = $dataSaida->format("Y-m-d H:i:s");

        return $entradaModel;
    }
}