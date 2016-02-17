//Dichiaro delle variabili globali
//Inizialmente le coordinate per la mappa sono impostate al centro di Bologna
var user_lat = 44.494240;
var user_long = 11.346452;
var map;
//Conta mi servira per una funzione successivamente
var conta=0;

// FUNZIONI //
function startMapsLocation(){
		//Carico la mappa con la posizione dell'utente         
		if (GBrowserIsCompatible()) {
				 
				if (navigator.geolocation) 
				{		
					navigator.geolocation.getCurrentPosition( 
				 
						function (position) 
						{  					
							Ext.MessageBox.alert('<center>Google Maps</center>', 'Tutte le distanze avranno come punto di partenza la tua posizione.');
							user_lat = position.coords.latitude;
							user_long = position.coords.longitude;
										
						}		
						);
				}
		}else{//Se il Browser non accetta la geolocation
			//Imposto come centro della mappa il centro di bologna
			Ext.MessageBox.alert('<center>Google Maps</center>', 'Browser non compatibile con la geolocalizzazione.<br>Tutte le distanze avranno come punto di partenza il centro di Bologna.');
			user_lat = 44.494240;
			user_long = 11.346452;
				
		}
		//Carico nuovamente la mappa con le coordinate dell'utente(se ha accettato di essere localizzato)
		//altrimenti la mappa rimarra centrata su Bologna Centro
		Ext.MessageBox.alert('<center>Google Maps</center>', 'Qualora non si voglia accettare la geolocalizzazione, tutte le distanze avranno come punto di partenza il centro di Bologna');
		
		      
		loadAll(store.proxy.url);
}



//Funzione per convalidare la selezione di una o piu categorie
function controlla(categorie)
{
	if (categorie.checked)
	{
		conta++;		
	}
	else
	{
		conta>0?conta--:null; 
	}
}

//Descrittore Descrizione
function dettagli(sito){

	var subid = sito.substring(3,0);
	//Ricavo gli id degli aggregatori selezionati
	switch(subid){
		case "cin": var cat = "ltw1210-cinema";break;
		case "ris": var cat = "ltw1209-ristoranti";break;
		case "bar": var cat = "ltw1209-bar";break;
	}
	//Creo il collegamento al descrittore
	var web = 'http://ltw1210.web.cs.unibo.it/cgi-bin/localproxy.php?url=http://ltw1134.web.cs.unibo.it/descrittore-descrizione/'+cat+'/params/'+sito;

	//Creo una finestra iframe di Extjs
	this.win = new Ext.Window({  
		 title: '<center>Detail Windows</center>',  
		 width: 700,  
		 height: 400,    
		 maximizable: true,  
		 maskDisabled: true,  
		 bodyStyle: 'background-color:#fff',  
		 html: ''	 
	});  
	
		  // Aggiorno il contenuto della finestra asincronicamente con la descrizione
        Ext.Ajax.request({
                        // Evito cross domain error chiamando uno script locale
                        url: web,
                        success: function (response) {
                if (response)
                        this.win.update('<section style="width:100%;height:100%;border:none">'+response.responseText+'</section>');
                else
                        this.win.update("<h1>Errore</h1><p>Si e' verificato un errore temporaneo con il descrittore 'Descrizione'. Ti preghiamo di riprovare piu' tardi.</p><br/>");
                        },
                        error : function(msg) {
                                descrBox.update('<h1 style="font-color:red;">Errore!</h1>'+msg+"<br/>");
                        }
                });
	
	//Apro la finestra  
	this.win.show(); 
}

//Geolocalizzazione Extra
function loca_geo_max(sito){
	//Creo un array con le variabili passate comke argomento
	var data=sito.split("&&");
	//Creo la finestra iframe di ExtJs
	this.win = new Ext.Window({  
		 title: 'Geo Multiple Maps',  
		 width: 810,  
		 height: 590,    
		 maximizable: true,  
		 maskDisabled: true,  
		 bodyStyle: 'background-color:#fff',  
		 html: '<iframe style="width:100%;height:100%;padding:0;border:solid 1px black" src="http://data.dualmaps.com/dualmaps4/map.htm?x='+data[1]+'&y='+data[0]+'&z=15&gm=0&ve=5&gc=0&bz=1&bd=0&mw=1&sv=1&sva=1&svb=0&svp=0&svz=0&svm=2&svf=0&sve=1&mi=0&mg=1&mv=1&addr='+data[2]+'&fp=0" marginwidth="0" marginheight="0" frameborder="0" scrolling="no"></iframe>'  
	});  
	
	//Apro la finestra  
	this.win.show(); 
}

