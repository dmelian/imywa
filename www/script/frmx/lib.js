//Scroll View

function scrollHandler(view, hscrollbar, vscrollbar){	
	this.view= view;
	this.view.addEventListener('DOMMouseScroll', this, false);
	this.view.addEventListener('mousewheel', this, false);
	this.hListeners= [];
	this.vListeners= [];
	
	if (hscrollbar != undefined){
		this.hscrollbar= hscrollbar;
		this.hscrollbar.addEventListener('scroll', this, false);
	}
	
	if (vscrollbar != undefined){
		this.vscrollbar= vscrollbar;
		this.vscrollbar.addEventListener('scroll', this, false);		
	}
};

scrollHandler.prototype.addListener= function(axis, listener){
	if (axis == 1 || axis == 'x' || axis == 'h') this.hListeners.push(listener);
	if (axis == 2 || axis == 'y' || axis == 'v') this.vListeners.push(listener);
};

scrollHandler.prototype.handleEvent= function(event){
	switch(event.type) {
	case 'scroll':
		if (this.hscrollbar != undefined) {
			view.style.left= -this.hscrollbar.scrollLeft + "px";
			for(var listener=0; listener < this.hListeners.length; listener++){
				this.hListeners[listener].OnHScroll(event.target.id);
			}
		}
		if (this.vscrollbar != undefined) {
			view.style.top= -this.vscrollbar.scrollTop + "px";
			for(var listener=0; listener < this.vListeners.length; listener++){
				this.vListeners[listener].OnVScroll(event.target.id);
			}
		}
		break;

	case 'DOMMouseScroll': 
		// Firefox: event.axis= 1-x,2-y; event.detail= ticks.
		this.scrollWheel(event.axis, event.detail*4);
		event.preventDefault();
		break;

	case 'mousewheel': 
		// event.wheelDelta= ticks*(-120); event.wheelDeltaX= ticksX*(-120); event.wheelDeltaY= ticksY*(-120);
		this.scrollWheel(event.wheelDeltaX != 0? 1 : 2, -event.wheelDelta/15);
		event.preventDefault();
		break;				
	}
};

scrollHandler.prototype.scrollWheel= function(axis, delta){
	switch (axis){
		case 1: // X axis
			if (this.hscrollbar != undefined) {
				this.hscrollbar.scrollLeft= this.hscrollbar.scrollLeft + delta;
				//TODO: Test for limits. 0 < scrollLeft < scrollsizewidth 
			}
			break;

		case 2: // Y axis
			if (this.vscrollbar != undefined) {
				this.vscrollbar.scrollTop= this.vscrollbar.scrollTop + delta;
				//TODO: Test for limits. 0 < scrollTop < scrollsizeheight 
			}
			break;
	}
};	


// Resize

function resizeHandler(handgrip, listener){
	this.dragging= false;
	this.dragginId= '';
	this.listener= listener;
	this.initialPoint= {x:0, y:0};
	
	handgrip.addEventListener('mousedown', this, false);
	handgrip.addEventListener('dragstart', this, false);
	handgrip.addEventListener('selectstart', this, false);
	
	window.addEventListener('mouseup', this, false);
};

resizeHandler.prototype.handleEvent= function(event){
	switch(event.type) {
	case 'dragstart': case 'selectstart': 
		event.preventDefault();
		break;

	case 'mousedown':
		this.dragging= true;
		this.initialPoint.x= event.clientX;
		this.initialPoint.y= event.clientY;
		this.draggingId= event.target.id;
		document.getElementsByTagName('body')[0].style.cursor= 'ew-resize'; 
		//document.childNodes[0].style.cursor= 'ew-resize'; //'se-resize';
		event.preventDefault();
		break;
	
	case 'mouseup':
		if (this.dragging) {
			document.getElementsByTagName('body')[0].style.cursor= 'auto'; 
			//document.childNodes[0].style.cursor= 'auto';
			this.dragging= false;
			//TODO: En lugar de lanzar un evento, puede redimensionar la división padre.
			this.listener.OnResize(this.draggingId
					, event.clientX - this.initialPoint.x
					, event.clientY - this.initialPoint.y);
			event.preventDefault();
		}
		break;
	}
	
};


function bas_copyAttributes(src, dst){
	for (var attr in src) dst[attr]= src[attr];
};
