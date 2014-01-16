<?php
namespace Andrey\MySqlTerminalBundle\Services;
use Symfony\Component\HttpFoundation\Request;

use Andrey\MySqlTerminalBundle\Models\MysqlterminalModel;
use Symfony\Component\HttpFoundation\Session\Session;
class MysqlterminalService {
    protected $model = null;

    public function __construct()
    {
        $this->model = new MysqlterminalModel();
    }

    public function checkRequest(Request $request, $form, $session)
    {
        $param = array(
            'showResult'     => false,
            'errorMessage'   => false,
            'queriesHistory' => false
        );

        if ($param['isPOST'] = $this->isPOST($request)) {
            $form->bind($request);
            $postData = $form->getData();
            $results  = $this->db($postData);

            if (!$this->model->errorMessage) {
                $session->set('queriesHistory',
                    $this->populatQueriesHistory((array)$session->get('queriesHistory'),
                        $postData['query']));

                $param['showResult'] = true;
                $param['results']    = $results;
            } else {
                $param['errorMessage'] = $this->model->errorMessage;
            }

            $param['queriesHistory'] = $session->get('queriesHistory');
        }

        $param['form'] = $form->createView();

        return $param;
    }

    protected function isPOST($request)
    {
        return $request->getMethod() == 'POST' ? : false;
    }

    protected function populatQueriesHistory(Array $queries, $query)
    {
        array_unshift($queries, $query);
        return array_unique($queries);
    }

    protected function db($postData)
    {
        $connection = $this->model->getConnectDB($postData['database'], $postData['username'],
            $postData['password'], $postData['host']);
        $statement  = $this->model->getStatementDB($connection, $postData['query']);
        return $this->model->getResult($statement);
    }














//    protected function populatSession(&$session, $postData)
//    {
//        $session->set('user', $postData['User:']);
//        $session->set('password', $postData['Password:']);
//    }
//
//    protected function passVerify()
//    {
//        if (!(is_null($this->getSessUser()) && is_null($this->getSessPassword()))) {
//            if ($this->getSessUser() != $this->getUser()) {
//                $this->setSessUser($this->getUser())
//                    ->setSessPassword($this->getPassword());
//            } elseif($this->getSessUser() == $this->getUser()
//                && ($this->getSessPassword() != $this->getPassword() && $this->getPassword() != null)) {
//                $this->setSessPassword($this->getPassword());
//            }
//        } else {
//            $this->setSessUser($this->getUser())
//                ->setSessPassword($this->getPassword());
//        }
//    }
} 