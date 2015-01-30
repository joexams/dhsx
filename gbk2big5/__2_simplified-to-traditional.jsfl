// http://www.actionscript.org/forums/showthread.php3?t=217810

// ²Ù×÷ client/ Ä¿ä›£¬ÔÙ¿½ØÖÁclient-tw/

// ĞèÒª¸²ÉwµÄÙYÔ´
// 1. ¹¦ÄÜé_†¢µÄˆDÆ¬
// 2. ‘ğ”¡ÌáÊ¾
// 3. my_faction.fla" °ïÅÉQQ" ¸ÄÎª "°ïÅÉÉçÈº"
// 4. ¿½±´¸±±¾Êı¾İtxt

// ĞèÒªĞŞ¸ÄµÄ´ú´a
// 1. a. chat„h³ıÓĞÒ»é_Ê¼µÄÌáÊ¾ĞÅÏ¢
//    b. È¡ÏûÈ«Æ½Ì¨ÆÁ±Î

var dirName = "file:///F|//dev/20111201/";
var logPath = "file:///F|//dev/gbk2big5/log/";

var flas = getFlas();

// ĞèÒª×ª»»Îª·±Ìå°æµÄÎÄ¼ş
// ¹¦ÄÜé_†¢µÄˆDÆ¬
flas.push(
		'activities/activities_tw.fla',
		'chat/chat_tw.fla',
		'choose_roles/choose_roles_tw.fla',
		//'farm/farm_tw.fla',
		'mission_rank/mission_rank_tw.fla',
		'partners/partners_tw.fla',
		'super_sport/super_sport_tw.fla',
		'toolbar/toolbar_tw.fla',
		'vip/vip_tw.fla',
		'roles/effects/levelUp_·±Ìå.fla',
		'faction/my_faction/my_faction_tw.fla'
);

/*
*/
flas = [
		'role_msg/role_detail_info.fla',
		'toolbar/toolbar_tw.fla'
];

var sIndex = 0;

for (var i = sIndex; i < flas.length; i++) {
	var file = dirName + "client-resources/" + flas[i];
	if (file == "") continue;
	
	fl.trace(i + '.open >>> ' + file);
	
	var dom = fl.openDocument(file);//fl.getDocumentDOM();
	var library = dom.library;
	var items = library.items;
	
	checkItems(items);
	
	dom.publish();
	
	fl.trace(i + '. close >>> ' + file);
	
	//if (! confirm(file)) {
	//	break;
	//}
	
	fl.compilerErrors.save(logPath + i + "_" + dom.name + ".txt");
	
	fl.closeDocument(dom, false);
	
	//break;
}

// (1)
function checkItems (items) {
	var len = items.length;
	for(var i = 0; i < len; i++) {
		checkItem(items[i]);
	}
}

// (2)
function checkItem (item) {
	switch (item.itemType) {
		case 'folder':
			//checkFolder(item);
			break;
		case 'button':
			checkButton(item);
			break;
		default:
			name = item.name;
			checkMovieClip(item);
			name = '';
			break;
	}
}

// (3)(A)
function checkFolder (folder) {
}

// (3)(B)
function checkButton (button) {
	// [object SymbolItem]
	var layers = button.timeline.layers;
	var len1 = layers.length;
	for (var i = 0; i < len1; i++) {
		var frames = layers[i].frames;
		var len2 = frames.length;
		for (var j = 0; j < len2; j++) {
			var elements = frames[j].elements;
			var len3 = elements.length;
			for (var k = 0; k < len3; k++) {
				switch (elements[k].elementType) {
					case 'shape':
						break;
					case 'text':
						checkTextField(elements[k]);
						break;
					// movieclip instance
					case 'instance':
						//fl.trace(elements[k].symbolType);
						//checkInstance(elements[k]);
						break;
					case 'shapeObj':
						break;
				}
			}
		}
	}
}

// (3)(c)
// mc.timeline[index].layers[index].frames[index].elements[index];
function checkMovieClip (mc) {
	if (mc.itemType != 'movie clip') return;
		
	var layers = mc.timeline.layers;
	var len1 = layers.length;
	for (var i = 0; i < len1; i++) {
		var frames = layers[i].frames;
		var len2 = frames.length;
		for (var j = 0; j < len2; j++) {
			var elements = frames[j].elements;
			var len3 = elements.length;
			for (var k = 0; k < len3; k++) {
				switch (elements[k].elementType) {
					case 'shape':
						break;
					case 'text':
						checkTextField(elements[k]);
						break;
					// movieclip instance
					case 'instance':
						//fl.trace(elements[k].symbolType);
						//checkInstance(elements[k]);
						break;
					case 'shapeObj':
						break;
				}
			}
		}
	}
}

// (4)(A)
function checkInstance (instance) {
	switch (instance.symbolType) {
		case 'button':
			break;
		case 'movie clip':
			checkMovieClip(instance['libraryItem']);
			//for (var item in instance['libraryItem']) {
			//	fl.trace(item + ': ' + instance['libraryItem']['timeline']);
			//}
			//fl.trace(instance);
			break;
		case 'graphic':
			break;
	}
}

// (4)(B)
function checkTextField (tf) {
	if (tf.elementType != 'text') return;
	
	if (tf.fontRenderingMode != 'device') {
		tf.fontRenderingMode = 'device';
	}
	
	
	// ¼ò×ª·±
	var value = tf.getTextString();
	fl.trace(value);
	value = traditionalized(value);
	value = value.replace(/³äÖµ/g, "ƒ¦Öµ");
	value = value.replace(/×Ö·û/g, "×ÖÔª");
	tf.setTextString(value);
}

/**
 * ·±¼ò×ª»»
 */

