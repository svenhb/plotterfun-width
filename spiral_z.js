importScripts('helpers.js')

postMessage(['sliders', defaultControls.concat([
  {label: 'Spacing', value: 1, min: 0.5, max: 5, step: 0.1},
  {label: 'Pen width', value: 5, min: 0.1, max: 20, step: 1},
  {label: 'Z steps', value: 8, min: 2, max: 255, step: 4},
])]);



onmessage = function(e) {
  const [ config, pixData ] = e.data;

  const spacing = parseFloat(config.Spacing);
  const width = config['Pen width'];
  const step = config['Z steps'];

  const getPixel = pixelProcessor(config, pixData)
  let r = 5;
  let a = 0;

  const cx = config.width/2;
  const cy = config.height/2;
  let points = [];
  points.push([cx,cy])
  
  let x = cx, y = cy;
  let radius = 1;
  let theta = 0;
  
  while ( x>0 && y>0 && x<config.width && y<config.height ) {
  
    z = getPixel( x , y ) 
	let w = Math.round(z * step / 255) * width / step;
  
    let tempradius = radius;
    points.push([
     cx + tempradius*Math.sin(theta) ,
     cy + tempradius*Math.cos(theta) ,
	 w
    ])
   
    let incr = Math.asin(1/radius);
    radius +=incr*spacing;
    theta +=incr;

    x = Math.floor( cx + radius*Math.sin(theta))
    y = Math.floor( cy + radius*Math.cos(theta))
  }


  postLines(points);
}


