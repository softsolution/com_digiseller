<?php

/**
 * функция отсылки запроса на сервер DigiSeller'а и получения XML-ответа
 *
 */
function SendRequest($URL, $REQUEST, $send_post_type){
	
	# в зависимости от пользовательских настроек работаем через CURL или сокеты
	if ( $send_post_type == 1 ) {
		return POST_curl($URL, $REQUEST);
	} else {
		return  POST_socket($URL, $REQUEST);
	}
}

/**
 * CURL-функция для отсылки запроса на сервер DigiSeller'а и получения XML-ответа
 *
 */
function POST_curl($URL, $REQUEST){
	if ( extension_loaded('curl') && function_exists('curl_init') ) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $URL);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml") );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $REQUEST);
		$result = curl_exec($ch);
		curl_close($ch);
		
		return $result;
	} else {
		echo 'На этом хостинге нет возможности использовать библиотеку cURL';
		die();
	}
}

/**
 * socket-функция для отсылки запроса на сервер DigiSeller'а и получения XML-ответа
 *
 */
function POST_socket($URL, $REQUEST) {
	# получаем составляющие от URL: host и path
	$URL = parse_url($URL);
	
	$fp = fsockopen ( $URL['host'], 80, $errno, $errstr, 10 );
	if ( !$fp ) {
		echo "Возникла ошибка при использовании сокетов.<br /> $errstr ($errno)";
	} else {
		fputs($fp, "POST ".$URL['path']." HTTP/1.1\r\n");
		fputs($fp, "Host: ".$URL['host']."\r\n");
		fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
		fputs($fp, "Content-length: ".strlen($REQUEST)."\r\n");
		fputs($fp, "Connection: close\r\n\r\n");
		fputs($fp, $REQUEST . "\r\n\r\n");
		while ( !feof($fp) ) {
			$result .= fgets($fp,4096);
		};
		fclose ($fp);
	};
	
	# отделяем заголовок от "тела" XML-ответа
	list($tmp, $result) = explode("\r\n\r\n", $result, 2);
	
	return $result;
}

/**
 * функция перевода html-мнемоников в символы. Обратна действию функции htmlentities()
 *
 */
function unhtmlentities ($string){
	$trans_tbl = get_html_translation_table (HTML_ENTITIES);
	$trans_tbl = array_flip ($trans_tbl);
	
	return strtr ($string, $trans_tbl);
}

/**
 * функция вывода заданной ошибки
 *
 */
function ShowErrorMessage($message){
	echo '<center>'
	.'<div align="center" style="color:red; border:1px dashed red; font-weight:bold; width:400; float:center; padding:20px 20px 20px 20px;">'
	.$message
	.'</div></center>';
}

/**
 * функция выяснения номера страницы которую отображать
 *
 */
function CurrentPage($cur_page){
	# выясняем номер страницы которую отображать (если не указано номер страницы,
	# тогда отображается первая страница)
        if ( !empty($cur_page) && is_numeric($cur_page) ) {
		return $cur_page;
	} else {
		$cur_page = 1;
	}
	return $cur_page;
}

/**
 * функция поиска названия раздела
 *
 */
function set_title($id_group, $sellerID, $menuOrder){
	
	# адрес запроса
	$host  = 'http://shop.digiseller.ru/xml/personal_groups.asp';
	
	# формируем текст запроса
	$entry = '<?xml version="1.0" encoding="windows-1251"?>'
		.'<digiseller.request>'
		.'<id_seller>'.$sellerID.'</id_seller>'
		.'<order></order>'
		.'</digiseller.request>';
	
	# отправляем запрос и переводим XML-ответ в массив
	$base = new SimpleXMLElement( SendRequest($host, $entry) );
	
	# в зависимости от кода выполнения запроса делаем соответствующие действия
	if ( $base->retval == '0') {
		foreach ( $base->rows->row as $k ) {
			if ( $id_group == $k->id_group ) {
				$name = iconv('UTF-8', 'windows-1251', $k->name_group);
				break;
			}
		}
	}

	return $name;
}

/**
 * функция вывода списка разделов товаров
 *
 */
