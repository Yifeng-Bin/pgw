var __allWorkType = {
    "first": [
        {"code": 35, "name": "工程管理"},  
		{"code": 36, "name": "装修工匠"},
		{"code": 40, "name": "家居维修"},
		{"code": 42, "name": "成品安装"},
		{"code": 43, "name": "杂工搬运"},
    ],   
    "second": {
        "35":[
            {"code": 197, "name": "室内设计师"},
            {"code": 195, "name": "工程监理"},
  			{"code": 196, "name": "包工长"},
  			{"code": 304, "name": "木工组长"},
  			{"code": 305, "name": "水电组长"},
  			{"code": 306, "name": "泥瓦工组长"},
  			{"code": 307, "name": "墙面油漆组长"},
  			{"code": 308, "name": "家具油漆组长"},
  			{"code": 309, "name": "防水组长"},		
        ], 
        "36":[
            {"code": 205, "name":"水电工"},
			{"code": 206, "name": "泥水工"},
			{"code": 207, "name":"木工"},
			{"code": 208, "name": "刮胶涂料"},
			{"code": 209, "name":"家具油漆"},
			{"code": 210, "name": "贴墙纸工"},
			{"code": 211, "name": "防盗网"},
			{"code": 212, "name": "防护网"},
			{"code": 213, "name": "电焊工"},
			{"code": 215, "name": "石材"},
			{"code": 216, "name": "硅藻泥工"},
			{"code": 217, "name": "专业打孔"},
			{"code": 218, "name": "防水补漏"},
        ],  
		"40":[
            {"code": 253, "name":"开锁换锁"},
			{"code": 255, "name": "家电维护"},
			{"code": 256, "name":"管道疏通"},
			{"code": 258, "name": "外墙维护"},
        ], 
		"42":[
            {"code": 287, "name":"灯具安装"},
			{"code": 288, "name": "洁具安装"},
			{"code": 289, "name":"地板安装"},
			{"code": 290, "name": "门窗安装"},
			{"code": 291, "name":"家具安装"},
			{"code": 292, "name": "楼梯安装"},
			{"code": 293, "name": "电器安装"},
			{"code": 294, "name": "集成吊顶"},
			{"code": 295, "name": "智能安防"},
        ], 
		"43":[
            {"code": 297, "name":"搬运工"},
			{"code": 298, "name": "清洁工"},
			{"code": 299, "name":"三轮车"},
			{"code": 300, "name": "货运车"},
			{"code": 301, "name":"杂工"},
        ],        
    },  
}


function initChangeWorkType(f,s,fValue,sValue){
	var fObj = $('#'+f);
	var sObj = $('#'+s);      
	for(var i in __allWorkType.first){
		fObj.append('<option value="'+__allWorkType.first[i].code+'">'+__allWorkType.first[i].name+'</option>');	
	}
        sObj.append('<option value="0">请选择</option>');
	fObj.change(function(){
		sObj.empty();
		sObj.append('<option value="0">请选择</option>');
                var fValue = $(this).val();
                for(var i in __allWorkType.second[fValue]){
                        sObj.append('<option value="'+__allWorkType.second[fValue][i].code+'">'+__allWorkType.second[fValue][i].name+'</option>');	
                }                
	});
        if(typeof fValue !== 'undefined' && fValue != 0){
            fObj.find("option[value='"+fValue+"']").attr("selected", true).change(); 
        }else{
            fObj.change();
        }
        if(sValue != 0){
            sObj.find("option[value='"+sValue+"']").attr("selected", true).change(); 
        }         
}

