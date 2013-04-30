
function frmxform(themeId){

	if (arguments.length == 0) return; // to be able to inherit from this

	
	//Attributes
	//this.theme= new theme("theme/" + themeId);
	this.logText= '';
	this.bodyObjects= new Array();

	
	
	//Tomamos todas las divisiones que existen en el cuerpo y lanzamos su carga inicial (load).
	//Este procedimiento maqueta la división en el mínimo espacio posible.
	var myBodyChilds= document.getElementsByTagName('form')[0].childNodes;
	var myTopAdjustment= 0;
	for (var child= 0, object=0; child < myBodyChilds.length; child++){
		var myObjectCreated= false;
		if (myBodyChilds[child].tagName == 'DIV' && myBodyChilds[child].className != undefined){
			if (myBodyChilds[child].className in window) {
				this.bodyObjects[object]= new window[myBodyChilds[child].className](
					this
					, myBodyChilds[child]
				); 

				myBodyChilds[child].style.position= 'relative';
				myBodyChilds[child].style.top= myTopAdjustment;

				myTopAdjustment+= this.bodyObjects[object].relativePosAdjustment.top;
				myObjectCreated= true;
				object++;

			} else {
				if (myBodyChilds[child].className != '') {
					alert('No se ha definido la clase <'.concat(myBodyChilds[child].className, '> en el formulario.'));
				}
			}
		}
		//if (!myObjectCreated) document.getElementsByTagName('body')[0].removeChild(myBodyChilds[child--]);
		
	}


	
}

frmxform.prototype.resize= function(){
	var myTopAdjustment= 0;

	for (var o=0; o < this.bodyObjects.length; o++){
		this.bodyObjects[o].div.style.top= myTopAdjustment;
		myTopAdjustment+= this.bodyObjects[o].relativePosAdjustment.top; 
	}
};	
