<script>
var bgColors = ['#FF4000', '#FF8000', '#FE9A2E', '#FACC2E', '#F7FE2E', '#C8FE2E', '#9AFE2E', '#9AFE2E', 
	'#58FA82', '#58FAAC', '#58FAD0', '#58FAF4', '#58D3F7', '#58ACFA', '#5882FA', '#5858FA', '#8258FA', 
	'#AC58FA', '#D358F7', '#FA58F4', '#FA58D0', '#FA58AC', '#FA5882' ];

function generateBackground() {
	
	var col1 = Math.floor(Math.random() * bgColors.length);  
	var col2 = null;
	do {
		col2 = Math.floor(Math.random() * bgColors.length);  
	} while( col1 == col2 );
	
	width = 150;
	height = 150;
	var canvas = document.createElement('canvas');
	canvas.setAttribute("type", "hidden");

    canvas.width = width;
    canvas.height = height;

    var ctx = canvas.getContext("2d");
	ctx.globalCompositeOperation = "xor";

	var gradient = ctx.createLinearGradient(0,0,width, height);
	gradient.addColorStop(0,bgColors[col1]);
	gradient.addColorStop(1,bgColors[col2]);
	
	ctx.fillStyle = gradient;
	ctx.fillRect(0, 0, width, height);	
	
	dataURL = canvas.toDataURL("image/png");
	var html = document.documentElement;
	html.style['background-image'] = 'url(' + dataURL + ')';
	html.style['background-repeat'] = 'no-repeat'; 
	html.style['background-attachment'] = 'fixed';
	html.style['background-size'] = 'cover';
}

setTimeout( generateBackground(), 0);
</script>