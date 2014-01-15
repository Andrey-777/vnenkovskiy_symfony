<?php
namespace Andrey\MySqlTerminalBundle\Services;
use Symfony\Component\HttpFoundation\Request;

use Andrey\MySqlTerminalBundle\Models\MysqlterminalModel;
use Symfony\Component\HttpFoundation\Session\Session;
class MysqlterminalService {
    protected $model      = null;
    protected $showResult = false;
    protected $queriesHistory = false;

    public function __construct()
    {
        $this->model = new MysqlterminalModel();
    }

    public function checkRequest(Request $request, $form, $em = null, $session = null)
    {

        if ($this->isPOST($request)) {
            $form->bind($request);

            if ($form->isValid()) {
                $postData   = $form->getData();
                $connection = $this->model->getConnectDB($em);
                $statement  = $this->model->getStatementDB($connection, $postData['Query:']);
                $results    = $this->model->getResult($statement);

                if (!$this->model->errorMessage) {
                    $session->set('queriesHistory',
                        $this->populatQueriesHistory((array)$session->get('queriesHistory'),
                            $postData['Query:']));
                }

                return array(
                    'form'           => $form->createView(),
                    'showResult'     => $this->showResult = true,
                    'results'        => $results,
                    'errorMessage'   => $this->model->errorMessage,
                    'queriesHistory' => $session->get('queriesHistory')
                );
            }
        }

        return array(
            'form'         => $form->createView(),
            'showResult'   => $this->showResult,
            'errorMessage' => $this->model->errorMessage,
            'queriesHistory' => $this->queriesHistory
        );
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
} 