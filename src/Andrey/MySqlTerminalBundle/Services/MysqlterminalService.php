<?php
namespace Andrey\MySqlTerminalBundle\Services;
use Symfony\Component\HttpFoundation\Request;

use Andrey\MySqlTerminalBundle\Models\MysqlterminalModel;
class MysqlterminalService {
    protected $model      = null;
    protected $showResult = false;

    public function __construct()
    {
        $this->model = new MysqlterminalModel();
    }

    public function checkRequest(Request $request, $form, $em = null)
    {
        if ($this->isPOST($request)) {
            $form->bind($request);

            if ($form->isValid()) {
                $arr        = $form->getData();
                $connection = $this->model->getConnectDB($em);
                $statement  = $this->model->getStatementDB($connection, $arr['Query:']);
                $results    = $this->model->getResult($statement);

                return array(
                                'form'         => $form->createView(),
                                'showResult'   => $this->showResult = true,
                                'results'      => $results,
                                'errorMessage' => $this->model->errorMessage
                            );
            }
        }

        return array(
                        'form'         => $form->createView(),
                        'showResult'   => $this->showResult,
                        'errorMessage' => $this->model->errorMessage
                    );
    }

    public function isPOST($request)
    {
        return $request->getMethod() == 'POST' ? : false;
    }
} 