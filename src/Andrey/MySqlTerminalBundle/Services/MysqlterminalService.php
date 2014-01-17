<?php
namespace Andrey\MySqlTerminalBundle\Services;
use Symfony\Component\HttpFoundation\Request;

//use Symfony\Component\HttpFoundation\Session\Session;
class MysqlterminalService {
    protected $model = null;

    public function getFormData(Request $request, $form, $session, $model)
    {
        $this->model = $model;

        $param = array(
            'showResult'     => false,
            'errorMessage'   => false,
            'queriesHistory' => false
        );

        if ($param['isPOST'] = $this->isPOST($request)) {
            $form->bind($request);
            $postData = $form->getData();
            $this->verifyPassword($session, $postData);
            $results  = $this->db($postData, $session->get('password'));

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

    protected function db($postData, $password)
    {
        if($connection = $this->model->getConnectDB($postData['database'], $postData['username'],
            $password, $postData['host'])) {
            if($statement  = $this->model->getStatementDB($connection, $postData['query'])) {
                return $this->model->getResult($statement);
            }
        }

        return false;
    }

    protected function verifyPassword(&$session, &$postData)
    {
        if (!(is_null($session->get('username')) && is_null($session->get('password')))) {
            if ($session->get('username') != $postData['username']) {
                $session->set('username', $postData['username']);
                $session->set('password', $postData['password']);
            } elseif($session->get('username') == $postData['username']
                && ($session->get('password') != $postData['password'] && $postData['password'] != null)) {
                $session->set('password', $postData['password']);
            }
        } else {
            $session->set('username', $postData['username']);
            $session->set('password', $postData['password']);
        }
    }
} 