var dom = fl.getDocumentDOM();

var swfUrl = "file:///D:/work/dev/client/assets/roles/war/";

var list = [{url : "file:///D:/work/������Ʒ/����/player/F - ������/T0/",  name : "ս��", sign : "FeiYuNan"},
{url : "file:///D:/work/������Ʒ/����/player/F - ������/T0/",  name : "ս��", sign : "FeiYuNanMini"},
{url : "file:///D:/work/������Ʒ/����/player/F - ������/T1/",  name : "ս��", sign : "FeiYuNanT1Q3"},
{url : "file:///D:/work/������Ʒ/����/player/F - ������/T1/",  name : "ս��", sign : "FeiYuNanT1Q3Mini"},
{url : "file:///D:/work/������Ʒ/����/player/F - ������/T2/",  name : "ս��", sign : "FeiYuNanT2Q3"},
{url : "file:///D:/work/������Ʒ/����/player/F - ������/T2/",  name : "ս��", sign : "FeiYuNanT2Q3Mini"},
{url : "file:///D:/work/������Ʒ/����/player/F - ������/T3/",  name : "ս��", sign : "FeiYuNanT3Q3"},
{url : "file:///D:/work/������Ʒ/����/player/F - ������/T3/",  name : "ս��", sign : "FeiYuNanT3Q3Mini"},
{url : "file:///D:/work/������Ʒ/����/player/F - ������/T4/",  name : "ս��", sign : "FeiYuNanT4Q3"},
{url : "file:///D:/work/������Ʒ/����/player/F - ������/T4/",  name : "ս��", sign : "FeiYuNanT4Q3Mini"},
{url : "file:///D:/work/������Ʒ/����/player/F - ����Ů/T0/",  name : "ս��", sign : "FeiYuNv"},
{url : "file:///D:/work/������Ʒ/����/player/F - ����Ů/T0/",  name : "ս��", sign : "FeiYuNvMini"},
{url : "file:///D:/work/������Ʒ/����/player/F - ����Ů/T1/",  name : "ս��", sign : "FeiYuNvT1Q3"},
{url : "file:///D:/work/������Ʒ/����/player/F - ����Ů/T1/",  name : "ս��", sign : "FeiYuNvT1Q3Mini"},
{url : "file:///D:/work/������Ʒ/����/player/F - ����Ů/T2/",  name : "ս��", sign : "FeiYuNvT2Q3"},
{url : "file:///D:/work/������Ʒ/����/player/F - ����Ů/T2/",  name : "ս��", sign : "FeiYuNvT2Q3Mini"},
{url : "file:///D:/work/������Ʒ/����/player/F - ����Ů/T3/",  name : "ս��", sign : "FeiYuNvT3Q3"},
{url : "file:///D:/work/������Ʒ/����/player/F - ����Ů/T3/",  name : "ս��", sign : "FeiYuNvT3Q3Mini"},
{url : "file:///D:/work/������Ʒ/����/player/F - ����Ů/T4/",  name : "ս��", sign : "FeiYuNvT4Q3"},
{url : "file:///D:/work/������Ʒ/����/player/F - ����Ů/T4/",  name : "ս��", sign : "FeiYuNvT4Q3Mini"},
{url : "file:///D:/work/������Ʒ/����/player/J - ������/T0/",  name : "ս��", sign : "JianLingNan"},
{url : "file:///D:/work/������Ʒ/����/player/J - ������/T0/",  name : "ս��", sign : "JianLingNanMini"},
{url : "file:///D:/work/������Ʒ/����/player/J - ������/T1/",  name : "ս��", sign : "JianLingNanT1Q3"},
{url : "file:///D:/work/������Ʒ/����/player/J - ������/T1/",  name : "ս��", sign : "JianLingNanT1Q3Mini"},
{url : "file:///D:/work/������Ʒ/����/player/J - ������/T2/",  name : "ս��", sign : "JianLingNanT2Q3"},
{url : "file:///D:/work/������Ʒ/����/player/J - ������/T2/",  name : "ս��", sign : "JianLingNanT2Q3Mini"},
{url : "file:///D:/work/������Ʒ/����/player/J - ������/T3/",  name : "ս��", sign : "JianLingNanT3Q3"},
{url : "file:///D:/work/������Ʒ/����/player/J - ������/T3/",  name : "ս��", sign : "JianLingNanT3Q3Mini"},
{url : "file:///D:/work/������Ʒ/����/player/J - ������/T4/",  name : "ս��", sign : "JianLingNanT4Q3"},
{url : "file:///D:/work/������Ʒ/����/player/J - ������/T4/",  name : "ս��", sign : "JianLingNanT4Q3Mini"},
{url : "file:///D:/work/������Ʒ/����/player/J - ����Ů/T0/",  name : "ս��", sign : "JianLingNv"},
{url : "file:///D:/work/������Ʒ/����/player/J - ����Ů/T0/",  name : "ս��", sign : "JianLingNvMini"},
{url : "file:///D:/work/������Ʒ/����/player/J - ����Ů/T1/",  name : "ս��", sign : "JianLingNvT1Q3"},
{url : "file:///D:/work/������Ʒ/����/player/J - ����Ů/T1/",  name : "ս��", sign : "JianLingNvT1Q3Mini"},
{url : "file:///D:/work/������Ʒ/����/player/J - ����Ů/T2/",  name : "ս��", sign : "JianLingNvT2Q3"},
{url : "file:///D:/work/������Ʒ/����/player/J - ����Ů/T2/",  name : "ս��", sign : "JianLingNvT2Q3Mini"},
{url : "file:///D:/work/������Ʒ/����/player/J - ����Ů/T3/",  name : "ս��", sign : "JianLingNvT3Q3"},
{url : "file:///D:/work/������Ʒ/����/player/J - ����Ů/T3/",  name : "ս��", sign : "JianLingNvT3Q3Mini"},
{url : "file:///D:/work/������Ʒ/����/player/J - ����Ů/T4/",  name : "ս��", sign : "JianLingNvT3Q3"},
{url : "file:///D:/work/������Ʒ/����/player/J - ����Ů/T4/",  name : "ս��", sign : "JianLingNvT3Q3Mini"},
{url : "file:///D:/work/������Ʒ/����/player/W - ��ʥ��/T0/",  name : "ս��", sign : "WuShengNan"},
{url : "file:///D:/work/������Ʒ/����/player/W - ��ʥ��/T0/",  name : "ս��", sign : "WuShengNanMini"},
{url : "file:///D:/work/������Ʒ/����/player/W - ��ʥ��/T1/",  name : "ս��", sign : "WuShengNanT1Q3"},
{url : "file:///D:/work/������Ʒ/����/player/W - ��ʥ��/T1/",  name : "ս��", sign : "WuShengNanT1Q3Mini"},
{url : "file:///D:/work/������Ʒ/����/player/W - ��ʥ��/T2/",  name : "ս��", sign : "WuShengNanT2Q3"},
{url : "file:///D:/work/������Ʒ/����/player/W - ��ʥ��/T2/",  name : "ս��", sign : "WuShengNanT2Q3Mini"},
{url : "file:///D:/work/������Ʒ/����/player/W - ��ʥ��/T3/",  name : "ս��", sign : "WuShengNanT3Q3"},
{url : "file:///D:/work/������Ʒ/����/player/W - ��ʥ��/T3/",  name : "ս��", sign : "WuShengNanT3Q3Mini"},
{url : "file:///D:/work/������Ʒ/����/player/W - ��ʥ��/T4/",  name : "ս��", sign : "WuShengNanT4Q3"},
{url : "file:///D:/work/������Ʒ/����/player/W - ��ʥ��/T4/",  name : "ս��", sign : "WuShengNanT4Q3Mini"},
{url : "file:///D:/work/������Ʒ/����/player/W - ��ʥŮ/T0/",  name : "ս��", sign : "WuShengNv"},
{url : "file:///D:/work/������Ʒ/����/player/W - ��ʥŮ/T0/",  name : "ս��", sign : "WuShengNvMini"},
{url : "file:///D:/work/������Ʒ/����/player/W - ��ʥŮ/T1/",  name : "ս��", sign : "WuShengNvT1Q3"},
{url : "file:///D:/work/������Ʒ/����/player/W - ��ʥŮ/T1/",  name : "ս��", sign : "WuShengNvT1Q3Mini"},
{url : "file:///D:/work/������Ʒ/����/player/W - ��ʥŮ/T2/",  name : "ս��", sign : "WuShengNvT2Q3"},
{url : "file:///D:/work/������Ʒ/����/player/W - ��ʥŮ/T2/",  name : "ս��", sign : "WuShengNvT2Q3Mini"},
{url : "file:///D:/work/������Ʒ/����/player/W - ��ʥŮ/T3/",  name : "ս��", sign : "WuShengNvT3Q3"},
{url : "file:///D:/work/������Ʒ/����/player/W - ��ʥŮ/T3/",  name : "ս��", sign : "WuShengNvT3Q3Mini"},
{url : "file:///D:/work/������Ʒ/����/player/W - ��ʥŮ/T4/",  name : "ս��", sign : "WuShengNvT4Q3"},
{url : "file:///D:/work/������Ʒ/����/player/W - ��ʥŮ/T4/",  name : "ս��", sign : "WuShengNvT4Q3Mini"},
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