//Ricerca entro il raggio
function get_check_value(parametri)
{
	//Creola lista degli aggregatori in cui ricercare
	var c_value = "";
	//prelevando i valori dei checkbox del form
	for (var i=0; i < document.orderform.categoria.length; i++)
		{
		if (document.orderform.categoria[i].checked)
		   {
		   c_value = c_value + document.orderform.categoria[i].value + "/";
		   }
		}
		if(c_value==""){
			Ext.MessageBox.alert('<center>Attenzione</center>', 'Selezionare almeno una tipologia di attivita.');
		}else{
		//Genero l'url
		var dede = 'http://ltw1134.web.cs.unibo.it/descrittore-entro-il-raggio/'+c_value+'params/'+parametri;
		var url_complete = "http://ltw1210.web.cs.unibo.it/cgi-bin/localproxy.php?url="+dede;
								
		//Aggiorno il proxy dello store						
		store.proxy.url = url_complete;
		store.read();
		}
		
		loadAll(store.proxy.url);
		oldMarker(parametri);
}

//Funzione per aggiornare il contenuto dello store 
function updateStore(url){
	//Leggo la categoria selezionata nella listview
	var w = document.myform.mylist.selectedIndex;
   var cat = document.myform.mylist.options[w].value;
   //Aggiorno il proxy dello store facendo richiesta al descrittore Ricerca per nome
	if(url)
	{				
		store.proxy.url = 'http://ltw1210.web.cs.unibo.it/Trova-per-nome/'+cat+'/params/'+url;
		store.read();        
	}
	else
	{
		store.proxy.url = 'http://ltw1210.web.cs.unibo.it/Trova-per-nome/'+cat+'/params/*';
		store.read();
	}
	loadAll(store.proxy.url);
}

var distanza = 0;  

