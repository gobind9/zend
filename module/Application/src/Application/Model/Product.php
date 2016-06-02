<?php
namespace Application\Model;

class Product {
	
	protected $_name = 'products';

    public $id;
    public $name;
    public $id_uom;
    public $price_per_unit;
    public $qty_in_stock;

    function exchangeArray($data) {
        $this->id             = (isset($data['id'])) ? $data['id'] : null;
        $this->name           = (!empty($data['name'])) ? $data['name'] : null;
        $this->id_uom         = (!empty($data['id_uom'])) ? $data['id_uom'] : null;
        $this->price_per_unit = (!empty($data['price_per_unit'])) ? $data['price_per_unit'] : null;
        $this->qty_in_stock   = (!empty($data['qty_in_stock'])) ? $data['qty_in_stock'] : null;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }   

}