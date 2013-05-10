<?php
/*
	Copyright 2009-2012 Domingo Melian

	This file is part of imywa.

	imywa is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	imywa is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with imywa.  If not, see <http://www.gnu.org/licenses/>.
*/


class bas_sqlx_cronoPointer extends bas_sqlx_dataview{
	protected $cronoHeaders=array();
// 	protected $period;
// 	protected $date;
	
// 	protected function pivotFormat(){
//         $container = array();
//         $pos = 0;
//         foreach($this->current as $row){
//             foreach($row as $field => $value){
//                 if (($field != $this->pivot["pivot"])  && ($field != $this->pivot["value"])){ // nos enctramos con un campo que no pertenece a los campos pivot ni valor.
//                     if (isset($container[$pos][$field])){
//                         if ($container[$pos][$field] != $value){  // hemos encontrado un campo distinto al anterior, por lo que se ha completado este registro.
//                             $pos++;
//                             $container[$pos][$field] = $value;
//                         }
//                     }
//                     else
//                         $container[$pos][$field] = $value;
//                 }
//                 else{
//                     if ($field == $this->pivot["pivot"]){
//                         $container[$pos][$value] = $row[$this->pivot["value"]];
//                         $this->cronoHeaders[]= $value;
//                     }
//                 }
//                     
//             }
//         }
//         $this->current = $container;
//     }
	
	/*public function setDate($date){
        $this->date = $date;
	}
	
	public function setPeriod($period){
        $this->period = $period;
	}*/
	
    public function load_data($date,$period){
        $proc = new bas_sql_myextprocedure('imywa');
        $this->connect = $proc->connection;
        if ($proc->success){ 
            $proc->call('createCronoHeaders', array($date, $period),'imywa'); 
//             $proc->call('createCronoHeaders', array('2010-05-05', 'week'),'imywa');
            
            $qry = "select name from imywa.cronoHeaders";

            $ds = new bas_sql_myqrydataset($qry,'','',$this->connect);       
            $rec = $ds->reset();
            
            while ($rec){ // obtenemos los periodos por factura
                $this->cronoHeaders[]= $rec["name"];
                $rec = $ds->next();         
            }         
            
            parent::load_data();
            $proc->commit();

            $aux = $this->cronoHeaders;
            $this->cronoHeaders = array();
            return $aux;
        }
        else    
            return array();
    }
    
    /*
    public function load_data($date,$period){
        $proc = new bas_sql_myextprocedure('imywa');
        $this->connect = $proc->connection;
        if ($proc->success){ 
            $proc->call('createCronoHeaders', array($this->date, $this->period),'imywa'); 
//             $proc->call('createCronoHeaders', array('2010-05-05', 'week'),'imywa');
            
            $qry = "select name from imywa.cronoHeaders";

            $ds = new bas_sql_myqrydataset($qry,'','',$this->connect);       
            $rec = $ds->reset();
            
            while ($rec){ // obtenemos los periodos por factura
                $this->cronoHeaders[]= $rec["name"];
                $rec = $ds->next();         
            }         
            
            parent::load_data();
            $proc->commit();

            $aux = $this->cronoHeaders;
            $this->cronoHeaders = array();
            return $aux;
        }
        else    
            return array();
    }
    
    */
    
    
    
}

?>