function ShowMenu($sellerID, $menuOrder, $send_post_type){
	
	# адрес запроса
	$host  = 'http://shop.digiseller.ru/xml/personal_groups.asp';
	
	# формируем текст запроса
	$entry = '<?xml version="1.0" encoding="windows-1251"?>'
		.'<digiseller.request>'
		.'<id_seller>'.$sellerID.'</id_seller>'
		.'<order>'.$menuOrder.'</order>'
		.'</digiseller.request>';
	
	# отправляем запрос и переводим XML-ответ в массив
	$base = new SimpleXMLElement( SendRequest($host, $entry, $send_post_type) );
	
	# в зависимости от кода выполнения запроса делаем соответствующие действия
	if ( $base->retval == '0') {
		foreach ( $base->rows->row as $k ) {
			$result .= '<li><a href="/digiseller/'.$k->id_group.'" title="'.iconv('UTF-8', 'windows-1251', $k->name_group).'">'.iconv('UTF-8', 'windows-1251', $k->name_group).'</a></li>';
		}
	} else {
		if ( !empty( $base->retdesc ) ) {
			echo iconv('UTF-8', 'windows-1251', $base->retdesc);
			die();
		}
	}
	
	return $result;
}

/**
 * функция вывода списка товаров по заданному разделу
 *
 */
function ListGoods($id_group, $rows, $sort, $cur_page, $sellerID, $menuOrder){

	# устанавливаем по умолчанию виды сортировок
	$ord = 'name';
	$name_ord = 'nameDESC';
	$price_ord = 'priceDESC';
	
	# проверяем изменена ли сортировка, которая установлена по умолчанию
	if((@!empty($sort))and((@$sort == 'name')or(@$sort == 'nameDESC')or($sort == 'price')or($sort == 'priceDESC'))){
		switch($sort){
			case 'name': $ord = 'name';
			             $name_ord ='nameDESC';
			             break;
			case 'nameDESC': $ord = 'nameDESC';
				     $name_ord ='name';
				     break;
			case 'price': $ord = 'price';
				     $price_ord ='priceDESC';
				     break;
			case 'priceDESC': $ord = 'priceDESC';
				     $price_ord ='price';
				     break;
		}
	}
	
	# адрес запроса
	$host  = 'http://shop.digiseller.ru/xml/personal_goods.asp';
	
	# формируем текст запроса
	$entry = '<digiseller.request>'
			.'<id_group>'.$id_group.'</id_group>'
			.'<page>'.CurrentPage($cur_page).'</page>'
			.'<rows>'.$rows.'</rows>'
			.'<order>'.$ord.'</order>'
			.'</digiseller.request>';
	
	# отправляем запрос и переводим XML-ответ в массив
	$base = new SimpleXMLElement( SendRequest($host, $entry) );
	
        $item = array();//массив который будем возвращать
	# в зависимости от кода выполнения запроса делаем соответствующие действия
	if ( $base->retval == '0' ) {
		# если передали ID группы товаров - значит страница не главная и
		# устанавливаем динамический TITLE страницы, как название раздела
		# иначе (если страница главная) делаем его пустым
		if (!empty($id_group) ) {
			$item['title'] = set_title($id_group, $sellerID, $menuOrder);
		}
		
		# загружаем шаблон таблицы
		$page = implode('', file(PATH.'/components/digiseller/template/xmlListGoods.html') );
		
		# выбираем шаблон-строку по комментариям-меткам
		preg_match_all("/<!-- begin shablon -->.*<!-- end shablon -->/isU", $page, $tpl);
		
		# если удачно выбрали шаблон-строку и в заданной группе на заданной странице
		# есть товары, тогда формируем строки таблицы
		if ( !empty($tpl[0][0]) && $base->cnt_goods>0 ){
			# создаем массив с макросами в шаблоне
			$macrosArray = array('{GOODS_NAME}', '{PRICE}');
			
			for ( $i=0; $i<$base->rows['cnt']; $i++ ) {
				# создаем массив с данными и формируем строку, заменяя макросы на данные.
				if ( empty($base->rows->row[$i]->price) ) {
					$base->rows->row[$i]->price = 'нет';
				}
				$valueArray  = array('<a href="/digiseller/goods/'.$base->rows->row[$i]->id_goods.'" title="'.iconv('UTF-8', 'windows-1251', $base->rows->row[$i]->name_goods).'">'.iconv('UTF-8', 'windows-1251', $base->rows->row[$i]->name_goods).'</a>',
							$base->rows->row[$i]->price);
				$lines .= str_replace($macrosArray, $valueArray, $tpl[0][0]);
			}
			
			# создаем список страниц с товарами
			for($i=1; $i<=$base->pages; $i++) {
				if ( CurrentPage($cur_page) == $i ) {
					$list_pages .= '<span style="color:#ffffff;background-color:#B2B2B2;">&nbsp;'.$i.'&nbsp;</span>&nbsp;';
				} else {
					$list_pages .= '<a href="/digiseller/'.$id_group.'/page-'.$i.'">'.$i.'</a>&nbsp;';
				}
			}
                        
			//url в зависимоcти от пагинации
                        if(CurrentPage($cur_page)!=1){
                            $url = "/digiseller/".$id_group."/page-".$cur_page;
                        } else {
                            $url = "/digiseller/".$id_group;
                        }
                        
			# заменяем шаблоны значениями и выводим таблицу
			$result = str_replace(
				array('{URL}', '{SORT_NAME}', '{SORT_PRICE}', '{LIST_PAGES}'),
				array($url, $name_ord, $price_ord, $list_pages),
				$page);
			$item['result'] = preg_replace("/<!-- begin shablon -->.*<!-- end shablon -->/isU", $lines, $result);
                        return $item;
		}
	}else{
		# если код выполнения не "0" (запрос выполнен) выводим это сообщение
		if ( !empty($base->retdesc) ) {
			ShowErrorMessage( iconv('UTF-8', 'windows-1251', $base->retdesc) );
		} else {
			ShowErrorMessage('Не могу подключиться к сервису DigiSeller.');
		}
	}
}

