importScripts('helpers.js')

postMessage(['sliders', defaultControls.concat([
	{label: 'Line Count', value: 50, min: 10, max: 200},
	{label: 'Sampling', value: 1, min: 0.5, max: 2.9, step: 0.1},
	{label: 'Pen width', value: 5, min: 0.1, max: 20, step: 1},
	{label: 'Z steps', value: 8, min: 2, max: 255, step: 4},
//	{label: 'Join Ends', type:'checkbox'},
	{label: 'Always start at x=0', type:'checkbox'},
])]);


onmessage = function(e) {
	const [ config, pixData ] = e.data;
	const getPixel = pixelProcessor(config, pixData)

	const width = parseInt(config.width);
	const height = parseInt(config.height);
	const lineCount = parseInt(config['Line Count']);
	const spacing = parseFloat(config.Sampling);
	const incr_x = config.Sampling;
	const incr_y = Math.floor(config.height / lineCount);
	const pwidth = config['Pen width'];
	const step = config['Z steps'];
	const keep = config['Always start at x=0'];
	const joined = false;	//config['Join Ends']

	let squiggleData = [];
	if (joined) squiggleData[0]=[]
	let toggle = false;
	let horizontalLineSpacing = Math.floor(height / lineCount);
	let oldW =0;

	for (let y = 0; y < height; y+= horizontalLineSpacing) {
		let a = 0;
		oldW =0;
		toggle=!toggle;
		currentLine = [];
		currentLine.push([toggle?0:width, y]);

		for (let x = toggle? spacing: width-spacing; (toggle && x <= width) || (!toggle && x >= 0); x += toggle?spacing:-spacing ) {
			let z = getPixel(x, y)
		//	let r = amplitude * z / lineCount;
			let w = Math.round(z * step / 255) * pwidth / step;
			if ((x==0) || (x == config.width))// || (w != oldW))
			{	
				if (keep)
					currentLine.push([x, y, w]);
			}
			else if (w != oldW)
			{
				currentLine.push([x-1, y, oldW]);
				currentLine.push([x, y, w]);				
			}
			oldW = w;
		}

	//	if (joined && keep)
	//	  squiggleData[0]=squiggleData[0].concat(currentLine);
	//	else
		  squiggleData.push(currentLine)
	}

	postLines(squiggleData);
}