function simplified () {
	return '°¡°¢°£°¤°¥°¦°§°¨°©°ª°«°¬°­°®°¯°°°±°²°³°´°µ°¶°·°¸°¹°º°»°¼°½°¾°¿°À°Á°Â°Ã°Ä°Å°Æ°Ç°È°É°Ê°Ë°Ì°Í°Î°Ï°Ğ°Ñ°Ò°Ó°Ô°Õ°Ö°×°Ø°Ù°Ú°Û°Ü°İ°Ş°ß°à°á°â°ã°ä°å°æ°ç°è°é°ê°ë°ì°í°î°ï°ğ°ñ°ò°ó°ô°õ°ö°÷°ø°ù°ú°û°ü°ı°ş±¡±¢±£±¤±¥±¦±§±¨±©±ª±«±¬±­±®±¯±°±±±²±³±´±µ±¶±·±¸±¹±º±»±¼±½±¾±¿±À±Á±Â±Ã±Ä±Å±Æ±Ç±È±É±Ê±Ë±Ì±Í±Î±Ï±Ğ±Ñ±Ò±Ó±Ô±Õ±Ö±×±Ø±Ù±Ú±Û±Ü±İ±Ş±ß±à±á±â±ã±ä±å±æ±ç±è±é±ê±ë±ì±í±î±ï±ğ±ñ±ò±ó±ô±õ±ö±÷±ø±ù±ú±û±ü±ı±ş²¡²¢²£²¤²¥²¦²§²¨²©²ª²«²¬²­²®²¯²°²±²²²³²´²µ²¶²·²¸²¹²º²»²¼²½²¾²¿²À²Á²Â²Ã²Ä²Å²Æ²Ç²È²É²Ê²Ë²Ì²Í²Î²Ï²Ğ²Ñ²Ò²Ó²Ô²Õ²Ö²×²Ø²Ù²Ú²Û²Ü²İ²Ş²ß²à²á²â²ã²ä²å²æ²ç²è²é²ê²ë²ì²í²î²ï²ğ²ñ²ò²ó²ô²õ²ö²÷²ø²ù²ú²û²ü²ı²ş³¡³¢³£³¤³¥³¦³§³¨³©³ª³«³¬³­³®³¯³°³±³²³³³´³µ³¶³·³¸³¹³º³»³¼³½³¾³¿³À³Á³Â³Ã³Ä³Å³Æ³Ç³È³É³Ê³Ë³Ì³Í³Î³Ï³Ğ³Ñ³Ò³Ó³Ô³Õ³Ö³×³Ø³Ù³Ú³Û³Ü³İ³Ş³ß³à³á³â³ã³ä³å³æ³ç³è³é³ê³ë³ì³í³î³ï³ğ³ñ³ò³ó³ô³õ³ö³÷³ø³ù³ú³û³ü³ı³ş´¡´¢´£´¤´¥´¦´§´¨´©´ª´«´¬´­´®´¯´°´±´²´³´´´µ´¶´·´¸´¹´º´»´¼´½´¾´¿´À´Á´Â´Ã´Ä´Å´Æ´Ç´È´É´Ê´Ë´Ì´Í´Î´Ï´Ğ´Ñ´Ò´Ó´Ô´Õ´Ö´×´Ø´Ù´Ú´Û´Ü´İ´Ş´ß´à´á´â´ã´ä´å´æ´ç´è´é´ê´ë´ì´í´î´ï´ğ´ñ´ò´ó´ô´õ´ö´÷´ø´ù´ú´û´ü´ı´şµ¡µ¢µ£µ¤µ¥µ¦µ§µ¨µ©µªµ«µ¬µ­µ®µ¯µ°µ±µ²µ³µ´µµµ¶µ·µ¸µ¹µºµ»µ¼µ½µ¾µ¿µÀµÁµÂµÃµÄµÅµÆµÇµÈµÉµÊµËµÌµÍµÎµÏµĞµÑµÒµÓµÔµÕµÖµ×µØµÙµÚµÛµÜµİµŞµßµàµáµâµãµäµåµæµçµèµéµêµëµìµíµîµïµğµñµòµóµôµõµöµ÷µøµùµúµûµüµıµş¶¡¶¢¶£¶¤¶¥¶¦¶§¶¨¶©¶ª¶«¶¬¶­¶®¶¯¶°¶±¶²¶³¶´¶µ¶¶¶·¶¸¶¹¶º¶»¶¼¶½¶¾¶¿¶À¶Á¶Â¶Ã¶Ä¶Å¶Æ¶Ç¶È¶É¶Ê¶Ë¶Ì¶Í¶Î¶Ï¶Ğ¶Ñ¶Ò¶Ó¶Ô¶Õ¶Ö¶×¶Ø¶Ù¶Ú¶Û¶Ü¶İ¶Ş¶ß¶à¶á¶â¶ã¶ä¶å¶æ¶ç¶è¶é¶ê¶ë¶ì¶í¶î¶ï¶ğ¶ñ¶ò¶ó¶ô¶õ¶ö¶÷¶ø¶ù¶ú¶û¶ü¶ı¶ş·¡·¢·£·¤·¥·¦·§·¨·©·ª·«·¬·­·®·¯·°·±·²·³·´·µ·¶···¸·¹·º·»·¼·½·¾·¿·À·Á·Â·Ã·Ä·Å·Æ·Ç·È·É·Ê·Ë·Ì·Í·Î·Ï·Ğ·Ñ·Ò·Ó·Ô·Õ·Ö·×·Ø·Ù·Ú·Û·Ü·İ·Ş·ß·à·á·â·ã·ä·å·æ·ç·è·é·ê·ë·ì·í·î·ï·ğ·ñ·ò·ó·ô·õ·ö·÷·ø·ù·ú·û·ü·ı·ş¸¡¸¢¸£¸¤¸¥¸¦¸§¸¨¸©¸ª¸«¸¬¸­¸®¸¯¸°¸±¸²¸³¸´¸µ¸¶¸·¸¸¸¹¸º¸»¸¼¸½¸¾¸¿¸À¸Á¸Â¸Ã¸Ä¸Å¸Æ¸Ç¸È¸É¸Ê¸Ë¸Ì¸Í¸Î¸Ï¸Ğ¸Ñ¸Ò¸Ó¸Ô¸Õ¸Ö¸×¸Ø¸Ù¸Ú¸Û¸Ü¸İ¸Ş¸ß¸à¸á¸â¸ã¸ä¸å¸æ¸ç¸è¸é¸ê¸ë¸ì¸í¸î¸ï¸ğ¸ñ¸ò¸ó¸ô¸õ¸ö¸÷¸ø¸ù¸ú¸û¸ü¸ı¸ş¹¡¹¢¹£¹¤¹¥¹¦¹§¹¨¹©¹ª¹«¹¬¹­¹®¹¯¹°¹±¹²¹³¹´¹µ¹¶¹·¹¸¹¹¹º¹»¹¼¹½¹¾¹¿¹À¹Á¹Â¹Ã¹Ä¹Å¹Æ¹Ç¹È¹É¹Ê¹Ë¹Ì¹Í¹Î¹Ï¹Ğ¹Ñ¹Ò¹Ó¹Ô¹Õ¹Ö¹×¹Ø¹Ù¹Ú¹Û¹Ü¹İ¹Ş¹ß¹à¹á¹â¹ã¹ä¹å¹æ¹ç¹è¹é¹ê¹ë¹ì¹í¹î¹ï¹ğ¹ñ¹ò¹ó¹ô¹õ¹ö¹÷¹ø¹ù¹ú¹û¹ü¹ı¹şº¡º¢º£º¤º¥º¦º§º¨º©ºªº«º¬º­º®º¯º°º±º²º³º´ºµº¶º·º¸º¹ººº»º¼º½º¾º¿ºÀºÁºÂºÃºÄºÅºÆºÇºÈºÉºÊºËºÌºÍºÎºÏºĞºÑºÒºÓºÔºÕºÖº×ºØºÙºÚºÛºÜºİºŞºßºàºáºâºãºäºåºæºçºèºéºêºëºìºíºîºïºğºñºòºóºôºõºöº÷ºøºùºúºûºüºıºş»¡»¢»£»¤»¥»¦»§»¨»©»ª»«»¬»­»®»¯»°»±»²»³»´»µ»¶»·»¸»¹»º»»»¼»½»¾»¿»À»Á»Â»Ã»Ä»Å»Æ»Ç»È»É»Ê»Ë»Ì»Í»Î»Ï»Ğ»Ñ»Ò»Ó»Ô»Õ»Ö»×»Ø»Ù»Ú»Û»Ü»İ»Ş»ß»à»á»â»ã»ä»å»æ»ç»è»é»ê»ë»ì»í»î»ï»ğ»ñ»ò»ó»ô»õ»ö»÷»ø»ù»ú»û»ü»ı»ş¼¡¼¢¼£¼¤¼¥¼¦¼§¼¨¼©¼ª¼«¼¬¼­¼®¼¯¼°¼±¼²¼³¼´¼µ¼¶¼·¼¸¼¹¼º¼»¼¼¼½¼¾¼¿¼À¼Á¼Â¼Ã¼Ä¼Å¼Æ¼Ç¼È¼É¼Ê¼Ë¼Ì¼Í¼Î¼Ï¼Ğ¼Ñ¼Ò¼Ó¼Ô¼Õ¼Ö¼×¼Ø¼Ù¼Ú¼Û¼Ü¼İ¼Ş¼ß¼à¼á¼â¼ã¼ä¼å¼æ¼ç¼è¼é¼ê¼ë¼ì¼í¼î¼ï¼ğ¼ñ¼ò¼ó¼ô¼õ¼ö¼÷¼ø¼ù¼ú¼û¼ü¼ı¼ş½¡½¢½£½¤½¥½¦½§½¨½©½ª½«½¬½­½®½¯½°½±½²½³½´½µ½¶½·½¸½¹½º½»½¼½½½¾½¿½À½Á½Â½Ã½Ä½Å½Æ½Ç½È½É½Ê½Ë½Ì½Í½Î½Ï½Ğ½Ñ½Ò½Ó½Ô½Õ½Ö½×½Ø½Ù½Ú¾¥¾¦¾§¾¨¾©¾ª¾«¾¬¾­¾®¾¯¾°¾±¾²¾³¾´¾µ¾¶¾·¾¸¾¹¾º¾»¾¼¾½¾¾¾¿¾À¾Á¾Â¾Ã¾Ä¾Å¾Æ¾Ç¾È¾É¾Ê¾Ë¾Ì¾Í¾Î¾Ï¾Ğ¾Ñ¾Ò¾Ó¾Ô¾Õ¾Ö¾×¾Ø¾Ù¾Ú¾Û¾Ü¾İ¾Ş¾ß¾à¾á¾â¾ã¾ä¾å¾æ¾ç¾è¾é¾ê¾ë¾ì¾í¾î¾ï¾ğ¾ñ¾ò¾ó¾ô½Û½Ü½İ½Ş½ß½à½á½â½ã½ä½å½æ½ç½è½é½ê½ë½ì½í½î½ï½ğ½ñ½ò½ó½ô½õ½ö½÷½ø½ù½ú½û½ü½ı½ş¾¡¾¢¾£¾¤¾õ¾ö¾÷¾ø¾ù¾ú¾û¾ü¾ı¾ş¿¡¿¢¿£¿¤¿¥¿¦¿§¿¨¿©¿ª¿«¿¬¿­¿®¿¯¿°¿±¿²¿³¿´¿µ¿¶¿·¿¸¿¹¿º¿»¿¼¿½¿¾¿¿¿À¿Á¿Â¿Ã¿Ä¿Å¿Æ¿Ç¿È¿É¿Ê¿Ë¿Ì¿Í¿Î¿Ï¿Ğ¿Ñ¿Ò¿Ó¿Ô¿Õ¿Ö¿×¿Ø¿Ù¿Ú¿Û¿Ü¿İ¿Ş¿ß¿à¿á¿â¿ã¿ä¿å¿æ¿ç¿è¿é¿ê¿ë¿ì¿í¿î¿ï¿ğ¿ñ¿ò¿ó¿ô¿õ¿ö¿÷¿ø¿ù¿ú¿û¿ü¿ı¿şÀ¡À¢À£À¤À¥À¦À§À¨À©ÀªÀ«À¬À­À®À¯À°À±À²À³À´ÀµÀ¶À·À¸À¹ÀºÀ»À¼À½À¾À¿ÀÀÀÁÀÂÀÃÀÄÀÅÀÆÀÇÀÈÀÉÀÊÀËÀÌÀÍÀÎÀÏÀĞÀÑÀÒÀÓÀÔÀÕÀÖÀ×ÀØÀÙÀÚÀÛÀÜÀİÀŞÀßÀàÀáÀâÀãÀäÀåÀæÀçÀèÀéÀêÀëÀìÀíÀîÀïÀğÀñÀòÀóÀôÀõÀöÀ÷ÀøÀùÀúÀûÀüÀıÀşÁ¡Á¢Á£Á¤Á¥Á¦Á§Á¨Á©ÁªÁ«Á¬Á­Á®Á¯Á°Á±Á²Á³Á´ÁµÁ¶Á·Á¸Á¹ÁºÁ»Á¼Á½Á¾Á¿ÁÀÁÁÁÂÁÃÁÄÁÅÁÆÁÇÁÈÁÉÁÊÁËÁÌÁÍÁÎÁÏÁĞÁÑÁÒÁÓÁÔÁÕÁÖÁ×ÁØÁÙÁÚÁÛÁÜÁİÁŞÁßÁàÁáÁâÁãÁäÁåÁæÁçÁèÁéÁêÁëÁìÁíÁîÁïÁğÁñÁòÁóÁôÁõÁöÁ÷ÁøÁùÁúÁûÁüÁıÁşÂ¡Â¢Â£Â¤Â¥Â¦Â§Â¨Â©ÂªÂ«Â¬Â­Â®Â¯Â°Â±Â²Â³Â´ÂµÂ¶Â·Â¸Â¹ÂºÂ»Â¼Â½Â¾Â¿ÂÀÂÁÂÂÂÃÂÄÂÅÂÆÂÇÂÈÂÉÂÊÂËÂÌÂÍÂÎÂÏÂĞÂÑÂÒÂÓÂÔÂÕÂÖÂ×ÂØÂÙÂÚÂÛÂÜÂİÂŞÂßÂàÂáÂâÂãÂäÂåÂæÂçÂèÂéÂêÂëÂìÂíÂîÂïÂğÂñÂòÂóÂôÂõÂöÂ÷ÂøÂùÂúÂûÂüÂıÂşÃ¡Ã¢Ã£Ã¤Ã¥Ã¦Ã§Ã¨Ã©ÃªÃ«Ã¬Ã­Ã®Ã¯Ã°Ã±Ã²Ã³Ã´ÃµÃ¶Ã·Ã¸Ã¹ÃºÃ»Ã¼Ã½Ã¾Ã¿ÃÀÃÁÃÂÃÃÃÄÃÅÃÆÃÇÃÈÃÉÃÊÃËÃÌÃÍÃÎÃÏÃĞÃÑÃÒÃÓÃÔÃÕÃÖÃ×ÃØÃÙÃÚÃÛÃÜÃİÃŞÃßÃàÃáÃâÃãÃäÃåÃæÃçÃèÃéÃêÃëÃìÃíÃîÃïÃğÃñÃòÃóÃôÃõÃöÃ÷ÃøÃùÃúÃûÃüÃıÃşÄ¡Ä¢Ä£Ä¤Ä¥Ä¦Ä§Ä¨Ä©ÄªÄ«Ä¬Ä­Ä®Ä¯Ä°Ä±Ä²Ä³Ä´ÄµÄ¶Ä·Ä¸Ä¹ÄºÄ»Ä¼Ä½Ä¾Ä¿ÄÀÄÁÄÂÄÃÄÄÄÅÄÆÄÇÄÈÄÉÄÊÄËÄÌÄÍÄÎÄÏÄĞÄÑÄÒÄÓÄÔÄÕÄÖÄ×ÄØÄÙÄÚÄÛÄÜÄİÄŞÄßÄàÄáÄâÄãÄäÄåÄæÄçÄèÄéÄêÄëÄìÄíÄîÄïÄğÄñÄòÄóÄôÄõÄöÄ÷ÄøÄùÄúÄûÄüÄıÄşÅ¡Å¢Å£Å¤Å¥Å¦Å§Å¨Å©ÅªÅ«Å¬Å­Å®Å¯Å°Å±Å²Å³Å´ÅµÅ¶Å·Å¸Å¹ÅºÅ»Å¼Å½Å¾Å¿ÅÀÅÁÅÂÅÃÅÄÅÅÅÆÅÇÅÈÅÉÅÊÅËÅÌÅÍÅÎÅÏÅĞÅÑÅÒÅÓÅÔÅÕÅÖÅ×ÅØÅÙÅÚÅÛÅÜÅİÅŞÅßÅàÅáÅâÅãÅäÅåÅæÅçÅèÅéÅêÅëÅìÅíÅîÅïÅğÅñÅòÅóÅôÅõÅöÅ÷ÅøÅùÅúÅûÅüÅıÅşÆ¡Æ¢Æ£Æ¤Æ¥Æ¦Æ§Æ¨Æ©ÆªÆ«Æ¬Æ­Æ®Æ¯Æ°Æ±Æ²Æ³Æ´ÆµÆ¶Æ·Æ¸Æ¹ÆºÆ»Æ¼Æ½Æ¾Æ¿ÆÀÆÁÆÂÆÃÆÄÆÅÆÆÆÇÆÈÆÉÆÊÆËÆÌÆÍÆÎÆÏÆĞÆÑÆÒÆÓÆÔÆÕÆÖÆ×ÆØÆÙÆÚÆÛÆÜÆİÆŞÆßÆàÆáÆâÆãÆäÆåÆæÆçÆèÆéÆêÆëÆìÆíÆîÆïÆğÆñÆòÆóÆôÆõÆöÆ÷ÆøÆùÆúÆûÆüÆıÆşÇ¢Ç£Ç¤Ç¥Ç¦Ç§Ç¨Ç©ÇªÇ«Ç¬Ç­Ç®Ç¯Ç°Ç±Ç²Ç³Ç´ÇµÇ¶Ç·Ç¸Ç¹ÇºÇ»Ç¼Ç½Ç¾Ç¿ÇÀÇÁÇÂÇÃÇÄÇÅÇÆÇÇÇÈÇÉÇÊÇËÇÌÇÍÇÎÇÏÇĞÇÑÇÒÇÓÇÔÇÕÇÖÇ×ÇØÇÙÇÚÇÛÇÜÇİÇŞÇßÇàÇáÇâÇãÇäÇåÇæÇçÇèÇéÇêÇëÇìÇíÇîÇïÇğÇñÇòÇóÇôÇõÇöÇ÷ÇøÇùÇúÇûÇüÇıÇşÈ¡È¢È£È¤È¥È¦È§È¨È©ÈªÈ«È¬È­È®È¯È°È±È²È³È´ÈµÈ¶È·È¸È¹ÈºÈ»È¼È½È¾È¿ÈÀÈÁÈÂÈÃÈÄÈÅÈÆÈÇÈÈÈÉÈÊÈËÈÌÈÍÈÎÈÏÈĞÈÑÈÒÈÓÈÔÈÕÈÖÈ×ÈØÈÙÈÚÈÛÈÜÈİÈŞÈßÈàÈáÈâÈãÈäÈåÈæÈçÈèÈéÈêÈëÈìÈíÈîÈïÈğÈñÈòÈóÈôÈõÈöÈ÷ÈøÈùÈúÈûÈüÈıÈşÉ¡É¢É£É¤É¥É¦É§É¨É©ÉªÉ«É¬É­É®É¯É°É±É²É³É´ÉµÉ¶É·É¸É¹ÉºÉ»É¼É½É¾É¿ÉÀÉÁÉÂÉÃÉÄÉÅÉÆÉÇÉÈÉÉÉÊÉËÉÌÉÍÉÎÉÏÉĞÉÑÉÒÉÓÉÔÉÕÉÖÉ×ÉØÉÙÉÚÉÛÉÜÉİÉŞÉßÉàÉáÉâÉãÉäÉåÉæÉçÉèÉéÉêÉëÉìÉíÉîÉïÉğÉñÉòÉóÉôÉõÉöÉ÷ÉøÉùÉúÉûÉüÉıÉşÊ¡Ê¢Ê£Ê¤Ê¥Ê¦Ê§Ê¨Ê©ÊªÊ«Ê¬Ê­Ê®Ê¯Ê°Ê±Ê²Ê³Ê´ÊµÊ¶Ê·Ê¸Ê¹ÊºÊ»Ê¼Ê½Ê¾Ê¿ÊÀÊÁÊÂÊÃÊÄÊÅÊÆÊÇÊÈÊÉÊÊÊËÊÌÊÍÊÎÊÏÊĞÊÑÊÒÊÓÊÔÊÕÊÖÊ×ÊØÊÙÊÚÊÛÊÜÊİÊŞÊßÊàÊáÊâÊãÊäÊåÊæÊçÊèÊéÊêÊëÊìÊíÊîÊïÊğÊñÊòÊóÊôÊõÊöÊ÷ÊøÊùÊúÊûÊüÊıÊşË¡Ë¢Ë£Ë¤Ë¥Ë¦Ë§Ë¨Ë©ËªË«Ë¬Ë­Ë®Ë¯Ë°Ë±Ë²Ë³Ë´ËµË¶Ë·Ë¸Ë¹ËºË»Ë¼Ë½Ë¾Ë¿ËÀËÁËÂËÃËÄËÅËÆËÇËÈËÉËÊËËËÌËÍËÎËÏËĞËÑËÒËÓËÔËÕËÖË×ËØËÙËÚËÛËÜËİËŞËßËàËáËâËãËäËåËæËçËèËéËêËëËìËíËîËïËğËñËòËóËôËõËöË÷ËøËùËúËûËüËıËşÌ¡Ì¢Ì£Ì¤Ì¥Ì¦Ì§Ì¨Ì©ÌªÌ«Ì¬Ì­Ì®Ì¯Ì°Ì±Ì²Ì³Ì´ÌµÌ¶Ì·Ì¸Ì¹ÌºÌ»Ì¼Ì½Ì¾Ì¿ÌÀÌÁÌÂÌÃÌÄÌÅÌÆÌÇÌÈÌÉÌÊÌËÌÌÌÍÌÎÌÏÌĞÌÑÌÒÌÓÌÔÌÕÌÖÌ×ÌØÌÙÌÚÌÛÌÜÌİÌŞÌßÌàÌáÌâÌãÌäÌåÌæÌçÌèÌéÌêÌëÌìÌíÌîÌïÌğÌñÌòÌóÌôÌõÌöÌ÷ÌøÌùÌúÌûÌüÌıÌşÍ¡Í¢Í£Í¤Í¥Í¦Í§Í¨Í©ÍªÍ«Í¬Í­Í®Í¯Í°Í±Í²Í³Í´ÍµÍ¶Í·Í¸Í¹ÍºÍ»Í¼Í½Í¾Í¿ÍÀÍÁÍÂÍÃÍÄÍÅÍÆÍÇÍÈÍÉÍÊÍËÍÌÍÍÍÎÍÏÍĞÍÑÍÒÍÓÍÔÍÕÍÖÍ×ÍØÍÙÍÚÍÛÍÜÍİÍŞÍßÍàÍáÍâÍãÍäÍåÍæÍçÍèÍéÍêÍëÍìÍíÍîÍïÍğÍñÍòÍóÍôÍõÍöÍ÷ÍøÍùÍúÍûÍüÍıÍşÎ¡Î¢Î£Î¤Î¥Î¦Î§Î¨Î©ÎªÎ«Î¬Î­Î®Î¯Î°Î±Î²Î³Î´ÎµÎ¶Î·Î¸Î¹ÎºÎ»Î¼Î½Î¾Î¿ÎÀÎÁÎÂÎÃÎÄÎÅÎÆÎÇÎÈÎÉÎÊÎËÎÌÎÍÎÎÎÏÎĞÎÑÎÒÎÓÎÔÎÕÎÖÎ×ÎØÎÙÎÚÎÛÎÜÎİÎŞÎßÎàÎáÎâÎãÎäÎåÎæÎçÎèÎéÎêÎëÎìÎíÎîÎïÎğÎñÎòÎóÎôÎõÎöÎ÷ÎøÎùÎúÎûÎüÎıÎşÏ¡Ï¢Ï£Ï¤Ï¥Ï¦Ï§Ï¨Ï©ÏªÏ«Ï¬Ï­Ï®Ï¯Ï°Ï±Ï²Ï³Ï´ÏµÏ¶Ï·Ï¸Ï¹ÏºÏ»Ï¼Ï½Ï¾Ï¿ÏÀÏÁÏÂÏÃÏÄÏÅÏÆÏÇÏÈÏÉÏÊÏËÏÌÏÍÏÎÏÏÏĞÏÑÏÒÏÓÏÔÏÕÏÖÏ×ÏØÏÙÏÚÏÛÏÜÏİÏŞÏßÏàÏáÏâÏãÏäÏåÏæÏçÏèÏéÏêÏëÏìÏíÏîÏïÏğÏñÏòÏóÏôÏõÏöÏ÷ÏøÏùÏúÏûÏüÏıÏşĞ¡Ğ¢Ğ£Ğ¤Ğ¥Ğ¦Ğ§Ğ¨Ğ©ĞªĞ«Ğ¬Ğ­Ğ®Ğ¯Ğ°Ğ±Ğ²Ğ³Ğ´ĞµĞ¶Ğ·Ğ¸Ğ¹ĞºĞ»Ğ¼Ğ½Ğ¾Ğ¿ĞÀĞÁĞÂĞÃĞÄĞÅĞÆĞÇĞÈĞÉĞÊĞËĞÌĞÍĞÎĞÏĞĞĞÑĞÒĞÓĞÔĞÕĞÖĞ×ĞØĞÙĞÚĞÛĞÜĞİĞŞĞßĞàĞáĞâĞãĞäĞåĞæĞçĞèĞéĞêĞëĞìĞíĞîĞïĞğĞñĞòĞóĞôĞõĞöĞ÷ĞøĞùĞúĞûĞüĞıĞşÑ¡Ñ¢Ñ£Ñ¤Ñ¥Ñ¦Ñ§Ñ¨Ñ©ÑªÑ«Ñ¬Ñ­Ñ®Ñ¯Ñ°Ñ±Ñ²Ñ³Ñ´ÑµÑ¶Ñ·Ñ¸Ñ¹ÑºÑ»Ñ¼Ñ½Ñ¾Ñ¿ÑÀÑÁÑÂÑÃÑÄÑÅÑÆÑÇÑÈÑÉÑÊÑËÑÌÑÍÑÎÑÏÑĞÑÑÑÒÑÓÑÔÑÕÑÖÑ×ÑØÑÙÑÚÑÛÑÜÑİÑŞÑßÑàÑáÑâÑãÑäÑåÑæÑçÑèÑéÑêÑëÑìÑíÑîÑïÑğÑñÑòÑóÑôÑõÑöÑ÷ÑøÑùÑúÑûÑüÑıÑşÒ¡Ò¢Ò£Ò¤Ò¥Ò¦Ò§Ò¨Ò©ÒªÒ«Ò¬Ò­Ò®Ò¯Ò°Ò±Ò²Ò³Ò´ÒµÒ¶Ò·Ò¸Ò¹ÒºÒ»Ò¼Ò½Ò¾Ò¿ÒÀÒÁÒÂÒÃÒÄÒÅÒÆÒÇÒÈÒÉÒÊÒËÒÌÒÍÒÎÒÏÒĞÒÑÒÒÒÓÒÔÒÕÒÖÒ×ÒØÒÙÒÚÒÛÒÜÒİÒŞÒßÒàÒáÒâÒãÒäÒåÒæÒçÒèÒéÒêÒëÒìÒíÒîÒïÒğÒñÒòÒóÒôÒõÒöÒ÷ÒøÒùÒúÒûÒüÒıÒşÓ¡Ó¢Ó£Ó¤Ó¥Ó¦Ó§Ó¨Ó©ÓªÓ«Ó¬Ó­Ó®Ó¯Ó°Ó±Ó²Ó³Ó´ÓµÓ¶Ó·Ó¸Ó¹ÓºÓ»Ó¼Ó½Ó¾Ó¿ÓÀÓÁÓÂÓÃÓÄÓÅÓÆÓÇÓÈÓÉÓÊÓËÓÌÓÍÓÎÓÏÓĞÓÑÓÒÓÓÓÔÓÕÓÖÓ×ÓØÓÙÓÚÓÛÓÜÓİÓŞÓßÓàÓáÓâÓãÓäÓåÓæÓçÓèÓéÓêÓëÓìÓíÓîÓïÓğÓñÓòÓóÓôÓõÓöÓ÷ÓøÓùÓúÓûÓüÓıÓşÔ¡Ô¢Ô£Ô¤Ô¥Ô¦Ô§Ô¨Ô©ÔªÔ«Ô¬Ô­Ô®Ô¯Ô°Ô±Ô²Ô³Ô´ÔµÔ¶Ô·Ô¸Ô¹ÔºÔ»Ô¼Ô½Ô¾Ô¿ÔÀÔÁÔÂÔÃÔÄÔÅÔÆÔÇÔÈÔÉÔÊÔËÔÌÔÍÔÎÔÏÔĞÔÑÔÒÔÓÔÔÔÕÔÖÔ×ÔØÔÙÔÚÔÛÔÜÔİÔŞÔßÔàÔáÔâÔãÔäÔåÔæÔçÔèÔéÔêÔëÔìÔíÔîÔïÔğÔñÔòÔóÔôÔõÔöÔ÷ÔøÔùÔúÔûÔüÔıÔşÕ¡Õ¢Õ£Õ¤Õ¥Õ¦Õ§Õ¨Õ©ÕªÕ«Õ¬Õ­Õ®Õ¯Õ°Õ±Õ²Õ³Õ´ÕµÕ¶Õ·Õ¸Õ¹ÕºÕ»Õ¼Õ½Õ¾Õ¿ÕÀÕÁÕÂÕÃÕÄÕÅÕÆÕÇÕÈÕÉÕÊÕËÕÌÕÍÕÎÕÏÕĞÕÑÕÒÕÓÕÔÕÕÕÖÕ×ÕØÕÙÕÚÕÛÕÜÕİÕŞÕßÕàÕáÕâÕãÕäÕåÕæÕçÕèÕéÕêÕëÕìÕíÕîÕïÕğÕñÕòÕóÕôÕõÕöÕ÷ÕøÕùÕúÕûÕüÕıÕşÖ¡Ö¢Ö£Ö¤Ö¥Ö¦Ö§Ö¨Ö©ÖªÖ«Ö¬Ö­Ö®Ö¯Ö°Ö±Ö²Ö³Ö´ÖµÖ¶Ö·Ö¸Ö¹ÖºÖ»Ö¼Ö½Ö¾Ö¿ÖÀÖÁÖÂÖÃÖÄÖÅÖÆÖÇÖÈÖÉÖÊÖËÖÌÖÍÖÎÖÏÖĞÖÑÖÒÖÓÖÔÖÕÖÖÖ×ÖØÖÙÖÚÖÛÖÜÖİÖŞÖßÖàÖáÖâÖãÖäÖåÖæÖçÖèÖéÖêÖëÖìÖíÖîÖïÖğÖñÖòÖóÖôÖõÖöÖ÷ÖøÖùÖúÖûÖüÖıÖş×¡×¢×£×¤×¥×¦×§×¨×©×ª×«×¬×­×®×¯×°×±×²×³×´×µ×¶×·×¸×¹×º×»×¼×½×¾×¿×À×Á×Â×Ã×Ä×Å×Æ×Ç×È×É×Ê×Ë×Ì×Í×Î×Ï×Ğ×Ñ×Ò×Ó×Ô×Õ×Ö×××Ø×Ù×Ú×Û×Ü×İ×Ş×ß×à×á×â×ã×ä×å×æ×ç×è×é×ê×ë×ì×í×î×ï×ğ×ñ×ò×ó×ô×õ×ö×÷×ø×ù';
}