/**
 * функция вывода выпадающего меню для сортировки списка товаров
 *
 */
function GoodsSort($idn, $sort){
	
	# загружаем шаблон таблицы
	$page = implode( '', file(PATH.'/components/digiseller/template/xmlGoodsSort.html') );
	
	# устанавливаем виды сортировок по умолчанию
	$ord = 'name';
	$name_ord = 'nameDESC';
	$price_ord = 'priceDESC';
	
	# проверяем изменена ли сортировка, которая установлена по умолчанию
	if ( !empty($sort) && ( $sort == 'name' || $sort == 'nameDESC' || $sort == 'price' || $sort == 'priceDESC') ) {
		switch ($sort) {
			case 'name': $ord = 'name';
					$n_selected = 'selected';
					break;
			case 'nameDESC': $ord = 'nameDESC';
					$nd_selected = 'selected';
					break;
			case 'price': $ord = 'price';
					$p_selected = 'selected';
					break;
			case 'priceDESC': $ord = 'priceDESC';
					$pd_selected = 'selected';
					break;
		}
	}
	
	# заменяем макросы на значения и выводим
	return str_replace(
			array('{URL}','{SORT_NAME}','{SORT_PRICE}','{N_SELECTED}','{ND_SELECTED}','{P_SELECTED}','{PD_SELECTED}'),
			array('/digiseller/'.$idn, $name_ord, $price_ord, $n_selected, $nd_selected, $p_selected, $pd_selected),
			$page);
}

/**
 * функция вывода списка найденных товаров по заданному критерию
 *
 */
