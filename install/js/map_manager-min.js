/*
 * Copyright (c) 7/10/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

;(function(window) {
 
	if (window.BX.KITMapOfficesMap)
		return;
		
	var
		BX = window.BX,
		defaultConfig = {
			debug		: false, 
			
			map			: 'KIT_MapOffice_YMAP',
			map_center	: [0,0],
			map_zoom	: 2,

			auto_zoom_correct: -1,
			city_id: -1,
			selector_office_block : "selector-office",

            ymap_api_error: 'Yandex Maps script is not connected to the page. Please check the component option "Connect script download Yandex maps 2.0" should be checked.',

			//callback
			ymaps_ready: function(){},
			create_placemark: function(){},
			action_traffic : function(){},
			action_map	   : function(){},
			select_city: function(){},
			select_point: function(){}
			
			},
		config = {}, 
		data = {},
		YMap = Object,
		MCollection = Object,
		traffic_status = false,
		actualProvider;
	
	BX.KITMapOfficesMap = function(data, params){

		var ready_function = function(){};
		this.data = data;
		this.attr_action = "map-action";
		this.ready = function(callback){
			if(typeof callback == "function")
				ready_function = callback;
			};
		this.actions= {
			"traffic":["toggle", "show", "hide"],
			"map":["setDefault", "setCity", "setOffice"]
			};

		this.Action = function(action, id){
		
			if(typeof action != "string")
				return false;
			
			if(typeof id == "undefined") 
				SetAction(action);
			else
				SetAction(action, id); 
			} 
			
		var controller = this;
			
		config = params;

		//config
		for (var i in defaultConfig) {
			if (typeof (params[i]) == "undefined") 
				config[i] = defaultConfig[i]; 
			}
		
		if(config.debug)
			console.log("config",config);

		if(typeof config.map != "string" || config.map.length == 0)
			return false;
			
		if(typeof config.selector_office_block != "string")
			return false;

        if(typeof config.ymap_api_error != "string")
            config.ymap_api_error = "";

		config.city_id = config.city_id - 0;
		

		//init event for controls
		this.controls = BX.findChild(document, {"attr": controller.attr_action}, true, true);

		for(i in controller.controls) {			
	
			var test = false, 
				attr = "",
				pos = -1;

			if(typeof controller.controls[i] == "undefined") 
				continue;
				
			attr = controller.controls[i].getAttribute(controller.attr_action);
			
			//check attr
			for(t in controller.actions){

				var re = new RegExp("^"+t+".", "g"),
					pos = attr.search(re);

				if(pos === 0)
					break;
				}

			if(pos !== 0)
				continue;

			;(function(item, attr){
				
				if(item.tagName == "SELECT"){
					BX.bind(item, 'change', function(e){
						var id = this.options[this.selectedIndex].value - 0;
						SetAction(attr, id);
						BX.PreventDefault(e);
						return false;			
						});
				}
				else
				{
					BX.bind(item, 'click', function(e){
						var id = this.getAttribute("data-id") - 0;

						SetAction(attr, id);
						BX.PreventDefault(e);
						return false;			
						});
				}
				

				})(controller.controls[i], attr);
			}
	
		if(typeof ymaps != 'undefined')
		{
			ymaps.ready(function(){
				
				//create map
				YMap = new ymaps.Map(config.map, {
					center: config.map_center,
					zoom: config.map_zoom
					}),
				MCollection = new ymaps.GeoObjectCollection();
				
				if(config.city_id >= 0)	{
					
					//calc city position on map
					var res = setCity(config.city_id);
					
					//set new default center and zoom
					config.map_center = res[0];
					config.map_zoom = res[1];
					}
				else {
					var res = GetCityPosition(YMap, 0);
					config.map_center = res[0];
					config.map_zoom = res[1];
					}

				//set def center
				YMap.setCenter(config.map_center);
				YMap.setZoom(7); //config.map_zoom
				
				panTo(config.map_center, config.map_zoom, 0, 500, 0);
				
				//init traffic provider
				actualProvider = new ymaps.traffic.provider.Actual({}, {infoLayerShown: true});
				
				//events
				YMap.events.add('boundschange', function (e) {
					
					var delta = 0.000000001;
					var delta_x = e.originalEvent["newCenter"][0] - config.map_center[0];
					var delta_y = e.originalEvent["newCenter"][1] - config.map_center[1];

                    if(config.debug)
                        console.log("YMap.events boundschange ", 'delta_x = ' + delta_x, ' delta_y = ' + delta_y, 'delta = ' + delta);

                    if(config.debug)
                        console.log("YMap.events boundschange ", 'newZoom = ' + e.get('newZoom'), ' oldZoom = ' + e.get('oldZoom'), 'map_zoom = ' + config.map_zoom);


					if(Math.abs(delta_x) > delta || Math.abs(delta_y) > delta || (e.get('newZoom') != e.get('oldZoom') && e.get('newZoom') != config.map_zoom)) {
                        if(config.debug)
                            console.log("controls map.setDefault show");

                        var obj = getControls("map.setDefault", function(obj){
							BX.show(BX(obj));
							});
						}
                    else {

                        var obj = getControls("map.setDefault", function(obj){
                            BX.hide(BX(obj));
                            });

                        }
					});
				
				getControls("map.setDefault", function(obj){
					BX.hide(BX(obj));
					});
					
				createCollection(data);
				
				YMap.geoObjects.add(MCollection);				
		
				//set click event on office link 
				for( k in controller.controls) {
				
					var a = controller.controls[k].getAttribute('map-action');
					var d = controller.controls[k].getAttribute('data-id') - 0;
					
					if(a != 'map.setOffice' || d<=0 || typeof data[d] != 'object')
						continue;

					(function (item, point) {
						
						BX.bind(controller.controls[k], 'click', function(e){
							panTo(point.center, 15);
							
							if(typeof config.select_point == "function")
								config.select_point(item, point);
				
							BX.PreventDefault(e);
							return false;
							});
						})(d, data[d]);
						
					}
		
				//callback
				if(typeof config.ymaps_ready == "function")
					config.ymaps_ready(YMap, MCollection, this.data);
				
				if(typeof ready_function == "function")
					ready_function(controller);

				});
		}
		else
		{
			alert(config.ymap_api_error);
		}

		//if(form != 'null');
		//	form.onsubmit = BX.proxy(this.SendForm, this);
		
			var getControls = function(action, callback){
				var obj = [];
				for(i in controller.controls) {
				
					var attr = "",
						re = new RegExp("^"+action, "i");

					if(typeof controller.controls[i] == "undefined") 
						continue;

					attr = controller.controls[i].getAttribute(controller.attr_action);

					if(attr.search(re) === 0 && attr != null){
						obj.push(controller.controls[i]);
						if(typeof callback == "function")
							callback(controller.controls[i]);
						}

					}
					return obj;
				}
			
			var SetAction = function(action, id){ 
				var a = action.split('.');
				if(a.length != 2)
					return false;
					
				if(typeof id != "number" || id < 1)
					id = 0;

				if(a[0] == "traffic"){
					
					if(typeof controller.actions["traffic"] != "object")
						return false;
					
					switch(a[1]){
					
						case "toggle":
							controller.traffic_status = controller.traffic_status ? false : true;
							break;

						case "show":
							controller.traffic_status = true;
							break;

						case "hide":
							controller.traffic_status = false;
							break;
						}

					//search controls
					var obj = getControls(a[0]+".", function(obj){});


                    if(YMap){
                        if(controller.traffic_status)
                            actualProvider.setMap(YMap);
                        else
                            actualProvider.setMap(null);
                        }

					//callback
					if(typeof config.action_traffic == "function")
						config.action_traffic(a[1], controller.traffic_status, obj);

					}
				else if(a[0] == "map") {

					switch(a[1]){
					
						case "setDefault":

                            if(config.city_id > 0)
                                setCity(config.city_id);
							else
                                panTo(config.map_center, config.map_zoom);

							break;
					
						case "setCity":
	
							if(id > 0)
								setCity(id);

							break;
							
						case "setOffice":
						
							
						
							break;
						}
						
					//callback
					if(typeof config.action_map == "function")
						config.action_map(a[1]);
						
					}	
				}
			
			GetCityPosition = function(Map, city){
			
				var zoom = 1, 
					location = [0,0],
					objects = [];
					
				for(i in data){
					if((data[i]["city"] == city || city == 0) && data[i]["center"].length == 2 && typeof data[i]["center"] == "object") {
						objects.push(new ymaps.Placemark(data[i]["center"]));
						}
					}

				if(typeof objects != "object" || objects.length == 0)
					return [location, Math.ceil(zoom)];

				var 
					geoQuery = ymaps.geoQuery(objects);
					zoom 	 = geoQuery.getMaxZoom(Map) + config.auto_zoom_correct;
					location = geoQuery.getCenter(Map);

				zoom = zoom > 16 ? 16 : zoom ;
				zoom = zoom <= 0 ? 1 : zoom ;	

				return [location, Math.ceil(zoom)];
				}

			var setCity = function(city, item){
				
				if(city > 0){

					//city selector
					var city_selector = getControls("map.setCity", function(obj){
						BX.removeClass(obj, 'active');
						});
					
					for(i in city_selector){
						var attr = city_selector[i].getAttribute("data-id");
						
						if(city_selector[i].tagName == 'SELECT'){
							
							//select
							var sel_index = -1;
							var options = city_selector[i].options;

							for(k in city_selector[i].options) {
								if(city_selector[i].options[k].value == city)
									sel_index = k;
								}
							
							city_selector[i].options[sel_index].selected = true;
							
							} else {
							
							if(attr == city)
								BX.addClass(city_selector[i], 'active');

							}								
						}
						

					//hide all
					var items = BX.findChild(document, {className: config.selector_office_block}, true, true);
					for(k in items){
						if(k!="indexOf" && typeof items[k] == "object")
							BX.hide(items[k]);
						}
					
					//show in current city
					items = BX.findChild(document, {className: config.selector_office_block, attr:{'data-city':city}}, true, true);
					for(k in items){
						if(k!="indexOf" && typeof items[k] == "object")
							BX.show(items[k]);
						}

				}

				var res = GetCityPosition(YMap, city);
				panTo(res[0], res[1]);
				
				if(typeof config.select_city == "function")
					config.select_city(city_selector, city, res[0], res[1], data);
									
				return res;
				}
				
			var createPlaceMark = function(placemark_data){
				
				var res = null;
				
				if(placemark_data["center"].length != 2 || typeof placemark_data["center"] != "object")
					return false;
				
				if(typeof config.create_placemark == "function")
					res = config.create_placemark(placemark_data);
				
				if(typeof res.data != "object")
					res.data = Object;
				
				if(typeof res.options != "object")
					res.options = Object;
					
				var placemark = new ymaps.Placemark(placemark_data['center'],	res.data, res.options);
				
				return placemark;
				}
				
			var createCollection = function(data){
				for (index in data)	{
					
					var placeMark = createPlaceMark(data[index]);
					
					if(placeMark === false)
						continue;

					MCollection.add(placeMark);
					
					
					
					}
				}
				
				

			var panTo = function(location, zoom, duration, duration_zoom, delay){

                if(config.debug)
                    console.log("panTo");

				if(typeof duration != "number")
					duration = 1000;
					
				if(typeof duration_zoom != "number")
					duration_zoom = 1000;
					
				if(typeof delay != "number")
					delay = 500;
			
				YMap.panTo(location, {
					flying: true,
					duration: duration,
					delay: delay,
					callback: function(err) {
						if (err) {
							alert('Error: Could not display the specified region');
							}
						YMap.setZoom(zoom, {duration: duration_zoom});
						}
					});
				}

			return this;
		}
		
	})(window);
 