function traditional () {
	return '°¡°¢°£°¤°¥°¦°§°}°©Ì@°«°¬µKÛ°¯°°°±°²°³°´°µ°¶°·°¸°¹°º°»°¼°½°¾ÂOÒ\°ÁŠW°Ã°Ä°Å°Æ°Ç°È°É°Ê°Ë°Ì°Í°Î°Ï°Ğ°Ñ°Ò‰Î°ÔÁT°Ö°×°Ø°Ù”[°Û”¡°İ°Ş°ß°à°á°â°ãîC°å°æ°ç°è°é°ê°ëŞk½O°îÍ°ğ°ñ°ò½‰°ô°õ°öæ^°øÖr°ú°û°ü°ı„ƒ±¡±¢±£±¤ï–Œš±§ˆó±©±ªõU±¬±­±®±¯±°±±İ…±³Øä^±¶ªN‚ä‘v±º±»±¼±½±¾±¿±À¿‡±Â±Ã±Ä±Å±Æ±Ç±È±É¹P±Ë±Ì±Í±Î®…”À±ÑÅ±Ó±Ôé]±Ö±×±Ø±Ù±Ú±Û±Ü±İ±Şß…¾ÙH±â±ã×ƒ±å±æŞqŞp±é˜Ë±ë±ì±íü‚±ï„e°T±ò±ólIÙe”P±ø±ù±ú±û±üï±ş²¡K²£²¤²¥“ÜÀ²¨²©²ª²«ãK²­²®²¯²°²±²²²³²´ñg²¶ÊN²¸Ña²º²»²¼²½²¾²¿²À²Á²Â²Ã²Ä²ÅØ”²Ç²È²É²Ê²Ë²Ì²Í…¢ĞQšˆ‘M‘K NÉnÅ“‚}œæ²Ø²Ù²Ú²Û²Ü²İú²ß‚ÈƒÔœyŒÓ²ä²å²æ²ç²è²é²ê²ë²ì²í²îÔŒ²ğ²ñ²ò”v“½Ïsğ’×‹ÀpçP®bêUî²ı²şˆö‡L³£éLƒ”ÄcS³¨•³³ª³«³¬³­ân³¯³°³±³²³³³´Ü‡³¶³·³¸Ø³º³»³¼³½‰m³¿³À³Áê³ÃÒr“Î·Q³Ç³È³É³Ê³Ë³Ì‘Í³ÎÕ\³Ğ³ÑòG³Ó³Ô°V³Ö³×³Øßt³ÚñYuıX³Ş³ß³à³á³âŸë³ä›_Ïx³çŒ™³é³ê® ÜP³í³î»I³ğ¾I³òáh³ô³õ³ö™»N³ùäzër³ü³ı³şµAƒ¦´£´¤Ó|Ì´§´¨´©´ª‚÷´¬´­´®¯´°´±´²êJ„“´µ´¶´·åN´¹´º´»´¼´½´¾¼ƒ´À´Á¾b´Ã´Ä´Å´ÆŞo´È´ÉÔ~´Ë´ÌÙn´ÎÂ”Ê[‡è´ÒÄ…²œ´Ö´×´Ø´ÙÜf´Û¸Z´İ´Ş´ß´à´á´â´ã´ä´å´æ´ç´è´é´ê´ë´ìåe´îß_´ğ´ñ´ò´ó´ô´õ´ö´÷§´ù´úÙJ´ü´ı´şµ¡µ¢“úµ¤†Îà“ÛÄ‘µ©µªµ«‘„µ­ÕQ—µ°®”“õühÊ™nµ¶“vµ¸µ¹u¶\Œ§µ½µ¾µ¿µÀ±IµÂµÃµÄµÅŸôµÇµÈµÉµÊà‡µÌµÍµÎµÏ”³µÑµÒœìµÔµÕµÖµ×µØµÙµÚµÛµÜßf¾†îµàµáµâücµäµå‰|ëŠµèµéµêµëµìÕµîµïµğµñµòµóµôµõáÕ{µøµùµúµûµşÕ™¯B¶¡¶¢¶£á”í”¶¦åV¶¨Ó†G–|¶¬¶­¶®„Ó—¶±¶²ƒö¶´¶µ¶¶ôY¶¸¶¹¶º¶»¶¼¶½¶¾ Ùªš×x¶Â¶ÃÙ€¶Ååƒ¶Ç¶È¶É¶Ê¶Ë¶Ìå‘¶Î”à¾„¶Ñƒ¶ê Œ¦¶Õ‡¶×¶ØîD¶Úâg¶Ü¶İ¶Ş¶ß¶àŠZ¶â¶ã¶ä¶å¶æ¶ç¶è‰™¶ê¶ëùZ¶íî~Ó¶ğº¶ò¶ó¶ô¶õğI¶÷¶øƒº¶ú –ğD¶ı¶şÙE°lÁP·¤·¥·¦éy·¨¬m·ª·«·¬·­·®µ\âC·±·²Ÿ©·´·µ¹ Øœ·¸ïˆ·º·»·¼·½·¾·¿·À·Á·ÂÔL¼·Å·Æ·Ç·Èïw·Ê·ËÕu·Í·ÎU·ĞÙM·Ò·Ó·Ô·Õ·Ö¼Š‰·Ù·Ú·ÛŠ^·İ·Ş‘¼SØS·â—÷·ä·åähïL¯‚·é·êñT¿pÖS·îøP·ğ·ñ·ò·óÄw·õ·ö·÷İ—·ù·ú·û·ü·ı·ş¸¡¸¢¸£¸¤¸¥¸¦“áİo¸©¸ª¸«¸¬¸­¸®¸¯¸°¸±¸²ÙxÑ}¸µ¸¶¸·¸¸¸¹Ø“¸»Ó‡¸½‹D¿`¸À¸Á¸ÂÔ“¸Ä¸Åâ}Éw¸ÈÖ¸Ê—U¸Ì¸Í¸ÎÚs¸Ğ¶’¸ÒÚMŒù„‚ä“¸×¸Ø¾V¸Û¸Ü¸İÅV¸ß¸à¸á¸â¸ãæ€¸å¸æ¸ç¸è”R¸êø¸ì¸í¸î¸ï¸ğ¸ñ¸òéw¸ôãt‚€¸÷½o¸ù¸ú¸û¸ü¸ı¸ş¹¡¹¢¹£¹¤¹¥¹¦¹§ı¹©¹ª¹«Œm¹­ì–¹¯¹°Ø•¹²âh¹´œÏ¹¶¹·¹¸˜‹Ù‰ò¹¼¹½¹¾¹¿¹À¹Á¹Â¹Ã¹Ä¹ÅĞM¹Ç¹È¹É¹Êî™¹Ì¹Í¹Î¹Ï„¹Ñ¹Ò¹Ó¹Ô¹Õ¹Ö¹×êP¹Ù¹ÚÓ^¹Üğ^¹Ş‘T¹àØ¹âV¹ä¹åÒ¹çÎùšwı”é|Ü‰¹íÔ¹ï¹ğ™™¹òÙF„£İL¹÷å¹ù‡ø¹û¹üß^¹şº¡º¢º£º¤º¥º¦ñ”º¨º©ºªínº¬º­º®º¯º°º±º²º³º´ºµº¶º·º¸º¹hº»º¼º½º¾º¿ºÀºÁºÂºÃºÄÌ–ºÆºÇºÈºÉºÊºËºÌºÍºÎºÏºĞºÑéuºÓºÔºÕºÖúQÙRºÙºÚºÛºÜºİºŞºßºà™MºâºãŞZºåºæºçø™ºéºêºë¼tºíºîºïºğºñºòááºôºõºöº÷‰Øºùºúºûºüºıºş»¡»¢»£×o»¥œû‘ô»¨‡WÈA»«»¬®‹„»¯Ô’»±»²‘Ñ»´‰Äšg­h»¸ß€¾“Q»¼†¾¯ˆ»¿Ÿ¨œo»Â»Ã»Ä»ÅüS»Ç»È»É»Ê»Ë»Ì»Í»Î»Ï»ĞÖe»Ò“]İx»Õ»Ö»×»Øš§»Ú»Û»Ü»İ»ŞÙV·x•ş Z¡ÖMÕdÀLÈ»è»é»êœ†»ì»í»îâ·»ğ«@»ò»ó»ôØ›µœ“ô»ø»ù™C»û»ü·e»ş¼¡ğ‡¼£¼¤×Iëu¼§¿ƒ¾ƒ¼ª˜O¼¬İ‹¼®¼¯¼°¼±¼²¼³¼´¼µ¼‰”D×¼¹¼ºËE¼¼¼½¼¾¼¿¼À„©¼Âú¼Ä¼ÅÓ‹Ó›¼È¼ÉëH¼ËÀ^¼o¼Î¼ÏŠA¼Ñ¼Ò¼ÓÇvîaÙZ¼×â›¼Ù¼Úƒr¼Üñ{¼Şš±OˆÔ¼â¹{ég¼å¼æ¼çÆD¼é¾}ÀO™z¼í‰Aû|’ş“ìº†ƒ€¼ôœpË]™‘èbÛ`ÙvÒŠæI¼ı¼ş½¡Å„¦ğTuR¾½¨½©½ªŒ¢{½­½®ÊY˜ªª„Öv½³áu½µ½¶½·½¸½¹Äz½»½¼²òœ‹É½À”‡ãq³CƒeÄ_½Æ½ÇïœÀU½g½Ë½Ì½ÍŞIİ^½Ğ½Ñ½Ò½Ó½Ô·M½ÖëA½Ø½Ù¹Ço¾¦¾§öL¾©ó@¾«¾¬½›¾®¾¯¾°îiìo¾³¾´çR½¯d¾¸¾¹¸‚œQ¾¼¾½¾¾¾¿¼m¾Á¾Â¾Ã¾Ä¾Å¾Æı¾ÈÅf¾Ê¾Ë¾Ì¾Í¾Î¾Ï¾Ğ¾Ñ¾Ò¾Óñx¾Õ¾Ö¾×¾ØÅe¾Ú¾Û¾Ü“ş¾Ş¾ß¾à¾áä¾ã¾ä‘Ö¾æ„¡¾èùN¾ê¾ë¾ì¾í½¾ï¾ğ¾ñ¾ò¾ó¾ô½Û‚Ü½İ½Ş½ß½Y½â½ã½ä½å½æ½ç½è½é½êÕ]ŒÃ½í½î½ï½ğ½ñ½ò½ó¾oå\ƒHÖ”ßM½ù•x½û½ü a½ş±M„ÅÇG¾¤ÓX›QÔE½^¾ù¾úâxÜŠ¾ı¾ş¿¡¿¢¿£¿¤òE¿¦¿§¿¨¿©é_¿«¿¬„P¿®¿¯¿°¿±¿²¿³¿´¿µ¿¶¿·¿¸¿¹¿º¿»¿¼¿½¿¾¿¿¿À¿Á¿Â¿Ã¿Äîw¿Æš¤¿È¿É¿Ê¿Ë¿Ì¿ÍÕn¿Ï¿Ğ‰¨‘©¿Ó¿Ô¿Õ¿Ö¿×¿Ø“¸¿Ú¿Û¿Ü¿İ¿Ş¿ß¿à¿áìÑÕF¿å¿æ¿ç¿è‰K¿êƒ~¿ìŒ’¿î¿ï¿ğ¿ñ¿òµV¿ô•ç›rÌ¿øh¸Q¿û¿ü¿ı¿şğÀ¢¢À¤À¥À¦À§À¨”UÀªéŸÀ¬À­À®ÏÅDÀ±À²ÈRíÙ‡Ë{À·™Ú”r»@ê@Ìm‘×”ˆÓ[‘ĞÀ| €EÀÅÀÆÀÇÀÈÀÉÀÊÀË“Æ„ÚÀÎÀÏÀĞÀÑÀÒÀÓ³ÀÕ˜·À×èDÀÙÀÚÀÛÀÜ‰¾ÀŞÀßîœIÀâÀãÀäÀåÀæÀçÀè»hÀêëxÀìÀíÀîÑYõ¶YÀòÀóÀôÀõû…–„îµ[švÀûÀüÀıÀşÁ¡Á¢Á£rë`Á¦Á§Á¨‚zÂ“ÉßBç Á®‘ziºŸ”¿Ä˜æœ‘ÙŸ’¾š¼Z›öÁºÁ»Á¼ƒÉİvÁ¿ÁÀÁÁÕÁÃÁÄÁÅ¯ŸÁÇÁÈß|ÁÊÁËÁÌç‚ÁÎÁÏÁĞÁÑÁÒÁÓ«CÁÕÁÖÁ×ÁØÅRà÷[ÁÜ„CÙUÁßÁàÁáÁâÁãıgâÁæÁçœRì`ÁêXîIÁíÁîÁïÁğÁñÁòğsÁô„¢ÁöÁ÷ÁøÁùıˆÃ@‡µ»\ÁşÂ¡‰Å”në]˜ÇŠä“§ºtÂ©ÂªÌJ±RïB] t“ïûuÌ”ô”Â´ÂµÂ¶Â·ÙTÂ¹Âºµ“ä›ê‘Â¾óH…ÎäX‚HÂÃÂÄŒÒ¿|‘]ÂÈÂÉÂÊV¾Gn”Œ\´ÂÑyÂÓÂÔ’àİ†‚öœS¾]Õ“Ì}ÂİÁ_ß‰èŒ»jò…ÂãÂäÂåñ˜½j‹ŒÂé¬”´aÎ›ñRÁRÂï†áÂñÙIûœÙuß~Ã}²mğzĞUMÂûÂüÂıÂşÖ™Ã¢Ã£Ã¤Ã¥Ã¦Ã§ØˆÃ©å^Ã«Ã¬ãTÃ®Ã¯Ã°Ã±Ã²ÙQ÷áÃµÃ¶Ã·Ã¸üqÃº›]Ã¼Ã½æVÃ¿ÃÀÃÁÃÂÃÃÃÄéT‚ƒÃÈÃÉÃÊÃËåiÃÍ‰ôÃÏÃĞÃÑÃÒÃÓÃÔÖi›Ã×ÃØÒ’ÃÚÃÛÃÜƒçÃŞÃß¾dÃáÃâÃãÃä¾’ÃæÃçÃèÃéÃêÃëÃìRÃîÃïœçÃñÃòÃóÃô‘‘é}Ã÷ÃøøQã‘ÃûÃüÖ‡ÃşÄ¡Ä¢Ä£Ä¤Ä¥Ä¦Ä§Ä¨Ä©ÄªÄ«Ä¬Ä­Ä®Ä¯Ä°Ö\Ä²Ä³Ä´Äµ®€Ä·Ä¸Ä¹ÄºÄ»Ä¼Ä½Ä¾Ä¿ÄÀÄÁÄÂÄÃÄÄÄÅâcÄÇÄÈ¼{ÄÊÄËÄÌÄÍÄÎÄÏÄĞëyÄÒ“ÏÄXÀô[Ä×ÄØğHƒÈÄÛÄÜÄİÄŞÄßÄàÄá”MÄãÄäÄÄæÄçÄèÄéÄêÄë”f“ÓÄîÄïá„øBÄòÄóÂ™Äõımè‡æ‡ÄùÄú™ªŸÄıå¸”QôÅ£Å¤âo¼~Ä“âŞrÅªÅ«Å¬Å­Å®Å¯Å°¯‘Å²Å³Å´ÖZÅ¶šWútšªÅº‡IÅ¼aÅ¾Å¿ÅÀÅÁÅÂÅÃÅÄÅÅÅÆÅÇÅÈÅÉÅÊÅË±PÅÍÅÎÅÏÅĞÅÑÅÒı‹ÅÔÅÕÅÖÅ×ÅØÅÙÅÚÅÛÅÜÅİÅŞÅßÅàÅáÙrÅãÅäÅåÅæ‡ŠÅèÅéÅêÅëÅìÅíÅîÅïÅğÅñÅòÅóùiÅõÅöÅ÷ÅøÅùÅúÅûÅüÅıÅşÆ¡Æ¢Æ£Æ¤Æ¥Æ¦Æ§Æ¨Æ©ÆªÆ«Æ¬ò_ïhÆ¯Æ°Æ±Æ²Æ³Æ´îlØšÆ·Æ¸Æ¹ÆºÌOÆ¼Æ½‘{Æ¿ÔuÆÁÆÂŠîHÆÅÆÆÆÇÆÈÆÉÆÊ“ääÆÍÆÎÆÏÆĞÆÑÆÒ˜ãÆÔÆÕÆÖ×VÆØÆÙÆÚÆÛ—«ÆİÆŞÆßœDÆáÆâÆãÆäÆåÆæÆçÆèÆéÄšıRÆìÆíÆîòTÆğØMÆòÆó†™ÆõÆöÆ÷šâÆù—‰ÆûÆüÓ™ÆşÇ¢ ¿’LâTãUÇ§ßwºÇªÖtÇ¬Ç­åXãQÇ°“Ç²œ\×l‰qÇ¶Ç·Ç¸˜Œ†ÜÇ»Ç¼ ËNŠ“ŒÇÁæ@ÇÃÇÄ˜òÇÆ†ÌƒSÇÉÇÊÇËÂNÇÍÇÎ¸[ÇĞÇÑÇÒÇÓ¸`šJÇÖÓHÇØÇÙÇÚÇÛÇÜÇİŒ‹ÇßÇàİpšäƒAÇäÇåÇæÇçÇèÇéí•Õˆ‘c­‚¸FÇïÇğÇñÇòÇóÇôÇõÇöÚ……^ÇùÇúÜ|ÇüòŒÇşÈ¡È¢ıxÈ¤È¥È¦ïE™àÈ©ÈªÈ«È¬È­È®È¯„ñÈ±È²È³…sùoÈ¶´_È¸È¹ÈºÈ»È¼È½È¾È¿ÈÀÈÁÈÂ×Œğˆ”_À@ÈÇŸáÈÉÈÊÈËÈÌígÈÎÕJÈĞÈÑ¼xÈÓÈÔÈÕÈÖÈ×ÈØ˜sÈÚÈÛÈÜÈİ½qÈßÈàÈáÈâÈãÈäÈåÈæÈçÈèÈéÈêÈëÈìÜ›ÈîÈïÈğäJéc™ÈôÈõÈö¢Ë_ÈùöwÈûÙÈıÈı‚ãÉ¢É£É¤†ÊÉ¦ò}’ßÉ©ÉªÉ«­É­É®É¯É°š¢É²É³¼†ÉµÉ¶É·ºY•ñÉºÉ»É¼É½„hÉ¿ÉÀéWê„ÉÃÙ ÉÅÉÆÉÇÉÈ¿˜ÉÊ‚ûÉÌÙpÉÎÉÏÉĞÉÑÉÒÉÓÉÔŸıÉÖÉ×ÉØÉÙÉÚÉÛ½BÉİÙdÉßÉàÉáÉâ”zÉä‘ØÉæÉçÔOÉéÉêÉëÉìÉíÉîÉï¼ÉñÉòŒ‹ğÉõÄIÉ÷BÂ•ÉúÉûÉüÉıÀKÊ¡Ê¢Ê£„ÙÂ}ŸÊ§ª{Ê©ñÔŠŒÆÊ­Ê®Ê¯Ê°•rÊ²Ê³ÎgŒ×RÊ·Ê¸Ê¹Êºñ‚Ê¼Ê½Ê¾Ê¿ÊÀÊÁÊÂÊÃÊÄÊÅ„İÊÇÊÈÊÉßmÊËÊÌáŒï—ÊÏÊĞÊÑÊÒÒ•Ô‡ÊÕÊÖÊ×ÊØ‰ÛÊÚÊÛÊÜÊİ«FÊß˜ĞÊáÊâÊãİ”ÊåÊæÊçÊè•øÚHÊëÊìÊíÊîÊïÊğÊñÊòÊóŒÙĞgÊö˜äÊøÊùØQÊûÊü”µÊşË¡Ë¢Ë£Ë¤Ë¥Ë¦›Ë¨Ë©ËªëpË¬ÕlË®Ë¯¶Ë±Ë²í˜Ë´Õf´TË· qË¹ËºË»Ë¼Ë½Ë¾½zËÀËÁËÂËÃËÄËÅËÆï•ËÈËÉÂ–‘ZíËÍËÎÔAÕbËÑËÒ”\ËÔÌKËÖË×ËØËÙËÚËÛËÜËİËŞÔVÃCËáËâËãëmËåëS½—ËèËéšqËëËìËíËîŒO“p¹SËòËóËô¿s¬Ë÷æiËùËúËûËüËıËş«H“éÌ£Ì¤Ì¥Ì¦”EÌ¨Ì©ÌªÌ«‘BÌ­Ì®”‚Ø°c©‰¯Ì´ÌµÌ¶×TÕ„Ì¹ÌºÌ»Ì¼Ì½šUÌ¿œ«ÌÁÌÂÌÃÌÄÌÅÌÆÌÇÌÈÌÉÌÊÌË CÌÍıÌÏ¿lÌÑÌÒÌÓÌÔÌÕÓ‘Ì×ÌØÌÙòvÌÛÖ`ÌİÌŞÌßäRÌáî}ÌãÌäówÌæÌçÌèÌéÌêŒÏÌìÌíÌîÌïÌğÌñÌòÌóÌô—lÌöÌ÷ÌøÙNèFÌûdÂ ŸNÍ¡Í¢Í£Í¤Í¥Í¦Í§Í¨Í©ÍªÍ«Í¬ã~Í®Í¯Í°Í±Í²½yÍ´ÍµÍ¶î^Í¸Í¹¶dÍ»ˆDÍ½Í¾‰TÍÀÍÁÍÂÍÃÍÄˆFÍÆîjÍÈÍ‘ÍÊÍËÍÌÍÍÍÎÍÏÍĞÃ“ørÍÓñWñ„™EÍ×ÍØÍÙÍÚÍÛÍÜ¸DÍŞÍßÒmÍáÍâÍã³ÍæîBÍèÍéÍêÍëÍìÍíÍîÍïÍğÍñÈfÍóÍôÍõÍöÍ÷¾WÍùÍúÍûÍüÍıÍşÎ¡Î¢Î£ífß`Î¦‡úÎ¨Î© ‘H¾SÈ”Î®Î¯‚¥ƒ^Î²¾•Î´ÎµÎ¶Î·Î¸Î¹ÎºÎ»Î¼Ö^Î¾Î¿ĞlÎÁœØÎÃÎÄÂ„¼yÎÇ·€ÎÉ†–ÎËÎÌ®Y“ëÎœu¸CÎÒÎÓÅPÎÕÎÖÎ×†èæuõ›@Õ_ÎİŸoÊÎàÎá…ÇÎãÎäÎåÎæÎçÎèÎéÎê‰]ÎììFÎîÎïÎğ„ÕÎòÕ`ÎôÎõÎöÎ÷ÎøÎùÎúÎûÎüåa ŞÏ¡Ï¢Ï£Ï¤Ï¥Ï¦Ï§Ï¨Ï©ÏªÏ«Ï¬Ï­ÒuÏ¯Á•Ï±Ï²ãŠÏ´ÏµÏ¶‘ò¼šÏ¹ÎrÏ»Ï¼İ Ï¾{‚bªMÏÂBÏÄ‡˜ÏÆåvÏÈÏÉõrÀwûyÙtã•ÏÏéeÏÑÏÒÏÓï@ëU¬F«I¿hÏÙğWÁw‘—ÏİÏŞ¾€Ïàûè‚ÏãÏäÏåÏæàlÏèÏéÔ”Ïëí‘Ïíí—ÏïÏğÏñÏòÏóÊ’ÏõÏöÏ÷Ïø‡ÌäNÏûÏüÏı•ÔĞ¡Ğ¢Ğ£Ğ¤‡[Ğ¦Ğ§Ğ¨Ğ©ĞªÏĞ¬…f’¶”yĞ°Ğ±Ã{ÖCŒ‘ĞµĞ¶Ğ·Ğ¸Ğ¹aÖxĞ¼Ğ½Ğ¾ä\ĞÀĞÁĞÂĞÃĞÄĞÅá…ĞÇĞÈĞÉĞÊÅdĞÌĞÍĞÎĞÏĞĞĞÑĞÒĞÓĞÔĞÕĞÖĞ×ĞØĞÙ›°ĞÛĞÜĞİĞŞĞßĞàĞáçnĞãĞäÀCĞæĞçĞèÌ“‡uíšĞìÔSĞîĞï”¢ĞñĞòĞóĞôĞõĞö¾wÀmÜĞúĞû‘ÒĞıĞşßx°_Ñ£½kÑ¥Ñ¦ŒWÑ¨Ñ©Ñª„ìÑ¬Ñ­Ñ®ÔƒŒ¤ñZÑ²Ñ³Ñ´Ó–ÓßdÑ¸‰ºÑºøfø†Ñ½Ñ¾Ñ¿ÑÀÑÁÑÂÑÃÑÄÑÅ†¡†Ó ÑÉÑÊéŸŸÑÍû}‡ÀÑĞÑÑÑÒÑÓÑÔî†éÑ×ÑØÑÙÑÚÑÛÑÜÑİØWÑßÑà…’³ÑãÑä©ÑæÑçÖVòÑêÑëø„Ñí—î“PÑğ¯ƒÑòÑóê–ÑõÑö°WğB˜ÓÑúÑûÑüÑı¬“uˆòßb¸GÖ{Ò¦Ò§Ò¨ËÒªÒ«Ò¬Ò­Ò® ”Ò°Ò±Ò²í“Ò´˜IÈ~Ò·Ò¸Ò¹ÒºÒ»Ò¼átÒ¾ãÒÀÒÁÒÂîUÒÄßzÒÆƒxÒÈÒÉÒÊÒËÒÌ¤ÒÎÏÒĞÒÑÒÒÒÓÒÔË‡ÒÖÒ×ÒØÒÙƒ|ÒÛÒÜÒİÒŞÒßÒàÒáÒâÒã‘›ÁxÒæÒçÔ„×hÕx×g®ÒíÒîÀ[ÒğÊaÒòÒóÒôêÒöÒ÷ãyÒùÒúï‹ÒüÒıë[Ó¡Ó¢™Ñ‹ëú—‘ªÀt¬“Î IŸÉÏ‰Ó­ÚAÓ¯Ó°·fÓ²Ó³†Ñ“í‚òÓ·°bÓ¹ÓºÛxÓ¼ÔÓ¾œ¥ÓÀÓÁÓÂÓÃÓÄƒÓÆ‘nÓÈÓÉà]â™ªqÓÍß[ÓÏÓĞÓÑÓÒÓÓÓÔÕTÓÖÓ×ÓØÓÙÓÚÓÛÓÜÓİÓŞİ›ÓàÓáÓâô~ÓäÓåOÓçÓèŠÊÓêÅcZÓíÓîÕZÓğÓñÓòÓóÓô»nÓöÓ÷Óø¶RÓúÓûªzÓı×uÔ¡Ô¢Ô£îAÔ¥ñSøxœYÔ©ÔªÔ«Ô¬Ô­Ô®Ş@ˆ@†TˆAÔ³Ô´¾‰ßhÔ·îŠÔ¹ÔºÔ»¼sÔ½ÜSè€[»›ÔÂ‚é†ÔÅë…ày„òëEÔÊß\ÌNáj•íÔĞÔÑÔÒësÔÔÔÕÄÔ×İdÔÙÔÚÔÛ”€•ºÙÚEóvÔáÔâÔãèÔå——ÔçÔèÔéÔêÔëÔìÔí¸^ÔïØŸ“ñ„tÉÙ\ÔõÔöÔ÷ÔøÙ›¼™ÔûÔü„ÜˆåélÕ£–ÅÕ¥Õ¦Õ§Õ¨ÔpÕªıSÕ¬Õ­‚ùÕ¯Õ°šÖÕ²Õ³Õ´±K”ØİšäÕ¹Õº—£Õ¼‘ğÕ¾Õ¿¾`ÕÁÕÂÕÃÕÄˆÕÆqÕÈÕÉ¤Ù~ÕÌÃ›ÕÎÕÏÕĞÕÑÕÒÕÓÚwÕÕÕÖÕ×ÕØÕÙÕÚÕÛÕÜÏUŞHÕßæNÕáß@ÕãÕäÕåÕæÕçÕèÕéØ‘á˜‚ÉÕíÕîÔ\ÕğÕñæ‚ê‡Õô’ê± Õ÷ªb ÕúÕûÕüÕıÕş¬Ö¢à×CÖ¥Ö¦Ö§Ö¨Ö©ÖªÖ«Ö¬Ö­Ö®¿—ÂšÖ±Ö²Ö³ˆÌÖµÖ¶Ö·Ö¸Ö¹ÖºÖ»Ö¼¼ˆÖ¾“´”SÖÁÖÂÖÃÃÖÅÖÆÖÇÖÈÖÉÙ|ÖËÖÌœşÖÎÖÏÖĞÖÑÖÒçŠÖÔ½K·NÄ[ÖØÖÙĞ\ÖÛÖÜÖİÖŞÖaÖàİSÖâÖãÖä°™Öæ•ƒóEÖéÖêÖëÖìØiÖTÕDÖğÖñ TÖóÖô²š‡ÚÖ÷ÖøÖùÖúÖûÙAèTºB×¡×¢×£ñv×¥×¦×§Œ£´uŞD×«Ù×­˜¶ÇfÑbŠy×²‰Ñ î×µåF×·Ù˜‰‹¾YÕ×¼×½×¾×¿×À×Á×Â×Ã×ÄÖø×ÆáÆ×ÉÙY×Ë×Ì×Í×Î×Ï×Ğ×Ñ×Ò×Ó×Ôn×Ö×××ØÛ™×Ú¾C¿‚¿vàu×ß×à×á×â×ã×ä×å×æÔ{×è½Mè×ë×ì×í×î×ï×ğ×ñ×ò×ó×ô×õ×ö×÷×ø×ù';
}

