var __alldiv = {
    "first": [
        {"areacode": 2, "areaname": "直辖市"},  
        {"areacode": 1, "areaname": "湖南省"},  
    ],   
    "second": {
        "2":[
            {"areacode": 108, "areaname": "北京市"},
        ], 
        "1":[
            {"areacode": 107, "areaname": "郴州市"}
        ],         
    },  
  
    "third": {
        "108":[
            {"areacode": 804, "areaname": "东城区"},
            {"areacode": 818, "areaname": "密云区"},
            {"areacode": 817, "areaname": "平谷区"},
            {"areacode": 816, "areaname": "怀柔区"},
            {"areacode": 815, "areaname": "昌平区"},
            {"areacode": 814, "areaname": "大兴区"},
            {"areacode": 813, "areaname": "房山区"},
            {"areacode": 812, "areaname": "顺义区"},
            {"areacode": 811, "areaname": "通州区"},
            {"areacode": 810, "areaname": "门头沟区"},
            {"areacode": 809, "areaname": "石景山区"},
            {"areacode": 808, "areaname": "丰台区"},
            {"areacode": 807, "areaname": "朝阳区"},
            {"areacode": 806, "areaname": "海淀区"},
            {"areacode": 805, "areaname": "西城区"},
            {"areacode": 819, "areaname": "延庆区"},
        ],  
        "107":[
            {"areacode": 794, "areaname": "北湖区"},
            {"areacode": 821, "areaname": "苏仙区"},
            {"areacode": 795, "areaname": "资兴市"},
            {"areacode": 797, "areaname": "桂阳县"},
            {"areacode": 799, "areaname": "宜章县"},
            {"areacode": 798, "areaname": "永兴县"},
            {"areacode": 800, "areaname": "嘉禾县"},
            {"areacode": 801, "areaname": "临武县"},
            {"areacode": 802, "areaname": "汝城县"},
            {"areacode": 803, "areaname": "桂东县"},
            {"areacode": 796, "areaname": "安仁县"}, 
        ],
    }
}


function initChangeRegionData(p,c,a,pValue,cValue,aValue){
	var pObj = $('#'+p);
	var cObj = $('#'+c);
	var aObj = $('#'+a);
        pObj.append('<option value="0">请选择</option>');        
	for(var i in __alldiv.first){
		pObj.append('<option value="'+__alldiv.first[i].areacode+'">'+__alldiv.first[i].areaname+'</option>');	
	}

        cObj.append('<option value="0">请选择</option>');
        aObj.append('<option value="0">请选择</option>');
	pObj.change(function(){
		cObj.empty();
		cObj.append('<option value="0">请选择</option>');
		aObj.empty();
		aObj.append('<option value="0">请选择</option>');
                var pValue = $(this).val();
                for(var i in __alldiv.second[pValue]){
                        cObj.append('<option value="'+__alldiv.second[pValue][i].areacode+'">'+__alldiv.second[pValue][i].areaname+'</option>');	
                }                
	});
	cObj.change(function(){
                var cValue = $(this).val();
		aObj.empty();
		aObj.append('<option value="0">请选择</option>');	
                for(var i in __alldiv.third[cValue]){
                        aObj.append('<option value="'+__alldiv.third[cValue][i].areacode+'">'+__alldiv.third[cValue][i].areaname+'</option>');	
                }                 
	});
        
        pObj.find("option[value='"+pValue+"']").attr("selected", true).change(); 
        cObj.find("option[value='"+cValue+"']").attr("selected", true).change(); 
        aObj.find("option[value='"+aValue+"']").attr("selected", true); 
}

