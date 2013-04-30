<?php

class frmx_csv_prueba extends bas_frm_emptyhtml{
	private $csv_mode='csv1';
	
	public function OnCsv2(){
		$miobjeto= new bas_csv_listnew();
		$miobjeto->load("contaluz","concepto");
		$miobjeto->Onprint("tabla.csv");
		
	}
	public function OnCsv1(){
		$texto1=utf8_decode("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum at sagittis libero. Quisque eu eros at est pretium elementum. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Integer odio est, lacinia sed blandit eu, venenatis in elit. Donec laoreet lacus diam, eu sodales tortor. Curabitur nisl sem, porta non congue non, lacinia eget risus. Pellentesque ornare ligula vel nulla varius eu laoreet est commodo. Duis sed hendrerit velit. Proin tristique accumsan magna, non hendrerit dolor malesuada ut. Duis sit amet tincidunt leo. Sed rhoncus cursus ipsum, sed pretium lacus gravida ut. Donec at ipsum id dui lacinia pulvinar. Morbi urna lacus, molestie sed pulvinar a, vestibulum vel nunc.");
		$texto2=utf8_decode("Aliquam volutpat diam eget ante malesuada condimentum. Aenean et lectus ac sapien aliquet luctus a nec nisi. Aliquam lobortis gravida neque et ornare. Aliquam erat volutpat. Curabitur ornare neque eget ipsum blandit eu varius odio dictum. Mauris tempus aliquet mauris, quis dapibus orci malesuada vel. Fusce in elit placerat nisi lacinia aliquam. Praesent porta magna non quam rhoncus sed cursus erat suscipit. Nulla eros metus, cursus eget fermentum et, rhoncus sed erat. Donec porttitor, justo faucibus commodo varius, eros quam ultrices arcu, ac vestibulum sem lacus eu mauris. Cras arcu orci, vestibulum a sodales condimentum, lacinia ac arcu. Fusce posuere, ligula nec auctor pulvinar, augue nunc condimentum tortor, eu venenatis felis eros ac tellus. Nunc ullamcorper lectus a leo adipiscing tincidunt nec eget arcu. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Aliquam commodo sapien non purus dapibus tempus.");
		$texto3=utf8_decode("Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vestibulum ut ligula eu mauris vulputate dapibus. Aenean orci velit, condimentum nec mollis ut, rhoncus quis metus. Mauris faucibus imperdiet diam pulvinar malesuada. Quisque est lacus, placerat id pulvinar eget, venenatis quis lacus. Pellentesque adipiscing nulla mi. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut tempus felis eu risus dapibus quis euismod enim gravida. Aliquam scelerisque gravida tincidunt. In hac habitasse platea dictumst. Donec nec orci leo. Mauris varius lorem et nulla ullamcorper pellentesque. In hac habitasse platea dictumst.");
		$texto4=utf8_decode("Pellentesque ultrices, enim id imperdiet congue, magna sapien imperdiet ipsum, vitae cursus sapien tellus at diam. Fusce sed leo hendrerit velit ullamcorper posuere et in urna. Mauris ut adipiscing magna. Cras ut dolor quis odio tincidunt viverra sed id velit. Praesent ut diam est. Maecenas vitae tellus mauris. Proin consectetur mollis diam, vehicula viverra turpis elementum egestas. Sed et orci id dui sollicitudin consectetur. In aliquet magna et metus tincidunt at condimentum metus suscipit. Ut sit amet lorem metus, sed congue nunc. Integer id posuere nulla. Fusce a lectus id arcu posuere semper. ");
		$texto5=utf8_decode("Quisque blandit porta mauris eget congue. Pellentesque dapibus tortor nec orci porttitor gravida. Morbi sollicitudin posuere nulla non vehicula. Mauris pellentesque interdum sodales. Aliquam velit nulla, malesuada imperdiet malesuada id, dictum at risus. Sed ipsum orci, ornare ut fringilla et, rutrum id augue. Mauris in orci a nisi scelerisque mattis. Duis tincidunt pellentesque purus, sit amet commodo est ornare quis. Sed suscipit sollicitudin orci, a pretium velit rutrum quis. Pellentesque pellentesque rhoncus viverra. Aenean in lorem in sem scelerisque malesuada sed at dui. Morbi bibendum pretium nisi, ac iaculis ligula condimentum quis. Fusce iaculis libero aliquet ipsum interdum a convallis dui placerat. Vivamus volutpat pretium tortor, ut placerat urna eleifend eu. Fusce a risus mauris, sed aliquam leo. Fusce rutrum sapien non ipsum cursus eu convallis dolor sagittis.");
		$miobjeto=new bas_csv_paragraph();
		$vector=array();

		$vector[0]="setFont";
		$vector[1]="Arial";
		$vector[2]=12;
		$vector[3]="255,0,0";
		$vector[4]="bold";
		$miobjeto->addorder($vector);
		//unset($vector);
		$vector="";

		$vector[0]="text";
		$vector[1]=$texto1;
		$vector[2]="";
		$vector[3]=0;
		$miobjeto->addorder($vector);
		//unset($vector);
		$vector="";
		/*$miobjeto->setFont('Arial',12, "0,255,0","underline");
		$miobjeto->text($texto2,"right",false);*/

		$vector[0]="setFont";
		$vector[1]="Arial";
		$vector[2]=12;
		$vector[3]="0,255,0";
		$vector[4]="underline";
		$miobjeto->addorder($vector);
		//unset($vector);
		$vector="";

		$vector[0]="text";
		$vector[1]=$texto2;
		$vector[2]="right";
		$vector[3]=0;
		$miobjeto->addorder($vector);
		//unset($vector);
		$vector="";
		/*$miobjeto->setFont('Arial',12, "0,0,255","italic");
		$miobjeto->text($texto3,"center",false);*/

		$vector[0]="setFont";
		$vector[1]="Arial";
		$vector[2]=12;
		$vector[3]="0,0,255";
		$vector[4]="italic,underline";
		$miobjeto->addorder($vector);
		//unset($vector);
		$vector="";

		$vector[0]="text";
		$vector[1]=$texto3;
		$vector[2]="center";
		$vector[3]=0;
		$miobjeto->addorder($vector);
		//unset($vector);
		$vector="";

		$vector[0]="image";
		$vector[1]="http://elpatiodeatras.com/blog/wp-content/uploads/2009/06/lorem_ipsum.gif";
		$miobjeto->addorder($vector);
		//unset($vector);
		$vector="";
		/*$miobjeto->setFont('Arial',12, "0","bold,italic,underline");
		$miobjeto->text($texto4,"justify",false);*/

		$vector[0]="setFont";
		$vector[1]="Arial";
		$vector[2]=12;
		$vector[3]="0";
		$vector[4]="bold,italic,underline";
		$miobjeto->addorder($vector);
		//unset($vector);
		$vector="";

		$vector[0]="text";
		$vector[1]=$texto4;
		$vector[2]="justify";
		$vector[3]=0;
		$miobjeto->addorder($vector);
		//unset($vector);
		$vector="";

		$vector[0]="setFont";
		$vector[1]="Arial";
		$vector[2]=12;
		$vector[3]="126";
		$vector[4]="";
		$miobjeto->addorder($vector);
		//unset($vector); 
		$vector="";

		$vector[0]="text";
		$vector[1]=$texto5;
		$vector[2]="left";
		$vector[3]=1;
		$miobjeto->addorder($vector);
		//unset($vector);
		$vector="";

		$miobjeto->Onprint("texto.csv");
	}
	
	public function OnCsv(){
		switch($this->csv_mode){
			case 'csv1':
				$this->OnCsv1();//parrafo
				break;
			
			case 'csv2': 
				$this->OnCsv2();//lista
				break;
				
		}
		
	}
	
	public function OnAction($action, $data){
		switch($action){
			case 'csv1': case 'csv2': $this->csv_mode=$action; return array('csv');  
			case 'close': return array($action);
			
		}
	}
	
}

?>