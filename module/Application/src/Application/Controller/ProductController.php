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

use Application\Model\Product;
use Application\Model\ProductTable;
use Application\Model\Order;
use Application\Model\OrderTable;
use Application\Model\OrderLine;
use Application\Model\OrderLineTable;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Session\Container; // We need this when using sessions


class ProductController extends AbstractActionController
{
	protected $authservice;
   
	 public function __construct() {
        
    }
   
	public function getAuthService() {
        if (!$this->authservice) {
            $authService = $this->getServiceLocator()->get('AuthService');
            $this->authservice = $authService;
        }
        return $this->authservice;
    }
    public function indexAction() {	
	
        $page = (int)$this->params()->fromQuery('page', 1);
		$productTable = $this->getServiceLocator()->get('ProductTable');		
        $products = $productTable->fetchAll($page);
		
         
        $viewModel = new ViewModel(array('products' => $products));
        return $viewModel;
    }
    
    public function createAction() {
		$productTable 	= $this->getServiceLocator()->get('ProductTable');
		$dbadapter  	= $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');		
		$requestParams 	= $this->params()->fromRoute();
		
		$measureUnits 	= $productTable->measureUnit($dbadapter);
		$arrUnits 		= array();
		foreach($measureUnits as  $unit){
			$arrUnits[$unit['id']] = $unit['name'];
		}
		
		$id = isset($requestParams['id'])?$requestParams['id']:'';
		
		if(!empty($id)){			
			$productDetail = $productTable->getProduct($id);			
		}
		
        $form = $this->getServiceLocator()->get('ProductForm');
		if(!empty($id)){
			$form->bind($productDetail);
		}
        $viewModel = new ViewModel(array('form' => $form, 'id'=>$id,'measureUnits'=>$arrUnits));
		// $viewModel = new ViewModel(array('form' => $form,'requestParams'=>$requestParams));
        return $viewModel;
    }
	
    public function confirmAction() {
        $viewModel = new ViewModel(array());
        return $viewModel;
    }
    
    public function getFileUploadLocation() {
        // Fetch Configuration from Module Config
        $config = $this->getServiceLocator()->get('config');
        return $config['module_config']['upload_location'];
    }
    
    public function processAction() {
		
		$requestParams = $this->params()->fromRoute();
		$id = isset($requestParams['id'])?$requestParams['id']:'';
        $upload = new Product(); 
		$exchange_data = array();
		$exchange_data['id'] = $id;
		$exchange_data['name'] = $this->params()->fromPost('name', null);
		$exchange_data['id_uom'] = $this->params()->fromPost('id_uom', null);
		$exchange_data['price_per_unit'] = $this->params()->fromPost('price_per_unit', null);
		$exchange_data['qty_in_stock'] = $this->params()->fromPost('qty_in_stock', null); 
		
		$upload->exchangeArray($exchange_data);
        // Create product
        $this->createProduct($upload);
      
        return false;
    }
    protected function createProduct($upload) {
        $userTable = $this->getServiceLocator()->get('ProductTable');
        $userTable->saveProduct($upload);
		  
          return $this->redirect()->toRoute(NULL, array(
          'controller' => 'product',
          'action' => 'index'
          ));
        
        return true;
    }
	
	public function deleteproductAction()
	{
		
		$productTable = $this->getServiceLocator()->get('ProductTable');
		$id = $this->params()->fromRoute('id');
		if($id!='')
		{
			$productTable->deleteProduct(array('id'=>$id));
			
		}
		return $this->redirect()->toRoute('application/default',array('controller'=>'product','action'=>'index'));
        		
	}
    
    public function createorderAction(){
    	$authData = $this->identity();
    	$orderLineTable = $this->getServiceLocator()->get('OrderLineTable');
    	$cartData	= $orderLineTable->getCart(array('id_customer=?'=>$authData['id'],'id_order=?'=>0));
    	
    	$viewModel = new ViewModel(array('cartData'=>$cartData, 'flashMessages' => $this->flashMessenger()->getMessages()));
        return $viewModel;
    }
	
	public function addtocartAction() {	
	/*$session = new Container('myapp');
	echo "<pre>";
	print_r($session);
	echo "test".$session->id;*/
		
		
	
        $productTable = $this->getServiceLocator()->get('ProductTable');		
        $products = $productTable->fetchAll();
		
         
        $viewModel = new ViewModel(array('products' => $products));
        return $viewModel;	
		
		
    }
	