function SearchGoods($sellerID, $rows){

        # получаем поисковый запрос переданный через GET-запрос
	if(!@empty($_GET['query'])){
		$search_str = trim($_GET['query']);
		$s_empty = '';
	}else{
		$search_str = '';
		$s_empty = '<br />Задан пустой поисковый запрос.';
	}
        
        if ( !empty($_GET['page']) && is_numeric($_GET['page']) ) {
		$cur_page = (int) trim($_GET['page']);
	} else {
		$cur_page = 1;
	}

	# адрес запроса
	$host  = 'http://shop.digiseller.ru/xml/personal_search_goods.asp';
	
	# формируем текст запроса
	$entry = '<?xml version="1.0" encoding="windows-1251"?>'
			.'<digiseller.request>'
			.'<id_seller>'.$sellerID.'</id_seller>'
			.'<search_str>'.$search_str.'</search_str>'
			.'<cnt_goods>1000</cnt_goods>'
			.'<page>'.CurrentPage($cur_page).'</page>'
			.'<rows>'.$rows.'</rows>'
			.'</digiseller.request>';
	
	# отправляем запрос и переводим XML-ответ в массив
	$base = new SimpleXMLElement( SendRequest($host,$entry) );
	
	# если не найдено ни одного товара выводим надпись о том что найдено "0"
	# иначе выводим таблицу со списком найденных товаров
	if ( $base->cnt_goods == '0' ) {
		return '<p align="left"><strong>найдено товаров - 0. '.$s_empty.'<strong></p>';

	} else {
		# в зависимости от кода выполнения запроса делаем соответствующие действия
		if ( $base->retval == '0' ) {
			
			# загружаем шаблон таблицы списка найденых товаров
                        $page = implode('', file(PATH.'/components/digiseller/template/xmlListSearchGoods.html') );

			# выбираем шаблон-строку по комментариям-меткам
			preg_match_all("/<!-- begin shablon -->.*<!-- end shablon -->/isU",$page,$tpl);
			
			# если удачно выбрали шаблон-строку и количество найденых товаров больше чем 0,
			# тогда формируем строки таблицы
			if ( !empty($tpl[0][0]) && $base->cnt_goods>0 ) {
				# создаем массив с макросами в шаблоне
				$macrosArray = array('{GOODS_NAME}', '{PRICE}');
				
				for ( $i=0; $i<$base->rows['cnt']; $i++ ) {
					# создаем массив с данными и формируем строку, заменяя макросы на данные.
					if ( empty($base->rows->row[$i]->price) ) {
						$base->rows->row[$i]->price = 'нет';
					}
					$valueArray  = array('<a href="/digiseller/goods/'.$base->rows->row[$i]->id_goods.'/search?query='.$search_str.'">'.iconv('UTF-8', 'windows-1251', $base->rows->row[$i]->name_goods).'</a>',
					                     $base->rows->row[$i]->price);
					$lines .= str_replace( $macrosArray, $valueArray, $tpl[0][0] );
				}
				
				# создаем список страниц
				for ( $i=0; $i<$base->pages; $i++ ) {
					if ( CurrentPage($cur_page) == ($i+1) ) {
						$list_pages .= "<font class=\"listing\">".($i+1)."</font>&nbsp;";
					} else {
						$list_pages .= '<a href="'.$search_result.'?query='.$search_str.'&page='.($i+1).'">'.($i+1).'</a>&nbsp; ';
					}
				}
				
				# заменяем шаблоны значениями и выводим таблицу
				return preg_replace("/<!-- begin shablon -->.*<!-- end shablon -->/isU", $lines, str_replace('{LIST_PAGES}', $list_pages, $page) );
			}
		}else{
			# если код выполнения не "0" (запрос выполнен) выводим это сообщение
			if ( !empty($base->retdesc) ) {
				ShowErrorMessage( iconv('UTF-8', 'windows-1251', $base->retdesc) );
			}else{
				ShowErrorMessage('Не могу подключиться к сервису DigiSeller.');
			}
		}
	}
}

/**
 * функция вывода поисковой формы
 *
 */
function ShowSearchForm($sellerID, $rows){
	
	# получаем поисковый запрос переданный через GET-запрос
	if ( !empty($_GET['query']) ) {
		$search_str = trim($_GET['query']);
	} else {
		$search_str = '';
	};
	
	# загружаем шаблон с формой поиска
        $page = implode( '', file(PATH.'/components/digiseller/template/xmlSearchGoods.html') );
	
	# заменяем макросы на значения и выводим форму поиска и количество найденых товаров
	return str_replace(array('{SEARCH_URL}', '{SEARCH_STRING}'), array('/digiseller/search', $search_str), $page);
}

/**
 * функция вывода описание товара + форма запроса на оплату
 *
 */
