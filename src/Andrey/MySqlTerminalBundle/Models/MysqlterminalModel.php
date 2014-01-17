<?php
namespace Andrey\MySqlTerminalBundle\Models;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Configuration;
use Exception;
class MysqlterminalModel {
    public $errorMessage = '';

    public function getConnectDB($dbname, $user, $password, $host)
    {
        try {
//            $con =  DriverManager::getConnection(array(
//                'dbname'   => $dbname,
//                'user'     => $user,
//                'password' => $password,
//                'host'     => $host,
//                'driver'   => 'pdo_mysql'), new Configuration);
//
//            $con->query("set names utf8");
//            return $con;
            return DriverManager::getConnection(array(
                                                        'dbname'   => $dbname,
                                                        'user'     => $user,
                                                        'password' => $password,
                                                        'host'     => $host,
                                                        'driver'   => 'pdo_mysql'), new Configuration);
        } catch(Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function getStatementDB($connection, $query)
    {
        try {
            return $connection->prepare($query);
        } catch(Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function getResult($statement)
    {
        try {
            $statement->execute();
            return $statement->fetchAll();
        } catch(Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }
} 