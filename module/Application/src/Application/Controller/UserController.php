<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Form\CustomerForm;
use Application\Form\CustomerFilter;
use Application\Form\UserFilter;
use Application\Form\UserEditFilter;
use Application\Model\User;
use Application\Model\UserTable;

class UserController extends AbstractActionController
{
     public function indexAction() { 
		$page = (int) $this->params()->fromQuery('page', 1);
        $userTable = $this->getServiceLocator()->get('UserTable');  
        $customers = $userTable->getUserList($page);
        $viewModel = new ViewModel(array('customers' => $customers));
        return $viewModel;
    }
	
	
	public function addAction()
    {
		
		//$form = $this->getServiceLocator()->get('CustomerForm');
		$form = new CustomerForm();
		$request = $this->getRequest();
       if ($request->isPost()) {

				
			$form->setInputFilter(new UserFilter());
			$form->setData($request->getPost());

			
			if ($form->isValid()) {

				$userData = new User();
				$exchange_data = array();
				$exchange_data['name'] = $this->params()->fromPost('name', null);
				$exchange_data['email'] = $this->params()->fromPost('email', null);
				$exchange_data['password'] = $this->params()->fromPost('password', null);
				$exchange_data['country'] = $this->params()->fromPost('country', null);
				$exchange_data['city'] = $this->params()->fromPost('city', null);
				$exchange_data['address1'] = $this->params()->fromPost('address1', null);
				$exchange_data['address2'] = $this->params()->fromPost('address2', null);
				//$exchange_data['credit_limit'] = $this->params()->fromPost('credit_limit', null);
				$exchange_data['user_type'] = '0';
				$userData->exchangeArray($exchange_data);
				$userTable = $this->getServiceLocator()->get('UserTable');
				$userTable->saveUser($userData);
				
				//return $this->redirect()->toRoute('application/customer/index');
				return $this->redirect()->toRoute('application/default', array('controller' => 'user', 'action' => 'index'));
				//return false;
			}
	   }
		$view = new ViewModel(array('form' => $form,'action' => 'add'));		
		return $view;
       
    }
	
	public function editAction()
    {
	
		$userTable = $this->getServiceLocator()->get('UserTable');
		$id = (int) $this->params()->fromRoute('id', 0);
         if (!$id) {
             return $this->redirect()->toRoute('application', array(
                 'action' => 'add'
             ));
         }
		 
		
        $userData = $userTable->getUser($id);

		$form = new CustomerForm();
		$form->bind($userData);
		$form->get('submit')->setAttribute('value', 'Edit');
		$request = $this->getRequest();
		
       if ($request->isPost()) {

				
			$form->setInputFilter(new UserEditFilter());
			$form->setData($request->getPost());
		
			if ($form->isValid()) {

				$userData = new User();
				$exchange_data = array();
				$exchange_data['id'] = $id;
				$exchange_data['name'] = $this->params()->fromPost('name', null);
				//$exchange_data['email'] = $this->params()->fromPost('email', null);
				//$exchange_data['password'] = $this->params()->fromPost('password', null);
				$exchange_data['country'] = $this->params()->fromPost('country', null);
				$exchange_data['city'] = $this->params()->fromPost('city', null);
				$exchange_data['address1'] = $this->params()->fromPost('address1', null);
				$exchange_data['address2'] = $this->params()->fromPost('address2', null);
				//$exchange_data['credit_limit'] = $this->params()->fromPost('credit_limit', null);
				$exchange_data['user_type'] = '0';
				$userData->exchangeArray($exchange_data);
				$userTable = $this->getServiceLocator()->get('UserTable');
				$userTable->saveUser($userData);
				
				//return $this->redirect()->toRoute('application/customer/index');
				return $this->redirect()->toRoute('application/default', array('controller' => 'user', 'action' => 'index'));
				//return false;
			}
	   }
		$view = new ViewModel(array('form' => $form,'action' => 'edit','id'=>$id));		
		return $view;
       
    }
	
		
	
	public function deleteuserAction()
	{
		
		$userTable = $this->getServiceLocator()->get('UserTable');
		$id = $this->params()->fromRoute('id');
		if($id!='')
		{
			$userTable->deleteUser(array('id'=>$id));
			
		}
		return $this->redirect()->toRoute('application/default',array('controller'=>'user','action'=>'index'));
        		
	}
	
	
	
	 protected function createUser($data) {
        $userTable = $this->getServiceLocator()->get('UserTable');
        $userTable->saveUser($data);
        return true;
    }
	
	public function processAction() {

        $upload = new User();
      
       
            $exchange_data = array();
            $exchange_data['name'] = $this->params()->fromPost('name', null);
            $exchange_data['filename'] = $uploadFile['name'];
            $exchange_data['price'] = $this->params()->fromPost('price', null);
            $exchange_data['description'] = $this->params()->fromPost('description', null);
            $upload->exchangeArray($exchange_data);
 
        // Create product
        $this->createProduct($upload);
        /*
          return $this->redirect()->toRoute(NULL, array(
          'controller' => 'product',
          'action' => 'confirm'
          ));
         */
        return false;
    }
}
