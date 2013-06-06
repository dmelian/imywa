<?php
/*
	This iterator is based on code found in
 	http://techblog.procurios.nl/k/news/view/33914/14863/Syntactic-Sugar-for-MySQLi-Results-using-SPL-Iterators.html
  	By Werner Segers
*/


class bas_sqlx_resultIterator implements Iterator{
	protected $result;
	protected $fetchMode;
	protected $position;
	protected $currentRow;
	protected $closed;
	
	//$fetchMode (MYSQLI_ASSOC, MYSQLI_NUM, MYSQLI_BOTH)
	public function __construct($result, $fetchMode = MYSQLI_ASSOC){
		$this->result= $result;
		$this->fetchMode= $fetchMode;
		$this->closed= false;
		$this->rewind();
	}
	
	public function close(){		
		if (!$this->closed) {
			$this->result->free();
			$this->closed= true;
		}
	}
	
	public function __destruct(){ $this->close(); }
	
	public function rewind(){
		$this->result->data_seek($this->position = 0);
		$this->currentRow = $this->result->fetch_array($this->fetchMode);
	}
	
	public function next(){
		$this->currentRow = $this->result->fetch_array($this->fetchMode);
		++$this->position;
	}
	
	public function valid(){
		return $this->position < $this->result->num_rows;
	}
	
	public function current(){
		return $this->currentRow;
	}
	
	public function key(){
		return $this->position;
	}
	
	
}
?>