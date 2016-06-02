<?php

namespace Application\Model;

class OrderLine {

	public $id;
	public $id_order;
	public $id_product;
	public $qty;
	public $sale_price_per_unit;    

    public function __construct() {
        
    }

    function exchangeArray($data) {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->id_order = (isset($data['id_order'])) ? $data['id_order'] : null;
        $this->id_product = (isset($data['id_product'])) ? $data['id_product'] : null;
        $this->qty = (isset($data['qty'])) ? $data['qty'] : null;
        $this->sale_price_per_unit = (isset($data['sale_price_per_unit'])) ? $data['sale_price_per_unit'] : null;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}