function GoodsInfo($id_goods, $sellerID, $response_rows, $responses, $cur_page){

		# адрес запроса
		$host  = 'http://shop.digiseller.ru/xml/personal_goods_info.asp';
		
                # получаем поисковый запрос переданный через GET-запрос
		if ( !empty($_GET['query']) ) {
			$search_str = trim($_GET['query']);
		} else {
			$search_str = '';
		};
                
		# формируем текст запроса
		$entry = '<?xml version="1.0" encoding="windows-1251"?>'
			.'<digiseller.request>'
			.'<id_goods>'.$id_goods.'</id_goods>'
			.'<search_str>'.$search_str.'</search_str>'
			.'</digiseller.request>';
			
		# отправляем запрос и переводим XML-ответ в массив
		$base = new SimpleXMLElement( str_replace( array('<![CDATA[', ']]>'), array('',''), SendRequest($host,$entry) ) );
		$item = array();
		# в зависимости от кода выполнения запроса делаем соответствующие действия
		if ( $base->retval == '0' ) {
			/** Устанавливаем значения макросов */
			
			# название товара
			$tplArray['{GOODSNAME}'] = str_replace( array('[ss]','[/ss]'), array('<span style="color:white;background-color:red;">','</span>'), iconv('UTF-8', 'windows-1251', $base->name_goods) );
			
			# ID товара
			$tplArray['{ID_GOODS}'] = $id_goods;
			
			# цена товара
			$tplArray['{GOODSPRICE}'] = $base->price_goods->wmz.' $';
			
			# информация о товаре
			$tplArray['{GOODSINFO}'] = str_replace( array('[ss]','[/ss]'), array('<span style="color:white;background-color:red;">','</span>'), trim( nl2br( unhtmlentities( trim( iconv('UTF-8', 'windows-1251', $base->info_goods) ) ) ) ) );
			
			# дополнительная информацию о товаре
			$tplArray['{GOODSINFOADDITION}'] = str_replace( array('[ss]','[/ss]'), array('<span style="color:white;background-color:red;">','</span>'), trim( nl2br( unhtmlentities( trim( iconv('UTF-8', 'windows-1251', $base->add_info_goods) ) ) ) ) );
			
			# цена для покупки в кредит
			$tplArray['{GOODSCREDITPRICE}'] = trim($base->credit_price->wmz);
			
			# срок для покупки в кредит
			$tplArray['{GOODSCREDITDAYS}'] = trim($base->credit_period);
			
			# создаем Fail Page для возвращения после отказа от оплаты или ошибки при оплате
			$tplArray['{FAIL_PAGE}'] = HOST.'/digiseller/goods/'.$id_goods;
                        
			
			# ID агента
			$tplArray['{ID_AGENT}'] = $sellerID;
			
			# устанавливаем сумму и процент для скидок, если они есть
			if ( $base->discount == 'yes' ) {
				$tplArray['{GOODSDISCOUNT}'] = 'На товар предоставляется скидка постоянным покупателям.';
			} else {
				$tplArray['{GOODSDISCOUNT}'] = '';
			}
			
			# устанавливаем тип товара и время загрузки
			if ( $base->type_goods == 'text' ) {
				$tplArray['{GOODSTYPE}']     = 'текстовая информация ('.$base->text_info->size.' байт)';
				$tplArray['{GOODSDATALOAD}'] = substr($base->text_info->date_put, 0, 10);
			} else {
				$tplArray['{GOODSTYPE}']     = $base->file_info->name.' ('.$base->file_info->size.' байт)';
				$tplArray['{GOODSDATALOAD}'] = substr($base->file_info->date_put, 0, 10);
			}
			
			# устанавливаем статистику товара
			if ( $base->statistics->cnt_goodresponses>0 ) {
				$good_href_begin = '<a href="/digiseller/goods/'.$id_goods.'/responses-good" style="color: green; font-weight: bold;">';
				$good_href_end = '</a>';
			} else {
				$good_href_begin = '<span  style="color: green; font-weight: bold;">';
				$good_href_end = '</span>';
			}
			if ( $base->statistics->cnt_badresponses>0 ) {
				$bad_href_begin = '<a href="/digiseller/goods/'.$id_goods.'/responses-bad" style="color:red; font-weight: bold;">';
				$bad_href_end = '</a>';
			} else {
				$bad_href_begin = '<span  style="color:red; font-weight: bold;">';
				$bad_href_end = '</span>';
			}
			$tplArray['{GOODSSTATISTIC}'] = 'количество продаж: <strong>'.$base->statistics->cnt_sell.'</strong><br />'
							.'количество возвратов: <strong style="color:red;">'.$base->statistics->cnt_return.'</strong><br />'
							.'отзывы покупателей:<br />'
							.'<span style="margin-left: 15px;">положительных - '.$good_href_begin.$base->statistics->cnt_goodresponses.$good_href_end.'</span><br />'
							.'<span style="margin-left: 15px;">отрицательных - '.$bad_href_begin.$base->statistics->cnt_badresponses.$bad_href_end.'</span><br />'
							.GoodsResponses($id_goods, $sellerID, $response_rows, $responses, $cur_page);
			
			# устанавливаем предосмотр для товара если он есть
			if ( $base->previews_goods['cnt']>0 ) {
				foreach ( $base->previews_goods->preview_goods as $k ) {
					$tplArray['{GOODSPREVIEW}'] .= '<a href="'.$k->img_real.'" target="blank" alt="Увеличить">'
					.'<img src="'.$k->img_small.'" width="'.$k->width_small.'" height="'.$k->height_small.'" border="0">'
					.'</a>'; 
				}
				$tplArray['{GOODSPREVIEW}'] .= '<br /><br />';
			} else {
				$tplArray['{GOODSPREVIEW}'] = '';
			}
			
			# устанавливаем значение subTITLE и subDESCRIPTION
			$item['title'] = strip_tags($tplArray['{GOODSNAME}']);
			$item['description'] = substr( str_replace( array("\r","\n","\t"), array('','',''), strip_tags( $tplArray['{GOODSINFO}'] ) ), 0, 400 );
			
			# загружаем шаблон таблицы
                        $page = implode( '', file(PATH.'/components/digiseller/template/xmlGoodsInfo.html') );
			
			# выбираем шаблон-строку по комментариям-меткам
			preg_match_all("/<!-- begin shablon -->.*<!-- end shablon -->/isU", $page, $tpl);
			
			# если удачно выбрали шаблон-строки, формируем строки таблицы
			if ( count($tpl[0])>0 ) {
				for ( $i=0; $i<count($tpl[0]); $i++) {
					# заменяем в шаблоне "переменную" на ее значение, если оно не нулевое
					# если нулевое, тогда шаблон к выводу не добавляем
					preg_match_all( '/\{.*\}/isU', $tpl[0][$i], $tmp);
					if ( !empty($tplArray[trim($tmp[0][0])]) || !empty($tplArray[trim($tmp[0][1])]) ) {
						for ( $j=0; $j<count($tmp[0]); $j++ ) {
							$tpl[0][$i] = str_replace($tmp[0][$j],$tplArray[trim($tmp[0][$j])],$tpl[0][$i]);
						}
						$result .= $tpl[0][$i];
					}
				}
				
				# выводим таблицу "Инфомация о товаре"
				$item['result'] = preg_replace("/<!-- begin shablon -->.*<!-- end shablon -->/is", $result, str_replace( array('{ID_GOODS}','{ID_AGENT}','{FAIL_PAGE}'), array($tplArray['{ID_GOODS}'], $sellerID, $tplArray['{FAIL_PAGE}'] ), $page ) );
                                return $item;
			}
		} else {
			# если код выполнения не "0" (запрос выполнен) выводим это сообщение
			if ( !empty($base->retdesc) ){
				return iconv( 'UTF-8', 'windows-1251', $base->retdesc);
			} else {
				ShowErrorMessage('Не могу подключиться к сервису DigiSeller.');
			}
		}
}

