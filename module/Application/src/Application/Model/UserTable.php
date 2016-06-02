<?php
namespace Application\Model;

//use Zend\Db\Adapter\Adapter;
//use Zend\Db\ResultSet\ResultSet;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Sql,Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;

class UserTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function saveUser(User $user)
    {
        $data = array(
            'email' => $user->email,
            'name'  => $user->name,
            'password'  => md5($user->password),
	        'remember_token'  => $user->remember_token,
	        'address1'  => $user->address1,
	        'address2'  => $user->address2,
	        'city'  => $user->city,
	        'country'  => $user->country,
	        'credit_limit'  => $user->credit_limit,
	        'user_type'  => $user->user_type,
	        'created_at'  => $user->created_at,
	        'updated_at'  => $user->updated_at,
        );

        $id = (int)$user->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getUser($id)) {
            	if (empty($data['password'])) {
            		unset($data['password']);
            	}
				if (empty($data['email'])) {
            		unset($data['email']);
            	}
				if (empty($data['user_type'])) {
            		unset($data['user_type']);
            	}
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('User ID does not exist');
            }
        }
    }
    
    public function fetchAll()
    {
    	$resultSet = $this->tableGateway->select();
    	return $resultSet;
    }
    
    public function getUser($id)
    {
    	$id  = (int) $id;
    	$rowset = $this->tableGateway->select(array('id' => $id));
    	$row = $rowset->current();
    	if (!$row) {
    		throw new \Exception("Could not find row $id");
    	}
    	return $row;
    }
	
	public function getCustomerList($page=1)
	{
		$select  = new Select();
		$select->from(array('user'=>'user'));
		$select->columns(array('name', 'email', 'city','credit_limit','id'));
		$select->join(array('order'=>'order'), 'order.id_user = user.id', array('totalOrder'=> new \Zend\Db\Sql\Expression('count(order.id)'),'totalAmount'=> new \Zend\Db\Sql\Expression('SUM(order_total)')),'Left');        
		$select->where("user.user_type=1");
		$select->group (array ("user.id"));
		$this->tableGateway->getSql()->buildSqlString($select);
		$paginatorAdapter = new DbSelect($select, $this->tableGateway->getAdapter());
		$paginator = new Paginator($paginatorAdapter);
		$paginator->setItemCountPerPage(10);
		$paginator->setPageRange(5);
		$paginator->setCurrentPageNumber($page);
		return $paginator;
    }
	
	public function getUserList($page=1)
	{
		$select  = new Select();
		$select->from(array('user'=>'user'));
		$select->columns(array('name', 'email', 'city','credit_limit','id'));
		$select->join(array('order'=>'order'), 'order.id_user = user.id', array('totalOrder'=> new \Zend\Db\Sql\Expression('count(order.id)'),'totalAmount'=> new \Zend\Db\Sql\Expression('SUM(order_total)')),'Left');        
		$select->where("user.user_type=0");
		$select->group (array ("user.id"));
		$this->tableGateway->getSql()->buildSqlString($select);
		$paginatorAdapter = new DbSelect($select, $this->tableGateway->getAdapter());
		$paginator = new Paginator($paginatorAdapter);
		$paginator->setItemCountPerPage(10);
		$paginator->setPageRange(5);
		$paginator->setCurrentPageNumber($page);
		return $paginator;
    }
	
	
	public function getCustomerOrderList($id, $page=1, $from='', $to='')
	{
		$select  = new Select();
		$select->from(array('order'=>'order'));
		$select->columns(array('id','total_cost', 'tax','order_total','order_date'));
		$select->join(array('user'=>'user'), 'order.id_user = user.id',array('name'),'INNER');        
		$select->where("user.user_type=1");
		if($id>0){
			$select->where("id_customer=".$id);
		}
		
		if($from!='' && $to==''){
			$select->where("order_date>='".$from."'");
		}
		
		if($from=='' && $to!=''){
			$select->where("order_date<='".$to."'");
		}
		
		if($from!='' && $to!=''){
			$select->where("(order_date >= '".$from."' and order_date <= '".$to."')");
		}
		
		$this->tableGateway->getSql()->buildSqlString($select);
		$paginatorAdapter = new DbSelect($select, $this->tableGateway->getAdapter());
		$paginator = new Paginator($paginatorAdapter);
		$paginator->setItemCountPerPage(1);
		$paginator->setPageRange(5);
		$paginator->setCurrentPageNumber($page);
		return $paginator;
    }
	
	
	public function getOrderDetailsList($id,$page=1)
	{
		$select  = new Select();
		$select->from(array('order'=>'order'));
		$select->columns(array('id','total_cost', 'tax','order_total','order_date'));
		$select->join(array('order_line'=>'order_line'), 'order.id = order_line.id_order',array('qty','sale_price_per_unit','id_product'),'Left');        
		$select->join(array('user'=>'user'), 'user.id = order.id_customer',array('customerName'=>'name'),'Left');        
		$select->join(array('product'=>'products'), 'product.id = order_line.id_product',array('productName'=>'name'),'Left');        
		$select->where("user.user_type=1 and order.id=".$id);
		//echo $this->tableGateway->getSql()->buildSqlString($select);
		$paginatorAdapter = new DbSelect($select, $this->tableGateway->getAdapter());
		$paginator = new Paginator($paginatorAdapter);
		$paginator->setItemCountPerPage(100);
		$paginator->setPageRange(5);
		$paginator->setCurrentPageNumber($page);
		return $paginator;
    }
	
	public function deleteOrder($id)
	{

		$adapter = $this->tableGateway->getAdapter();
        $sql     = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('order'=>'order'));
		$select->columns(array('id','total_cost', 'tax','order_total','order_date'));
		$select->join(array('order_line'=>'order_line'), 'order.id = order_line.id_order',array('qty','sale_price_per_unit','id_product','OLId'=>'id'),'Left');        
		$select->join(array('user'=>'user'), 'user.id = order.id_customer',array('customerName'=>'name', 'userId'=>'id'),'Left');        
		$select->join(array('product'=>'products'), 'product.id = order_line.id_product',array('productName'=>'name'),'Left');        
		$select->where("user.user_type=1 and order.id=".$id);
		//echo $resutl = $this->tableGateway->getSql()->buildSqlString($select);

		$statement = $sql->getSqlStringForSqlObject($select);
        $results   = $adapter->query($statement, $adapter::QUERY_MODE_EXECUTE);
		
		if(count($results)>0){
		
			foreach($results  as $result) {
				$amount  =$result->sale_price_per_unit;
				$userId  =$result->userId;
				$prod = $result->id_product;
				$prodQty = $result->qty;
				$orderId = $result->id;
				$OLId = $result->OLId;
				$dataUpdate = array('credit_limit'=>new \Zend\Db\Sql\Expression("credit_limit + ".(float)$amount));
				
				$this->tableGateway->update($dataUpdate, array('id' => $userId));
				
				if($this->isProductExits($prod)){
					$dataUpdatePro = array('qty_in_stock'=>new \Zend\Db\Sql\Expression("qty_in_stock + ".(float)$prodQty));
					//$adapter = $this->tableGateway->getAdapter();
					$sql = new Sql($adapter);
					$update = $sql->update();
					$update->table('products');
					$update->set($dataUpdatePro);
					$update->where('id = '.$prod.'');
					$statement = $sql->prepareStatementForSqlObject($update);
					$result = $statement->execute();
				}
					
				if($OLId>0){	
					//$adapter = $this->tableGateway->getAdapter();
					$sql = new Sql($adapter);
					$delete = $sql->delete('order_line')->where("id = $OLId"); 
					$statement = $sql->prepareStatementForSqlObject($delete);
					$statement->execute();
				}
			}
			
		} 
		
		$sql = new Sql($adapter);
		$delete = $sql->delete('order')->where("id = $id"); 
		$statement = $sql->prepareStatementForSqlObject($delete);
		$statement->execute();
		
    }
	
	 public function isProductExits($proid)
    {
    	$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('products');
		$select->where(array('id' => $proid));

		$selectString = $sql->buildSqlString($select);
		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		if(count($results)>0){
			return true;
			}else{
				return false;
		}
    }
	
    public function deleteUser($id)
    {
    	$this->tableGateway->delete(array('id' => $id));
    }
    
    /*
     * Get User account by Email
     */
    public function getUserByEmail($user_email)
    {
    	$rowset = $this->tableGateway->select(array('email' => $user_email));
    	$row = $rowset->current();
    	if (!$row) {
    		throw new \Exception("Could not find row $user_email");
    	}
    	return $row;
    }
	
	
	
	
}
