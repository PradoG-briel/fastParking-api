<?php

use App\Core\Model;

class Preco
{

    public $id;
    public $primeira_hora;
    public $demais_hora;
   

    public function getUltimoInserido()
    {
        $sql = "select * from tbl_preco order by id desc limit 1";

        $stmt = Model::getConexao()->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $resultado =  $stmt->fetch(PDO::FETCH_OBJ);
            return $resultado;
        } else {
           return null;
        }     
    }
    public function inserir()
    {
        $sql = "insert into tbl_preco
                    (primeira_hora,
                    demais_hora)
                        values(?,
                                ?)";

        $stmt = Model::getConexao()->prepare($sql);
        $stmt->bindValue(1, $this->primeira_hora);
        $stmt->bindValue(2, $this->demais_hora);

        if ($stmt->execute()) {

            $this->id = Model::getConexao()->lastInsertId();
            return $this;
        } else {

            return null;
        }
    }
    public function buscarPorId($id)
    {
        $sql = "select * from tbl_preco where id = ?";

        $stmt = Model::getConexao()->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $preco = $stmt->fetch(PDO::FETCH_OBJ);

            $this->id = $preco->id;
            $this->primeira_hora = $preco->primeira_hora;
            $this->demais_hora = $preco->demais_hora;

            return $this;
        } else {

            return null;
        }
    }
    public function atualizar()
    {
        $sql = "update tbl_preco set
                    primeira_hora = ?,
                    demais_hora = ? WHERE id = ?";

        $stmt = Model::getConexao()->prepare($sql);
        $stmt->bindValue(1, $this->primeira_hora);
        $stmt->bindValue(2, $this->demais_hora);
        $stmt->bindValue(3, $this->id);

        return $stmt->execute();
    }

}