function GoodsResponses($id_goods, $sellerID, $response_rows, $responses, $cur_page) {
	
	# получаем тип отзывов
	if ( !empty($responses) && ( $responses == 'good' || $responses == 'bad') ) {
		
		# URL для XML-запроса
		$xmlhost  = 'http://shop.digiseller.ru/xml/personal_responses.asp';
		
		# формируем текст запроса
		$entry = '<digiseller.request>'
			.'<id_seller>'.$sellerID.'</id_seller>'
			.'<id_goods>'.$id_goods.'</id_goods>'
			.'<type_response>'.$responses.'</type_response>'
			.'<page>'.CurrentPage($cur_page).'</page>'
			.'<rows>'.$response_rows.'</rows>'
			.'</digiseller.request>';
			
		# отправляем запрос и переводим XML-ответ в массив
		$base = new SimpleXMLElement( SendRequest($xmlhost,$entry) );
		
		# загружаем шаблон таблицы
		$response_tpl = implode( "", file(PATH.'/components/digiseller/template/xmlGoodsResponse.html') );
		
		# выбираем шаблон-строку по комментариям-меткам
		preg_match_all("/<!-- begin shablon -->.*<!-- end shablon -->/isU", $response_tpl, $tpl );
		
		if ( $base->rows['cnt']>0 && !empty($tpl[0][0]) ) {
			foreach ( $base->rows->row as $k ) {
				$result .= str_replace( array('{DATE}', '{RESPONSE}', '{ADMIN_RESPONSE}'), array(substr($k->date_response, 0, 10), iconv('UTF-8', 'windows-1251', $k->text_response), iconv('UTF-8', 'windows-1251', $k->comment) ), $tpl[0][0] );
			}
			
			# создаем список страниц с отзывами
			if ( $base->pages > 1 ) {
				$list_pages = 'Страницы: ';
				for ( $i=1; $i<=$base->pages; $i++ ) {
					if ( CurrentPage($cur_page) == $i ) {
						$list_pages .= '<span style="color:#ffffff;background-color:#B2B2B2;">&nbsp;'.$i.'&nbsp;</span>&nbsp;';
					} else {
						$list_pages .= '<a href="/digiseller/goods/'.$id_goods.'/responses-'.$responses.'/page-'.$i.'" title="Отзывы. Страница: '.$i.'">'.$i.'</a>&nbsp;';
					}
				}
			}

			return preg_replace( '/<!-- begin shablon -->.*<!-- end shablon -->/isU', $result, str_replace( '{LIST_PAGES}', $list_pages, $response_tpl ) );
		}
	}
}

/////////////////////////////////////////
/* ДАЛЕЕ СПЕЦИФИЧНЫЕ АГЕНТСКИЕ ФУНКЦИИ */
/////////////////////////////////////////

/**
 * функция вывода списка разделов товаров
 *
 */
