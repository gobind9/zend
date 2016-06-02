<?php
namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\DbSelect;



class ProductTable {
   protected $tableGateway;	
   public function __construct(TableGateway $tableGateway) {
	    
        $this->tableGateway = $tableGateway;
		
    }
   
   public function fetchAll($page=1) {
        //$resultSet = $this->tableGateway->select();
		$select  = new Select();
		$select->from(array('products'=>'products'));
		$select->columns(array('id', 'name', 'id_uom','price_per_unit','qty_in_stock'));
		$select->join(array('measure_units'=>'measure_units'), 'products.id_uom = measure_units.id',array('mname'=>'name'),'INNER');        
		//$select->where("user.user_type=1 and id_customer=".$id);
		$this->tableGateway->getSql()->buildSqlString($select);
		$paginatorAdapter = new DbSelect($select, $this->tableGateway->getAdapter());
		  $paginator = new Paginator($paginatorAdapter);
		  $paginator->setItemCountPerPage(10);
		  $paginator->setPageRange(5);
		  $paginator->setCurrentPageNumber($page);
		  return $paginator;
       
    }
	
	

    public function getProduct($id) {
        $id = (int) $id;
		
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();		
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }  

   protected function createProduct(array $data) {
	   
        $sm = $this->getServiceLocator();
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
        $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();

        $resultSetPrototype->setArrayObjectPrototype(new
                \Store\Model\Product);
        $tableGateway = new \Zend\Db\TableGateway\TableGateway('product', $dbAdapter, null, $resultSetPrototype);
        $product = new Product();
        $product->exchangeArray($data);
        $productTable = new ProductTable($tableGateway);
        $productTable->saveProduct($product);
        return true;
    }

    public function saveProduct(Product $product) {

         $data = array(
                'name' => $product->name,
                'id_uom' => $product->id_uom,
                'price_per_unit' => $product->price_per_unit,
				'qty_in_stock' => $product->qty_in_stock,
            );

        $id = (int) $product->id;
		
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getProduct($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Product ID does not exist');
            }
        }
    }

    public function getProductByName($productName) {
        $rowset = $this->tableGateway->select(array('name' =>
            $productName));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $ productEmail");
        }

        return $row;
    }

    public function deleteProduct($id) {
        $this->tableGateway->delete(array('id' => $id));
    }
	
	public function measureUnit($dbadapter) {		

		$measure_unit = new TableGateway('measure_units',$dbadapter);
		$resultSet = $measure_unit->select(function (Select $select) {
			 $select->columns( array( 'id' , 'name' ) );
			 $select->order('name ASC')->limit(1000);
		});
		$resultSetArray = $resultSet->toArray();
		return $resultSetArray;
    }
	


 	public function updateData(array $data, array $where) {
        $this->tableGateway->update($data, $where);
    }
}