	public function addcartAction(){
		$authData = $this->identity();
		$productTable = $this->getServiceLocator()->get('ProductTable');
		$orderLineTable = $this->getServiceLocator()->get('OrderLineTable');
		$pid = $_POST['pid'];
		//echo $pid;exit;
		$customer_id = $authData['id'];

		//fetch product detail
		if(!empty($pid)){			
			$productDetail = $productTable->getProduct($pid);
		}
		$addcartstatus = $orderLineTable->addCart($customer_id,$pid);
		$qty = $addcartstatus['qty'];
		$newqty = $qty + 1;
		if($addcartstatus == 0){
			//insert cart data
			$productData = array();
			$productData['id_product'] = $pid;
			$productData['id_order'] = 0;			
			$productData['qty']  = 1;
			$productData['sale_price_per_unit'] = $productDetail->price_per_unit;
			$productData['id_customer'] = $customer_id;
			$status = $orderLineTable->saveCartProduct($addcartstatus,$productData);
			
		}else{
			//update cart data
			$productData = array();
			$productData['id_product']   = $pid;
			$productData['id_order'] = 0;
			$productData['qty']  = $newqty;			
			$productData['id_customer'] = $customer_id;
			$status = $orderLineTable->saveCartProduct($addcartstatus,$productData);
		}
		echo $status;
		exit;
	}
	
    
   /**
    * ajax request to get credit info
    */
	public function creditcheckAction(){
		$products = $this->params()->fromPost();		
		$authData = $this->identity();
		
		$available 	= 1;
		$amt		= 1;
		$sum_amt	= 0;
		$sum_qty 	= 0;
		$id_user 	= $authData['id'];
		$tax 		= 0;
		
		if(count($products) > 0 && isset($products['pid'])){
			$amount			= $authData['credit_limit'];
			$productTable 	= $this->getServiceLocator()->get('ProductTable');
			foreach($products['pid'] as $val){
				$productArr	= $productTable->getProduct($val);
				
				if($products['qty_in_stock_'.$val] > $productArr->qty_in_stock){
					$available	= 0;
					$msg 		= 'Quantity not available';
					break;
				}else{
					$sum_amt = $sum_amt + ($products['qty_in_stock_'.$val] * $productArr->price_per_unit);
					$sum_qty = $sum_qty + $products['qty_in_stock_'.$val];
				}
			}
				
			if($sum_amt > $amount){
				$amt	= 0;	
				$msg 	= 'Insufficient amount';
				//return Redirect::to('products/order');
			}
			
			if($sum_qty == 0){
				$msg 	= 'Quantity cannot be null.';
				//return Redirect::to('products/order');
			}
			
			
			//INSERT into order_line table
			if($amt ==1 && $available == 1){
				//insert into order table
				$order_total= $tax + $sum_amt;		
				
				//save order details
				$order = new Order(); 
				$exchange_data 	= array('id_user'=>$id_user,'id_customer'=>$id_user,'total_cost'=>$sum_amt,'tax'=>$tax,'order_total'=>$order_total);
				$order->exchangeArray($exchange_data);
				$orderTable = $this->getServiceLocator()->get('OrderTable');
				$id_order	= $orderTable->saveOrder($order);
        		
				//update order line table
				$orderLineTable = $this->getServiceLocator()->get('OrderLineTable');
				$orderLineTable->updateData(array('id_order'=>$id_order), array('id'=>$products['orderline']));
				
				
				$productTable = $this->getServiceLocator()->get('ProductTable');
				//update order table
				foreach($products['pid'] as $val){
					$productArr		= $productTable->getProduct($val);
					
					//reduce quantity from product table
					$qty_in_stock 		= $productArr->qty_in_stock - $products['qty_in_stock_'.$val];					
					$products_update 	= array('qty_in_stock'=>$qty_in_stock);
					
					$productTable->updateData($products_update, array('id'=>$val));
				}
				
				//update user credits
				//$this->getAuthService()->setStorage()->write(array('credit_limit' => $amount - $sum_amt));
				$this->getAuthService()->getStorage()->write(
                        array(
                            'id' => $authData['id'],
                            'name' => $authData['name'],
                            'email' => $authData['email'],
                        	'credit_limit' => $amount - $sum_amt,
                            'user_type' => $authData['user_type'],
                        )
                );
				//$user['credit_limit'] = $amount - $sum_amt;
				$msg 	= 'Order has been done successfully!';
			}
		}else{
			$msg 	= 'Invalid request';
		}	
		$this->flashMessenger()->addMessage($msg);
		return $this->redirect()->toRoute(NULL, array(
                                'controller' => 'product',
                                'action' => 'createorder'
                    ));
		
   	}
   	
   	public function deletefromcardAction(){
   		$id = $this->params()->fromRoute('id');
   		if(isset($id)){
	   		$orderLineTable = $this->getServiceLocator()->get('OrderLineTable');
			$orderLineTable->deleteData(array('id'=>$id));
	   		$this->flashMessenger()->addMessage('Deleted successfully.');
			
   		}else{
   			$this->flashMessenger()->addMessage('Invalid product.');
   		}

   		return $this->redirect()->toRoute(NULL, array(
	                                'controller' => 'product',
	                                'action' => 'createorder'
	                    ));
   	}
   	
	public function deletecartAction(){
		$authData = $this->identity();
   		$orderLineTable = $this->getServiceLocator()->get('OrderLineTable');
		$orderLineTable->deleteData(array('id_customer'=>$authData['id'], 'id_order'=>0));
   		$this->flashMessenger()->addMessage('Deleted successfully.');

   		return $this->redirect()->toRoute(NULL, array(
	                                'controller' => 'product',
	                                'action' => 'createorder'
	                    ));
   	}
    
}