function loadAll(URLALL){ //Gabriele Cigna

      //Creo la mappa
      map = new GMap2(document.getElementById("map"));
	   map.setCenter(new GLatLng(44.493912, 11.343248),13);
	   map.setUIToDefault();      
		
		var gmarkers = [];
      var htmls = [];
      var i = 0;
		
      //Creo il marker
      function createMarker(point,name,html,address,lat,lng,open) {
        var marker = new GMarker(point);
        GEvent.addListener(marker, "click", function() {
          //////////////////////////////////////////

	   var xmlhttp = new XMLHttpRequest();
	   var xmlhttp2 = new XMLHttpRequest();
	      	   	   
  	
      		//Converto la data attuale nel formato richiesto nel protocollo
      		var currentTime = new Date();
				var month = currentTime.getMonth() + 1;
				var day = currentTime.getDate();
				var year = currentTime.getFullYear();
				var curr_hour = currentTime.getHours();
				var next_min = currentTime.getMinutes()+1;
				var next_hour = currentTime.getHours()+1;
				var curr_min = currentTime.getMinutes();

				if(curr_hour<10){curr_hour="0"+curr_hour; if(next_hour<10){next_hour="0"+next_hour;}}
				if(curr_min<10){curr_min="0"+curr_min; if(next_min<10){next_min="0"+next_min;}}

				if(next_hour==24 && next_min>00){ next_hour = '00'; }
				if(month<10){month="0"+month;}
				if(day<10){day="0"+day;}
				
				//Genero i due intervalli da mandare ai descrittori Aperto e Aprira'
				var Adesso = year + "-" + month + "-" + day+": "+curr_hour+curr_min+"-"+curr_hour+next_min;
				var Adesso2 = year + "-" + month + "-" + day+": "+curr_hour+curr_min+"-"+next_hour+curr_min;
				
				//Posiziono il marker sulla mappa e aggiorno il suo html con le informazioni ricavate dai descrittori
            var point = new GLatLng(lat,lng);								 								
 	 		   var html = "<b>"+name+""+"<br>Distanza:</b>"; 	 		    
            map.setCenter(new GLatLng(lat,lng),15);
          	
            xmlhttp.open("GET", "http://ltw1210.web.cs.unibo.it/Distanza/params/"+user_lat+"/"+user_long+"/"+lat+"/"+lng, true);
            				
				xmlhttp.onreadystatechange=function() {
				if (xmlhttp.readyState==4) {
						 	distanza = parseInt(xmlhttp.responseText);	
						 	html = html+distanza+' <i>(metri)</i>';

						 	xmlhttp2.open("GET", "http://ltw1210.web.cs.unibo.it/Aperto/params/"+open+"/"+Adesso, true);				
							xmlhttp2.onreadystatechange=function() {
								if (xmlhttp2.readyState==4) {
							 	var stato = xmlhttp2.responseText;	
							 	html = html+"<br><b>Stato Attuale:</b> "+stato;

								var temp = 'http://ltw1140.web.cs.unibo.it/aprira/params/'+open+'/'+Adesso2;
								var url = 'http://ltw1210.web.cs.unibo.it/cgi-bin/localproxy.php?url=';
								var url1 = url+temp;

								Ext.Ajax.request({
									url: url1,
									success:function(response) { 
										
										if(response.responseText == 0){ var stato_dopo = "Chiuso";}	else								
										if(response.responseText == 1){ var stato_dopo = "Aprirà";} else
										if(response.responseText == 2){ var stato_dopo = "Aperto";} else
										if(response.responseText == 3){ var stato_dopo = "Chiuderà";} else
										if(response.responseText == 4){ var stato_dopo = "Chiuderà";} else
										if(response.responseText == -1){ var stato_dopo = "Dati Errati";}else
											{var stato_dopo = "Non Disponibile";}
									
									 	html = html+"<br><b>Stato nella prossima ora:</b> "+stato_dopo;
									 	var nameGeo = lat+"&&"+lng+"&&"+address;
									 	html = html+'<br><input type="button" name="'+nameGeo+'" value="GeoLocalizzazione Extra" onclick="loca_geo_max(this.name)" />';		
									 	
									 	marker.openInfoWindowHtml(html);
									},
									failure:function() { alert("Errore di comunicazione"); } 
								});
							 	
							  }
							}
							xmlhttp2.send();
						 	
						  	}
					}
					xmlhttp.send();
					
          
        });
        return marker;
      }



		var request = GXmlHttp.create();
      request.open("GET", 'http://ltw1210.web.cs.unibo.it/cgi-bin/localproxy.php?url='+URLALL, true);
      
      request.onreadystatechange = function() {
        if (request.readyState == 4) {
          var xmlDoc = GXml.parse(request.responseText);
          // obtain the array of markers and loop through it
          var markers = xmlDoc.documentElement.getElementsByTagName("location");

          for (var i = 0; i < markers.length; i++) {

            // obtain the attribues of each marker
				var hs = markers[i].getElementsByTagName("address")[0].childNodes[0].nodeValue;   
				var open = markers[i].getElementsByTagName("opening")[0].childNodes[0].nodeValue;                     
				var lat = parseFloat(markers[i].getAttribute("lat"));
            var lng = parseFloat(markers[i].getAttribute("long"));
            var point = new GLatLng(lat,lng);
            var label = markers[i].getElementsByTagName("name")[0].childNodes[0].nodeValue;
 	  			var html = ""; 
          
           
	
            // create the marker
            var marker = createMarker(point,label,html,hs,lat,lng,open);
            map.addOverlay(marker);

          }
        }
      }
      request.send();

		var Icona = new GIcon(G_DEFAULT_ICON);
		Icona.image = "./casa.png";
		var markerOptions = { icon:Icona };
	
		var puntoCasa = new GLatLng(user_lat,user_long);								 								
		var markerCasa = new GMarker(puntoCasa,markerOptions);	 
				 
		map.addOverlay(markerCasa);					
      
}

function oldMarker(coord){

	var param=coord.split("/");

	var greenIcon = new GIcon(G_DEFAULT_ICON);
	greenIcon.image = "./gn.gif";
	var markerOptions = { icon:greenIcon };
	
	var puntoUser = new GLatLng(param[0],param[1]);								 								
	var markerUser = new GMarker(puntoUser,markerOptions);	 
			 
	map.setCenter(new GLatLng(param[0],param[1]),15);
	map.addOverlay(markerUser);
}

