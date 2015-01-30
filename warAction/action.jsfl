var dom = fl.getDocumentDOM();

var swfUrl = "file:///D:/work/dev/client/assets/roles/war/";

var list = [{url : "file:///D:/work/美术成品/人物/player/F - 飞羽男/T0/",  name : "战场", sign : "FeiYuNan"},
{url : "file:///D:/work/美术成品/人物/player/F - 飞羽男/T0/",  name : "战场", sign : "FeiYuNanMini"},
{url : "file:///D:/work/美术成品/人物/player/F - 飞羽男/T1/",  name : "战场", sign : "FeiYuNanT1Q3"},
{url : "file:///D:/work/美术成品/人物/player/F - 飞羽男/T1/",  name : "战场", sign : "FeiYuNanT1Q3Mini"},
{url : "file:///D:/work/美术成品/人物/player/F - 飞羽男/T2/",  name : "战场", sign : "FeiYuNanT2Q3"},
{url : "file:///D:/work/美术成品/人物/player/F - 飞羽男/T2/",  name : "战场", sign : "FeiYuNanT2Q3Mini"},
{url : "file:///D:/work/美术成品/人物/player/F - 飞羽男/T3/",  name : "战场", sign : "FeiYuNanT3Q3"},
{url : "file:///D:/work/美术成品/人物/player/F - 飞羽男/T3/",  name : "战场", sign : "FeiYuNanT3Q3Mini"},
{url : "file:///D:/work/美术成品/人物/player/F - 飞羽男/T4/",  name : "战场", sign : "FeiYuNanT4Q3"},
{url : "file:///D:/work/美术成品/人物/player/F - 飞羽男/T4/",  name : "战场", sign : "FeiYuNanT4Q3Mini"},
{url : "file:///D:/work/美术成品/人物/player/F - 飞羽女/T0/",  name : "战场", sign : "FeiYuNv"},
{url : "file:///D:/work/美术成品/人物/player/F - 飞羽女/T0/",  name : "战场", sign : "FeiYuNvMini"},
{url : "file:///D:/work/美术成品/人物/player/F - 飞羽女/T1/",  name : "战场", sign : "FeiYuNvT1Q3"},
{url : "file:///D:/work/美术成品/人物/player/F - 飞羽女/T1/",  name : "战场", sign : "FeiYuNvT1Q3Mini"},
{url : "file:///D:/work/美术成品/人物/player/F - 飞羽女/T2/",  name : "战场", sign : "FeiYuNvT2Q3"},
{url : "file:///D:/work/美术成品/人物/player/F - 飞羽女/T2/",  name : "战场", sign : "FeiYuNvT2Q3Mini"},
{url : "file:///D:/work/美术成品/人物/player/F - 飞羽女/T3/",  name : "战场", sign : "FeiYuNvT3Q3"},
{url : "file:///D:/work/美术成品/人物/player/F - 飞羽女/T3/",  name : "战场", sign : "FeiYuNvT3Q3Mini"},
{url : "file:///D:/work/美术成品/人物/player/F - 飞羽女/T4/",  name : "战场", sign : "FeiYuNvT4Q3"},
{url : "file:///D:/work/美术成品/人物/player/F - 飞羽女/T4/",  name : "战场", sign : "FeiYuNvT4Q3Mini"},
{url : "file:///D:/work/美术成品/人物/player/J - 剑灵男/T0/",  name : "战场", sign : "JianLingNan"},
{url : "file:///D:/work/美术成品/人物/player/J - 剑灵男/T0/",  name : "战场", sign : "JianLingNanMini"},
{url : "file:///D:/work/美术成品/人物/player/J - 剑灵男/T1/",  name : "战场", sign : "JianLingNanT1Q3"},
{url : "file:///D:/work/美术成品/人物/player/J - 剑灵男/T1/",  name : "战场", sign : "JianLingNanT1Q3Mini"},
{url : "file:///D:/work/美术成品/人物/player/J - 剑灵男/T2/",  name : "战场", sign : "JianLingNanT2Q3"},
{url : "file:///D:/work/美术成品/人物/player/J - 剑灵男/T2/",  name : "战场", sign : "JianLingNanT2Q3Mini"},
{url : "file:///D:/work/美术成品/人物/player/J - 剑灵男/T3/",  name : "战场", sign : "JianLingNanT3Q3"},
{url : "file:///D:/work/美术成品/人物/player/J - 剑灵男/T3/",  name : "战场", sign : "JianLingNanT3Q3Mini"},
{url : "file:///D:/work/美术成品/人物/player/J - 剑灵男/T4/",  name : "战场", sign : "JianLingNanT4Q3"},
{url : "file:///D:/work/美术成品/人物/player/J - 剑灵男/T4/",  name : "战场", sign : "JianLingNanT4Q3Mini"},
{url : "file:///D:/work/美术成品/人物/player/J - 剑灵女/T0/",  name : "战场", sign : "JianLingNv"},
{url : "file:///D:/work/美术成品/人物/player/J - 剑灵女/T0/",  name : "战场", sign : "JianLingNvMini"},
{url : "file:///D:/work/美术成品/人物/player/J - 剑灵女/T1/",  name : "战场", sign : "JianLingNvT1Q3"},
{url : "file:///D:/work/美术成品/人物/player/J - 剑灵女/T1/",  name : "战场", sign : "JianLingNvT1Q3Mini"},
{url : "file:///D:/work/美术成品/人物/player/J - 剑灵女/T2/",  name : "战场", sign : "JianLingNvT2Q3"},
{url : "file:///D:/work/美术成品/人物/player/J - 剑灵女/T2/",  name : "战场", sign : "JianLingNvT2Q3Mini"},
{url : "file:///D:/work/美术成品/人物/player/J - 剑灵女/T3/",  name : "战场", sign : "JianLingNvT3Q3"},
{url : "file:///D:/work/美术成品/人物/player/J - 剑灵女/T3/",  name : "战场", sign : "JianLingNvT3Q3Mini"},
{url : "file:///D:/work/美术成品/人物/player/J - 剑灵女/T4/",  name : "战场", sign : "JianLingNvT3Q3"},
{url : "file:///D:/work/美术成品/人物/player/J - 剑灵女/T4/",  name : "战场", sign : "JianLingNvT3Q3Mini"},
{url : "file:///D:/work/美术成品/人物/player/W - 武圣男/T0/",  name : "战场", sign : "WuShengNan"},
{url : "file:///D:/work/美术成品/人物/player/W - 武圣男/T0/",  name : "战场", sign : "WuShengNanMini"},
{url : "file:///D:/work/美术成品/人物/player/W - 武圣男/T1/",  name : "战场", sign : "WuShengNanT1Q3"},
{url : "file:///D:/work/美术成品/人物/player/W - 武圣男/T1/",  name : "战场", sign : "WuShengNanT1Q3Mini"},
{url : "file:///D:/work/美术成品/人物/player/W - 武圣男/T2/",  name : "战场", sign : "WuShengNanT2Q3"},
{url : "file:///D:/work/美术成品/人物/player/W - 武圣男/T2/",  name : "战场", sign : "WuShengNanT2Q3Mini"},
{url : "file:///D:/work/美术成品/人物/player/W - 武圣男/T3/",  name : "战场", sign : "WuShengNanT3Q3"},
{url : "file:///D:/work/美术成品/人物/player/W - 武圣男/T3/",  name : "战场", sign : "WuShengNanT3Q3Mini"},
{url : "file:///D:/work/美术成品/人物/player/W - 武圣男/T4/",  name : "战场", sign : "WuShengNanT4Q3"},
{url : "file:///D:/work/美术成品/人物/player/W - 武圣男/T4/",  name : "战场", sign : "WuShengNanT4Q3Mini"},
{url : "file:///D:/work/美术成品/人物/player/W - 武圣女/T0/",  name : "战场", sign : "WuShengNv"},
{url : "file:///D:/work/美术成品/人物/player/W - 武圣女/T0/",  name : "战场", sign : "WuShengNvMini"},
{url : "file:///D:/work/美术成品/人物/player/W - 武圣女/T1/",  name : "战场", sign : "WuShengNvT1Q3"},
{url : "file:///D:/work/美术成品/人物/player/W - 武圣女/T1/",  name : "战场", sign : "WuShengNvT1Q3Mini"},
{url : "file:///D:/work/美术成品/人物/player/W - 武圣女/T2/",  name : "战场", sign : "WuShengNvT2Q3"},
{url : "file:///D:/work/美术成品/人物/player/W - 武圣女/T2/",  name : "战场", sign : "WuShengNvT2Q3Mini"},
{url : "file:///D:/work/美术成品/人物/player/W - 武圣女/T3/",  name : "战场", sign : "WuShengNvT3Q3"},
{url : "file:///D:/work/美术成品/人物/player/W - 武圣女/T3/",  name : "战场", sign : "WuShengNvT3Q3Mini"},
{url : "file:///D:/work/美术成品/人物/player/W - 武圣女/T4/",  name : "战场", sign : "WuShengNvT4Q3"},
{url : "file:///D:/work/美术成品/人物/player/W - 武圣女/T4/",  name : "战场", sign : "WuShengNvT4Q3Mini"},
];
var listLen = list.length;
var actionNum = 0;

for(var j = 0; j < listLen; j++)
{
    var items = dom.library.items;
    var len = items.length;
    for (var i = len - 1; i > -1; i--) 
	{
	    dom.library.deleteItem(items[i].name);
    }
    
	fl.trace("importing" + list[j].url);
    dom.importFile(list[j].url + list[j].name + ".png", true);
    fl.trace("imported" + list[j].url);
	 
    items = dom.library.items;
    var item = items[0];

    item.linkageExportForAS = true;
    item.linkageExportInFirstFrame = true;
    item.linkageClassName = "RoleBmd";
    item.linkageBaseClass = "flash.display.BitmapData";

    dom.exportSWF(swfUrl + list[j].sign +".swf");
}