<?php
class bas_csv_form{
	private $fp;
	protected $success;
	private $filename;
	
	public function __construct($name){
		$this->filename=$name;
	}
	
	function Onprint($filename){
	}
	
	public function open(){
		if($this->fp=fopen("/usr/local/imywa/run/".$this->filename,'w'))$this->success=TRUE;
		else $this->success=FALSE;
	}
	
	public function close(){
		fclose($this->fp);
		header("Content-Type:application/force-download");//header('Content-Type: text/csv; charset=utf-8');//
		header("Cache-Control: no-store, no-cache");
		header("Content-Disposition: attachment; filename=/usr/local/imywa/run/".$this->filename);
		header("Content-Transfer-Encoding: binary");
		header("Pragma:no-cache");
		header("Expires:0");
		header("Content-Length: " .filesize("/usr/local/imywa/run/".$this->filename));
		readfile("/usr/local/imywa/run/".$this->filename);
		//ob_end_clean();
		flush(); //si no funciona el ob_end_clean
	}
	
	public function write($vector){
		fputcsv($this->fp,$vector,';');
	}
}
?>