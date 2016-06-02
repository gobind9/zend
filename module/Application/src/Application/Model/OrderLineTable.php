<?php

namespace Application\Model;

use Zend\Db\Sql\Select;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class OrderLineTable 
{
    protected $tableGateway;
    protected $adapter;

	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
    }

    public function saveOrderLine(OrderLine $order) {
        $data = array(
            'id_order' => $order->id_order,
            'id_product' => $order->id_product,
            'qty' => $order->qty,
            'sale_price_per_unit' => $order->sale_price_per_unit,
        );

        $id = (int) $order->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            return $this->tableGateway->lastInsertValue;
        } else {
            if ($this->getOrderLine($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Order ID does not exist');
            }
        }
    }

    public function fetchAll() {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getOrderLine($orderId) {
        $orderId = (int) $orderId;
        $rowset = $this->tableGateway->select(array('id' => $orderId));
        $order = $rowset->current();
        if (!$order) {
            throw new \Exception("Could not find row $orderId");
        }
        return $order;
    }
    
	public function getCart($whereArr=array()){
    	$sql  		= $this->tableGateway->getSql();
    	$select 	= $sql->select();
    	$select->columns(array('id', 'id_order', 'qty', 'sale_price_per_unit'));
    	$select->join(array('p'=>'products'), 'p.id = order_line.id_product', array('pid'=>'id','name','price_per_unit'), 'inner');
    	$select->join(array('mu'=>'measure_units'), 'mu.id = p.id_uom', array('unit_name'=>'name'), 'left');
    	count($whereArr) ? $select->where($whereArr) : '';
    	
    	$statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
    	//echo $this->tableGateway->getSql()->buildSqlString($select);die;
    	$resultSet = $statement->execute();
    	return $resultSet;
    }


	public function addCart($customer_id,$pid){
		    
        $select  = new Select();
		$select->from(array('order_line'=>'order_line'));
		$select->columns(array('id','qty'));		     
		$select->where("id_customer=".$customer_id." and id_product=".$pid." AND id_order=0");
		//$select->where("id_order",0);
		$select->order('id ASC')->limit(1);
		
		$this->tableGateway->getSql()->buildSqlString($select);
		$statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
		$resultSet = $statement->execute()->current();
		return $resultSet;
		
	}
	
	 public function saveCartProduct($addcartstatus,$productData) {
	
	 $adapter = $this->tableGateway->getAdapter();
     $sql = new Sql($adapter);
	 $id_product = 	$productData['id_product'];
	 $id_customer = $productData['id_customer'];
     if($addcartstatus == 0){
		 $dataUpdatePro = array('id_product'=>$id_product,'qty'=>$productData['qty'],'sale_price_per_unit'=>$productData['sale_price_per_unit'],'id_customer'=>$id_customer,'id_order' => 0);
		 $insert = $sql->insert('order_line');
		 $insert->values($dataUpdatePro);
		 $statement = $sql->prepareStatementForSqlObject($insert);
		 $result = $statement->execute();
		 
	 }else{		 
		 $dataUpdatePro = array('qty'=>$productData['qty']);		
		
		 $update = $sql->update();
		 $update->table('order_line');
		 
		 $update->set($dataUpdatePro);
		 $update->where('id_product = '.$id_product.' and id_customer = '.$id_customer.'');
		 
		 $statement = $sql->prepareStatementForSqlObject($update);
		 $result = $statement->execute();
		
	 }
	return true;	 
        
    }	
	

 	public function updateData(array $data, array $where) {
        $this->tableGateway->update($data, $where);
    }
    
	public function deleteData(array $where) {
        $this->tableGateway->delete($where);
    }



}
