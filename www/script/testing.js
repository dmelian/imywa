function addhidden(hidname, hidvalue){
	var newinput = document.createElement("input");
	
	newinput.setAttribute("type", "hidden");
	newinput.setAttribute("name", hidname);
	newinput.setAttribute("value", hidvalue);

	document.forms[0].appendChild(newinput);
}


function onload(){
//	sendxhr("action=onloadxhraction");
	new frmxform("default");
}


var times=0;

function sendxhr(data){
	var rq = new XMLHttpRequest();
	rq.open('POST','xhrindex.php', true);
	rq.onreadystatechange = function(aEvt){
		if (rq.readyState == 4 && rq.status == 200) {
			if (rq.responseText != '') alert('getting response:('+ rq.responseText +')');
		} //rq.responseText contiene la respuesta.
	} 
/* 	------ METODO POST
    ------ Para los nuevos Firefox
 	var data = new FormData();
	data.append('Nombre','Domingo');
	data.append('Apellidos', 'Melián Cárdenes');
	------ Para los antiguos
*/
	times++;
	var post = encodeURI("sessionno=" + document.getElementsByName('sessionno')[0].value
			+ "&installationid=" + document.getElementsByName('installationid')[0].value
			+ "&sequenceno=" + document.getElementsByName('sequenceno')[0].value
			+ "&times=" + times
			+ "&" + data
			);
	rq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
//	rq.setRequestHeader("Content-length", post.length);
//	rq.setRequestHeader("Connection", "close");
	
	rq.send(post);
}