function ShowMenuAgent($agentID, $menuOrder){

	# адрес запроса
	$host  = 'http://shop.digiseller.ru/xml/agent_groups.asp';
	
	# формируем текст запроса
	$entry = '<?xml version="1.0" encoding="windows-1251"?>'
		.'<digiseller.request>'
		.'<id_agent>'.$agentID.'</id_agent>'
		.'<order>'.$menuOrder.'</order>'
		.'</digiseller.request>';
	
	# отправляем запрос и переводим XML-ответ в массив
	$base = new SimpleXMLElement( SendRequest($host, $entry) );
	
	# в зависимости от кода выполнения запроса делаем соответствующие действия
	if ( $base->retval == '0') {
		foreach ( $base->rows->row as $k ) {
			$result .= '<li><a href="/digiseller/products/'.$k->id_group.'" title="'.iconv('UTF-8', 'windows-1251', $k->name_group).'">'.iconv('UTF-8', 'windows-1251', $k->name_group).'</a></li>';
		}
	} else {
		if ( !empty( $base->retdesc ) ) {
			echo iconv('UTF-8', 'windows-1251', $base->retdesc);
			die();
		}
	}
	
	return $result;
}

/**
 * функция вывода поисковой формы
 *
 */
function ShowSearchFormAgent($sellerID){
	
	# загружаем шаблон с формой поиска
        $page = implode( '', file(PATH.'/components/digiseller/template/xmlSearchAgentForm.html') );
	
	# заменяем макросы на значения и выводим форму поиска и количество найденых товаров
	return str_replace(array('{AGENT}'), array($sellerID), $page);
}

/**
 * функция вывода списка товаров по заданному разделу
 *
 */
function ListProducts($id_group, $rows, $sort, $cur_page, $sellerID, $menuOrder, $descr_link){

	# устанавливаем виды сортировок по умолчанию 
	$ord = 'name';
	$name_ord = 'nameDESC';
	$price_ord = 'priceDESC';
	
	# проверяем изменена ли сортировка, которая установлена по умолчанию, если изменена применяем ее
	if((@!empty($sort))and((@$sort == 'name')or(@$sort == 'nameDESC')or($sort == 'price')or($sort == 'priceDESC'))){
		switch($sort){
			case 'name': $ord = 'name';
			             $name_ord ='nameDESC';
			             $n_selected = 'selected';
			             break;
			case 'nameDESC': $ord = 'nameDESC';
			                 $name_ord ='name';
			                 $nd_selected = 'selected';
			                 break;
			case 'price': $ord = 'price';
			              $price_ord ='priceDESC';
			              $p_selected = 'selected';
			              break;
			case 'priceDESC': $ord = 'priceDESC';
			                  $price_ord ='price';
			                  $pd_selected = 'selected';
			                  break;
		}
	}
	
	# адрес запроса
	$host  = 'http://shop.digiseller.ru/xml/agent_goods.asp';
	
	# формируем текст запроса
	$entry = '<?xml version="1.0" encoding="windows-1251"?>'
		.'<digiseller.request>'
		.'<id_group>'.$id_group.'</id_group>'
		.'<page>'.CurrentPage($cur_page).'</page>'
		.'<rows>'.$rows.'</rows>'
		.'<order>'.$ord.'</order>'
		.'</digiseller.request>';
	
	# отправляем запрос и переводим XML-ответ в массив
	$base = new SimpleXMLElement( SendRequest($host, $entry) );
        
	$item = array();//массив который будем возвращать
	# в зависимости от кода выполнения запроса делаем соответствующие действия
	if ( $base->retval == '0') {
		# если передали ID группы товаров - значит страница не главная и
		# устанавливаем динамический TITLE страницы, как название раздела
		# иначе (если страница главная) делаем его пустым
		if ( !empty($id_group) and is_numeric($id_group) ) {
			$item['title'] = set_title_agent($id_group, $sellerID, $menuOrder);
		}
		
		# загружаем шаблон таблицы
                $page = implode('', file(PATH.'/components/digiseller/template/xmlListGoods.html') );
		
		# выбираем шаблон-строку по комментариям-меткам
		preg_match_all("/<!-- begin shablon -->.*<!-- end shablon -->/isU", $page, $tpl);
		
		# если удачно выбрали шаблон-строку и в заданной группе на заданной странице
		# есть товары, тогда формируем строки таблицы
		if ( ( !empty( $tpl[0][0] ) ) && ( $base->rows['cnt'] > 0 ) ) {
			# создаем массив с макросами в шаблоне
			$macrosArray = array('{GOODS_NAME}', '{PRICE}');
			
			for ($i=0; $i<$base->rows['cnt']; $i++) {
				# создаем массив с данными и формируем строку, заменяя макросы на данные.
				if ( empty( $base->rows->row[$i]->price ) ) {
					$base->rows->row[$i]->price = 'нет';
				}
				$valueArray  = array('<a href="'.str_replace('{ID_GOODS}', $base->rows->row[$i]->id_goods, $descr_link).'&agent='.$sellerID.'" target="_blank">'.iconv('UTF-8', 'windows-1251', $base->rows->row[$i]->name_goods).'</a>',
				                     $base->rows->row[$i]->price);
				$lines .= str_replace($macrosArray, $valueArray, $tpl[0][0]);
			}
			
                        # создаем список страниц
			for ( $i=0; $i < $base->pages; $i++ ) {
				if(CurrentPage($cur_page) == ($i+1)){
					$list_pages .= '<span style="color:#ffffff;background-color:#B2B2B2;">&nbsp;'.($i+1)."&nbsp;</span>&nbsp;";
				}else{
					$list_pages .= '<a href="/digiseller/products/'.$id_group.'/page-'.($i+1).'">'.($i+1).'</a>&nbsp;';
				}
			}
                        
                        //url в зависимоcти от пагинации
                        if(CurrentPage($cur_page)!=1){
                            $url = "/digiseller/products/".$id_group."/page-".$cur_page;
                        } else {
                            $url = "/digiseller/products/".$id_group;
                        }
			
			# заменяем шаблоны значениями и выводим таблицу
			$result = str_replace(
                                array('{URL}', '{SORT_NAME}', '{SORT_PRICE}', '{LIST_PAGES}'),
				array($url, $name_ord, $price_ord, $list_pages),
				$page);
			$item['result'] = preg_replace( "/<!-- begin shablon -->.*<!-- end shablon -->/isU",$lines, $result );
                        return $item;

		}
	}else{
		# если код выполнения не "0" (запрос выполнен) выводим это сообщение
		if ( !empty( $base->retdesc ) ) {
			ShowErrorMessage( $base->retdesc );
		}else{
			ShowErrorMessage( 'Не могу подключиться к сервису DigiSeller.' );
		}
	}
}

