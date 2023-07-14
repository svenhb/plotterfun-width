importScripts('helpers.js')

postMessage(['sliders', defaultControls.concat([
	{label: 'Line Count', value: 50, min: 10, max: 200},
	{label: 'Sampling', value: 1, min: 0.5, max: 2.9, step: 0.1},
	{label: 'Pen width', value: 1, min: 0.1, max: 5, step: 0.1},
	{label: 'Z steps', value: 8, min: 2, max: 255, step: 4},
	{label: 'Always start at x=0', type:'checkbox'},
])]);


onmessage = function(e) {
	const [ config, pixData ] = e.data;
	const getPixel = pixelProcessor(config, pixData)

	const lineCount = config['Line Count'];
	const incr_x = config.Sampling;
	const incr_y = Math.floor(config.height / lineCount);
	const pwidth = config['Pen width'];
	const step = config['Z steps'];
	const keep = config['Always start at x=0'];
	
	let squiggleData = [];
	let oldW =0;
	
	for (let y = 0; y < config.height; y += incr_y) {
		let line = [];
		oldW=0;
		for (let x = 0; x <= config.width; x += incr_x) {
			let z = getPixel(x, y)		// 0 to 255
			let w = Math.round(z * step / 255) * pwidth / step;
			if ((x==0) || (x == config.width))// || (w != oldW))
			{	
				if (keep)
					line.push([x, y, w]);
			}
			else if (w != oldW)					// remove to get all pixels
			{
				line.push([x-1, y, oldW]);		// remove to get all pixels
				line.push([x, y, w]);				
			}
			oldW = w;
		}
		if (line.length > 0)
			squiggleData.push(line)
	}
	postLines(squiggleData);
}

