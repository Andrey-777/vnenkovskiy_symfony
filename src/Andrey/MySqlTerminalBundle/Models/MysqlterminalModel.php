<?php
namespace Andrey\MySqlTerminalBundle\Models;

class MysqlterminalModel {
    public $errorMessage = '';

    public function getConnectDB($dbname, $user, $password, $host)
    {
        try {
            return \Doctrine\DBAL\DriverManager::getConnection(array(
                    'dbname'   => $dbname,
                    'user'     => $user,
                    'password' => $password,
                    'host'     => $host,
                    'driver'   => 'pdo_mysql',
                ), new \Doctrine\DBAL\Configuration());

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