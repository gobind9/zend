<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Session\Container; // We need this when using sessions

use Application\Form\LoginForm;
use Application\Form\LoginFilter;


class IndexController extends AbstractActionController
{
	protected $authservice;
	
    public function logoutAction() {
        if ($this->identity()) {
            $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
            $authService->clearIdentity();
        }
        return $this->redirect()->toRoute(NULL, array(
                                'controller' => 'index',
                                'action' => 'index'
                    ));
    }
    
    //login
    public function indexAction() {
    	$authdata = $this->getAuthService()->getStorage()->read();
        
    	$message = "";
        $form = new LoginForm();
  		$request = $this->getRequest();
    
		$form->setInputFilter(new LoginFilter());
   		$form->setData($request->getPost());
        
        ///POST /////
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $this->getAuthService()->getAdapter()->setIdentity($this->request->getPost('email'))->setCredential($this->request->getPost('password'));
            $result = $this->getAuthService()->authenticate();

            //echo $result->getIdentity() . "\n\n";
            $userData = $this->getAuthService()->getAdapter()->getResultRowObject();
            //print_r($result->isValid());die;
            //die;
            if ($result->isValid()) {
                $this->getAuthService()->getStorage()->write(
                        array(
                            'id' => $userData->id,
                            'name' => $userData->name,
                            'email' => $userData->email,
                        	'credit_limit' => $userData->credit_limit,
                            'user_type' => $userData->user_type,
                        )
                );
                //session 
                $user_session = new Container('myapp');
                $user_session->name = $userData->name;
                $user_session->email = $userData->email;
                $user_session->credit_limit = $userData->credit_limit;
                $user_session->user_type = $userData->user_type;
                
                if ($userData->user_type == '1') {
                    return $this->redirect()->toRoute(NULL, array(
                                'controller' => 'index',
                                'action' => 'index'
                    ));
                } else {
                    return $this->redirect()->toRoute(NULL, array(
                                'controller' => 'index',
                                'action' => 'index'
                    ));
                }
            } else {
                $message = "Username or password wrong. Please try again.";
                $this->flashMessenger()->addMessage($message);
                return $this->redirect()->toRoute(NULL, array(
                                'controller' => 'index',
                                'action' => 'index'
                    ));
            }
        }
        
        //////////        
        $viewModel = new ViewModel(array('form' => $form, 'flashMessages' => $this->flashMessenger()->getMessages()));
        return $viewModel;
    }
    
    //Database Table Authentication¶
    public function getAuthService() {
        if (!$this->authservice) {
            $authService = $this->getServiceLocator()->get('AuthService');
            $this->authservice = $authService;
        }
        return $this->authservice;
    }
    
    public function confirmAction() {
        // $user_email = $this->getAuthService()->getStorage()->read();
        $user_session = new Container('myapp');
        $name = $user_session->name;
        $viewModel = new ViewModel(array(
            'name' => $name
        ));
        return $viewModel;
    }
}