function traditionalized (cc) {
	var str = '';
	
	var s = simplified();
	var t = traditional();
	
	for (var i = 0; i < cc.length; i++) {
		if (s.indexOf(cc.charAt(i)) != -1) {
			str += t.charAt(s.indexOf(cc.charAt(i)));
		}
		else {
			str += cc.charAt(i);
		}
	}
	return str;
}

function simplized (cc) {
	var str = '';
	
	var s = simplified();
	var t = traditional();
	
	for (var i = 0; i < cc.length; i++) {
		if (t.indexOf(cc.charAt(i)) != -1) {
			str += s.charAt(t.indexOf(cc.charAt(i)));
		}
		else {
			str += cc.charAt(i);
		}
	}
	return str;
}

// »ù±¾µÄ¼òÌå°æÎÄ¼ş
function getFlas () {
var list = [
		'components/ScrollBar/Scrollbar.fla',
		
		// 2011-12-03
		'role_msg/role_msg_see.fla',
		'role_msg/dujie.fla',
		'server_war/serverwar_record.fla',
		'server_war/serverwar_signup.fla',
		'achievement/achievement.fla',
		'roll_cake/roll_cake.fla',
		'server_war/serverwar_cup.fla',
		'achievement/achievement_complete.fla',
		
		// 2011-11-18
		
		'server_war/server_war.fla',
		'friend/friendMessage/friend_message.fla',
		
		// 2011-10-20
		
		'setting/consume_alert_setting.fla',
		'hero_mission/mission/hero_mission.fla',
		'hero_mission/practice/hero_practice.fla',
		
		// 2011-10-12
		
		'drama_playback/drama_playback.fla',
		'seal_soul/seal_soul.fla',
		'tower/tower.fla',
		'send_flower/send_flower.fla',
		'drama/DiYiJuan.fla',
		'drama/HaoTianTa.fla',
		'drama/KongTongYin.fla',
		'drama/KunLunJing.fla',
		'inherit/inherit.fla',
		'role_msg/role_detail_info.fla',
		
		// 2011-08-31
		
		'delay_message/delay_message.fla',
		'world_boss_msg/faction_boss_select.fla',
		
		// 2011-08-17
		
		'faction/faction_seal/faction_seal.fla',
		'faction_window/faction_window.fla',
		'whats_new/whats_new.fla',
		'addons/progress_bar/progress_bar.fla',
		'faction_war/faction_war_sign_up.fla',
		'faction_war/faction_war_sign_up_list.fla',
		
		// 2011-08-05
		
		//'faction_war/sign_up_list.fla',
		
		// 2011-07-21
		
		'faction_war/faction_war_table.fla',
		'take_bible/take_bible_ready/take_bible_ready.fla',
		'take_bible/take_bible_road/take_bible_road.fla',
		
		// 2011-07-05
		
		'faction/faction_blessing/faction_blessing.fla',
		'faction_war/faction_map_msg.fla',
		'faction_war/faction_trophy.fla',
		//'faction_war/faction_war_select.fla',
		'world_boss_msg/world_boss_msg.fla',
		'war/war_sport_detail/war_sport_detail.fla',
		'war/war_sport_report/war_sport_report.fla',
		'immortality_msg/immortality_msg.fla',
		
		// 2011-07-04
		
		'activation_keys/activation_keys.fla',
		'activities/activities.fla',
		'activity_window/activity_window.fla',
		'addons/button_effect/button_effect.fla',
		'addons/notice/notice.fla',
		'other_head/other_head.fla',
		'addons/quest_goods/quest_goods.fla',
		'addons/standalone_war/standalone_war.fla',
		'addons/tip2/tip2.fla',
		'alert/alert.fla',
		//'boss/boss.fla', // R12 É¾³ı
		//'boss_war/boss_war.fla', // R12 É¾³ı
		'camp_war/camp_war.fla',
		'cartoon/dialogue.fla',
		'chat/chat.fla',
		'choose_camp/choose_camp.fla',
		'choose_mission/choose_mission.fla',
		/*
		'choose_roles/FeiYuBg.fla',
		'choose_roles/FeiYuNan.fla',
		'choose_roles/FeiYuNv.fla',
		'choose_roles/JianLingBg.fla',
		'choose_roles/JianLingNan.fla',
		'choose_roles/JianLingNv.fla',
		'choose_roles/WuShengBg.fla',
		'choose_roles/WuShengNan.fla',
		'choose_roles/WuShengNv.fla',
		*/
		'choose_roles/choose_roles.fla',
		//'common/common_mc.fla',
		//'common/¹¦ÄÜ¶¯»­/¹¦ÄÜ¶¯»­-ÑİÊ¾.fla',
		//'components/PanelBase/test.fla',
		//'components/StandaloneChat/standalone_chat.fla',
		//'cutover/MC_Cutover.fla',
		'daily_quest/daily_quest.fla',
		'deploy/deploy.fla',
		
		'drama/LianYaoTaDaZhan.fla',
		'drama/JiuJianXianYaoYuan.fla',
		'drama/MoShi.fla',
		'drama/NvTongDaoDi.fla',
		'drama/QiShu.fla',
		'drama/ShenChui.fla',
		'drama/ShenChuiBeiGuang.fla',
		'drama/ShiFangLingHun.fla',
		'drama/ShilipoShouyi.fla',
		'drama/ShouYaoHu.fla',
		//'drama/ShouZhua.fla', // R12 É¾³ı
		//'drama/VoidPlayer.fla',
		'drama/XuanHuoJian.fla',
		'drama/XuanHuoJianHuoYan.fla',
		'drama/YaoHuHuanShu.fla',
		'drama/ZhaoCaiFu.fla',
		'drama/drama.fla',
		
		'faction/join_faction/join_faction.fla',
		'faction/my_faction/my_faction.fla',
		'farm/farm.fla',
		'fate/fateBox/fate.fla',
		'fate/lodge/lodge.fla',
		'friend/Audience/audience.fla',
		'friend/friendChat/friend_chat.fla',
		'friend/friendList/friend_list.fla',
		'game_helper/game_helper.fla',
		'game_master/game_master.fla',
		'guide/guide.fla',
		//'horse_races/horse_races.fla',
		'init_loading/init_loading.fla',
		//'interaction/interaction.fla',
		'interaction/standalone.fla',
		'jindouyun/jindouyun.fla',
		'learn_stunt/cang_jing_ge/cang_jing_ge.fla',
		'learn_stunt/stunt/stunt.fla',
		//'login/index.fla',
		'login/login.fla',
		'login/login2.fla',
		'map/Map´úÂë½á¹¹/map-resources.fla',
		'map/Map´úÂë½á¹¹/map.fla',
		/*
		'map/Map´úÂë½á¹¹/¾çÇé±à¼­Æ÷.fla',
		'map/mission/°ïÅÉ/map_1.fla',
		'map/multi_mission/background.fla',
		'map/multi_mission/·âÉñÁê.fla',
		'map/multi_mission/»Ê¹¬.fla',
		'map/multi_mission/Á¶ÑıËş.fla',
		'map/multi_mission/ÂÒÔá¸Ú.fla',
		'map/multi_mission/ĞéÌìµî.fla',
		'map/multi_mission/ĞşÚ¤½ç.fla',
		'map/protal/portal1.fla',
		'map/protal/portal2.fla',
		'map/protal/portal3.fla',
		'map/protal/portal4.fla',
		'map/sport/1.fla',
		'map/sport/2.fla',
		'map/sport/3.fla',
		'map/town/GuDaoCheng/map.fla',
		'map/town/JiMo/map.fla',
		'map/town/JingCheng/map.fla',
		'map/town/KunLunCheng/map.fla',
		'map/town/LiShuShan/map.fla',
		'map/town/PengLaiDao/map.fla',
		'map/town/ShuShanCheng/map.fla',
		'map/town/SuZhou/map.fla',
		'map/town/XiaoYuCun/map.fla',
		'map/town/YuXuCheng/map.fla',
		//'map/ÊÓÆµ»º³å/shipin.fla',
		*/
		'mission_failed_tips/mission_failed_tips.fla',
		'mission_loading/mission_loading.fla',
		'mission_practice/mission_practice.fla',
		'mission_rank/mission_rank.fla',
		'multi_mission/multi_mission.fla',
		//'multi_mission/multi_mission_war.fla',
		'npc_dialog/npc_dialog.fla',
		'panel_loading/panel_loading.fla',
		'partners/partners.fla',
		//'practice/practice.fla', // R12 É¾³ı
		//'practice/practiceEffect - ¸±±¾.fla',
		//'practice/practiceEffect.fla', // R12 É¾³ı
		'prevent_indulge/prevent_indulge.fla',
		'process_tips/process_tips.fla',
		'quest/quest.fla',
		'ranking/ranking.fla',
		'vip/vip.fla',
		'refine/refine.fla',
		'research/research.fla',
		'role_msg/role_msg.fla',
		'role_msg/skill.fla',
		'role_msg/training.fla',
		/*
		'roles/effects/Defense.fla',
		'roles/effects/MonsterAttacked.fla',
		'roles/effects/Morale.fla',
		'roles/effects/PlayerAttacked.fla',
		'roles/effects/levelUp.fla',
		'roles/effects/quest.fla',
		'roles/effects/upgrade.fla',
		'roles/effects/yun2.fla',
		'roles/effects/µôÑª.fla',
		'roles/effects/·´»÷.fla',
		'roles/effects/¼ÓÑª.fla',
		'roles/effects/Éı¼¶¼ô²Ã ×îÖÕ.fla',
		'roles/effects/ÔÆ.fla',
		'roles/monsters/ÊÀ½çboss/´ı»ú/boss1.fla',
		'roles/monsters/ÊÀ½çboss/´ı»ú/boss2.fla',
		'roles/monsters/ÊÀ½çboss/´ı»ú/boss3.fla',
		'roles/monsters/ÊÀ½çboss/´ı»ú/boss4.fla',
		'roles/monsters/ÊÀ½çboss/ÅÜ/boss1.fla',
		'roles/monsters/ÊÀ½çboss/ÅÜ/boss2.fla',
		'roles/monsters/ÊÀ½çboss/ÅÜ/boss3.fla',
		'roles/monsters/Õ½³¡/BaiSeZhanXiong.fla',
		'roles/monsters/Õ½³¡/BaiWuChang.fla',
		'roles/monsters/Õ½³¡/BaoXiang.fla',
		'roles/monsters/Õ½³¡/BingJingShou.fla',
		'roles/monsters/Õ½³¡/Boss°×Ôó/BossBaiZe.fla',
		'roles/monsters/Õ½³¡/Boss³àÑ×ÊŞ/BossChiYanShou.fla',
		'roles/monsters/Õ½³¡/BossÇàÁú/BossQingLong.fla',
		'roles/monsters/Õ½³¡/BuKuai.fla',
		'roles/monsters/Õ½³¡/CaiShen.fla',
		'roles/monsters/Õ½³¡/ChangQiangJiaoYao.fla',
		'roles/monsters/Õ½³¡/ChenYu.fla',
		'roles/monsters/Õ½³¡/ChiGui.fla',
		'roles/monsters/Õ½³¡/ChiGuiWang.fla',
		'roles/monsters/Õ½³¡/ChiHuoXieZi.fla',
		'roles/monsters/Õ½³¡/ChiTouMan.fla',
		'roles/monsters/Õ½³¡/ChiYanShou.fla',
		'roles/monsters/Õ½³¡/CiBeiShanZhu.fla',
		'roles/monsters/Õ½³¡/DaNeiShiWei.fla',
		'roles/monsters/Õ½³¡/DaoDunJiaoYao.fla',
		'roles/monsters/Õ½³¡/DiHun.fla',
		'roles/monsters/Õ½³¡/DieJing.fla',
		'roles/monsters/Õ½³¡/DongShi.fla',
		'roles/monsters/Õ½³¡/DuTong.fla',
		'roles/monsters/Õ½³¡/DuanTouYao.fla',
		'roles/monsters/Õ½³¡/FeiTouMan.fla',
		'roles/monsters/Õ½³¡/FeiYi.fla',
		'roles/monsters/Õ½³¡/FengShenShouGuan.fla',
		'roles/monsters/Õ½³¡/FengXieShou.fla',
		'roles/monsters/Õ½³¡/GrassDemon.fla',
		'roles/monsters/Õ½³¡/GuiChengXiang.fla',
		'roles/monsters/Õ½³¡/HanYuJian.fla',
		'roles/monsters/Õ½³¡/HeiHuJing.fla',
		'roles/monsters/Õ½³¡/HeiWuChang.fla',
		'roles/monsters/Õ½³¡/HongSheYao.fla',
		'roles/monsters/Õ½³¡/HuSha.fla',
		'roles/monsters/Õ½³¡/HuXingJiuWei.fla',
		'roles/monsters/Õ½³¡/HuaYao.fla',
		'roles/monsters/Õ½³¡/HuangJinZhanXiong.fla',
		'roles/monsters/Õ½³¡/HuoGui.fla',
		'roles/monsters/Õ½³¡/HuoKui.fla',
		'roles/monsters/Õ½³¡/HuoLieNiao.fla',
		'roles/monsters/Õ½³¡/JiXiangRuYi.fla',
		'roles/monsters/Õ½³¡/JianHun.fla',
		'roles/monsters/Õ½³¡/JianPo.fla',
		'roles/monsters/Õ½³¡/JiangChen.fla',
		'roles/monsters/Õ½³¡/JiangShiJiangJun.fla',
		'roles/monsters/Õ½³¡/JinChan.fla',
		'roles/monsters/Õ½³¡/JinChiFengHuang.fla',
		'roles/monsters/Õ½³¡/JiuXianWeng.fla',
		'roles/monsters/Õ½³¡/JuChuiShuYao.fla',
		'roles/monsters/Õ½³¡/JuDunShuYao.fla',
		'roles/monsters/Õ½³¡/JuMang.fla',
		'roles/monsters/Õ½³¡/KuangBaoZhiZhu.fla',
		'roles/monsters/Õ½³¡/LanLingShiWei.fla',
		'roles/monsters/Õ½³¡/LeiShou.fla',
		'roles/monsters/Õ½³¡/LiChiLang.fla',
		'roles/monsters/Õ½³¡/LiYiWar.fla',
		'roles/monsters/Õ½³¡/LingHu.fla',
		'roles/monsters/Õ½³¡/LiuWeiHuoHu.fla',
		'roles/monsters/Õ½³¡/LiuWeiLingHu.fla',
		'roles/monsters/Õ½³¡/LuShiBingChan.fla',
		'roles/monsters/Õ½³¡/LuShiHuoChan.fla',
		'roles/monsters/Õ½³¡/LuoChaDaoSheng.fla',
		'roles/monsters/Õ½³¡/LuoChaJianShen.fla',
		'roles/monsters/Õ½³¡/LuoYan.fla',
		'roles/monsters/Õ½³¡/MengPo.fla',
		'roles/monsters/Õ½³¡/MoJian.fla',
		'roles/monsters/Õ½³¡/MoJiangWuLuo.fla',
		'roles/monsters/Õ½³¡/MoNvYeMei.fla',
		'roles/monsters/Õ½³¡/MoWangXingTian.fla',
		'roles/monsters/Õ½³¡/PuTongJiangShi.fla',
		'roles/monsters/Õ½³¡/QianNianShuYao.fla',
		'roles/monsters/Õ½³¡/QingHuoJiangShi.fla',
		'roles/monsters/Õ½³¡/QingZhuSheYao.fla',
		'roles/monsters/Õ½³¡/RenXingJiuWei.fla',
		'roles/monsters/Õ½³¡/RuYi.fla',
		'roles/monsters/Õ½³¡/SanXianBing.fla',
		'roles/monsters/Õ½³¡/SanXianJia.fla',
		'roles/monsters/Õ½³¡/SanXianYi.fla',
		'roles/monsters/Õ½³¡/ShaChong.fla',
		'roles/monsters/Õ½³¡/ShanZei.fla',
		'roles/monsters/Õ½³¡/SheYaoMengNan.fla',
		'roles/monsters/Õ½³¡/SheYaoNan.fla',
		'roles/monsters/Õ½³¡/ShiXueJian.fla',
		'roles/monsters/Õ½³¡/ShuangDaoXieZi.fla',
		'roles/monsters/Õ½³¡/ShuangTouShe.fla',
		'roles/monsters/Õ½³¡/TaiGuYuanJun.fla',
		'roles/monsters/Õ½³¡/TianBingShouWei.fla',
		'roles/monsters/Õ½³¡/TiaoTiaoTu.fla',
		'roles/monsters/Õ½³¡/TieJiaZhanXiong.fla',
		'roles/monsters/Õ½³¡/TreeDemon.fla',
		'roles/monsters/Õ½³¡/WanYaoHuang.fla',
		'roles/monsters/Õ½³¡/WildPig.fla',
		'roles/monsters/Õ½³¡/WolfDemon.fla',
		'roles/monsters/Õ½³¡/WolfDemonBoss.fla',
		'roles/monsters/Õ½³¡/WuCaiZhiZhu.fla',
		'roles/monsters/Õ½³¡/XiaBing.fla',
		'roles/monsters/Õ½³¡/XianBeiYinHun.fla',
		'roles/monsters/Õ½³¡/XianGuan.fla',
		'roles/monsters/Õ½³¡/XianRenZhang.fla',
		'roles/monsters/Õ½³¡/XiangLongShou.fla',
		'roles/monsters/Õ½³¡/XiaoBingJingShou.fla',
		'roles/monsters/Õ½³¡/XiaoFengHuang.fla',
		'roles/monsters/Õ½³¡/XieJian.fla',
		'roles/monsters/Õ½³¡/XieJiang.fla',
		'roles/monsters/Õ½³¡/XuanMingYaoZu.fla',
		'roles/monsters/Õ½³¡/XuanNiao.fla',
		'roles/monsters/Õ½³¡/XuanWuHuanShou.fla',
		'roles/monsters/Õ½³¡/XuanWuShenShou.fla',
		'roles/monsters/Õ½³¡/XueLangYao.fla',
		'roles/monsters/Õ½³¡/XueSeBianFu.fla',
		'roles/monsters/Õ½³¡/YaYi.fla',
		'roles/monsters/Õ½³¡/YanLangYao.fla',
		'roles/monsters/Õ½³¡/YaoGuJiaoYao.fla',
		'roles/monsters/Õ½³¡/YaoHuaTongLing.fla',
		'roles/monsters/Õ½³¡/YaoZhouShuShi.fla',
		'roles/monsters/Õ½³¡/YingLong.fla',
		'roles/monsters/Õ½³¡/YouGuei.fla',
		'roles/monsters/Õ½³¡/YuanBao.fla',
		'roles/monsters/Õ½³¡/ZhanHun.fla',
		'roles/monsters/Õ½³¡/ZhangMaZi.fla',
		'roles/monsters/Õ½³¡/ZhangMenYuanShi.fla',
		'roles/monsters/Õ½³¡/ZhiZhuJing.fla',
		'roles/monsters/Õ½³¡/ZhuHa.fla',
		'roles/monsters/Õ½³¡/ZiDianShe.fla',
		'roles/npcs/npc/npc-¾Æ½£ÏÉ/JiuJianXian.fla',
		'roles/npcs/npc/¾©³Ç- ¿ÍÕ»ÕÆ¹ñ-ÁøÈçÑÌ/LiuRuYan.fla',
		'roles/npcs/npc/¾©³Ç-Àî¸Õ/LiGangNPC.fla',
		'roles/npcs/npc/ËÕÖİ-²Ö¿â¹ÜÀíÔ±-ÂæÍÕ¸ç/LuoTuoGe.fla',
		'roles/npcs/npc/ËÕÖİ-µÀ¾ßÉÌÈË-Â½Îá/LuWu.fla',
		'roles/npcs/npc/ËÕÖİ-¿ÍÕ»ÕÆ¹ñ-ÌúËãÅÌ/TieSuanPan.fla',
		'roles/npcs/npc/ËÕÖİ-ÁÖÌìÄÏ/LinTianNan.fla',
		'roles/npcs/npc/Ğ¡Óæ´å-´å³¤/CunZhang.fla',
		'roles/npcs/npc/Ğ¡Óæ´å-¿ÍÕ»ÕÆ¹ñ-Àî´óÄï/LiDaNiang.fla',
		'roles/npcs/npc/Ğ¡Óæ´å-Ë®Áé/ShuiLing.fla',
		'roles/npcs/npc/Ğ¡Óæ´å-ÔÓ»õÉÌÈË-Ü¿Äï/YunNiang.fla',
		'roles/npcs/·ÇNPCÀàÎÄ×Ö/ÎÄ×ÖÎ»ÖÃ.fla',
		'roles/players/´«ËÍÃÅ/portal1.fla',
		'roles/players/´«ËÍÃÅ/portal2.fla',
		'roles/players/Õ½³¡/ChuChu.fla',
		'roles/players/Õ½³¡/FangShiNv.fla',
		'roles/players/Õ½³¡/FeiYuNan.fla',
		'roles/players/Õ½³¡/FeiYuNv.fla',
		'roles/players/Õ½³¡/JianLingNan.fla',
		'roles/players/Õ½³¡/JianLingNv.fla',
		'roles/players/Õ½³¡/JiangChen.fla',
		'roles/players/Õ½³¡/JinMingCheng.fla',
		'roles/players/Õ½³¡/NieXiaoQian.fla',
		'roles/players/Õ½³¡/NingCaiChen.fla',
		'roles/players/Õ½³¡/ShuShiNan.fla',
		'roles/players/Õ½³¡/WuShengNan.fla',
		'roles/players/Õ½³¡/WuShengNv.fla',
		'roles/players/Õ½³¡/XiaoXianTong.fla',
		'roles/portal/portal1.fla',
		'roles/portal/portal2.fla',
		'roles/portal/´«ËÍÊ¯.fla',
		'roles/stunts/AXiuLuoBaWangQuan.fla',
		'roles/stunts/BaiLianHengJiang.fla',
		'roles/stunts/BeiCi.fla',
		'roles/stunts/BengJing.fla',
		'roles/stunts/BingJingZhou.fla',
		'roles/stunts/ChangHongGuanRi.fla',
		'roles/stunts/ChuiMian.fla',
		'roles/stunts/DingShenZhou.fla',
		'roles/stunts/DuWu.fla',
		'roles/stunts/DuZhou.fla',
		'roles/stunts/ErDuanJi.fla',
		'roles/stunts/FeiYuJian.fla',
		'roles/stunts/FengZhou.fla',
		'roles/stunts/GuWuShu.fla',
		'roles/stunts/GuangMangWanZhang.fla',
		'roles/stunts/HuanMing.fla',
		'roles/stunts/HuanShu.fla',
		'roles/stunts/HuanYing.fla',
		'roles/stunts/HuiChun.fla',
		'roles/stunts/HunLuan.fla',
		'roles/stunts/HuoYanZhou.fla',
		'roles/stunts/JianLiuYun.fla',
		'roles/stunts/JingLeiShan.fla',
		'roles/stunts/JuHun.fla',
		'roles/stunts/JueDuiFangYu.fla',
		'roles/stunts/KuangBao.fla',
		'roles/stunts/LeiZhou.fla',
		'roles/stunts/LiZhiBaTi.fla',
		'roles/stunts/LingBao.fla',
		'roles/stunts/LuoXingShi.fla',
		'roles/stunts/MengJi.fla',
		'roles/stunts/MengPoTang.fla',
		'roles/stunts/NianYa.fla',
		'roles/stunts/QianBeiBuZui.fla',
		'roles/stunts/Reel.fla',
		'roles/stunts/SanMeiZhenHuo.fla',
		'roles/stunts/ShiFangJieSha.fla',
		'roles/stunts/SiYao.fla',
		'roles/stunts/TianJiangHengCai.fla',
		'roles/stunts/TianShiFuFa.fla',
		'roles/stunts/TianShuangQuan.fla',
		'roles/stunts/TianXuanWuYin.fla',
		'roles/stunts/TuZhou.fla',
		'roles/stunts/WuZhiBaTi.fla',
		'roles/stunts/XiXue.fla',
		'roles/stunts/XingYun.fla',
		'roles/stunts/XuanYinJian.fla',
		'roles/stunts/YeQiuQuan.fla',
		'roles/stunts/YinShen.fla',
		'roles/stunts/YingXi.fla',
		'roles/stunts/YingZhiBaTi.fla',
		'roles/stunts/YuJianShu.fla',
		'roles/stunts/ZhenSheShu.fla',
		'roles/stunts/ZhiLiao.fla',
		'roles/stunts/ZiBao.fla',
		'roles/swf×ÊÔ´Êä³ö/SWF.fla',
		*/
		'rune/rune.fla',
		'setting/setting.fla',
		'shop/shop.fla',
		//'sport/sport.fla',
		'strategy/strategy.fla',
		'subline/subline.fla',
		'super_sport/super_sport.fla',
		//'toolbar/objects.fla',
		'pack/pack.fla',
		'toolbar/toolbar.fla',
		'toolbar/toolbar_footer.fla',
		//'tools/Robot/loaderRobot.fla',
		//'tools/Robot/robot.fla',
		//'tools/µØÍ¼±à¼­Æ÷(ĞÂ)/mapEdit.fla',
		//'tools/Õ½³¡¶¯×÷±à¼­/¶¯×÷±à¼­.fla',
		'travel_event/travel_event.fla',
		'trigger_function/trigger_function.fla',
		'upgrade/upgrade.fla',
		'war/enter_war_effect.fla',
		//'war/test/drawRoundRect.fla',
		//'war/test/t.fla',
		'war/war_resources.fla',
		'war/war.fla',
		'war/multi_war.fla',
		'war/mini_faction_war.fla',
		'world/world.fla',
		'lucky_shop/lucky_shop.fla'
];

return list;
}