//Creo il pannello ExtJs ed i vari componenti

Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.panel.*',
    'Ext.layout.container.Border'
]); 



   Ext.define('Locazione',{
        extend: 'Ext.data.Model',
        fields: [
            // set up the fields mapping into the xml doc
            // The first needs mapping, the others are very basic
            {name: 'id', mapping: '@id'},
            {name: 'lat', mapping: '@lat'},
            {name: 'long', mapping: '@long'},
            'name',
            'address',
            'tel',
            'category',
            'opening',
            'closing'
        ]
    });

    //Creo lo Store
    var store = Ext.create('Ext.data.Store', {
        model: 'Locazione',
        proxy: {
            // load using HTTP
            type: 'ajax',
            url: "http://ltw1210.web.cs.unibo.it/cinema/",
            headers: {'Accept': 'application/xml'},
            reader: {
                type: 'xml',
                record: 'location',
                totalProperty  : 'total'
            }
        }
    });
    
    //Creo la griglia
    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        columns: [   
            {text: "<center><b>Nome</b></center>", width: 150, dataIndex: 'name', sortable: true},
            {text: "<center><b>Categoria</b></center>", width: 100, dataIndex: 'category', sortable: true},            
            {text: "<center><b>Indirizzo</b></center>", width: 270, dataIndex: 'address', sortable: false},
            {text: "<center><b>Telefono</b></center>", width: 100, dataIndex: 'tel', sortable: false}            
            ],
	 defaults: {autoScroll: true},
        split: true,
	 width: '50%',
	 height: 700,
        region: 'west',
      	 minSize: 670,
   	 maxSize: 670
    });

   //Contenuto html del pannello dei dettagli
    var bookTplMarkup = [
        '<center><br><b>{name}</b><br>({category})<br>',
        '<input type="button" name="{id}" value="Descrizione Extra" onclick="dettagli(this.name)" /><br><br><b>Ricerca in zona</b><br>',
        'Selezionare i luoghi di interesse...<form name="orderform"><input type="checkbox" name="categoria" value="ltw1210-cinema">Cinema<input type="checkbox" name="categoria" value="ltw1209-ristoranti">Ristoranti<input type="checkbox" name="categoria" value="ltw1209-bar">Bar<br>...ed un range di ricerca dal luogo selezionato<br><input name="{lat}/{long}/120" value="100(mt)" type="button" onclick="get_check_value(this.name)"><input name="{lat}/{long}/270" value="250(mt)" type="button" onclick="get_check_value(this.name)"><input name="{lat}/{long}/520" value="500(mt)" type="button" onclick="get_check_value(this.name)"><input name="{lat}/{long}/1100" value="1000(mt)" type="button" onclick="get_check_value(this.name)"></form>'     
    ];
    var bookTpl = Ext.create('Ext.Template', bookTplMarkup);

    Ext.create('Ext.Panel', {
        renderTo: 'Data-store',
        frame: true,
        title: '',
        width: '100%',
        height: 750,
        layout: 'column',
		  items: [grid,{
			 title: '<center>Ricerca Personalizzata</center>',
			 id: 'strumenti',	
			 region: 'center',
			width: '25%',
			 height: 200,
			 html: '<br><center><b>Ricerca per Nome</b><br><br>Selezionare una Categoria per visualizzare le locazioni<br><br><FORM NAME="myform" action="javascript:updateStore(document.myform.s.value);"><SELECT NAME="mylist" onChange="javascript:updateStore();"><OPTION VALUE="ltw1210-cinema">Cinema<OPTION VALUE="ltw1209-ristoranti">Ristoranti<OPTION VALUE="ltw1209-bar">Bar<input type="text" name="s" value="" /></FORM><br>Per ricercare un luogo nella categoria selezionata digitare un Nome e premere Invio</center>',
			 cmargins: '5 0 0 0'
		},{
			 title: '<center>Info e Dettagli</center>',
			 id: 'detailPanel',	
			 region: 'center',
			 height: 200,
			 width: '25%',
			 html: '<br><br><br><center><b>Selezionare un luogo<br><br>per i dettagli</b></center>',
			 cmargins: '5 0 0 0'
		},{
			 title: '<center>Google Map</center>',
			 html: '<center><br><div id="map" style="width: 550px; height: 450px"></div></center>',	
			 collapsible: false,
			 width: '50%',
			 height: 500,
			 region:'center',
			 margins: '0 0 0 0'
		}]
			 });

    
    //Aggiorno il pannello dei dettagli alla selezione di un record
    grid.getSelectionModel().on('selectionchange', function(sm, selectedRecord) 
    {
        if (selectedRecord.length) 
        {	
            var detailPanel = Ext.getCmp('detailPanel');
            bookTpl.overwrite(detailPanel.body, selectedRecord[0].data);
            var Temp = selectedRecord[0].data['name']+'&&'+selectedRecord[0].data['lat']+'&&'+selectedRecord[0].data['long']+'&&'+selectedRecord[0].data['address']+'&&'+selectedRecord[0].data['opening'];
map.setCenter(new GLatLng(selectedRecord[0].data['lat'],selectedRecord[0].data['long']),15);

            
        }               
    }); 

//Carico lo store
store.load();
