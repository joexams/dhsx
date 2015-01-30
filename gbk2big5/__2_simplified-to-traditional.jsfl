// http://www.actionscript.org/forums/showthread.php3?t=217810

// ���� client/ Ŀ䛣��ٿ�ؐ��client-tw/

// ��Ҫ���w���YԴ
// 1. �����_���ĈDƬ
// 2. ����ʾ
// 3. my_faction.fla" ����QQ" ��Ϊ "������Ⱥ"
// 4. ������������txt

// ��Ҫ�޸ĵĴ��a
// 1. a. chat�h����һ�_ʼ����ʾ��Ϣ
//    b. ȡ��ȫƽ̨����

var dirName = "file:///F|//dev/20111201/";
var logPath = "file:///F|//dev/gbk2big5/log/";

var flas = getFlas();

// ��Ҫת��Ϊ�������ļ�
// �����_���ĈDƬ
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
		'roles/effects/levelUp_����.fla',
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
	
	
	// ��ת��
	var value = tf.getTextString();
	fl.trace(value);
	value = traditionalized(value);
	value = value.replace(/��ֵ/g, "��ֵ");
	value = value.replace(/�ַ�/g, "��Ԫ");
	tf.setTextString(value);
}

/**
 * ����ת��
 */

function simplified () {
	return '�������������������������������������������������������������������°ðİŰưǰȰɰʰ˰̰ͰΰϰаѰҰӰ԰հְװذٰڰ۰ܰݰް߰������������������������������������������������������������������������������������������������������������±ñıűƱǱȱɱʱ˱̱ͱαϱбѱұӱԱձֱױرٱڱ۱ܱݱޱ߱������������������������������������������������������������������������������������������������������������²òĲŲƲǲȲɲʲ˲̲ͲβϲвѲҲӲԲղֲײزٲڲ۲ܲݲ޲߲������������������������������������������������������������������������������������������������������������³óĳųƳǳȳɳʳ˳̳ͳγϳгѳҳӳԳճֳ׳سٳڳ۳ܳݳ޳߳������������������������������������������������������������������������������������������������������������´ôĴŴƴǴȴɴʴ˴̴ʹδϴдѴҴӴԴմִ״شٴڴ۴ܴݴ޴ߴ������������������������������������������������������������������������������������������������������������µõĵŵƵǵȵɵʵ˵̵͵εϵеѵҵӵԵյֵ׵صٵڵ۵ܵݵ޵ߵ������������������������������������������������������������������������������������������������������������¶öĶŶƶǶȶɶʶ˶̶Ͷζ϶жѶҶӶԶնֶ׶ضٶڶ۶ܶݶ޶߶������������������������������������������������������������������������������������������������������������·÷ķŷƷǷȷɷʷ˷̷ͷηϷзѷҷӷԷշַ׷طٷڷ۷ܷݷ޷߷������������������������������������������������������������������������������������������������������������¸øĸŸƸǸȸɸʸ˸̸͸θϸиѸҸӸԸոָ׸ظٸڸ۸ܸݸ޸߸������������������������������������������������������������������������������������������������������������¹ùĹŹƹǹȹɹʹ˹̹͹ιϹйѹҹӹԹչֹ׹عٹڹ۹ܹݹ޹߹������������������������������������������������������������������������������������������������������������ºúĺźƺǺȺɺʺ˺̺ͺκϺкѺҺӺԺպֺ׺غٺںۺܺݺ޺ߺ������������������������������������������������������������������������������������������������������������»ûĻŻƻǻȻɻʻ˻̻ͻλϻлѻһӻԻջֻ׻ػٻڻۻܻݻ޻߻������������������������������������������������������������������������������������������������������������¼üļżƼǼȼɼʼ˼̼ͼμϼмѼҼӼԼռּ׼ؼټڼۼܼݼ޼߼������������������������������������������������������������������������������������������������������������½ýĽŽƽǽȽɽʽ˽̽ͽνϽнѽҽӽԽսֽ׽ؽٽھ����������������������������������������������������������¾þľžƾǾȾɾʾ˾̾;ξϾоѾҾӾԾվ־׾ؾپھ۾ܾݾ޾߾����������������������۽ܽݽ޽߽����������������������������������������������������������������������������������������������������������������������������������������¿ÿĿſƿǿȿɿʿ˿̿ͿοϿпѿҿӿԿտֿ׿ؿٿڿۿܿݿ޿߿���������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������������¡¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶·¸¹º»¼½¾¿������������������������������������������������������������������������������������������������������������������������������áâãäåæçèéêëìíîïðñòóôõö÷øùúûüýþÿ������������������������������������������������������������������������������������������������������������������������������ġĢģĤĥĦħĨĩĪīĬĭĮįİıĲĳĴĵĶķĸĹĺĻļĽľĿ������������������������������������������������������������������������������������������������������������������������������šŢţŤťŦŧŨũŪūŬŭŮůŰűŲųŴŵŶŷŸŹźŻżŽžſ������������������������������������������������������������������������������������������������������������������������������ơƢƣƤƥƦƧƨƩƪƫƬƭƮƯưƱƲƳƴƵƶƷƸƹƺƻƼƽƾƿ������������������������������������������������������������������������������������������������������������������������������ǢǣǤǥǦǧǨǩǪǫǬǭǮǯǰǱǲǳǴǵǶǷǸǹǺǻǼǽǾǿ������������������������������������������������������������������������������������������������������������������������������ȡȢȣȤȥȦȧȨȩȪȫȬȭȮȯȰȱȲȳȴȵȶȷȸȹȺȻȼȽȾȿ������������������������������������������������������������������������������������������������������������������������������ɡɢɣɤɥɦɧɨɩɪɫɬɭɮɯɰɱɲɳɴɵɶɷɸɹɺɻɼɽɾɿ������������������������������������������������������������������������������������������������������������������������������ʡʢʣʤʥʦʧʨʩʪʫʬʭʮʯʰʱʲʳʴʵʶʷʸʹʺʻʼʽʾʿ������������������������������������������������������������������������������������������������������������������������������ˡˢˣˤ˥˦˧˨˩˪˫ˬ˭ˮ˯˰˱˲˳˴˵˶˷˸˹˺˻˼˽˾˿������������������������������������������������������������������������������������������������������������������������������̴̵̶̷̸̡̢̧̨̣̤̥̦̩̪̫̬̭̮̯̰̱̲̳̹̺̻̼̽̾̿������������������������������������������������������������������������������������������������������������������������������ͣͤͥͦͧͨͩͪͫͬͭͮͯ͢͡ͰͱͲͳʹ͵Ͷͷ͸͹ͺͻͼͽ;Ϳ������������������������������������������������������������������������������������������������������������������������������Ρ΢ΣΤΥΦΧΨΩΪΫάέήίΰαβγδεζηθικλμνξο������������������������������������������������������������������������������������������������������������������������������ϡϢϣϤϥϦϧϨϩϪϫϬϭϮϯϰϱϲϳϴϵ϶ϷϸϹϺϻϼϽϾϿ������������������������������������������������������������������������������������������������������������������������������СТУФХЦЧШЩЪЫЬЭЮЯабвгдежзийклмноп������������������������������������������������������������������������������������������������������������������������������ѡѢѣѤѥѦѧѨѩѪѫѬѭѮѯѰѱѲѳѴѵѶѷѸѹѺѻѼѽѾѿ������������������������������������������������������������������������������������������������������������������������������ҡҢңҤҥҦҧҨҩҪҫҬҭҮүҰұҲҳҴҵҶҷҸҹҺһҼҽҾҿ������������������������������������������������������������������������������������������������������������������������������ӡӢӣӤӥӦӧӨөӪӫӬӭӮӯӰӱӲӳӴӵӶӷӸӹӺӻӼӽӾӿ������������������������������������������������������������������������������������������������������������������������������ԡԢԣԤԥԦԧԨԩԪԫԬԭԮԯ԰ԱԲԳԴԵԶԷԸԹԺԻԼԽԾԿ������������������������������������������������������������������������������������������������������������������������������աբգդեզէըթժիլխծկհձղճմյնշոչպջռսվտ������������������������������������������������������������������������������������������������������������������������������ְֱֲֳִֵֶַָֹֺֻּֽ֢֣֤֥֦֧֪֭֮֡֨֩֫֬֯־ֿ������������������������������������������������������������������������������������������������������������������������������סעףפץצקרשת׫׬׭׮ׯװױײ׳״׵׶׷׸׹׺׻׼׽׾׿��������������������������������������������������������������������������������������������������������������������';
}

