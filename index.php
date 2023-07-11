<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Plotterfun</title>
        <style>
            *{font-family:sans-serif}
            h2{padding:10px;margin:0}
            input~input[type=text] {width:40px; border:1px solid #999;vertical-align:3px}
            input[type=checkbox]{vertical-align:-5%;margin:0px 5px}
            input[type=number]{width:50px}
            label:hover{color:#4a6071}
            #sidebar {width:300px;background:#dfedf7}
            video{max-width:100%;transform:scaleX(-1)}
            canvas{max-width:100%; max-height:320px}
            svg{position:fixed; left:320px; top:10px}
            form{padding:2px; line-height:1.6em}
            #msgbox{position:fixed; left:320px; bottom:20px; padding:6px}
            #msgbox:not(:empty) {background:rgba(255,255,255,0.8)}
            #webcam{display:none}
            button{cursor:pointer; border:0;padding:5px 10px;margin:0 20px}
            #tabbar {background:#c3d7e5;}
            #tabbar button{margin:0}
            button{background:#c3d7e5;outline:none;font-weight:bold;color:#2f404d}
            button:hover{background:#5d90bc !important;color:#fff}
            #tabbar button.active{background:#99afbf}
            #imgselect,#webcam {text-align:justify;text-align-last:justify}
            #algoSelect{background:#d2e4f0;margin:5px 0;border:solid #5d90bc;border-width:2px 0 2px 0}
            a:hover{color:red}
            canvas{ /* checkerboard pattern */ 
              background-color: #f8f8f8;
              background-image: 
                linear-gradient(45deg, #e0e0e0 25%, transparent 25%, transparent 75%, #e0e0e0 75%, #e0e0e0),
                linear-gradient(45deg, #e0e0e0 25%, transparent 25%, transparent 75%, #e0e0e0 75%, #e0e0e0);
              background-size: 40px 40px;
              background-position: 0 0, 20px 20px;
            }
            #body { resize: both;
                    -webkit-transform: scale(0.8);
                    -moz-transform: scale(0.8);
                    -ms-transform: scale(0.8);
                    -o-transform: scale(0.8);
                    transform: scale(0.8);
                    -webkit-transform-origin: left top 0;
                    -moz-transform-origin: left top 0;
                    -ms-transform-origin: left top 0;
                    -o-transform-origin: left top 0;
                    transform-origin: left top 0;
            }
        </style>
        <link rel=stylesheet href=range.css>
    </head>
    <body id=body onload='preset("example_modell.png")'>
        <div id=sidebar>
            <h2>Plotterfun pen-width</h2>
            <div id=tabbar>
                <button id=tab1 onclick='tabImage()'>Image</button><button id=tab2 onclick='tabWebcam()'>Webcam</button>
            </div>
            <div id=imgselect>
                <form>
                    Canvas
                    W:<input id=pcw type=number min=10 value=800 oninput='changeSize()'>
                    H:<input id=pch type=number min=10 value=600 oninput='changeSize()'>
                </form>
                <canvas></canvas><br>
                <button onclick='selectImage()'>Select image</button>
                <button onclick='useImage()'>Use image</button>
            </div>
            <div id=webcam>
                <video autoplay></video>
                <button onclick='video.paused?video.play():video.pause()'>Pause</button>
                <button onclick='snapshot()'>Use image</button>
            </div>
            <form id=imgSelect>
                Examples: 
                <select id=examples onchange='preset(this.value)'>
                    <option value=example_test_tv.jpg>TV test pattern</option>
                    <option value=example_grbl.svg>grbl sign</option>
                    <option value=example_modell.png>GRBL-Plotter</option>
                    <option value=example_flying_devil.png>Flying devil</option>
                    <option value=example_cent.bmp>Cent</option>
                    <option value=example_fry.png>Fry</option>
                    <option value=example_einstein.jpg>Einstein</option>
                </select>
            </form>
            <form id=algoSelect>
                Algorithm: 
                <select id=algorithm onchange='loadWorker(this.value)'>
                    <option value=squiggle_z.js>Squiggle Z</option>
                    <option value=squiggleLeftRight_z.js>Squiggle Left/Right Z</option>
                    <option value=spiral_z.js>Spiral Z</option>
                    <option value=polyspiral_z.js>Polygon Spiral Z</option>
            <!--        <option value=sawtooth.js>Sawtooth</option>
                    <option value=stipple.js>Stipples</option>
                    <option value=delaunay.js>Delaunay</option>
                    <option value=linedraw.js>Linedraw</option>
                    <option value=mosaic.js>Mosaic</option>
                    <option value=subline.js>Subline</option>
                    <option value=springs.js>Springs</option>
                    <option value=waves.js>Waves</option>
                    <option value=needles.js>Needles</option>
                    <option value=implode.js>Implode</option>
                    <option value=halftone.js>Halftone</option>-->
                </select>
                <img id=buffering src=loading.gif style='vertical-align:middle; display:none'>
            </form>
            <form id=algoParams></form>

            <button style='font-size:large; width: 250px; margin:10px' onclick=download()>Download SVG</button>
            <button style='font-size:large; width: 250px; margin:10px' onclick=copytoclipboard()>Copy SVG to clipboard</button>

            <div style='padding:10px 15px;'>
                 Plotterfun by mitxela<br>
                 <a href=https://mitxela.com/projects/plotting>More info about mitxela</a><br> 
                 <a href=https://github.com/mitxela/plotterfun>Plotterfun 'classic' source code</a><br>
                 <a href=https://github.com/svenhb/plotterfun-width>Plotterfun 'pen-width' source code</a><br>
 				 <a href="..//plotterfun">Plotterfun classic</a><br>
		<!--		 <a href="..//plotterfun-width">Plotterfun pen-width</a><br>-->
				 <a href="..//plotterfun-color">Plotterfun color</a><br>
          </div>
        </div>
    <svg></svg>
    <div id=msgbox></div>
		<!-- a spacer gif -->
		<img src="https://grbl-plotter.de/plugins/hitcount/ping/ping.php?from=plotterfun" />

    <script src=helpers.js></script>
    <script>
    preview=document.querySelector("canvas")
    prectx=preview.getContext("2d")
    svg=document.querySelector('svg');
    video=document.querySelector('video');
    canvas=document.createElement("canvas")
    ctx=canvas.getContext("2d")

    config = {};

    img = null;
    scale = 1;
    offset = [0,0]
    // TODO
    // Allow editing path display style
    // "Animate" button
    // plot time estimate


    function checkScroll(){
        svg.style.position= (window.innerWidth < canvas.width+320 || window.innerHeight < canvas.height+10) ? "absolute" : "fixed";
    }
    window.onresize=checkScroll;

    function changeSize(){
        cw = parseInt(pcw.value)
        ch = parseInt(pch.value)
        preview.width=cw
        preview.height=ch
        draw()
        checkScroll()
    }
    changeSize()

    function tabImage(){
        if (video.srcObject && video.srcObject.getTracks().length) video.srcObject.getTracks()[0].stop()

        webcam.style.display='none'
        imgselect.style.display='block'
        tab1.className='active'
        tab2.className=''
    }
    tabImage()

    function preset(source){
        img = new Image();
        img.onload=function(){
            let w=img.width, h=img.height;

            cw=parseInt(pcw.defaultValue) // reset size - worth it?

            if (w>cw || h>ch) { ch=Math.round(cw*h/w) }
            else if (w>10&&h>10){ch=h;cw=w}
            preview.height = pch.value = ch 
            preview.width = pcw.value = cw

            scale = Math.min(ch/h, cw/w)
            offset = [ cw/2, ch/2 ]
            draw()
            setTimeout(useImage, 500);
        }
        img.src = source;	//'https://grbl-plotter.de/plotterfun/grbl.jpg';
    }

    function selectImage(){
        let d= document.createElement('input')
        d.type='file'
        d.onchange=function(e){
            img = new Image();
            img.onload=function(){
                let w=img.width, h=img.height;

                cw=parseInt(pcw.defaultValue) // reset size - worth it?

                if (w>cw || h>ch) { ch=Math.round(cw*h/w) }
                else if (w>10&&h>10){ch=h;cw=w}
                preview.height = pch.value = ch 
                preview.width = pcw.value = cw

                scale = Math.min(ch/h, cw/w)
                offset = [ cw/2, ch/2 ]
                draw()
            }
            img.src = URL.createObjectURL(e.target.files[0]);
        }
        d.click()
    }
    function draw(){
        if (!img) return
        prectx.clearRect(0,0,cw,ch)
        prectx.drawImage(img, offset[0]-scale*img.width*0.5,offset[1]-scale*img.height*0.5, scale*img.width, scale*img.height)
    }
    preview.onmousedown=function(e){
        let dx=e.clientX, dy=e.clientY
        let csc = cw / preview.getBoundingClientRect().width

        document.onmousemove=function(e){
            let x=e.clientX-dx, y=e.clientY-dy
            offset[0] += x*csc
            offset[1] += y*csc
            dx=e.clientX
            dy=e.clientY
            draw()
            return false
        }
        document.onmouseup=function(){
            document.onmousemove=null
            document.onmouseup=null
        }
        return false
    }
    preview.addEventListener("wheel", function(e){
        e.preventDefault();
        e.stopPropagation();
        let os = scale
        scale *= e.deltaY>0 ?1.1:0.9;
        offset[0] = ((offset[0] -cw/2)/os)*scale +cw/2
        offset[1] = ((offset[1] -ch/2)/os)*scale +ch/2
        draw()
    },{passive:false});

    function tabWebcam(){
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            alert("could not open webcam");
        } else 
        navigator.mediaDevices.getUserMedia({video: {width:800}}).then(function(v) {
            video.srcObject = v;
            webcam.style.display='block'
            imgselect.style.display='none'
            tab2.className='active'
            tab1.className=''
        }).catch((e)=>alert("error opening webcam"));
    }

    var imageset = false;
    function snapshot(){
        imageset=true;
        // config width height should come from sliders maybe?
        [canvas.width, canvas.height] = [video.videoWidth, video.videoHeight];
        ctx.drawImage(video, 0, 0)
        checkScroll()
        process()
    }
    function useImage(){
        if (!img) return
        imageset=true;
        [canvas.width, canvas.height] = [cw, ch];
        ctx.fillStyle='#fff'
        ctx.fillRect(0,0,cw,ch)
        ctx.drawImage(preview, 0,0)
        checkScroll()
        process()
    }
    function sendToWorker(msg){
        if (!window.onmessage) myWorker.postMessage(msg)
        else setTimeout(()=>window.onmessage({data:msg}),10)
    }
    function process(){
        if (!imageset) return;
        [config.width, config.height] = [canvas.width, canvas.height]
        resetSVG()
        loadWorker(window.algo)
        sendToWorker([ window.config, ctx.getImageData(0,0,config.width,config.height) ]);
    }
    function ctrlChange(control){
        if (!imageset) return;
        if (!control.noRestart) 
        process()
        else
        sendToWorker([ window.config ]);
    }
    function addController(control, form){
        if (!control.type) control.type = "range"
        let s = document.createElement("input")
        Object.assign(s, control)
        let l = document.createElement('label')
        let p = document.createElement('div')

        // Don't reset values when reloading controls with same name
        if (control.type !='checkbox'){
            if (config[control.label]) control.value = config[control.label];
            else config[control.label] = control.value;
        }

        if (control.type=='select') {
            s = document.createElement('select')
            s.oninput=e=>{config[control.label] = s.value; ctrlChange(control)}
            for (let i in control.options) {
                let selected = control.options[i]==config[control.label]
                s.append(new Option( control.options[i], control.options[i], selected, selected ))
            }
            l.append(control.label+': ')
            l.append(s)
            form.append(l)
            return
        } else if (control.type=='checkbox') {
            if (config[control.label]!==undefined) s.checked = config[control.label]
            s.oninput=e=>{config[control.label] = s.checked; ctrlChange(control)}
            config[control.label] = s.checked
            l.append(s)  
        } else {
            let n = document.createElement('input')
            n.type="text" // type=number does not allow fractions
            n.pattern="\-?[0-9]+\.?[0-9]*"
            s.oninput=e=>{config[control.label] = Number( n.value = s.value ); ctrlChange(control)}
            n.oninput=e=>{config[control.label] = Number( s.value = n.value ); ctrlChange(control)}
            n.onkeydown=function(e){
                if (e.keyCode!=38 && e.keyCode!=40) return;
                n.value = parseFloat(n.value) + (e.keyCode==38?1:-1) * (control.step || 1)

                // avoid floating point errors like 1.0000001 when step is fractional
                if (control.step && control.step != Math.round(control.step))
                n.value = parseFloat( (n.value*1).toFixed( control.step.toString().split('.')[1].length ) )

                n.oninput()
            };
            s.value = n.value = config[control.label] //.assign already set value, but value has to be set after min/max are set
            p.append(s);
            p.append(n)
        }
        l.append(control.label)
        l.append(p)
        form.append(l)
    }

    function appendScript(src) {
        var script = document.createElement('script');
        script.setAttribute('type', 'text/javascript');
        script.setAttribute('src', src);
        document.head.append(script);
    }

    function loadWorker(src){
        buffering.style.display='inline-block'
        if (window.myWorker) myWorker.terminate()

        if (window.location.protocol == 'file:') {
            // Workers not working - fall back to embedding the script
            window.myWorker={terminate:()=>{}}
            window.importScripts=function(...args){
              for (let a of args) appendScript(a)
            }
            window.postMessage=function(msg){
              myWorker.onmessage({data:msg})
            }
            appendScript(src)

        } else window.myWorker = new Worker(src);

        msgbox.innerHTML = "";

        myWorker.onmessage = function(msg) {
            let [type, data] = msg.data

            // setup, declare parameters
            if (type == 'sliders') {
                buffering.style.display='none'
                if (src==window.algo) return;
                window.algo=src
                algoParams.innerHTML="";
                data.forEach(c=>addController( c, algoParams ))
                process()

                // message, e.g. progress bar
            } else if (type == 'msg') {
                msgbox.innerHTML = data;

            } else if (type == 'dbg'){
                window.data = data
                console.log(data) 

            // vector data result
            }else if (type == 'svg-path'){
                mainpath.setAttributeNS(null, "d", data) 
			}else if (type == 'svg-line'){
				var att = data.split(";");				
				window.mainpath=document.createElementNS(svgNS, "line");
				mainpath.setAttributeNS(null, "x1", att[0]) 
				mainpath.setAttributeNS(null, "y1", att[1]) 
				mainpath.setAttributeNS(null, "x2", att[2]) 
				mainpath.setAttributeNS(null, "y2", att[3]) 
				mainpath.setAttributeNS(null, "stroke-width", att[4]) 
				mainpath.setAttributeNS(null, "stroke", config.Inverted?"white":"black") 
				svg.appendChild(mainpath)
			} 
        }
    }

    const svgNS = "http://www.w3.org/2000/svg"
	const rdfNS= "http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	const ccNS = "http://creativecommons.org/ns#"
	const dcNS= "http://purl.org/dc/elements/1.1/"
    function resetSVG(){
        // erase existing contents
        let c; while (c = svg.firstChild) svg.removeChild(c)
        svg.setAttribute("width",config.width)
        svg.setAttribute("height",config.height)
        svg.setAttribute("viewBox", `0 0 ${config.width} ${config.height}`)
        svg.style.background=config.Inverted?"black":"white";
   //     window.mainpath=document.createElementNS(svgNS, "path");
//      mainpath.setAttributeNS(null, "style", "stroke-width: 2px; fill: none; stroke: " + (config.Inverted?"white":"black"))
  //      mainpath.setAttributeNS(null, "style", "stroke:" + (config.Inverted?"white":"black")+";stroke-width:0.2mm;fill:none;")  // switched order
 //       svg.appendChild(mainpath)
		metadata=document.createElementNS(svgNS, "metadata");
		rdf=document.createElementNS(rdfNS, "RDF");
		work=document.createElementNS(ccNS, "Work");		
		description = document.createElementNS(dcNS, "description");
		text = "Generated with https://grbl-plotter.de/plotterfun-test/ \r\n";
		text += "ZDown=-"+config['Pen width']+";"		
		data = document.createTextNode(text);
		
		description.appendChild(data);
		work.appendChild(description);
		rdf.appendChild(work);
		metadata.appendChild(rdf);		
		svg.appendChild(metadata)
  }

    function download(){
        const svgString = '<?xml version="1.0" standalone="no"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">'
        + (new XMLSerializer()).serializeToString(svg);
        const blob = new Blob([svgString], {type: 'image/svg+xml;charset=utf-8'});

        const downloadLink = document.createElement("a");
        downloadLink.href = URL.createObjectURL(blob);
        downloadLink.download = window.algo.replace('.js','_') + Date.now() + ".svg";
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }
    function copytoclipboard(){
        const svgString = '<?xml version="1.0" standalone="no"?>\r\n'	//<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">\r\n'
        + (new XMLSerializer()).serializeToString(svg);

        navigator.clipboard.writeText(svgString);

        /* Copy the text inside the text field */  
        set_GRBL_Plotter_Update();
    }
    // Accessing the registry does not work from normal HTML - paste code manually in GRBL-Plotter
    function set_GRBL_Plotter_Update()
    {	var WshShell = new ActiveXObject('WScript.Shell');
        var reg_key="HKEY_CURRENT_USER\\SOFTWARE\\GRBL-Plotter\\";
        try 
        {	WshShell.RegWrite(reg_key + 'Update',1,'REG_DWORD');	
		}
        catch (err) {}
    }

    algorithm.onchange()


    </script>

    </body>
</html>