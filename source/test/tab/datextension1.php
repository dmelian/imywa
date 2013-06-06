<?php
class dat_extension1{
	public $yo= 'Ilust.Sr.D. Domingo Melián Cárdenes';
	
	public function getcols(){
		return array(
			array('caption'=>'Copia codigo', 'id'=>'cod2')
			, array('caption'=>'Copia nombre', 'id'=>'nombre2')
			, array('caption'=>'Yo Mismo', 'id'=>'yo')
		);
	}
	
	public function mergedata(&$rec){
		$rec['cod2'] = $rec['codigo'];
		$rec['nombre2'] = $rec['nombre'];
		$rec['yo'] = $this->yo;
	}
}
?>