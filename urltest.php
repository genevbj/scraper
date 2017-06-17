<?php


$s_options=[
    'debug' => true,
    'max_sim_downloads' => 10,
    'max_documents_per_session' => 10,
    'obsolescence' => 3600,
    'cache_dir' => './cache_olx_uz',
    'img_cache_dir' => './cache_olx_uz_img',
    'own_host' => 'http://191.101.158.250',
    'img_web_dir' => '/img',
    'xml_web_dir' => '/export',
    'xml_cache_dir' => './cache_olx_uz_xml',
    'xml_name' => 'olx.xml',
    'use_proxy' => true,
    'reuse_curl' => true,
    'max_retries' => 3, // TODO
    'check_proxy' => false,
    'single_ua_per_session' => true,
    'single_proxy_per_session' => true,
    'waiting' => 180,
    'sleep_between' => 15,
    'check_proxy_url' => 'http://gde.ru/check_proxy.php',
    'domain' => 'olx.uz',

    'traverse_outer_domains' => false,
    'traverse_inner_domains' => true,
    'base_url' => 'http://olx.uz',
    'db' => [
	'name'=>'olx_uz', 
	'driver' => 'mongodb', 
	'connection' => "mongodb://localhost:27017", 
	'struct' => [
	    'tree' 	=> 'map',
	    'leaf' 	=> 'ads',
	    'proxies'	=> 'proxies',
	] 
    ],
    'ignore_ext' => ['css', 'js', 'ico'],
    'ignore_url' => [
		    '/i2',
		    '/javascript',
		    '/settings',
		    '/wishlist',
		    'itunes.apple.com',
		    '/store', 
		    '?category=', 
		    '/myaccount', 
		    '/post-new-ad', 
		    '/changelang', 
		    '/mobileapps',
		    '/help',
		    '/topfeature',
		    '/payment/features',
		    '/reklama',
		    '/terms',
		    '/free-mobile-internet',
		    '/akcia-ums',
		    '/howitworks',
		    '/safetyuser',
		    '/sitemap',
		    '/sitemap/regions',
		    '/archive',
		    '/contact',
		    '/oz',
		    '/m',
		    '/opensearch',
		    '/olx-landing',
		    '/favorites',
		    '/list',
		    '/disable',
		    '/account',
		    '/detskiy-mir/detskaya-obuv',
		    '/detskiy-mir/detskie-kolyaski',
		    '/detskiy-mir/detskie-avtokresla',
		    '/detskiy-mir/detskaya-mebel',
		    '/detskiy-mir/igrushki',
		    '/detskiy-mir/detskiy-transport',
		    '/detskiy-mir/kormlenie',
		    '/detskiy-mir/tovary-dlya-shkolnikov',
		    '/detskiy-mir/prochie-detskie-tovary',
		    '/nedvizhimost',
		    '/transport',
		    '/rabota',
		    '/zhivotnye',
		    '/dom-i-sad',
		    '/elektronika',
		    '/uslugi',
		    '/zhivotnye',
		    '/dom-i-sad',
		    '/moda-i-stil',
		    '/otdam-darom',
		    '/obmen-barter',
		    '/moda-i-stil',
		    '/hobbi-otdyh-i-sport/antikvariat-kollektsii',
		    '/hobbi-otdyh-i-sport/muzykalnye-instrumenty',
		    '/hobbi-otdyh-i-sport/drugoe',
		    '/hobbi-otdyh-i-sport/knigi-zhurnaly',
		    '/hobbi-otdyh-i-sport/cd-dvd-plastinki',
		    '/hobbi-otdyh-i-sport/bilety',
		    '/otdam-darom',
		    '/obmen-barter',


		    '/i2/myaccount', 
		    '/i2/post-new-ad', 
		    '/i2/changelang', 
		    '/i2/mobileapps',
		    '/i2/help',
		    '/i2/topfeature',
		    '/i2/payment/features',
		    '/i2/reklama',
		    '/i2/terms',
		    '/i2/free-mobile-internet',
		    '/i2/akcia-ums',
		    '/i2/howitworks',
		    '/i2/safetyuser',
		    '/i2/sitemap',
		    '/i2/sitemap/regions',
		    '/i2/archive',
		    '/i2/contact',
		    '/i2/oz',
		    '/i2/m',
		    '/i2/opensearch',
		    '/i2/olx-landing',
		    '/i2/favorites',
		    '/i2/list',
		    '/i2/disable',
		    '/i2/account',
		    '/i2/detskiy-mir/detskaya-obuv',
		    '/i2/detskiy-mir/detskie-kolyaski',
		    '/i2/detskiy-mir/detskie-avtokresla',
		    '/i2/detskiy-mir/detskaya-mebel',
		    '/i2/detskiy-mir/igrushki',
		    '/i2/detskiy-mir/detskiy-transport',
		    '/i2/detskiy-mir/kormlenie',
		    '/i2/detskiy-mir/tovary-dlya-shkolnikov',
		    '/i2/detskiy-mir/prochie-detskie-tovary',
		    '/i2/nedvizhimost',
		    '/i2/transport',
		    '/i2/rabota',
		    '/i2/zhivotnye',
		    '/i2/dom-i-sad',
		    '/i2/elektronika',
		    '/i2/uslugi',
		    '/i2/zhivotnye',
		    '/i2/dom-i-sad',
		    '/i2/moda-i-stil',
		    '/i2/otdam-darom',
		    '/i2/obmen-barter',
		    '/i2/moda-i-stil',
		    '/i2/hobbi-otdyh-i-sport/antikvariat-kollektsii',
		    '/i2/hobbi-otdyh-i-sport/muzykalnye-instrumenty',
		    '/i2/hobbi-otdyh-i-sport/drugoe',
		    '/i2/hobbi-otdyh-i-sport/knigi-zhurnaly',
		    '/i2/hobbi-otdyh-i-sport/cd-dvd-plastinki',
		    '/i2/hobbi-otdyh-i-sport/bilety',
		    '/i2/otdam-darom',
		    '/i2/obmen-barter',


		    '/m/myaccount', 
		    '/m/post-new-ad', 
		    '/m/changelang', 
		    '/m/mobileapps',
		    '/m/help',
		    '/m/topfeature',
		    '/m/payment/features',
		    '/m/reklama',
		    '/m/terms',
		    '/m/free-mobile-internet',
		    '/m/akcia-ums',
		    '/m/howitworks',
		    '/m/safetyuser',
		    '/m/sitemap',
		    '/m/sitemap/regions',
		    '/m/archive',
		    '/m/contact',
		    '/m/oz',
		    '/m/m',
		    '/m/opensearch',
		    '/m/olx-landing',
		    '/m/favorites',
		    '/m/list',
		    '/m/disable',
		    '/m/account',
		    '/m/detskiy-mir/detskaya-obuv',
		    '/m/detskiy-mir/detskie-kolyaski',
		    '/m/detskiy-mir/detskie-avtokresla',
		    '/m/detskiy-mir/detskaya-mebel',
		    '/m/detskiy-mir/igrushki',
		    '/m/detskiy-mir/detskiy-transport',
		    '/m/detskiy-mir/kormlenie',
		    '/m/detskiy-mir/tovary-dlya-shkolnikov',
		    '/m/detskiy-mir/prochie-detskie-tovary',
		    '/m/nedvizhimost',
		    '/m/transport',
		    '/m/rabota',
		    '/m/zhivotnye',
		    '/m/dom-i-sad',
		    '/m/elektronika',
		    '/m/uslugi',
		    '/m/zhivotnye',
		    '/m/dom-i-sad',
		    '/m/moda-i-stil',
		    '/m/otdam-darom',
		    '/m/obmen-barter',
		    '/m/moda-i-stil',
		    '/m/hobbi-otdyh-i-sport/antikvariat-kollektsii',
		    '/m/hobbi-otdyh-i-sport/muzykalnye-instrumenty',
		    '/m/hobbi-otdyh-i-sport/drugoe',
		    '/m/hobbi-otdyh-i-sport/knigi-zhurnaly',
		    '/m/hobbi-otdyh-i-sport/cd-dvd-plastinki',
		    '/m/hobbi-otdyh-i-sport/bilety',
		    '/m/otdam-darom',
		    '/m/obmen-barter',
    ],
    'leaf_ext' =>['html'],
    'xpath_struct' => [
	// <ID><![CDATA[15334]]></ID>
//	'ID'			=> '//ul[@id="contact_methods_below"]/li/@class', 
	'ID'			=> '//*[@id="offerdescription"]/div[2]/div/em/small',
	// <CRC><![CDATA[01b0074ed70ecdc79ee5c9adf22c903f]]></CRC>
	// <TITLE><![CDATA[Участок с домом ПГТ Новозавидосвкий, 17 соток]]></TITLE>
	'TITLE'			=> '//*[@id="offerdescription"]/div[2]/h1',
	//	<DESCRIPTION><![CDATA[Продаётся участок 17 соток с домом, Ленинградское ш., ПГТ Новозавидовский 95 км от Москвы. ПМЖ, домовая книга. Дом 2-х этаж рубленый на фундаменте (6х6) со всеми удобствами. В доме созданы все условия для жизни: мебель, душ сан. узел, отопление, г/х вода, городской тел., на окнах - желез ставни, кап кирпичный гараж на фундаменте, беседка со столом, колодец. Залит фундамент (12,5х12,5), перекрыт бетонными плитами под 2-х этаж дом, есть план-проект постройки. Общая площадь дома по проекту 172 м2. Врыта ёмкость под септик. Магистр газ. Элект-во, две зарегистрированные точки, 10 мин. пешком до ж/д станции «Завидово». Фруктовый сад. Московское море 500 м. Дорога к участку хорошая. Подъезд круглый год, асфальт. В посёлке имеется вся инфраструктура. 2700000 руб. [#15334#]]]></DESCRIPTION>
	'DESCRIPTION'		=> '//*[@id="textContent"]/p',
	//	<DATE><![CDATA[2017-04-21]]></DATE>
	'DATE'			=> '//*[@id="offerdescription"]/div[2]/div/em/text()',
	//	<EMAIL><![CDATA[3vda3@mail.ru]]></EMAIL>
//	'EMAIL'			=> '//*[@id="content"]/div[1]/div[3]/div[3]/div/div[3]',
	//	<LAT><![CDATA[56.689506]]></LAT>
	//	<LNG><![CDATA[36.782076]]></LNG>
	//	<LOCATION_COUNTRY><![CDATA[ RU ]]></LOCATION_COUNTRY>
	// 'LOCATION_COUNTRY'	=> '//*[@id="content"]/div[1]/div[3]/div[2]/div[2]/div/table/tbody/tr/td[2]',
	//	<LOCATION_STATE><![CDATA[TVR]]></LOCATION_STATE>
	//	<LOCATION_CITY><![CDATA[1496]]></LOCATION_CITY>
	//	<ADDRESS><![CDATA[улица Красногвардейская]]></ADDRESS>
	//	<CATEGORY><![CDATA[756]]></CATEGORY>
	//	<IMAGE_URL><![CDATA[http://www.jcat.ru/images/orders/2013-08/21/5f50a2bf209268efa8f212f2159ab2d2.jpg]]></IMAGE_URL>
	//	<PRICE><![CDATA[2700000]]></PRICE>
	'PRICE'			=> '//*[@id="offeractions"]/div[1]/strong',
////*[@id="content"]/div[1]/div[3]/div[2]/text()'
	//	<CONTACT><![CDATA[Анатолий]]></CONTACT>
	'CONTACT'		=> '//*[@id="offeractions"]/div[3]/div[2]/h4/a/text()',
	//	<CONTACT_PHONE><![CDATA[+7 (495) 7635590]]></CONTACT_PHONE>
//	'CONTACT_PHONE'		=> '//*[@id="content"]/div[1]/div[3]/div[3]/div/div[2]',
	//	<WATER><![CDATA[да]]></WATER>
	//	<fields>
//	'+fields'		=> '//table[contains(@class,"info")]/tbody',
//	'+contacts'		=> '//div[contains(@class,"offercontact")]',
	'+imgb'			=> '//div[contains(@class, "img-item")]/div/img/@src',
//	'+imgm'			=> '//div[contains(@class,"photos")]/a/img/@src',
	'@PHONE_TOKEN'		=> '//section[@id="body-container"]/script',
	'+SCRIPTS'		=> '//head/script',
	//http://irr.uz/photo/33195_b.jpg

	//		<heating><![CDATA[да]]></heating>
	//		<square><![CDATA[172]]></square>
	//		<rooms_count><![CDATA[3]]></rooms_count>
	//		<wall_material><![CDATA[445]]></wall_material>
	//		<plot><![CDATA[17]]></plot>
	//		<gas><![CDATA[да]]></gas>
	//		<water><![CDATA[да]]></water>
	//		<sewerage><![CDATA[да]]></sewerage>
	//	</fields>
	//	<extra_fields>
	//		<extra_field name="Название коттеджного поселка">пгт новозавидовский</extra_field>
	//		<extra_field name="Телефон">да</extra_field>
	//		<extra_field name="Интернет">нет</extra_field>
	//		<extra_field name="Мебель">да</extra_field>
	//		<extra_field name="Доп.постройки">да</extra_field>
	//		<extra_field name="Охрана">нет</extra_field>
	//	</extra_fields>
	//<AD>

    ],

];


