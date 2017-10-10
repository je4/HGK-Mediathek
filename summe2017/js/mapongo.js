(function ($) {
	'use strict';	
	var IS_FULLVIEW = location.href.search(/display.do/ig) > 0;
	var LANG = $('#exlidSelectedLanguage a').text().substring(0,2).toLowerCase();	

	var subLibraries = {
		"ZHDK-MIZ": "ZHdK-MIZ",
		"ZHAW Hochschulbibliothek Zürich": "ZHAW-HSB-ZH",
		"ZHAW Hochschulbibliothek Winterthur": "ZHAW-HSB-WIN",
		"PH Zürich, Bibliothek": "PHZH"
	}
    

	var libraries = {
		"ZHdK-MIZ": {
			"baseUrl": "http://zhdk.mapongo.de",
			"locationWhiteList": ["Galeriegeschoss","Eingangsgeschoss","Untergeschoss","Untergeschoss curating Degree Zero Archive"],
			"locationMap": {}
		},
		"ZHAW-HSB-ZH": {
			"baseUrl": "http://zhdk.mapongo.de",
			"locationWhiteList": ["Galeriegeschoss","Eingangsgeschoss","Untergeschoss"],
			"locationMap": {}
		},
		"ZHAW-HSB-WIN": {
			"baseUrl": "http://zhaw.mapongo.de",
			"locationWhiteList": ["Handapparate - Eingangsgeschoss 0","Compactus, offen - Eingangsgeschoss 0","Zeitschriften - Eingangsgeschoss 0","AN, Allgemeines und Nachschlagewerke - Eingangsgeschoss 0","AL, Angewandte Linguistik - Eingangsgeschoss 0","GM, Gesundheit und Medizin - Eingangsgeschoss 0","TN, Technik und Naturwissenschaften - Zwischengeschoss 0.1","WB, Wissenschaft und Bildung - Eingangsgeschoss 0","WM, Wirtschaft und Management - Zwischengeschoss 0.1","AB, Architektur und Bau- Zwischengeschoss 0.1","KU, Kunst und Unterhaltung - Eingangsgeschoss 0","RP, Recht und Politik - Zwischengeschoss 0.2"],
			"locationMap": {"Handapparate - Eingangsgeschoss 0":"E51B9","Compactus, offen - Eingangsgeschoss 0":"E51B2", "Zeitschriften - Eingangsgeschoss 0":"E51B8","AN, Allgemeines und Nachschlagewerke - Eingangsgeschoss 0":"E51BN","AL, Angewandte Linguistik - Eingangsgeschoss 0":"E51BL","GM, Gesundheit und Medizin - Eingangsgeschoss 0":"E51BG","TN, Technik und Naturwissenschaften - Zwischengeschoss 0.1":"E51BT","WB, Wissenschaft und Bildung - Eingangsgeschoss 0":"E51BB","WM, Wirtschaft und Management - Zwischengeschoss 0.1":"E51BW","AB, Architektur und Bau- Zwischengeschoss 0.1":"E51BA","KU, Kunst und Unterhaltung - Eingangsgeschoss 0":"E51BK","RP, Recht und Politik - Zwischengeschoss 0.2":"E51BR"}			
		},
		"PHZH": {
			"baseUrl": "http://phzh.mapongo.de",
			"locationWhiteList": ["Stockwerk F - frei zugänglich","Stockwerk G - frei zugänglich","Stockwerk G, Semesterapparat - frei zugänglich","Stockwerk H Nord - frei zugänglich","Stockwerk H Süd - frei zugänglich"],
			"locationMap": {"Stockwerk F - frei zugänglich":"ULFHF", "Stockwerk G - frei zugänglich":"ULFHG", "Stockwerk G, Semesterapparat - frei zugänglich":"ULFHT","Stockwerk H Nord - frei zugänglich":"ULFHN","Stockwerk H Süd - frei zugänglich":"ULFHS"}
		}
	}

    $.extend( subLibraries, {"FHNW HGK-Mediathek Basel":"HGK-MED"});
    $.extend( libraries, {"HGK-MED": {
			"baseUrl": "http://mediathek.hgk.fhnw.ch/nebis",
			"locationWhiteList": ["", "Handapparat"],
			"locationMap": {}
		}});
    
	function processPrimoLocations(locationList){
		var nDiv, nTable;
		locationList.each(function(){
			// Sublocations already in DOM?
			nTable = $(this).find(".EXLLocationTable");
			if (nTable.find("tr").length > 0) {
				//console.log("bereits Sublocations offen");
				processPrimoSublocations(nTable);
			}
			else{
				//console.log("Sublocations nicht offen");
				nDiv = $(this).find(".EXLSublocation");
				if (nDiv.length > 0) {
					setSubLocationsResponseHandler(nDiv.get(0));
				}
			}
		});
	}
	function processPrimoSublocations(nTable){
		var rid, colCallNumber, colCollection, colItemStatus, colLoanStatus;
		if(IS_FULLVIEW){
			rid = $(document).find(".EXLSummary .EXLSummaryContainer .EXLResultRecordId").attr("id");
			colCallNumber = "EXLAdditionalFieldsLink";
			colCollection = "EXLLocationTableColumn2";
			colItemStatus = "EXLLocationTableColumn3";
			colLoanStatus = "EXLLocationTableColumn4";
		}	
		else{
			rid = $(nTable).closest("tr.EXLResult").find(".EXLResultRecordId").attr("id");
			colCallNumber = "EXLAdditionalFieldsLink";
			colCollection = "EXLLocationTableColumn1";
			colItemStatus = "EXLLocationTableColumn2";
			colLoanStatus = "EXLLocationTableColumn3";
		}
		var subLibrary = $.trim($(nTable).closest(".EXLLocationList").find(".EXLLocationsTitleContainer").html());
		var library = subLibraries[subLibrary];
		var statusAvailable = $.trim($(nTable).closest(".EXLLocationList").find(".EXLLocationInfo em").html());
		if(isMapongoLibrary(subLibrary)){
			var baseUrl, callNumberDesc, callNumber, collection, itemStatus, loanStatus, barcode;
			var collectionCode = null;
			nTable.find("tr").each(function(){
					if($(this).attr('class')=='' || $(this).attr('class')=='EXLLocationTableRowBgColor'){
						if($(this).attr("data-loaded") == "true"){
							return;
						}
						else{
							baseUrl = libraries[library].baseUrl;
							// search result list: callno+description in one field
							callNumberDesc = $.trim($(this).find("." + colCallNumber + " a").text());
							collection = $.trim($(this).find("." + colCollection).text());
							itemStatus = $.trim($(this).find("." + colItemStatus).text());
							loanStatus = $.trim($(this).find("." + colLoanStatus).text());
							barcode = $(this).find(".EXLLocationTableActions .EXLLocationItemBarcode").attr("value");
							callNumber = $(this).find(".EXLLocationTableActions .EXLLocationCallNumber").attr("value");
							
							//console.log(rid + "**" + subLibrary + "**" + library +  "**" + statusAvailable + "**" + callNumber + "**"+ collection + "**" + itemStatus + "**" + loanStatus + "**");
							if(!isMapongoLocation(subLibrary, collection) || hasLoanStatus(loanStatus)){
								return;
							}
							if (library === "ZHAW-HSB-WIN") {
								collectionCode = libraries[library].locationMap[collection];
							}
							$(this).attr("data-loaded","true");
							// todo
							if(callNumber == "" || callNumber == null){
								callNumber = callNumberDesc;
							}
							if(callNumber != ""){
								$(this).find("." + colCollection).append(getMapongoLink(baseUrl, callNumber, rid, collectionCode, library, barcode));
								$(this).find("." + colCallNumber).append(getMapongoQRC(baseUrl, callNumber, rid, collectionCode, library, barcode));
							}
						}	
					}
			});	
		}		
	}
	function setLocationsTabResponseHandler(){
		if(window.MutationObserver){
			var observer = new MutationObserver(function(allMutations){
				allMutations.map(function(mr){
					if(mr.addedNodes && mr.addedNodes.length>0){
						for (var i = 0; i < mr.addedNodes.length; ++i){
							var node = mr.addedNodes[i];
							if (node.nodeName.toUpperCase()=="FORM"){ 		//locationsTabForm
								locationsTabResponseHandler($(node));
							}
						}
					}
				});	
			});
			var divLocationsTabs = document.querySelectorAll('.EXLContainer-locationsTab');
			var tab;
			for (var i = 0; i < divLocationsTabs.length; ++i) {
				tab = divLocationsTabs[i];
				observer.observe(tab, {
						'childList': true,
						'subtree': false
						}
				);
			}
		}
		else{
			$(document).ajaxSuccess(function(event, xhr, settings){
				if(settings.url.indexOf("tabs=locationsTab")>-1 || settings.url.indexOf("locations.do")>-1 ){
					// settings.url. -> recIdxs=3  -> exlidResult3-TabContainer-locationsTab
					var idx = settings.url.substr(settings.url.indexOf("recIdxs=") + 8, 2);
					if (idx.substr(1,1)=="&"){
						idx = idx.substr(0,1);
					}
					var locTabContainer = $("#exlidResult" + idx + "-TabContainer-locationsTab");
					if(locTabContainer.length == 0)return;
					locationsTabResponseHandler(locTabContainer.find("form"));
				}
			});
	
		}
	}
	// formNode: Form name=locationsTabForm
	function locationsTabResponseHandler(formNode){
		processPrimoLocations(formNode.find(".EXLLocationList"));
	}
	// nDiv: vanilla js object with class EXLSublocation (container for table of sublocations)
	function setSubLocationsResponseHandler(nDiv){
		if(window.MutationObserver){
			var observerSub = new MutationObserver(function(allMutations){
				allMutations.map(function(mr){
					if(mr.addedNodes && mr.addedNodes.length>0){
						for (var i = 0; i < mr.addedNodes.length; ++i){
							var node = mr.addedNodes[i];
							if (node.nodeName.toUpperCase()=="TABLE"){
								processPrimoSublocations($(node));
							}
						}
					}
				});	
			});
			observerSub.observe(nDiv, {
				'childList': true,
				'subtree': false
				}
			);
		}
		else{
			self.runSubLocationTimer = runSubLocationTimer;
			$(nDiv).closest(".EXLLocationList").find('a.EXLLocationsIcon').on('click', function(){
				runSubLocationTimer($(nDiv).attr("id"), 0);
			});
		}
	}
	function getMapongoLink(baseUrl, callNumber, rid, collectionCode,library, barcode) {
		var url = baseUrl + "/viewer?project_id=1&search_key=" + encodeURIComponent(callNumber) + "&language=" + LANG + "&e=" + rid;
		if (collectionCode != null) {
			url += "&search_context2=" + encodeURIComponent(collectionCode);
		}
		if (barcode != null && barcode != "") {
			url += "&bcd=" + barcode;
		}
		var link = document.createElement('a');
		link.setAttribute('class', 'mapongo_link');
		link.setAttribute('target', '_blank');
		link.setAttribute('href', url);

		var image_src = baseUrl + "/static_images/projects/1/search_thumbnail.jpg?project_id=1&search_key=" + encodeURIComponent(callNumber);
		if (collectionCode != null) {
			image_src += "&search_context2=" + encodeURIComponent(collectionCode);
		}
		if (library === "ZHdK-MIZ") {
			image_src += "&e=" + rid;
		}
		
		var img = document.createElement('img');
		img.setAttribute('src', image_src);
		img.setAttribute('style', 'max-width:100%;height:auto;');
		if(LANG=="de")
			img.setAttribute('alt', 'Karte mit Standort');
		else
			img.setAttribute('alt', 'location map');
		link.appendChild(img);
		var div = document.createElement('div');
		div.appendChild(link);
		return(div);
	}
	function getMapongoQRC(baseUrl, callNumber, rid, collectionCode, library, barcode) {
		var qrc_src = baseUrl + "/static_images/projects/1/search_qrcode.png?project_id=1&search_key=" + encodeURIComponent(callNumber) + "&language=" + LANG + "&e=" + rid;;	
		if (collectionCode != null) {
			qrc_src += "&search_context2=" + encodeURIComponent(collectionCode);
		}
		if (barcode != null && barcode != "") {
			qrc_src += "&bcd=" + barcode;
		}
		var qrc = document.createElement('img');
		qrc.setAttribute('src', qrc_src);
		//qrc.setAttribute('width', 130);
		//qrc.setAttribute('height', 130);		
		qrc.setAttribute('style', 'max-width:100%;height:auto;');
		qrc.setAttribute('alt', 'QR Code');
		var label = document.createElement('div');
		label.setAttribute('class','qrc-label');
		if(LANG=="de")
			label.appendChild(document.createTextNode('Karte auf Smartphone laden'));
		else
			label.appendChild(document.createTextNode('Load map on your smartphone')); 
		var div = document.createElement('div');
		div.setAttribute('class','qrc');
		//div.setAttribute('style','color:red');		
		div.appendChild(qrc);
		div.appendChild(label);
		return(div);
	}

	function isMapongoLibrary(subLibrary){
		for(var lib in subLibraries){
			if (subLibrary == lib){
				return true;
			}
		}
		return false;
	}
	function isMapongoLocation(subLibrary, collection){
		//console.log("**"+ subLibrary + "**" + collection + "**");
		if($.inArray( collection, libraries[subLibraries[subLibrary]].locationWhiteList)>-1){
			return true;
		};
		return false;
	}
	function hasLoanStatus(loanStatus){
		if(	$.trim(loanStatus)!=""){
			return true;
		};
		return false;
	}
	function runSubLocationTimer(id, subLocationTimerCounter){
		var nTable = $("#" + id).find(".EXLLocationTable");
		if(nTable.find("tr").length == 0){
			if(subLocationTimerCounter < 5){
				subLocationTimerCounter += 1;
				setTimeout('runSubLocationTimer("' + id + '", ' + subLocationTimerCounter + ')',1000);
			}
			return;
		}
		processPrimoSublocations(nTable);
	}
	$(document).ready(function(){
		if($(".EXLLocationList").length > 0 && window.location.search.indexOf("tabs=locationsTab")>-1 && window.location.href.indexOf("display.do")>-1 ){
			processPrimoLocations($(".EXLLocationList"));
		}
		else{
			setLocationsTabResponseHandler();
		}
	});
}(jQuery));
