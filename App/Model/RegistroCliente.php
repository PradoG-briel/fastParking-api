<?php

use App\Core\Model;

class RegistroCliente{

    public $id;
    public $nome;
    public $placa;
    public $data_hora_entrada;
    public $data_hora_saida;
    public $valor_total;
    public $precoId;
    
    public function listarTodas(){

        $sql = " SELECT * FROM tbl_registro_cliente order by id desc";

        $stmt = Model::getConexao()->prepare($sql);
        $stmt->execute();

        if($stmt->rowCount() > 0){
            $resultado = $stmt->fetchAll(PDO::FETCH_OBJ);

            return $resultado;
        }else{
            return [];
        }
    }

    public function inserir()
    {
        $sql = "insert into tbl_registro_cliente
                    (nome,
                    placa,
                    data_hora_entrada,
                    preco_id
                    ) 
                        values(?,
                                ?,
                                ?,
                                ?
                                )";

        $stmt = Model::getConexao()->prepare($sql);
        $stmt->bindValue(1, $this->nome);
        $stmt->bindValue(2, $this->placa);
        $stmt->bindValue(3, $this->data_hora_entrada);
        $stmt->bindValue(4, $this->precoId);

        print_r($stmt);

        if ($stmt->execute()) {

            $this->id = Model::getConexao()->lastInsertId();
            return $this;
        } else {

            return false;
        }
    }
    public function buscarPorId($id)
    {
        $sql = " SELECT * FROM tbl_registro_cliente WHERE id = ? ";

        $stmt = Model::getConexao()->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $registro = $stmt->fetch(PDO::FETCH_OBJ);

            $this->id = $registro->id;
            $this->nome = $registro->nome;
            $this->placa = $registro->placa;
            $this->data_hora_entrada = $registro->data_hora_entrada;
            $this->valor_total = $registro->valor_total;
            $this->precoId = $registro->preco_id;

            return $this;
        } else {

            return false;
        }
    }
    public function atualizar(){
        $sql = " UPDATE tbl_registro_cliente SET data_hora_saida = ?, valor_total = ? WHERE id = ? ";

        $stmt = Model::getConexao()->prepare($sql);
        $stmt->bindValue(1, $this->data_hora_saida);
        $stmt->bindValue(2, $this->valor_total);
        $stmt->bindValue(3, $this->id);
        
        return $stmt->execute();
    }

    public function editar(){
        $sql = " UPDATE tbl_registro_cliente SET nome = ?, placa = ? WHERE id = ? ";

        $stmt = Model::getConexao()->prepare($sql);
        $stmt->bindValue(1, $this->nome);
        $stmt->bindValue(2, $this->placa);
        $stmt->bindValue(3, $this->id);
        
        return $stmt->execute();
    }

}