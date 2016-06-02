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
use Application\Form\CustomerEditFilter;
use Application\Model\User;
use Application\Model\UserTable;
use Zend\Session\Container; // We need this when using sessions

class CustomerController extends AbstractActionController
{
   public function indexAction() { 
   
	 	$page = (int) $this->params()->fromQuery('page', 1);
		$userTable = $this->getServiceLocator()->get('UserTable');  
        $customers = $userTable->getCustomerList($page);
        $viewModel = new ViewModel(array('customers' => $customers));
        return $viewModel;
    }
	
	
	public function showorderAction() { 
	
		$id = (int) $this->params()->fromRoute('id', 0);
		$page = (int) $this->params()->fromQuery('page', 1);
        $userTable = $this->getServiceLocator()->get('UserTable');  
        $orders = $userTable->getOrderDetailsList($id,$page);
        $viewModel = new ViewModel(array('orders' => $orders));
        return $viewModel;
    }
	
	
	public function customerorderAction() {
		$page = (int) $this->params()->fromQuery('page', 1);
		
		$id = (int) $this->params()->fromRoute('id', 0);
		if($id==0){
			$id = (int) $this->params()->fromQuery('id', 0);
		}
		$from =  $this->params()->fromQuery('from','');
		$to  = $this->params()->fromQuery('to','');
        $userTable = $this->getServiceLocator()->get('UserTable');  
        $customers = $userTable->getCustomerOrderList($id, $page, $from, $to);
        $viewModel = new ViewModel(array('customers' => $customers,'from'=>$from,'to'=>$to,'id'=>$id));
        return $viewModel;
    }
	
	public function orderdeleteAction() {
		$id = (int) $this->params()->fromRoute('id', 0);
        $userTable = $this->getServiceLocator()->get('UserTable'); 		
        $delete = $userTable->deleteOrder($id);
        //$viewModel = new ViewModel(array('customers' => $customers));
        //return $viewModel;
		return $this->redirect()->toRoute('application/default',array('controller'=>'customer','action'=>'customerorder'));
    }
	
	public function addAction()
    {
		
		//$form = $this->getServiceLocator()->get('CustomerForm');
		$form = new CustomerForm();
		$request = $this->getRequest();
       if ($request->isPost()) {

				
			$form->setInputFilter(new CustomerFilter());
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
				$exchange_data['credit_limit'] = $this->params()->fromPost('credit_limit', null);
				$exchange_data['user_type'] = '1';
				$userData->exchangeArray($exchange_data);
				$userTable = $this->getServiceLocator()->get('UserTable');
				$userTable->saveUser($userData);
				
				//return $this->redirect()->toRoute('application/customer/index');
				return $this->redirect()->toRoute('application/default', array('controller' => 'customer', 'action' => 'index'));
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

				
			$form->setInputFilter(new CustomerEditFilter());
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
				$exchange_data['credit_limit'] = $this->params()->fromPost('credit_limit', null);
				$exchange_data['user_type'] = '1';
				$userData->exchangeArray($exchange_data);
				$userTable = $this->getServiceLocator()->get('UserTable');
				$userTable->saveUser($userData);
				
				//return $this->redirect()->toRoute('application/customer/index');
				return $this->redirect()->toRoute('application/default', array('controller' => 'customer', 'action' => 'index'));
				//return false;
			}
	   }
		$view = new ViewModel(array('form' => $form,'action' => 'edit','id'=>$id));		
		return $view;
       
    }
	
		
	
		public function deletecustomerAction()
	{
		
		$userTable = $this->getServiceLocator()->get('UserTable');
		$id = $this->params()->fromRoute('id');
		if($id!='')
		{
			$userTable->deleteUser(array('id'=>$id));
			
		}
		return $this->redirect()->toRoute('application/default',array('controller'=>'customer','action'=>'index'));
        		
	}
	
	
	
	 protected function createUser($data) {
        $userTable = $this->getServiceLocator()->get('UserTable');
        $userTable->saveUser($data);
        return true;
    }
}