function set_title_agent($id_group, $sellerID, $menuOrder){
	
	# адрес запроса
	$host  = 'http://shop.digiseller.ru/xml/agent_groups.asp';

	# формируем текст запроса
	$entry = '<?xml version="1.0" encoding="windows-1251"?>'
		.'<digiseller.request>'
		.'<id_agent>'.$sellerID.'</id_agent>'
		.'<order></order>'
		.'</digiseller.request>';
	
	# отправляем запрос и переводим XML-ответ в массив
	$base = new SimpleXMLElement( SendRequest($host, $entry) );
	
	# в зависимости от кода выполнения запроса делаем соответствующие действия
	if ( $base->retval == '0') {
		foreach ( $base->rows->row as $k ) {
			if ( $id_group == $k->id_group ) {
				$name = iconv('UTF-8', 'windows-1251', $k->name_group);
				break;
			}
		}
	}

	return $name;
}
/**
 * функция вывода выпадающего меню для сортировки списка товаров
 *
 */
function ProductsSort($idn, $sort){
	
	# загружаем шаблон таблицы
	$page = implode( '', file(PATH.'/components/digiseller/template/xmlGoodsSort.html') );
	
	# устанавливаем виды сортировок по умолчанию
	$ord = 'name';
	$name_ord = 'nameDESC';
	$price_ord = 'priceDESC';
	
	# проверяем изменена ли сортировка, которая установлена по умолчанию
	if ( !empty($sort) && ( $sort == 'name' || $sort == 'nameDESC' || $sort == 'price' || $sort == 'priceDESC') ) {
		switch ($sort) {
			case 'name': $ord = 'name';
					$n_selected = 'selected';
					break;
			case 'nameDESC': $ord = 'nameDESC';
					$nd_selected = 'selected';
					break;
			case 'price': $ord = 'price';
					$p_selected = 'selected';
					break;
			case 'priceDESC': $ord = 'priceDESC';
					$pd_selected = 'selected';
					break;
		}
	}
	
	# заменяем макросы на значения и выводим
	return str_replace(
			array('{URL}','{SORT_NAME}','{SORT_PRICE}','{N_SELECTED}','{ND_SELECTED}','{P_SELECTED}','{PD_SELECTED}'),
			array('/digiseller/products/'.$idn, $name_ord, $price_ord, $n_selected, $nd_selected, $p_selected, $pd_selected),
			$page);
}
?>