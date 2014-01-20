<?php
namespace Andrey\MySqlTerminalBundle\Models;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Configuration;
use Exception;

class MysqlterminalModel
{
    public $errorMessage = '';

    public function getConnectDB($dbname, $user, $password, $host)
    {
        try {
            return DriverManager::getConnection(array(
                'dbname' => $dbname,
                'user' => $user,
                'password' => $password,
                'host' => $host,
                'driver' => 'pdo_mysql',
                'charset' => 'utf8',
                'driverOptions' => array(
                    1002 => 'SET NAMES utf8'
                )
            ), new Configuration);
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function getStatementDB($connection, $query)
    {
        try {
            $st = $connection->prepare($query);

            return $st;
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function getResult($statement)
    {
        try {
            $statement->execute();
            return $statement->fetchAll();
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }
} 