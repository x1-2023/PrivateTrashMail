<?php

$adjectives = ["quick", "brown", "lazy", "silly", "happy", "angry", "brave", "calm", "eager", "fancy"];

function getDirForEmail($email)
{
    return realpath(ROOT.DS.'..'.DS.'data'.DS.$email);
}

function startsWith($haystack, $needle)
{
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

function getEmail($email,$id)
{
    return json_decode(file_get_contents(getDirForEmail($email).DS.$id.'.json'),true);
}

function getRawEmail($email,$id)
{
    $data = json_decode(file_get_contents(getDirForEmail($email).DS.$id.'.json'),true);

    return $data['raw'];
}

function emailIDExists($email,$id)
{
    return file_exists(getDirForEmail($email).DS.$id.'.json');
}

function getEmailsOfEmail($email,$includebody=false,$includeattachments=false)
{
    $o = [];
    $settings = loadSettings();

    if($settings['ADMIN'] && $settings['ADMIN']==$email)
    {
        $emails = listEmailAdresses();
        if(count($emails)>0)
        {
            foreach($emails as $email)
            {
                if ($handle = opendir(getDirForEmail($email))) {
                    while (false !== ($entry = readdir($handle))) {
                        if (endsWith($entry,'.json')) {
                            $time = substr($entry,0,-5);
                            $json = json_decode(file_get_contents(getDirForEmail($email).DS.$entry),true);
                            $o[$time] = array(
                                'email'=>$email,'id'=>$time,
                                'from'=>$json['parsed']['from'],
                                'subject'=>$json['parsed']['subject'],
                                'md5'=>md5($time.$json['raw']),
                                'maillen'=>strlen($json['raw'])
                            );
                            if($includebody==true)
                                $o[$time]['body'] = $json['parsed']['body'];
                                if($includeattachments==true)
                                {
                                    $o[$time]['attachments'] = $json['parsed']['attachments'];
                                    //add url to attachments
                                    foreach($o[$time]['attachments'] as $k=>$v)
                                        $o[$time]['attachments'][$k] = $settings['URL'].'/api/attachment/'.$email.'/'. $v;
                                }
                        }
                    }
                    closedir($handle);
                }
            }
        }
    }
    else
    {
        if ($handle = opendir(getDirForEmail($email))) {
            while (false !== ($entry = readdir($handle))) {
                if (endsWith($entry,'.json')) {
                    $time = substr($entry,0,-5);
                    $json = json_decode(file_get_contents(getDirForEmail($email).DS.$entry),true);
                    $o[$time] = array(
                                        'email'=>$email,
                                        'id'=>$time,
                                        'from'=>$json['parsed']['from'],
                                        'subject'=>$json['parsed']['subject'],
                                        'md5'=>md5($time.$json['raw']),'maillen'=>strlen($json['raw'])
                                    );
                                    if($includebody==true)
                                        $o[$time]['body'] = $json['parsed']['body'];
                                    if($includeattachments==true)
                                    {
                                        $o[$time]['attachments'] = $json['parsed']['attachments'];
                                        //add url to attachments
                                        foreach($o[$time]['attachments'] as $k=>$v)
                                            $o[$time]['attachments'][$k] = $settings['URL'].'/api/attachment/'.$email.'/'. $v;
                                    }
                }                   
            }
            closedir($handle);
        }
    }

    if(is_array($o))
        krsort($o);

    return $o;
}

function listEmailAdresses()
{
    $o = array();
    if ($handle = opendir(ROOT.DS.'..'.DS.'data'.DS)) {
        while (false !== ($entry = readdir($handle))) {
            if(filter_var($entry, FILTER_VALIDATE_EMAIL))
                $o[] = $entry;
        }
        closedir($handle);
    }

    return $o;
}

function attachmentExists($email,$id,$attachment=false)
{
    return file_exists(getDirForEmail($email).DS.'attachments'.DS.$id.(($attachment)?'-'.$attachment:''));
}

function listAttachmentsOfMailID($email,$id)
{
    $data = json_decode(file_get_contents(getDirForEmail($email).DS.$id.'.json'),true);
    $attachments = $data['parsed']['attachments'];
    if(!is_array($attachments))
        return [];
    else
        return $attachments;
}

function deleteEmail($email,$id)
{
    $dir = getDirForEmail($email);
    $attachments = listAttachmentsOfMailID($email,$id);
    foreach($attachments as $attachment)
        unlink($dir.DS.'attachments'.DS.$attachment);
    return unlink($dir.DS.$id.'.json');
}


function loadSettings()
{
    if(file_exists(ROOT.DS.'..'.DS.'config.ini'))
        return parse_ini_file(ROOT.DS.'..'.DS.'config.ini');
    return false;
}


function escape($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function array2ul($array)
{
    $out = "<ul>";
    foreach ($array as $key => $elem) {
        $out .= "<li>$elem</li>";
    }
    $out .= "</ul>";
    return $out;
}

function tailShell($filepath, $lines = 1) {
    ob_start();
    passthru('tail -'  . $lines . ' ' . escapeshellarg($filepath));
    return trim(ob_get_clean());
}

function getUserIP()
{
    if($_SERVER['HTTP_CF_CONNECTING_IP'])
        return $_SERVER['HTTP_CF_CONNECTING_IP'];
	$client  = @$_SERVER['HTTP_CLIENT_IP'];
	$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	$remote  = $_SERVER['REMOTE_ADDR'];
	
    if(strpos($forward,','))
    {
        $a = explode(',',$forward);
        $forward = trim($a[0]);
    }
	if(filter_var($forward, FILTER_VALIDATE_IP))
	{
		$ip = $forward;
	}
    elseif(filter_var($client, FILTER_VALIDATE_IP))
	{
		$ip = $client;
	}
	else
	{
		$ip = $remote;
	}
	return $ip;
}

/**
 * Check if a given IPv4 or IPv6 is in a network
 * @param  string $ip    IP to check in IPV4 format eg. 127.0.0.1
 * @param  string $range IP/CIDR netmask eg. 127.0.0.0/24, or 2001:db8::8a2e:370:7334/128
 * @return boolean true if the ip is in this range / false if not.
 * via https://stackoverflow.com/a/56050595/1174516
 */
function isIPInRange( $ip, $range ) {

    if(strpos($range,',')!==false)
    {
        // we got a list of ranges. splitting
        $ranges = array_map('trim',explode(',',$range));
        foreach($ranges as $range)
            if(isIPInRange($ip,$range)) return true;
        return false;
    }
    // Get mask bits
    list($net, $maskBits) = explode('/', $range);

    // Size
    $size = (strpos($ip, ':') === false) ? 4 : 16;

    // Convert to binary
    $ip = inet_pton($ip);
    $net = inet_pton($net);
    if (!$ip || !$net) {
        throw new InvalidArgumentException('Invalid IP address');
    }

    // Build mask
    $solid = floor($maskBits / 8);
    $solidBits = $solid * 8;
    $mask = str_repeat(chr(255), $solid);
    for ($i = $solidBits; $i < $maskBits; $i += 8) {
        $bits = max(0, min(8, $maskBits - $i));
        $mask .= chr((pow(2, $bits) - 1) << (8 - $bits));
    }
    $mask = str_pad($mask, $size, chr(0));

    // Compare the mask
    return ($ip & $mask) === ($net & $mask);
}

function getVersion()
{
    if(file_exists(ROOT.DS.'..'.DS.'VERSION'))
        return trim(file_get_contents(ROOT.DS.'..'.DS.'VERSION'));
    else return '';
}

function generateRandomEmail()
{
    $nouns = ["aardvark","abyssinian","accelerator","accordion","account","accountant","acknowledgment","acoustic","acrylic","act","action","activity","actor","actress","adapter","addition","address","adjustment","adult","advantage","advertisement","aftermath","afternoon","aftershave","afterthought","age","agenda","agreement","air","airbus","airmail","airplane","airport","airship","alarm","albatross","alcohol","algebra","algeria","alibi","alley","alligator","alloy","almanac","alphabet","alto","aluminium","aluminum","ambulance","america","amount","amusement","anatomy","anethesiologist","anger","angle","angora","animal","anime","ankle","answer","ant","anteater","antelope","anthony","anthropology","apartment","apology","apparatus","apparel","appeal","appendix","apple","appliance","approval","april","aquarius","arch","archaeology","archeology","archer","architecture","area","argentina","argument","aries","arithmetic","arm","armadillo","armchair","army","arrow","art","ash","ashtray","asia","asparagus","asphalt","asterisk","astronomy","athlete","ATM","atom","attack","attempt","attention","attic","attraction","august","aunt","australia","australian","author","authority","authorization","avenue","baboon","baby","back","backbone","bacon","badge","badger","bag","bagel","bagpipe","bail","bait","baker","bakery","balance","balinese","ball","balloon","bamboo","banana","band","bandana","bangle","banjo","bank","bankbook","banker","bar","barbara","barber","barge","baritone","barometer","base","baseball","basement","basin","basket","basketball","bass","bassoon","bat","bath","bathroom","bathtub","battery","battle","bay","beach","bead","beam","bean","bear","beard","beast","beat","beautician","beauty","beaver","bed","bedroom","bee","beech","beef","beer","beet","beetle","beggar","beginner","begonia","behavior","belgian","belief","bell","belt","bench","bengal","beret","berry","bestseller","betty","bibliography","bicycle","bike","bill","billboard","biology","biplane","birch","bird","birth","birthday","bit","bite","black","bladder","blade","blanket","blinker","blizzard","block","blouse","blow","blowgun","blue","board","boat","bobcat","body","bolt","bomb","bomber","bone","bongo","bonsai","book","bookcase","booklet","boot","border","botany","bottle","bottom","boundary","bow","bowl","box","boy","bra","brace","bracket","brain","brake","branch","brand","brandy","brass","brazil","bread","break","breakfast","breath","brian","brick","bridge","british","broccoli","brochure","broker","bronze","brother","brother-in-law","brow","brown","brush","bubble","bucket","budget","buffer","buffet","bugle","building","bulb","bull","bulldozer","bumper","bun","burglar","burma","burn","burst","bus","bush","business","butane","butcher","butter","button","buzzard","cabbage","cabinet","cable","cactus","cafe","cake","calculator","calculus","calendar","calf","call","camel","camera","camp","can","cancer","candle","cannon","canoe","canvas","cap","capital","cappelletti","capricorn","captain","caption","car","caravan","carbon","card","cardboard","cardigan","care","carnation","carol","carp","carpenter","carriage","carrot","cart","cartoon","case","cast","castanet","cat","catamaran","caterpillar","cathedral","catsup","cattle","cauliflower","cause","caution","cave","c-clamp","cd","ceiling","celery","celeste","cell","cellar","cello","celsius","cement","cemetery","cent","centimeter","century","ceramic","cereal","certification","chain","chair","chalk","chance","change","channel","character","chard","charles","chauffeur","check","cheek","cheese","cheetah","chef","chemistry","cheque","cherry","chess","chest","chick","chicken","chicory","chief","child","children","chill","chime","chimpanzee","chin","china","chinese","chive","chocolate","chord","christmas","christopher","chronometer","church","cicada","cinema","circle","circulation","cirrus","citizenship","city","clam","clarinet","class","claus","clave","clef","clerk","click","client","climb","clipper","cloakroom","clock","close","closet","cloth","cloud","clover","club","clutch","coach","coal","coast","coat","cobweb","cockroach","cocktail","cocoa","cod","coffee","coil","coin","coke","cold","collar","college","collision","colombia","colon","colony","color","colt","column","columnist","comb","comfort","comic","comma","command","commission","committee","community","company","comparison","competition","competitor","composer","composition","computer","condition","condor","cone","confirmation","conga","congo","conifer","connection","consonant","continent","control","cook","copper","copy","copyright","cord","cork","cormorant","corn","cornet","correspondent","cost","cotton","couch","cougar","cough","country","course","court","cousin","cover","cow","cowbell","crab","crack","cracker","craftsman","crate","crawdad","crayfish","crayon","cream","creator","creature","credit","creditor","creek","crib","cricket","crime","criminal","crocodile","crocus","croissant","crook","crop","cross","crow","crowd","crown","crush","cry","cub","cuban","cucumber","cultivator","cup","cupboard","cupcake","curler","currency","current","curtain","curve","cushion","custard","customer","cut","cuticle","cycle","cyclone","cylinder","cymbal","dad","daffodil","dahlia","daisy","damage","dance","dancer","danger","daniel","dash","dashboard","database","date","daughter","david","day","dead","deadline","deal","death","deborah","debt","debtor","decade","december","decimal","decision","decrease","dedication","deer","defense","deficit","degree","delete","delivery","den","denim","dentist","deodorant","department","deposit","description","desert","design","desire","desk","dessert","destruction","detail","detective","development","dew","diamond","diaphragm","dibble","dictionary","dietician","difference","digestion","digger","digital","dill","dime","dimple","dinghy","dinner","dinosaur","diploma","dipstick","direction","dirt","disadvantage","discovery","discussion","disease","disgust","dish","distance","distribution","distributor","division","dock","doctor","dog","dogsled","doll","dollar","dolphin","domain","donald","donkey","donna","door","double","doubt","downtown","dragon","dragonfly","drain","drake","drama","draw","drawbridge","drawer","dream","dredger","dress","dresser","drill","drink","drive","driver","drizzle","drop","drug","drum","dry","dryer","duck","duckling","dugout","dungeon","dust","eagle","ear","earth","earthquake","ease","edge","edger","editor","editorial","education","edward","eel","effect","egg","eggnog","eggplant","egypt","eight","elbow","element","elephant","elizabeth","ellipse","emery","employee","employer","encyclopedia","end","enemy","energy","engine","engineer","english","enquiry","entrance","environment","epoch","epoxy","equinox","equipment","era","error","estimate","ethernet","ethiopia","euphonium","europe","evening","event","examination","example","exchange","exclamation","exhaust","ex-husband","existence","expansion","experience","expert","explanation","ex-wife","eye","eyebrow","eyelash","eyeliner","face","fact","factory","fahrenheit","fall","family","fan","fang","farm","farmer","fat","father","father-in-law","faucet","fear","feast","feather","feature","february","fedelini","feedback","feeling","feet","felony","female","fender","ferry","ferryboat","fertilizer","fiber","fiberglass","fibre","fiction","field","fifth","fight","fighter","file","find","fine","finger","fir","fire","fired","fireman","fireplace","firewall","fish","fisherman","flag","flame","flare","flat","flavor","flax","flesh","flight","flock","flood","floor","flower","flugelhorn","flute","fly","foam","fog","fold","font","food","foot","football","footnote","force","forecast","forehead","forest","forgery","fork","form","format","fortnight","foundation","fountain","fowl","fox","foxglove","fragrance","frame","france","freckle","freeze","freezer","freighter","french","freon","friction","Friday","fridge","friend","frog","front","frost","frown","fruit","fuel","fur","furniture","galley","gallon","game","gander","garage","garden","garlic","gas","gasoline","gate","gateway","gauge","gazelle","gear","gearshift","geese","gemini","gender","geography","geology","geometry","george","geranium","german","germany","ghana","ghost","giant","giraffe","girdle","girl","gladiolus","glass","glider","glockenspiel","glove","glue","goal","goat","gold","goldfish","golf","gondola","gong","good-bye","goose","gore-tex","gorilla","gosling","government","governor","grade","grain","gram","granddaughter","grandfather","grandmother","grandson","grape","graphic","grass","grasshopper","gray","grease","great-grandfather","great-grandmother","greece","greek","green","grenade","grey","grill","grip","ground","group","grouse","growth","guarantee","guatemalan","guide","guilty","guitar","gum","gun","gym","gymnast","hacksaw","hail","hair","haircut","half-brother","half-sister","halibut","hall","hallway","hamburger","hammer","hamster","hand","handball","handicap","handle","handsaw","harbor","hardboard","hardcover","hardhat","hardware","harmonica","harmony","harp","hat","hate","hawk","head","headlight","headline","health","hearing","heart","heat","heaven","hedge","height","helen","helicopter","helium","hell","helmet","help","hemp","hen","heron","herring","hexagon","hill","himalayan","hip","hippopotamus","history","hockey","hoe","hole","holiday","home","honey","hood","hook","hope","horn","horse","hose","hospital","hot","hour","hourglass","house","hovercraft","hub","hubcap","humidity","humor","hurricane","hyacinth","hydrant","hydrofoil","hydrogen","hyena","hygienic","ice","icebreaker","icicle","icon","idea","ikebana","illegal","imprisonment","improvement","impulse","inch","income","increase","index","india","indonesia","industry","ink","innocent","input","insect","instruction","instrument","insulation","insurance","interactive","interest","internet","interviewer","intestine","invention","inventory","invoice","iran","iraq","iris","iron","island","israel","italian","italy","jacket","jaguar","jail","jam","james","january","japan","japanese","jar","jasmine","jason","jaw","jeep","jelly","jellyfish","jennifer","jet","jewel","join","joke","joseph","journey","judge","judo","juice","july","jumbo","jump","jumper","june","jury","justice","jute","kale","kamikaze","kangaroo","karate","karen","kayak","kendo","kenneth","kenya","ketchup","kettle","kettledrum","kevin","key","keyboard","kick","kidney","kilogram","kilometer","kimberly","kiss","kitchen","kite","kitten","kitty","knee","knife","knight","knot","knowledge","kohlrabi","korean","laborer","lace","ladybug","lake","lamb","lamp","lan","land","landmine","language","larch","lasagna","latency","latex","lathe","laugh","laundry","laura","law","lawyer","layer","lead","leaf","leather","leek","leg","legal","lemonade","lentil","leo","leopard","letter","lettuce","level","libra","library","license","lier","lift","light","lightning","lilac","lily","limit","linda","line","linen","link","lion","lip","lipstick","liquid","liquor","lisa","list","literature","litter","liver","lizard","llama","loaf","loan","lobster","lock","locket","locust","look","loss","lotion","love","low","lumber","lunch","lunchroom","lung","lunge","lute","luttuce","lycra","lynx","lyocell","lyre","lyric","macaroni","machine","macrame","magazine","magic","magician","maid","mail","mailbox","mailman","makeup","malaysia","male","mall","mallet","man","manager","mandolin","manicure","manx","map","maple","maraca","marble","march","margaret","margin","maria","marimba","mark","market","married","mary","mascara","mask","mass","match","math","mattock","may","mayonnaise","meal","measure","meat","mechanic","medicine","meeting","melody","memory","men","menu","mercury","message","metal","meteorology","meter","methane","mexican","mexico","mice","michael","michelle","microwave","middle","mile","milk","milkshake","millennium","millimeter","millisecond","mimosa","mind","mine","minibus","mini-skirt","minister","mint","minute","mirror","missile","mist","mistake","mitten","moat","modem","mole","mom","Monday","money","monkey","month","moon","morning","morocco","mosque","mosquito","mother","mother-in-law","motion","motorboat","motorcycle","mountain","mouse","moustache","mouth","move","multi-hop","multimedia","muscle","museum","music","musician","mustard","myanmar","nail","name","nancy","napkin","narcissus","nation","neck","need","needle","neon","nepal","nephew","nerve","nest","net","network","newsprint","newsstand","nic","nickel","niece","nigeria","night","nitrogen","node","noise","noodle","norwegian","nose","note","notebook","notify","novel","november","number","numeric","nurse","nut","nylon","oak","oatmeal","objective","oboe","observation","occupation","ocean","ocelot","octagon","octave","october","octopus","odometer","offence","offer","office","oil","okra","olive","onion","open","opera","operation","ophthalmologist","opinion","option","orange","orchestra","orchid","order","organ","organisation","organization","ornament","ostrich","otter","ounce","output","overcoat","owl","owner","ox","oxygen","oyster","package","packet","page","pail","pain","paint","pair","pajama","pakistan","palm","pamphlet","pan","pancake","pancreas","panda","pansy","panther","pantry","pair of pants","panty","pantyhose","paper","paperback","parade","parallelogram","parcel","parent","parenthesis","park","parrot","parsnip","part","particle","partner","partridge","party","passbook","passenger","passive","pasta","paste","pastor","pastry","patch","path","patient","patio","patricia","paul","payment","pea","peace","peak","peanut","pear","pedestrian","pediatrician","peen","peer-to-peer","pelican","pen","penalty","pencil","pendulum","pentagon","peony","pepper","perch","perfume","period","periodical","peripheral","permission","persian","person","peru","pest","pet","pharmacist","pheasant","philosophy","phone","physician","piano","piccolo","pickle","picture","pie","pig","pigeon","pike","pillow","pilot","pimple","pin","pine","ping","pink","pint","pipe","pisces","pizza","place","plain","plane","planet","plant","plantation","plaster","plasterboard","plastic","plate","platinum","play","playground","playroom","pleasure","plier","plot","plough","plow","plywood","pocket","poet","point","poison","poland","police","policeman","polish","politician","pollution","polo","polyester","pond","popcorn","poppy","population","porch","porcupine","port","porter","position","possibility","postage","postbox","pot","potato","poultry","pound","powder","power","precipitation","preface","prepared","pressure","price","priest","print","printer","prison","probation","process","produce","product","production","professor","profit","promotion","propane","property","prose","prosecution","protest","protocol","pruner","psychiatrist","psychology","ptarmigan","puffin","pull","puma","pump","pumpkin","punch","punishment","puppy","purchase","purple","purpose","push","pvc","pyjama","pyramid","quail","quality","quart","quarter","quartz","queen","question","quicksand","quiet","quill","quilt","quince","quit","quiver","quotation","rabbi","rabbit","radar","radiator","radio","radish","raft","rail","railway","rain","rainbow","raincoat","rainstorm","rake","ramie","random","range","rat","rate","raven","ravioli","ray","rayon","reaction","reading","reason","receipt","recess","record","recorder","rectangle","red","reduction","refrigerator","refund","regret","reindeer","relation","relative","religion","relish","reminder","repair","replace","report","representative","request","resolution","respect","responsibility","rest","restaurant","result","retailer","revolve","revolver","reward","rhinoceros","rhythm","rice","richard","riddle","rifle","ring","rise","risk","river","riverbed","road","roadway","roast","robert","robin","rock","rocket","rod","roll","romania","romanian","ronald","roof","room","rooster","root","rose","rotate","route","router","rowboat","rub","rubber","rugby","rule","run","russia","russian","rutabaga","ruth","sack","sagittarius","sail","sailboat","sailor","salad","salary","sale","salesman","salmon","salt","sampan","samurai","sand","sandra","sandwich","Santa","sardine","satin","Saturday","sauce","sausage","save","saw","saxophone","scale","scallion","scanner","scarecrow","scarf","scene","scent","schedule","school","science","scissor","scooter","scorpio","scorpion","scraper","screen","screw","screwdriver","sea","seagull","seal","seaplane","search","seashore","season","seat","second","secretary","secure","security","seed","seeder","segment","select","selection","self","semi","senile","sensate","senseless","septal","septate","sequent","sequined","seral","serene","serfish","serflike","serrate","serried","serviced","servo","setose","severe","sexism","sexist","sexless","sextan","sexy","shabby","shaded","shadeless","shadowed","shady","shaftless","shaken","shaky","shallow","shalwar","shamefaced","shameful","shameless","shapeless","shapely","shaping","shaven","shawlless","sheathy","sheepish","shellproof","shelly","shickered","shieldless","shieldlike","shier","shiest","shiftless","shifty","shingly","shining","shiny","shipboard","shipless","shipshape","shirtless","shirty","shocking","shoddy","shoeless","shopworn","shoreless","shoreward","shortcut","shortish","shorty","shotten","showy","shredded","shredless","shrewish","shrieval","shrinelike","shrouding","shroudless","shrubby","shrunken","shyer","shyest","sicker","sicklied","sickly","sideling","sidelong","sideward","sideways","sighful","sighted","sightless","sightly","sigmate","silenced","silken","silty","silvan","silvern","simplex","sincere","sinful","singing","singsong","sinless","sinning","sissy","sister","sixfold","sixteen","sixty","sizy","skaldic","sketchy","skewbald","skidproof","skilful","skillful","skimpy","skinking","skinless","skinny","skirtless","skittish","skyward","slaggy","slakeless","slangy","slantwise","slapstick","slashing","slaty","slavish","sleazy","sleekit","sleeky","sleepless","sleepwalk","sleepy","sleety","sleeveless","slender","slickered","slier","sliest","slighting","slimline","slimmer","slimmest","slimming","slimsy","slimy","slinky","slippy","slipshod","sloping","sloshy","slothful","slouchy","sloughy","sludgy","sluggard","sluggish","sluicing","slumbrous","slummy","slushy","sluttish","smacking","smallish","smarmy","smartish","smarty","smashing","smeary","smectic","smelly","smileless","smiling","smitten","smokeproof","smoking","smothered","smugger","smuggest","smutty","snafu","snaggy","snakelike","snaky","snappish","snappy","snarly","snatchy","snazzy","sneaking","sneaky","snider","snidest","sniffy","snippy","snobbish","snoopy","snooty","snoozy","snoring","snotty","snouted","snowless","snowlike","snubby","snuffly","snuffy","snugger","snuggest","snugging","soapless","soapy","soaring","sober","socko","sodden","softish","softwood","soggy","sola","solemn","soli","sollar","solus","solute","solvent","somber","sombre","sombrous","sometime","sonant","songful","songless","sonless","sonsie","sonsy","soothfast","soothing","sopping","soppy","sordid","sorer","sorest","sorry","sotted","sottish","soulful","soulless","soundless","soundproof","soupy","sourish","southmost","southpaw","southward","sovran","sozzled","splanchnic","splashy","spleenful","spleenish","spleeny","splendent","splendid","splendrous","splenic","splitting","splurgy","spoken","spokewise","spongy","spooky","spoony","sportful","sportive","sportless","sporty","spotless","spotty","spousal","spouseless","spouted","spoutless","spriggy","sprightful","sprightly","springing","springless","springlike","springtime","springy","sprucer","sprucest","sprucing","spryer","spryest","spunky","spurless","squabby","squalid","squally","squamate","squamous","squarish","squarrose","squashy","squeaky","squeamish","squiffy","squiggly","squirmy","squirting","squishy","stabbing","stabile","stagey","stagnant","stagy","stalkless","stalky","stalwart","stalworth","stannous","staple","starboard","starchy","staring","starless","starlight","starlike","starring","starry","starveling","starving","statant","stated","stateless","stateside","statewide","statist","stative","statued","steadfast","stealthy","steamtight","steamy","stedfast","steepled","stelar","stellar","stellate","stemless","stenosed","stepwise","steric","sterile","sternal","sternmost","sthenic","stickit","stiffish","stifling","stilly","stilted","stingless","stingy","stinko","stintless","stirless","stirring","stockinged","stockish","stockless","stocky","stodgy","stolen","stolid","stoneground","stoneless","stoneware","stonkered","stopless","stopping","store","storeyed","storied","stormbound","stormless","stormproof","stotious","stoutish","straining","strangest","strapless","strapping","stratous","strawless","strawlike","streaky","streaming","streamless","streamlined","streamy","stressful","stretchy","striate","stricken","strident","strifeful","strifeless","strigose","stringent","stringless","stringy","stripeless","stripy","strobic","strongish","strophic","stroppy","structured","strutting","strychnic","stubbled","stubbly","stubborn","stubby","studied","stuffy","stumbling","stumpy","stunning","stupid","sturdied","sturdy","stutter","stylar","styleless","stylised","stylish","stylized","styloid","subdued","subfusc","subgrade","sublimed","submerged","submersed","submiss","subscribed","subscript","subtile","subtle","succinct","suchlike","suffused","sugared","suited","sulcate","sulfa","sulkies","sulky","sullen","sullied","sultry","sunbaked","sunbeamed","sunburnt","sunfast","sunken","sunless","sunlike","sunlit","sunproof","sunrise","sunset","sunward","super","superb","supine","supple","supposed","sural","surbased","surer","surest","surfy","surgeless","surging","surgy","surly","surpliced","surplus","surprised","suspect","svelter","sveltest","swainish","swampy","swanky","swaraj","swarthy","sweated","sweaty","sweeping","sweetmeal","swelling","sweptwing","swindled","swingeing","swinish","swirly","swishy","swordless","swordlike","sylphic","sylphid","sylphish","sylphy","sylvan","systemless","taboo","tabu","tacit","tacky","tactful","tactile","tactless","tailing","tailless","taillike","tailored","taintless","taken","taking","talcose","talking","talky","taloned","tameless","tamer","tamest","taming","tandem","tangential","tangier","tangled","tangy","tannic","tapeless","tapelike","tardy","tarmac","tarnal","tarot","tarry","tarsal","tartish","tasseled","tasselled","tasteful","tasteless","tasty","tattered","tatty","taurine","tawie","tearful","tearing","tearless","teary","teasing","techy","teeming","teenage","teensy","teeny","telic","telling","telltale","tempered","templed","tempting","tender","tenfold","tenor","tenseless","tenser","tensest","tensing","tensive","tented","tentie","tentless","tenty","tepid","terbic","terete","tergal","termless","ternate","terrene","tertial","tertian","testate","testy","tetchy","textbook","textile","textless","textured","thallic","thalloid","thallous","thankful","thankless","thatchless","thecal","thecate","theism","theist","themeless","thenar","thermic","theroid","thetic","thickset","thievish","thinking","thinnish","thirdstream","thirstless","thirsty","thirteen","thistly","thornless","thorny","thoughtful","thoughtless","thousandth","thowless","thrashing","threadbare","threadlike","thready","threatful","threefold","threescore","thriftless","thrifty","thrilling","throaty","throbbing","throbless","throneless","throwback","thudding","thuggish","thumbless","thumblike","thumping","thymic","thymy","thyrsoid","ticklish","tiddly","tideless","tidied","tightknit","timbered","timeless","timely","timeous","timid","tingly","tinhorn","tinkling","tinkly","tinny","tinsel","tintless","tiny","tippy","tiptoe","tiptop","tireless","tiresome","titled","toeless","toey","togaed","togate","toilful","toilsome","tombless","tonal","toneless","tongueless","tonguelike","tonish","tonnish","tony","toothless","toothlike","toothsome","toothy","topfull","topless","topmost","torose","torpid","torquate","torrent","tortile","tortious","tortured","tother","touching","touchy","toughish","touring","tourist","toward","towered","townish","townless","towy","toxic","toyless","toylike","traceless","trackless","tractile","tractrix","trainless","tranquil","transcribed","transient","transposed","traplike","trappy","trashy","traveled","travelled","traverse","treacly","treasured","treen","trembling","trembly","trenchant","trendy","tressured","tressy","tribal","tribeless","trichoid","trickish","trickless","tricksome","tricksy","tricky","tricorn","trident","trifid","trifling","triform","trillion","trillionth","trilobed","trinal","triploid","trippant","tripping","tristful","triter","tritest","triune","trivalve","trochal","trochoid","trodden","trophic","trophied","tropic","troppo","trothless","troublous","truant","truceless","truer","truffled","truncate","trunnioned","trustful","trusting","trustless","trusty","truthful","truthless","tryptic","tsarism","tsarist","tubal","tubate","tubby","tubeless","tumbling","tumid","tuneful","tuneless","turbaned","turbid","turdine","turfy","turgent","turgid","tuskless","tussal","tussive","tutti","twaddly","tweedy","twelvefold","twenty","twiggy","twinkling","twinning","twofold","typal","typhous","typic","ugsome","ullaged","umber","umbral","umbrose","umpteen","umpteenth","unaimed","unaired","unapt","unarmed","unasked","unawed","unbacked","unbagged","unbaked","unbarbed","unbarred","unbathed","unbegged","unbent","unbid","unblamed","unbleached","unblenched","unblent","unblessed","unblocked","unblown","unboned","unborn","unborne","unbought","unbound","unbowed","unbraced","unbranched","unbreached","unbreathed","unbred","unbreeched","unbridged","unbroke","unbruised","unbrushed","unburned","unburnt","uncaged","uncalled","uncapped","uncashed","uncaught","unchained","unchanged","uncharge","uncharged","uncharmed","unchaste","unchecked","uncheered","unchewed","unclad","unclaimed","unclassed","unclean","uncleaned","uncleansed","unclear","uncleared","unclimbed","unclipped","unclogged","unclutched","uncocked","uncoined","uncombed","uncooked","uncouth","uncropped","uncross","uncrowned","unculled","uncurbed","uncured","uncursed","uncurved","uncut","undamped","undeaf","undealt","undecked","undimmed","undipped","undocked","undone","undrained","undraped","undrawn","undreamed","undreamt","undress","undressed","undried","undrilled","undrowned","undrunk","undubbed","undue","undug","undulled","undyed","unfair","unfanned","unfeared","unfed","unfelled","unfelt","unfenced","unfiled","unfilled","unfilmed","unfine","unfired","unfirm","unfished","unfit","unflawed","unfledged","unflushed","unfooled","unforced","unforged","unformed","unfought","unfound","unframed","unfraught","unfree","unfunded","unfurred","ungalled","ungauged","ungeared","ungilt","ungirthed","unglad","unglazed","ungloved","ungorged","ungowned","ungraced","ungrassed","ungrazed","ungroomed","unground","ungrown","ungrudged","ungual","unguessed","unguled","ungummed","ungyved","unhacked","unhailed","unhanged","unharmed","unhatched","unhealed","unheard","unhelped","unhewn","unhinged","unhired","unhooped","unhorsed","unhung","unhurt","unhusked","unique","unjust","unkempt","unkenned","unkept","unkind","unkinged","unkissed","unknelled","unlaid","unlearned","unlearnt","unleased","unled","unlet","unlike","unlimed","unlined","unlit","unlooked","unlopped","unlost","unloved","unmade","unmailed","unmaimed","unmanned","unmarked","unmarred","unmasked","unmatched","unmeant","unmeet","unmet","unmilked","unmilled","unmissed","unmixed","unmoaned","unmourned","unmoved","unmown","unnamed","unoiled","unowned","unpaced","unpaged","unpaid","unpained","unpaired","unpared","unpaved","unpeeled","unpent","unperched","unpicked","unpierced","unplaced","unplagued","unplanked","unplayed","unpleased","unpledged","unploughed","unplucked","unplumb","unplumbed","unplumed","unpoised","unpolled","unposed","unpraised","unpreached","unpressed","unpriced","unprimed","unprized","unpropped","unproved","unpruned","unpurged","unquelled","unquenched","unraised","unraked","unreached","unread","unreaped","unreined","unrent","unrhymed","unribbed","unrigged","unrimed","unripe","unroped","unrouged","unroused","unrubbed","unrude","unruled","unsafe","unsaid","unsailed","unsapped","unsashed","unsaved","unscaled","unscanned","unscarred","unscathed","unschooled","unscorched","unscoured","unscratched","unscreened","unsealed","unsearched","unseen","unseized","unsensed","unsent","unset","unshamed","unshaped","unshared","unshaved","unsheathed","unshed","unshipped","unshocked","unshod","unshoed","unshorn","unshown","unshrived","unshunned","unshut","unsight","unsigned","unsized","unskilled","unskimmed","unskinned","unslain","unsliced","unsluiced","unslung","unsmirched","unsmooth","unsmoothed","unsnuffed","unsoaped","unsoft","unsoiled","unsold","unsolved","unsought","unsound","unsown","unspared","unsparred","unspelled","unspent","unspied","unspilled","unspilt","unspoiled","unspoilt","unsprung","unspun","unsquared","unstack","unstacked","unstaid","unstained","unstamped","unstarched","unstilled","unstirred","unstitched","unstocked","unstopped","unstrained","unstreamed","unstressed","unstringed","unstriped","unstripped","unstrung","unstuck","unstuffed","unsucked","unsung","unsure","unswayed","unswept","unsworn","untailed","untame","untamed","untanned","untapped","untarred","untaught","unteamed","unthanked","unthawed","unthought","untied","untiled","untilled","untinged","untinned","untired","untold","untombed","untoned","untorn","untouched","untraced","untracked","untrained","untrenched","untressed","untried","untrimmed","untrod","untrue","unturfed","unturned","unurged","unused","unversed","unvexed","unviewed","unvoiced","unwaked","unwarmed","unwarned","unwarped","unwashed","unwatched","unweaned","unwebbed","unwed","unweened","unweighed","unwell","unwept","unwet","unwhipped","unwilled","unwinged","unwiped","unwired","unwise","unwished","unwitched","unwon","unwooed","unworked","unworn","unwound","unwrapped","unwrought","unwrung","upbeat","upbound","upcast","upgrade","uphill","upmost","uppish","upraised","upset","upstage","upstaged","upstair","upstairs","upstart","upstate","upstream","uptight","uptown","upturned","upward","upwind","urbane","urdy","urgent","urnfield","useful","useless","utile","utmost","vadose","vagal","vagrant","vagrom","vaguer","vaguest","valanced","valgus","valiant","valid","valval","valvar","valvate","vambraced","vaneless","vanward","vapid","varied","varus","vassal","vasty","vatic","vaulted","vaulting","vaunted","vaunting","vaunty","veilless","veiny","velar","velate","vellum","venal","vengeful","venose","venous","ventose","verbless","verbose","verdant","verism","verist","vespine","vestral","vibrant","viceless","viewless","viewy","villose","villous","vinous","viral","virgate","virile","visaged","viscid","viscose","viscous","vitric","vivid","vivo","vixen","voetstoots","vogie","voiceful","voiceless","voided","volant","volar","volumed","volvate","vorant","voteless","votive","vulpine","vying","wacky","wageless","waggish","waggly","wailful","wailing","waisted","wakeful","wakeless","wakerife","waking","walnut","wambly","wandle","waney","waning","wanner","wannest","wanning","wannish","wanting","wanton","warded","warlike","warming","warmish","warning","warring","wartless","wartlike","warty","wary","washy","waspish","waspy","wasted","wasteful","watchful","waveless","wavelike","waving","wavy","waxen","waxing","waxy","wayless","wayward","wayworn","weakly","weaponed","wearied","wearing","wearish","weary","weathered","webby","wedded","wedgy","weedy","weekday","weekly","weeny","weepy","weer","weest","weighted","weighty","welcome","weldless","westbound","western","wetter","wettish","whacking","whacky","whapping","whate'er","wheaten","wheezing","wheezy","wheyey","whilom","whining","whinny","whiny","whiplike","whirring","whiskered","whitish","whittling","whity","wholesale","wholesome","whopping","whoreson","whorish","wicked","wicker","wider","widespread","widest","widish","wieldy","wifeless","wifely","wiggly","wigless","wiglike","wilful","willful","willing","willyard","wily","wimpy","windburned","winded","windproof","windswept","windy","wingless","winglike","wintry","winy","wiretap","wiring","wiry","wiser","wisest","wising","wispy","wistful","witchy","withdrawn","withy","witless","witted","witting","witty","wizard","wizen","wizened","woaded","wobbling","woeful","woesome","wolfish","wonky","wonted","wooded","woodless","woodsy","woodwind","woolen","woollen","woozier","woozy","wordless","wordy","workless","worldly","worldwide","wormy","worried","worser","worshipped","worthless","worthwhile","worthy","wounded","woundless","woven","wrapround","wrathful","wrathless","wreathless","wreckful","wretched","wrier","wriest","wriggly","wrinkly","writhen","writhing","written","wrongful","xanthous","xerarch","xeric","xiphoid","xylic","xyloid","yarer","yarest","yawning","yclept","yearling","yearlong","yearly","yearning","yeastlike","yeasty","yestern","yielding","yogic","yolky","yonder","younger","youthful","yttric","yuletide","zany","zealous","zebrine","zeroth","zestful","zesty","zigzag","zillion","zincky","zincoid","zincous","zincy","zingy","zinky","zippy","zonate","zoning"];


    $settings = loadSettings();
    $domains = explode(',', $settings['DOMAINS']);
    $dom = $domains[array_rand($domains)];
    
    $dom = str_replace('*', $nouns[array_rand($nouns)], $dom);
    while (strpos($dom, '*') !== false) {
        $dom = str_replace('*', $nouns[array_rand($nouns)], $dom);
    }


    return $adjectives[array_rand($adjectives)] . '.' . $nouns[array_rand($nouns)].'@'.$dom;
}

function removeScriptsFromHtml($html) {
    // Remove script tags
    $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $html);

    // Remove event attributes that execute scripts
    $html = preg_replace('/\bon\w+="[^"]*"/i', "", $html);

    // Remove href attributes that execute scripts
    $html = preg_replace('/\bhref="javascript[^"]*"/i', "", $html);

    // Remove any other attributes that execute scripts
    $html = preg_replace('/\b\w+="[^"]*\bon\w+="[^"]*"[^>]*>/i', "", $html);

    return $html;
}

function countEmailsOfAddress($email)
{
    $count = 0;
    if ($handle = opendir(getDirForEmail($email))) {
        while (false !== ($entry = readdir($handle)))
            if (endsWith($entry,'.json'))
                $count++;
    }
    closedir($handle);
    return $count;
}

function delTree($dir) {

    $files = array_diff(scandir($dir), array('.','..'));
     foreach ($files as $file) {
       (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
     }
     return rmdir($dir);
 
   }