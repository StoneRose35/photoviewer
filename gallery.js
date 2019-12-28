function init(x)
{
	var img_el = document.getElementById("olimage");
	img_el.addEventListener("load",function(){on_overlay_loaded(img_el);},false);
	
	if (x*1 == 1)
	{
		var c_page = window.location.search.match(/page=([0-9]*)/);
		var first_el = document.getElementById("thmb" + c_page[1] + "_" + c_page[1]*get_page_size());
		openoverlay(first_el);
	}
	else if (x*1 == 2)
	{
		var c_page = window.location.search.match(/page=([0-9]*)/);
		var last_el = document.getElementById("thmb" + c_page[1] + "_" + ((c_page[1]*1+1)*get_page_size()-1));
		openoverlay(last_el);
	}    	
}

function processImageElement(el)
{
	if (document.getElementById("rotate_images").checked == true)
	{
		var cur_style = el.style.transform;
		var cur_src = el.src;
		var new_rot;
		cur_src = cur_src.replace(/http:\/\/.*\/photos\/thumbnail.php\//,"");
		var c_path = document.getElementById("imgpath_title").textContent;
		cur_src = cur_src.replace(c_path + "/","");
		if (cur_style == "")
		{
			new_rot = "rotate(90deg)";
		}
		else if (cur_style == "rotate(90deg)")
		{
			new_rot = "rotate(180deg)";
		}
		else if (cur_style == "rotate(180deg)")
		{
			new_rot = "rotate(270deg)";
		}
		else
		{
			new_rot = "";
		}
		el.style.transform = new_rot;
		
		const Http = new XMLHttpRequest();
		const url='/photos/rotate_image.php?path=' + c_path + "&image=" + cur_src + "&rotation=" + new_rot;
		Http.open("GET", url);
		Http.send();

		Http.onreadystatechange = (e) => {
		  console.log(Http.responseText)
		}
	}
	else
	{
		openoverlay(el);
	}
}

function openoverlay(el)
{
	var current_src = el.src;
        var olimage = document.getElementById("olimage");
        olimage.style.transform = el.style.transform;
	olimage.src=current_src.replace(/\/photos\/thumbnail.php\//,'/bilder/');
	olimage.alt = el.id;
	var el_loading =  document.getElementById("ol_loading");
    	el_loading.style.padding = document.documentElement.scrollHeight/2 + "px 0";
        el_loading.style.display = "block";
}

function on_overlay_loaded(el)
{
    document.getElementById("ol_loading").style.display = "none";
	document.getElementById("overlay").style.display = "block";
        var olimage = document.getElementById("olimage");

        var ol_inner=document.getElementById("overlay");
	if (olimage.style.transform=="rotate(90deg)" || olimage.style.transform=="rotate(270deg)")
	{
        	var img_width = olimage.naturalHeight;
        	var img_height = olimage.naturalWidth;
	} 
	else
	{
       		var img_width = olimage.naturalWidth;
        	var img_height = olimage.naturalHeight;
	}
        var img_ratio = img_height/img_width;
    var c_height=ol_inner.clientHeight;
    var c_width=ol_inner.clientWidth;
    var c_img_ratio = c_height/c_width;
    if (olimage.style.transform=="rotate(90deg)")
    {
		olimage.style.transformOrigin = "left";
		olimage.style.transform = "translate(50%, -50%) " + olimage.style.transform;
		if (img_ratio > c_img_ratio)
		{
			olimage.style.width = c_height;
			olimage.style.height = c_height/img_ratio;

		}
		else
		{   
			olimage.style.height = c_width;
			olimage.style.width = c_width/img_ratio;
		}
	}
	else if (olimage.style.transform=="rotate(270deg)")
	{
		olimage.style.transformOrigin = "center center";
		if (img_ratio > c_img_ratio)
		{
			olimage.style.width = c_height;
			olimage.style.height = c_height/img_ratio;

		}
		else
		{   
			olimage.style.height = c_width;
			olimage.style.width = c_width/img_ratio;
		}
		var tl_offet = olimage.width/2 - olimage.height/2;
		olimage.style.transform = "translate(0px, " + tl_offet + "px) " + olimage.style.transform;
	}
    else
    {
            olimage.style.transformOrigin = "inherit";
	    //olimage.style.transform = el.style.transform;
	    if (img_ratio > c_img_ratio)
	    {
		olimage.style.height = c_height;
		olimage.style.width = c_height/img_ratio;
	    }
	    else
	    {   
		olimage.style.width = c_width;
		olimage.style.height = c_width*img_ratio;
	    }
    }
    document.getElementById("ol_nav").style.display = "block";
}

function close_overlay()
{
	document.getElementById("overlay").style.display = "none";
	document.getElementById("ol_nav").style.display = "none";
}

function ol_navigate_fw()
{
	var current_img=document.getElementById("olimage");
	var img_idx = current_img.alt;
	var m_res = img_idx.match(/thmb([0-9]*)_([0-9]*)/);
	var page = m_res[1]*1;
	var cnt = m_res[2]*1;
	if (cnt < (page+1)*get_page_size()-1)
	{
		cnt+=1;
		close_overlay();
		var n_el = document.getElementById("thmb" + page + "_" + cnt);
		openoverlay(n_el);
	}
	else{
		// load next page
		var max_page = document.getElementById("tot_pages").getAttribute("data-mxpage")*1;
		if (page<max_page)
		{
			var current_loc = window.location;
			page += 1;
			var new_loc = current_loc.href.replace(/page=[0-9]*/,"page="+page);
			new_loc = new_loc.replace(/&dia=(fwd|back)/,"");
			window.location.href = new_loc + "&dia=fwd";
		}
	}
}

function ol_navigate_back()
{
	var current_img=document.getElementById("olimage");
	var img_idx = current_img.alt;
	var m_res = img_idx.match(/thmb([0-9]*)_([0-9]*)/);
	var page = m_res[1]*1;
	var cnt = m_res[2]*1;
	if (cnt > page*get_page_size())
	{
		cnt-=1;
		close_overlay();
		var n_el = document.getElementById("thmb" + page + "_" + cnt);
		openoverlay(n_el);
	}
	else{
		// load previous page
		var max_page = document.getElementById("tot_pages").getAttribute("data-mxpage")*1;
		if (page>0)
		{
			var current_loc = window.location;
			page -= 1;
			var new_loc = current_loc.href.replace(/page=[0-9]*/,"page="+page);
			new_loc = new_loc.replace(/&dia=(fwd|back)/,"");
			window.location.href = new_loc + "&dia=back";
		}
	}
}