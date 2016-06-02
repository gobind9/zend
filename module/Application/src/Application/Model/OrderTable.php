<?php

namespace Application\Model;

use Zend\Db\Sql\Select;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class OrderTable 
{
    protected $tableGateway;
    protected $adapter;

	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
    }

    public function saveOrder(Order $order) {
        $data = array(
            'id_user' => $order->id_user,
            'id_customer' => $order->id_customer,
            'total_cost' => $order->total_cost,
            'tax' => $order->tax,
            'order_total' => $order->order_total,
            'order_date' => $order->order_date,
        );

        $id = (int) $order->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            return $this->tableGateway->lastInsertValue;
        } else {
            if ($this->getOrder($id)) {
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

    public function getAllOrder() {
        $sqlSelect = $this->tableGateway->getSql()->select();
        $sqlSelect->columns(array('id', 'qty', 'name', 'email', 'stamp'));
        $sqlSelect->join('product', 'product.id = product_orders.store_product_id', array('filename', 'product_name' => 'name'), 'inner');
        $rowset = $this->tableGateway->selectWith($sqlSelect);
        return $rowset;
    }

    public function getOrder($orderId) {
        $orderId = (int) $orderId;
        $rowset = $this->tableGateway->select(array('id' => $orderId));
        $order = $rowset->current();
        if (!$order) {
            throw new \Exception("Could not find row $orderId");
        }
        return $order;
    }



}
