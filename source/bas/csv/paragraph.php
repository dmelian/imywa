<?php
class bas_csv_paragraph extends bas_csv_form{
	private $result=array();
	
	function addorder($properties=array()){
		if(count($this->result)>0)$index=count($this->result);
		else $index=0;
		$order=$properties[0];
		if($order=="text"){
			$aux=$properties[1];
			if(strlen($aux)<80){
				$this->result[$index]=$aux;
			}
			else{
				$pos;
				$line="";
				while(strlen($aux)>80){
					if(strpos($aux," ",80)>0){
						$pos=strpos($aux," ",80);
						$line=substr($aux,0,$pos);
						
					}else if(strrpos($aux," ",-80)>0){
						$pos=strrpos($aux," ",-80);
						$line=substr($aux,0,$pos);
					}else{
						$pos=80;
						$line=substr($aux,0,$pos);
					}
					$aux=substr($aux,$pos+1);
					$this->result[$index]=$line;
					$index++;
				}
				if(strlen($aux)>0){
					$index=count($this->result);
					$this->result[$index]=$aux;

				}
			}
			$index=count($this->result);
			$this->result[$index]="";
		}
	}
	function Onprint($filename){
		$vector=array();
		if($fp=fopen("/usr/local/imywa/run/".$filename,'w')){/*php://output*/
			$total=count($this->result);
			for($iter=0;$iter<$total;$iter++){
				$vector[0]=$this->result[$iter];
				$vector[1]="";
				fputcsv($fp,$vector,';');
			}
			fclose($fp);
			header("Content-Type:application/force-download");//header('Content-Type: text/csv; charset=utf-8');//
			header("Cache-Control: no-store, no-cache");
			header("Content-Disposition: attachment; filename=/usr/local/imywa/run/".$filename);
			header("Content-Transfer-Encoding: binary");
			header("Pragma:no-cache");
			header("Expires:0");
			header("Content-Length: " .filesize("/usr/local/imywa/run/".$filename));
			readfile("/usr/local/imywa/run/".$filename);
			flush();
		}
	}
}
?>