$domain = $s_options['domain'];
$ignore_ext = $s_options['ignore_ext'];
$ignore_url = $s_options['ignore_url'];
$leaf_ext = $s_options['leaf_ext'];

	$children_tree = [];
	$children = [];


	$link="http://google.com/?url=http%66%67%67olx.uz/hobbi-otdyh-i-sport/ferganskaya-oblast/";

		$link = trim($link);
		if (strlen($link) === 0) 
		    $link = '/';

    

		echo("Found ($link)");

		if (strpos($link, 'http') === false && strpos($link, '/') === 0) 
		{
            	    $link = $base_url . $link;
		    echo("Convert to ($link)");
		}
        	// check for a same directory reference
        	else 
		    if (strpos($link, 'http') === false && strpos($link, '/') === false) 
		    {
			if (strpos($link, 'www.') !== false) die("www.");
			$link = $base_url . '/' . $link;
			echo("Convert to ($link)");
		    }
		    // dont index email addresses
		    else 
			if (strpos($link, 'mailto:') !== false) 
			{
			    $ignore_url = true;
			    echo("Ignore email link");
			}
			// skip link if it isnt on the same domain
			else 
//			    if ( (strpos($link, '.'.$domain) === false) || (strpos($link, '/'.$domain) === false) )
			    if ( (strpos($link, '//'.$domain) === false) && (strpos($link, '//www.'.$domain) === false ) )
			    {
//				if ($this['options']['traverse_inner_domains'] && strpos($link, $domain) === false) 
//				{
				    $ignore_url = true;
				    echo("Ignore outside link");
//				    continue;
//				Ъ
//				if ($this['options']['traverse_outer_domains'] && strpos($link, $domain) === false) 
//				{
			    }
		
		// skip ignored
		foreach($ignore_ext as $ext)
		{
		    if (strpos($link, '.'.$ext) !== false) 
		    {
			echo("Ignore by extention \n");
			$ignore_url = true;
		    }
		}

		foreach($ignore_url as $iu)
		{
		    if (strpos($link, $domain.$iu) !== false) 
		    {
			echo("Ignore by uri \n");
			$ignore_url = true;
		    }
		}

		if(!$ignore_url)
		{
		    foreach($leaf_ext as $ext)
		    {
			if (strpos($link, '.'.$ext) !== false) 
			{
			    echo("Found leaf page ($link)\n");
			    $children[]= ($link!='/') ?  rtrim($link, '/') : $link;
			    $ignore_url = true;
			}
		    }
		}
	    // only add request if this is the first time in this session
//	    $link_id = $checkIfFirstRequest($link);
//	    if ($link_id === true) {
//	    	$addRequest($link);
//	    } else {
//		// update the inbound link count
//		$updateInboundCount($link_id);
//	    }
		if(!$ignore_url)
		{
		    $children_tree[]= ($link!='/') ?  rtrim($link, '/') : $link;
		}
	    

