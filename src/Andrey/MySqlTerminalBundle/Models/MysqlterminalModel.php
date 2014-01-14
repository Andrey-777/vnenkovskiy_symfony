<?php
namespace Andrey\MySqlTerminalBundle\Models;

class MysqlterminalModel {
    public $errorMessage = '';

    public function getConnectDB($em)
    {
        try {
            return $em->getConnection();
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function getStatementDB($connection, $query)
    {
        try {
            return $connection->prepare($query);
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function getResult($statement)
    {
        try {
            $statement->execute();
            return $statement->fetchAll();
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }
} 