function traditional () {
	return '���������������}���@�����K�۰��������������������������������O�\���W�ðİŰưǰȰɰʰ˰̰ͰΰϰаѰ҉ΰ��T�ְװذٔ[�۔��ݰް߰�����C���������k�O��Ͱ��򽉰������^���r����������������������󱩱��U������������݅��ؐ�^���N��v�����������������±ñıűƱǱȱɹP�˱̱ͱή����юűӱ��]�ֱױرٱڱ۱ܱݱ�߅���H���׃����q�p��˱�������e�T���l�I�e�P��������������K�������������������K�����������������g���N���a�����������������²òĲ�ؔ�ǲȲɲʲ˲̲ͅ��Q���M�K�N�nœ�}��زٲڲ۲ܲݎ��߂ȃԜy�Ӳ�����������Ԍ����v���s�׋�p�P�b�U������L���L���c�S�������������n������������܇�������س��������m������ꐳ��r�ηQ�ǳȳɳʳ˳̑ͳ��\�г��G�ӳ԰V�ֳ׳��t���Y�u�X�޳߳�����_�x�猙��ꮠ�P���I��I���h���������N���z�r�������A�������|̎�������������������������J���������N�������������������b�ôĴŴ��o�ȴ��~�˴��n���[��ҏą����ִ״ش��f�۸Z�ݴ޴ߴ��������������e���_�������������������J��������������������đ�����������Q���������hʎ�n���v�����u�\�����������I�µõĵş��ǵȵɵ����̵͵εϔ��ѵҜ�Եյֵ׵صٵڵ۵��f������c���|늵�����յ�����������{����������ՙ�B�������픶��V��ӆ�G�|�������ӗ��������������Y���������������٪��x�¶�ـ��僶Ƕȶɶʶ˶�呶Δ྄�у�ꠌ��Շ��׶��D���g�ܶݶ޶߶��Z�������艙����Z���~Ӟ�𐺶������I�����������D�����E�l�P�������y���m�����������\�C������������؜��������������������L���ŷƷǷ��w�ʷ��u�ͷΏU���M�ҷӷԷշּ����ٷڷۊ^�ݷޑ��S�S�������h�L������T�p�S���P������w������ݗ���������������������������o���������������������x�}����������ؓ��Ӈ���D�`������ԓ�ĸ��}�w�ȎָʗU�̸͸��s�ж����M����䓸׸ؾV���۸ܸ��V�߸����怸����R�������������w���t�����o�����������������������������������m��얹���ؕ���h���Ϲ�������ُ�򹼹����������¹ùĹ��M�ǹȹɹ�̹͹ιτ��ѹҹӹԹչֹ��P�ٹ��^���^�ޑT��؞��V���Ҏ�����w���|܉��Ԏ��𙙹��F��݁�L��偹��������^��������������񔺨�����n�����������������������������h���������������ºú�̖�ƺǺȺɺʺ˺̺ͺκϺк��u�ӺԺպ��Q�R�ٺںۺܺݺ޺ߺ��M����Z����������t�����������������غ������������������o���������W�A����������Ԓ�����ѻ��Ěg�h��߀���Q�����������o�»ûĻ��S�ǻȻɻʻ˻̻ͻλϻ��e�ғ]�x�ջֻ׻ؚ��ڻۻܻݻ��V�x���Z���M�d�Lȝ���꜆����ⷻ�@����؛���������C�����e�����������I�u���������O��݋�������������������D�׼����E���������������ļ�Ӌӛ�ȼ��H���^�o�μϊA�ѼҼ��v�a�Z��⛼ټڃr���{�ޚ��O�Լ�{�g�����D��}�O�z��A�|���캆�����p�]���b�`�vҊ�I������Ş���T�u�R�����������{�����Y�����v���u�����������z�������ɽ����q�C�e�_�ƽ���U�g�˽̽��I�^�нѽҽӽԷM���A�ؽٹ��o�����L���@�������������i�o�����R���d�������Q���������m���¾þľžƎ����f�ʾ˾̾;ξϾоѾҾ��x�վ־׾��e�ھ۾ܓ��޾߾��䏾��־愡���N�������������ۂܽݽ޽ߝ��Y�����������]�ý�������o�\�H֔�M���x�����a���M���G���X�Q�E�^�����x܊�������������E���������_�����P�����������������������������������������¿ÿ��w�ƚ��ȿɿʿ˿̿��n�ϿЉ����ӿԿտֿ׿ؓ��ڿۿܿݿ޿߿���ѝ�F�����K��~�쌒������V����r̝���h�Q�������������������������U���������Ϟ�D�����R��ه�{���ڔr�@�@�m��׎���[���|���E�������������˓Ƅ������������ӝ��՘����D�������܉�����I��������������h���x�������Y���Y��������������[�v���������������r�`�������zɏ�B����z�i����Ę朑ٟ����Z�����������v������Տ�����ů������|������������������ӫC���������R���[�܄C�U�����������g�����R�`��X�I�������������s�������������@���\��¡�Ŕn�]�Ǌ䓧�t©ª�J�R�B�]�t���u̔��´µ¶·�T¹º����¾�H���X�H���Čҿ|�]�����ʞV�G�n���\���сy���Ԓ�݆�����S�]Փ�}���_߉茻j��������j���鬔�aΛ�R�R������I���u�~�}�m�z�U�M��������֙âãäåæç؈é�^ëì�Tîïðñò�Q��õö÷ø�qú�]üý�Vÿ�����������T�������������i�͉��������������i������Ғ�����܃����߾d�������侒��������������R����������������}�����Q�����և��ġĢģĤĥĦħĨĩĪīĬĭĮįİ�\ĲĳĴĵ��ķĸĹĺĻļĽľĿ�������������c���ȼ{���������������y�ғ��X���[�����H����������������M����ā������������f��������B�������m������������帔Q��ţŤ�o�~ē���rŪūŬŭŮůŰ��ŲųŴ�ZŶ�W�t��ź�Iż�ažſ�����������������������˱P�������������������������������������������r�������懊�������������������������i��������������������ơƢƣƤƥƦƧƨƩƪƫƬ�_�hƯưƱƲƳƴ�lؚƷƸƹƺ�OƼƽ�{ƿ�u�����H�����������ʓ�������������Ҙ��������V�������ۗ������ߜD������������������Ě�R�������T���M��������������������ә��Ǣ���L�T�Uǧ�w��Ǫ�tǬǭ�X�Qǰ��ǲ�\�l�qǶǷǸ����ǻǼ���N�������@���Ę��Ɔ̃S�������N���θ[�������Ӹ`�J���H�����������݌������p��A�������������Ո�c���F����������������څ�^�����|�����ȡȢ�xȤȥȦ�E��ȩȪȫȬȭȮȯ��ȱȲȳ�s�oȶ�_ȸȹȺȻȼȽȾȿ������׌���_�@�ǟ����������g���J���Ѽx�����������ؘs�������ݽq����������������������������ܛ�������J�c�����������_���w��ِ������ɢɣɤ��ɦ�}��ɩɪɫ��ɭɮɯɰ��ɲɳ��ɵɶɷ�Y��ɺɻɼɽ�hɿ���W���٠�������ȿ��ʂ����p�������������ԟ������������۽B���d��������z��������O�������������＝���򌏋����I���B���������Kʡʢʣ���}��ʧ�{ʩ��Ԋ��ʭʮʯʰ�rʲʳ�g���Rʷʸʹʺ�ʼʽʾʿ�����������ń��������m��������������ҕԇ�������؉��������ݫF�ߘ�������ݔ����������H��������������������g���������Q��������ˡˢˣˤ˥˦��˨˩˪�pˬ�lˮ˯��˱˲�˴�f�T˷�q˹˺˻˼˽˾�z��������������������Z������A�b���Ҕ\���K�������������������V�C�������m���S������q��������O�p�S�������s�����i�������������H��̣̤̥̦�Ę̩̪̫�B̭̮��؝�c����̴̵̶�TՄ̹̺̻̼̽�U̿�����������������������ˠC�͝��Ͽl����������ӑ�������v���`�������R���}�����w������������������������������l�������N�F���d �Nͣͤͥͦͧͨͩͪͫͬ͢͡�~ͮͯͰͱͲ�yʹ͵Ͷ�^͸͹�dͻ�Dͽ;�T���������ĈF���j��͑��������������Ó�r���W�E�����������ܸD�����m�����㏝�����B���������������������f�����������W������������Ρ΢Σ�f�`Φ��ΨΩ���H�SȔήί���^β��δεζηθικλμ�^ξο�l���������y�Ƿ��Ɇ����̮Y��΁�u�C�����P�����׆��u���@�_�ݟoʏ���������������������]���F�����������`�������������������a��ϡϢϣϤϥϦϧϨϩϪϫϬϭ�uϯ��ϱϲ�ϴϵ϶��Ϲ�rϻϼݠϾ�{�b�M�B�ć����v�����r�w�y�t����e�������@�U�F�I�h���W�w�����޾���������������l����Ԕ����������������ʒ�����������N��������СТУФ�[ЦЧШЩЪϐЬ�f���yаб�{�C��ежзий�a�xмно�\����������������������d���������������������������ٛ����������������n�����C������̓�u����S����������������w�m܎�����������x�_ѣ�kѥѦ�WѨѩѪ��ѬѭѮԃ���ZѲѳѴӖӍ�dѸ��Ѻ�f��ѽѾѿ�����������ņ���Ӡ����鎟����}�����������������������������W�����������䏩�����V����������P������������W�B�������������u���b�G�{ҦҧҨˎҪҫҬҭҮ��ҰұҲ�Ҵ�I�~ҷҸҹҺһҼ�tҾ��������U���z�ƃx���������̏���ρ����������ˇ�������ك|�����������������㑛�x����Ԅ�h�x�g�������[���a������������y����������[ӡӢ�ы������t��Ξ�I��ωӭ�AӯӰ�fӲӳ�ѓ��ӷ�bӹӺ�xӼԁӾ�����������ă��Ƒn�����]♪q���[�������������T������������������ݛ�������~����O��������c�Z�����Z�����������n�������R�����z���uԡԢԣ�Aԥ�S�x�YԩԪԫԬԭԮ�@�@�T�AԳԴ���hԷ�ԹԺԻ�sԽ�S耎[���������y���E���\�N�j����������s���՞����d�����۔���ٝ�E�v��������嗗��������������^��؟��t���\��������ٛ��������܈��lգ��եզէը�pժ�Sլխ��կհ��ղճմ�K��ݚ��չպ��ռ��վտ�`�������ď��Ɲq���Ɏ��~��Û�������������w�����������������U�H���N���@��������������ؑᘂ������\���������걠���b��������������֢���C֥֦֧֪֭֮֨֩֫֬��ֱֲֳ��ֵֶַָֹֺֻּ��־���S�����Î������������|���̜�������������ԽK�N�[�����\���������a���S�����䰙�敃�E���������i�T�D����T�������������������A�T�Bסעף�vץצק���u�D׫ٍ׭���f�b�yײ�Ѡ�׵�F׷٘���YՁ׼׽׾׿�������������Ɲ�Ɲ���Y�������������������ԝn������ۙ�ھC���v�u�����������������{��M�������������������������������';
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

// �����ļ�����ļ�
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
		//'boss/boss.fla', // R12 ɾ��
		//'boss_war/boss_war.fla', // R12 ɾ��
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
		//'common/���ܶ���/���ܶ���-��ʾ.fla',
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
		//'drama/ShouZhua.fla', // R12 ɾ��
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
		'map/Map����ṹ/map-resources.fla',
		'map/Map����ṹ/map.fla',
		/*
		'map/Map����ṹ/����༭��.fla',
		'map/mission/����/map_1.fla',
		'map/multi_mission/background.fla',
		'map/multi_mission/������.fla',
		'map/multi_mission/�ʹ�.fla',
		'map/multi_mission/������.fla',
		'map/multi_mission/�����.fla',
		'map/multi_mission/�����.fla',
		'map/multi_mission/��ڤ��.fla',
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
		//'map/��Ƶ����/shipin.fla',
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
		//'practice/practice.fla', // R12 ɾ��
		//'practice/practiceEffect - ����.fla',
		//'practice/practiceEffect.fla', // R12 ɾ��
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
		'roles/effects/��Ѫ.fla',
		'roles/effects/����.fla',
		'roles/effects/��Ѫ.fla',
		'roles/effects/�������� ����.fla',
		'roles/effects/��.fla',
		'roles/monsters/����boss/����/boss1.fla',
		'roles/monsters/����boss/����/boss2.fla',
		'roles/monsters/����boss/����/boss3.fla',
		'roles/monsters/����boss/����/boss4.fla',
		'roles/monsters/����boss/��/boss1.fla',
		'roles/monsters/����boss/��/boss2.fla',
		'roles/monsters/����boss/��/boss3.fla',
		'roles/monsters/ս��/BaiSeZhanXiong.fla',
		'roles/monsters/ս��/BaiWuChang.fla',
		'roles/monsters/ս��/BaoXiang.fla',
		'roles/monsters/ս��/BingJingShou.fla',
		'roles/monsters/ս��/Boss����/BossBaiZe.fla',
		'roles/monsters/ս��/Boss������/BossChiYanShou.fla',
		'roles/monsters/ս��/Boss����/BossQingLong.fla',
		'roles/monsters/ս��/BuKuai.fla',
		'roles/monsters/ս��/CaiShen.fla',
		'roles/monsters/ս��/ChangQiangJiaoYao.fla',
		'roles/monsters/ս��/ChenYu.fla',
		'roles/monsters/ս��/ChiGui.fla',
		'roles/monsters/ս��/ChiGuiWang.fla',
		'roles/monsters/ս��/ChiHuoXieZi.fla',
		'roles/monsters/ս��/ChiTouMan.fla',
		'roles/monsters/ս��/ChiYanShou.fla',
		'roles/monsters/ս��/CiBeiShanZhu.fla',
		'roles/monsters/ս��/DaNeiShiWei.fla',
		'roles/monsters/ս��/DaoDunJiaoYao.fla',
		'roles/monsters/ս��/DiHun.fla',
		'roles/monsters/ս��/DieJing.fla',
		'roles/monsters/ս��/DongShi.fla',
		'roles/monsters/ս��/DuTong.fla',
		'roles/monsters/ս��/DuanTouYao.fla',
		'roles/monsters/ս��/FeiTouMan.fla',
		'roles/monsters/ս��/FeiYi.fla',
		'roles/monsters/ս��/FengShenShouGuan.fla',
		'roles/monsters/ս��/FengXieShou.fla',
		'roles/monsters/ս��/GrassDemon.fla',
		'roles/monsters/ս��/GuiChengXiang.fla',
		'roles/monsters/ս��/HanYuJian.fla',
		'roles/monsters/ս��/HeiHuJing.fla',
		'roles/monsters/ս��/HeiWuChang.fla',
		'roles/monsters/ս��/HongSheYao.fla',
		'roles/monsters/ս��/HuSha.fla',
		'roles/monsters/ս��/HuXingJiuWei.fla',
		'roles/monsters/ս��/HuaYao.fla',
		'roles/monsters/ս��/HuangJinZhanXiong.fla',
		'roles/monsters/ս��/HuoGui.fla',
		'roles/monsters/ս��/HuoKui.fla',
		'roles/monsters/ս��/HuoLieNiao.fla',
		'roles/monsters/ս��/JiXiangRuYi.fla',
		'roles/monsters/ս��/JianHun.fla',
		'roles/monsters/ս��/JianPo.fla',
		'roles/monsters/ս��/JiangChen.fla',
		'roles/monsters/ս��/JiangShiJiangJun.fla',
		'roles/monsters/ս��/JinChan.fla',
		'roles/monsters/ս��/JinChiFengHuang.fla',
		'roles/monsters/ս��/JiuXianWeng.fla',
		'roles/monsters/ս��/JuChuiShuYao.fla',
		'roles/monsters/ս��/JuDunShuYao.fla',
		'roles/monsters/ս��/JuMang.fla',
		'roles/monsters/ս��/KuangBaoZhiZhu.fla',
		'roles/monsters/ս��/LanLingShiWei.fla',
		'roles/monsters/ս��/LeiShou.fla',
		'roles/monsters/ս��/LiChiLang.fla',
		'roles/monsters/ս��/LiYiWar.fla',
		'roles/monsters/ս��/LingHu.fla',
		'roles/monsters/ս��/LiuWeiHuoHu.fla',
		'roles/monsters/ս��/LiuWeiLingHu.fla',
		'roles/monsters/ս��/LuShiBingChan.fla',
		'roles/monsters/ս��/LuShiHuoChan.fla',
		'roles/monsters/ս��/LuoChaDaoSheng.fla',
		'roles/monsters/ս��/LuoChaJianShen.fla',
		'roles/monsters/ս��/LuoYan.fla',
		'roles/monsters/ս��/MengPo.fla',
		'roles/monsters/ս��/MoJian.fla',
		'roles/monsters/ս��/MoJiangWuLuo.fla',
		'roles/monsters/ս��/MoNvYeMei.fla',
		'roles/monsters/ս��/MoWangXingTian.fla',
		'roles/monsters/ս��/PuTongJiangShi.fla',
		'roles/monsters/ս��/QianNianShuYao.fla',
		'roles/monsters/ս��/QingHuoJiangShi.fla',
		'roles/monsters/ս��/QingZhuSheYao.fla',
		'roles/monsters/ս��/RenXingJiuWei.fla',
		'roles/monsters/ս��/RuYi.fla',
		'roles/monsters/ս��/SanXianBing.fla',
		'roles/monsters/ս��/SanXianJia.fla',
		'roles/monsters/ս��/SanXianYi.fla',
		'roles/monsters/ս��/ShaChong.fla',
		'roles/monsters/ս��/ShanZei.fla',
		'roles/monsters/ս��/SheYaoMengNan.fla',
		'roles/monsters/ս��/SheYaoNan.fla',
		'roles/monsters/ս��/ShiXueJian.fla',
		'roles/monsters/ս��/ShuangDaoXieZi.fla',
		'roles/monsters/ս��/ShuangTouShe.fla',
		'roles/monsters/ս��/TaiGuYuanJun.fla',
		'roles/monsters/ս��/TianBingShouWei.fla',
		'roles/monsters/ս��/TiaoTiaoTu.fla',
		'roles/monsters/ս��/TieJiaZhanXiong.fla',
		'roles/monsters/ս��/TreeDemon.fla',
		'roles/monsters/ս��/WanYaoHuang.fla',
		'roles/monsters/ս��/WildPig.fla',
		'roles/monsters/ս��/WolfDemon.fla',
		'roles/monsters/ս��/WolfDemonBoss.fla',
		'roles/monsters/ս��/WuCaiZhiZhu.fla',
		'roles/monsters/ս��/XiaBing.fla',
		'roles/monsters/ս��/XianBeiYinHun.fla',
		'roles/monsters/ս��/XianGuan.fla',
		'roles/monsters/ս��/XianRenZhang.fla',
		'roles/monsters/ս��/XiangLongShou.fla',
		'roles/monsters/ս��/XiaoBingJingShou.fla',
		'roles/monsters/ս��/XiaoFengHuang.fla',
		'roles/monsters/ս��/XieJian.fla',
		'roles/monsters/ս��/XieJiang.fla',
		'roles/monsters/ս��/XuanMingYaoZu.fla',
		'roles/monsters/ս��/XuanNiao.fla',
		'roles/monsters/ս��/XuanWuHuanShou.fla',
		'roles/monsters/ս��/XuanWuShenShou.fla',
		'roles/monsters/ս��/XueLangYao.fla',
		'roles/monsters/ս��/XueSeBianFu.fla',
		'roles/monsters/ս��/YaYi.fla',
		'roles/monsters/ս��/YanLangYao.fla',
		'roles/monsters/ս��/YaoGuJiaoYao.fla',
		'roles/monsters/ս��/YaoHuaTongLing.fla',
		'roles/monsters/ս��/YaoZhouShuShi.fla',
		'roles/monsters/ս��/YingLong.fla',
		'roles/monsters/ս��/YouGuei.fla',
		'roles/monsters/ս��/YuanBao.fla',
		'roles/monsters/ս��/ZhanHun.fla',
		'roles/monsters/ս��/ZhangMaZi.fla',
		'roles/monsters/ս��/ZhangMenYuanShi.fla',
		'roles/monsters/ս��/ZhiZhuJing.fla',
		'roles/monsters/ս��/ZhuHa.fla',
		'roles/monsters/ս��/ZiDianShe.fla',
		'roles/npcs/npc/npc-�ƽ���/JiuJianXian.fla',
		'roles/npcs/npc/����- ��ջ�ƹ�-������/LiuRuYan.fla',
		'roles/npcs/npc/����-���/LiGangNPC.fla',
		'roles/npcs/npc/����-�ֿ����Ա-���ո�/LuoTuoGe.fla',
		'roles/npcs/npc/����-��������-½��/LuWu.fla',
		'roles/npcs/npc/����-��ջ�ƹ�-������/TieSuanPan.fla',
		'roles/npcs/npc/����-������/LinTianNan.fla',
		'roles/npcs/npc/С���-�峤/CunZhang.fla',
		'roles/npcs/npc/С���-��ջ�ƹ�-�����/LiDaNiang.fla',
		'roles/npcs/npc/С���-ˮ��/ShuiLing.fla',
		'roles/npcs/npc/С���-�ӻ�����-ܿ��/YunNiang.fla',
		'roles/npcs/��NPC������/����λ��.fla',
		'roles/players/������/portal1.fla',
		'roles/players/������/portal2.fla',
		'roles/players/ս��/ChuChu.fla',
		'roles/players/ս��/FangShiNv.fla',
		'roles/players/ս��/FeiYuNan.fla',
		'roles/players/ս��/FeiYuNv.fla',
		'roles/players/ս��/JianLingNan.fla',
		'roles/players/ս��/JianLingNv.fla',
		'roles/players/ս��/JiangChen.fla',
		'roles/players/ս��/JinMingCheng.fla',
		'roles/players/ս��/NieXiaoQian.fla',
		'roles/players/ս��/NingCaiChen.fla',
		'roles/players/ս��/ShuShiNan.fla',
		'roles/players/ս��/WuShengNan.fla',
		'roles/players/ս��/WuShengNv.fla',
		'roles/players/ս��/XiaoXianTong.fla',
		'roles/portal/portal1.fla',
		'roles/portal/portal2.fla',
		'roles/portal/����ʯ.fla',
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
		'roles/swf��Դ���/SWF.fla',
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
		//'tools/��ͼ�༭��(��)/mapEdit.fla',
		//'tools/ս�������༭/�����༭.fla',
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