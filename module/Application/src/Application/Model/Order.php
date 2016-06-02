<?php

namespace Application\Model;

class Order {

    public $id;
    public $id_user;
    public $id_customer;
    public $total_cost;
    public $tax;
    public $order_total;
    public $order_date;
    

    public function __construct() {
        
    }

    function exchangeArray($data) {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->id_user = (isset($data['id_user'])) ? $data['id_user'] : null;
        $this->id_customer = (isset($data['id_customer'])) ? $data['id_customer'] : null;
        $this->total_cost = (isset($data['total_cost'])) ? $data['total_cost'] : null;
        $this->tax = (isset($data['tax'])) ? $data['tax'] : null;
        $this->order_total = (isset($data['order_total'])) ? $data['order_total'] : null;
        $this->order_date = (isset($data['order_date'])) ? $data['order_date'] : null;
        
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}
