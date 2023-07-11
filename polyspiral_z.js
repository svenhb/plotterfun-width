importScripts('helpers.js')

postMessage(['sliders', defaultControls.concat([
  {label: 'Polygon', value: 4, min: 3, max: 8},
  {label: 'Spacing', value: 1, min: 0.5, max: 5, step: 0.1},
  {label: 'Pen width', value: 5, min: 0.1, max: 20, step: 1},
  {label: 'Z steps', value: 8, min: 2, max: 255, step: 4},
])]);


onmessage = function(e) {
  const [ config, pixData ] = e.data;
  const getPixel = pixelProcessor(config, pixData)
  let r = 5;
  let a = 0;

  const width = config['Pen width'];
  const step = config['Z steps'];

  const cx = config.width/2;
  const cy = config.height/2;
  let points = [];
  points.push([cx,cy])
  
  let x = cx, y = cy;
  let radius = 1;
  let theta = 0;
  let travelled = 0;
  let segmentLength = 1;
  const pi = Math.PI;
  
  let incrTheta = 2*pi/config.Polygon;
  let incrLength = Math.round(10/config.Polygon);

  while ( x>0 && y>0 && x<config.width && y<config.height ) {
  
    z = getPixel( x , y ) 
	let w = Math.round(z * step / 255) * width / step;
  
    r = z *0.02*config.Spacing;
    a += z;// / config.Frequency;
  
    let displacement = r;
    points.push([
     x - displacement*Math.sin(theta) ,
     y + displacement*Math.cos(theta) ,
	 w
    ])
    
    if (++travelled >= segmentLength){
      travelled = 0
      theta += incrTheta 
      segmentLength+= incrLength;
    }
    x += config.Spacing*Math.cos(theta)
    y += config.Spacing*Math.sin(theta)
  }

  postLines(points);
}

