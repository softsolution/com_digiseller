<?php

/**
 * ������� ������� ������� �� ������ DigiSeller'� � ��������� XML-������
 *
 */
function SendRequest($URL, $REQUEST, $send_post_type){
	
	# � ����������� �� ���������������� �������� �������� ����� CURL ��� ������
	if ( $send_post_type == 1 ) {
		return POST_curl($URL, $REQUEST);
	} else {
		return  POST_socket($URL, $REQUEST);
	}
}

/**
 * CURL-������� ��� ������� ������� �� ������ DigiSeller'� � ��������� XML-������
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
		echo '�� ���� �������� ��� ����������� ������������ ���������� cURL';
		die();
	}
}

/**
 * socket-������� ��� ������� ������� �� ������ DigiSeller'� � ��������� XML-������
 *
 */
function POST_socket($URL, $REQUEST) {
	# �������� ������������ �� URL: host � path
	$URL = parse_url($URL);
	
	$fp = fsockopen ( $URL['host'], 80, $errno, $errstr, 10 );
	if ( !$fp ) {
		echo "�������� ������ ��� ������������� �������.<br /> $errstr ($errno)";
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
	
	# �������� ��������� �� "����" XML-������
	list($tmp, $result) = explode("\r\n\r\n", $result, 2);
	
	return $result;
}

/**
 * ������� �������� html-���������� � �������. ������� �������� ������� htmlentities()
 *
 */
function unhtmlentities ($string){
	$trans_tbl = get_html_translation_table (HTML_ENTITIES);
	$trans_tbl = array_flip ($trans_tbl);
	
	return strtr ($string, $trans_tbl);
}

/**
 * ������� ������ �������� ������
 *
 */
function ShowErrorMessage($message){
	echo '<center>'
	.'<div align="center" style="color:red; border:1px dashed red; font-weight:bold; width:400; float:center; padding:20px 20px 20px 20px;">'
	.$message
	.'</div></center>';
}

/**
 * ������� ��������� ������ �������� ������� ����������
 *
 */
function CurrentPage($cur_page){
	# �������� ����� �������� ������� ���������� (���� �� ������� ����� ��������,
	# ����� ������������ ������ ��������)
        if ( !empty($cur_page) && is_numeric($cur_page) ) {
		return $cur_page;
	} else {
		$cur_page = 1;
	}
	return $cur_page;
}

/**
 * ������� ������ �������� �������
 *
 */
function set_title($id_group, $sellerID, $menuOrder){
	
	# ����� �������
	$host  = 'http://shop.digiseller.ru/xml/personal_groups.asp';
	
	# ��������� ����� �������
	$entry = '<?xml version="1.0" encoding="windows-1251"?>'
		.'<digiseller.request>'
		.'<id_seller>'.$sellerID.'</id_seller>'
		.'<order></order>'
		.'</digiseller.request>';
	
	# ���������� ������ � ��������� XML-����� � ������
	$base = new SimpleXMLElement( SendRequest($host, $entry) );
	
	# � ����������� �� ���� ���������� ������� ������ ��������������� ��������
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
 * ������� ������ ������ �������� �������
 *
 */
function ShowMenu($sellerID, $menuOrder, $send_post_type){
	
	# ����� �������
	$host  = 'http://shop.digiseller.ru/xml/personal_groups.asp';
	
	# ��������� ����� �������
	$entry = '<?xml version="1.0" encoding="windows-1251"?>'
		.'<digiseller.request>'
		.'<id_seller>'.$sellerID.'</id_seller>'
		.'<order>'.$menuOrder.'</order>'
		.'</digiseller.request>';
	
	# ���������� ������ � ��������� XML-����� � ������
	$base = new SimpleXMLElement( SendRequest($host, $entry, $send_post_type) );
	
	# � ����������� �� ���� ���������� ������� ������ ��������������� ��������
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
 * ������� ������ ������ ������� �� ��������� �������
 *
 */
function ListGoods($id_group, $rows, $sort, $cur_page, $sellerID, $menuOrder){

	# ������������� �� ��������� ���� ����������
	$ord = 'name';
	$name_ord = 'nameDESC';
	$price_ord = 'priceDESC';
	
	# ��������� �������� �� ����������, ������� ����������� �� ���������
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
	
	# ����� �������
	$host  = 'http://shop.digiseller.ru/xml/personal_goods.asp';
	
	# ��������� ����� �������
	$entry = '<digiseller.request>'
			.'<id_group>'.$id_group.'</id_group>'
			.'<page>'.CurrentPage($cur_page).'</page>'
			.'<rows>'.$rows.'</rows>'
			.'<order>'.$ord.'</order>'
			.'</digiseller.request>';
	
	# ���������� ������ � ��������� XML-����� � ������
	$base = new SimpleXMLElement( SendRequest($host, $entry) );
	
        $item = array();//������ ������� ����� ����������
	# � ����������� �� ���� ���������� ������� ������ ��������������� ��������
	if ( $base->retval == '0' ) {
		# ���� �������� ID ������ ������� - ������ �������� �� ������� �
		# ������������� ������������ TITLE ��������, ��� �������� �������
		# ����� (���� �������� �������) ������ ��� ������
		if (!empty($id_group) ) {
			$item['title'] = set_title($id_group, $sellerID, $menuOrder);
		}
		
		# ��������� ������ �������
		$page = implode('', file(PATH.'/components/digiseller/template/xmlListGoods.html') );
		
		# �������� ������-������ �� ������������-������
		preg_match_all("/<!-- begin shablon -->.*<!-- end shablon -->/isU", $page, $tpl);
		
		# ���� ������ ������� ������-������ � � �������� ������ �� �������� ��������
		# ���� ������, ����� ��������� ������ �������
		if ( !empty($tpl[0][0]) && $base->cnt_goods>0 ){
			# ������� ������ � ��������� � �������
			$macrosArray = array('{GOODS_NAME}', '{PRICE}');
			
			for ( $i=0; $i<$base->rows['cnt']; $i++ ) {
				# ������� ������ � ������� � ��������� ������, ������� ������� �� ������.
				if ( empty($base->rows->row[$i]->price) ) {
					$base->rows->row[$i]->price = '���';
				}
				$valueArray  = array('<a href="/digiseller/goods/'.$base->rows->row[$i]->id_goods.'" title="'.iconv('UTF-8', 'windows-1251', $base->rows->row[$i]->name_goods).'">'.iconv('UTF-8', 'windows-1251', $base->rows->row[$i]->name_goods).'</a>',
							$base->rows->row[$i]->price);
				$lines .= str_replace($macrosArray, $valueArray, $tpl[0][0]);
			}
			
			# ������� ������ ������� � ��������
			for($i=1; $i<=$base->pages; $i++) {
				if ( CurrentPage($cur_page) == $i ) {
					$list_pages .= '<span style="color:#ffffff;background-color:#B2B2B2;">&nbsp;'.$i.'&nbsp;</span>&nbsp;';
				} else {
					$list_pages .= '<a href="/digiseller/'.$id_group.'/page-'.$i.'">'.$i.'</a>&nbsp;';
				}
			}
                        
			//url � ��������c�� �� ���������
                        if(CurrentPage($cur_page)!=1){
                            $url = "/digiseller/".$id_group."/page-".$cur_page;
                        } else {
                            $url = "/digiseller/".$id_group;
                        }
                        
			# �������� ������� ���������� � ������� �������
			$result = str_replace(
				array('{URL}', '{SORT_NAME}', '{SORT_PRICE}', '{LIST_PAGES}'),
				array($url, $name_ord, $price_ord, $list_pages),
				$page);
			$item['result'] = preg_replace("/<!-- begin shablon -->.*<!-- end shablon -->/isU", $lines, $result);
                        return $item;
		}
	}else{
		# ���� ��� ���������� �� "0" (������ ��������) ������� ��� ���������
		if ( !empty($base->retdesc) ) {
			ShowErrorMessage( iconv('UTF-8', 'windows-1251', $base->retdesc) );
		} else {
			ShowErrorMessage('�� ���� ������������ � ������� DigiSeller.');
		}
	}
}

/**
 * ������� ������ ����������� ���� ��� ���������� ������ �������
 *
 */
function GoodsSort($idn, $sort){
	
	# ��������� ������ �������
	$page = implode( '', file(PATH.'/components/digiseller/template/xmlGoodsSort.html') );
	
	# ������������� ���� ���������� �� ���������
	$ord = 'name';
	$name_ord = 'nameDESC';
	$price_ord = 'priceDESC';
	
	# ��������� �������� �� ����������, ������� ����������� �� ���������
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
	
	# �������� ������� �� �������� � �������
	return str_replace(
			array('{URL}','{SORT_NAME}','{SORT_PRICE}','{N_SELECTED}','{ND_SELECTED}','{P_SELECTED}','{PD_SELECTED}'),
			array('/digiseller/'.$idn, $name_ord, $price_ord, $n_selected, $nd_selected, $p_selected, $pd_selected),
			$page);
}

/**
 * ������� ������ ������ ��������� ������� �� ��������� ��������
 *
 */
function SearchGoods($sellerID, $rows){

        # �������� ��������� ������ ���������� ����� GET-������
	if(!@empty($_GET['query'])){
		$search_str = trim($_GET['query']);
		$s_empty = '';
	}else{
		$search_str = '';
		$s_empty = '<br />����� ������ ��������� ������.';
	}
        
        if ( !empty($_GET['page']) && is_numeric($_GET['page']) ) {
		$cur_page = (int) trim($_GET['page']);
	} else {
		$cur_page = 1;
	}

	# ����� �������
	$host  = 'http://shop.digiseller.ru/xml/personal_search_goods.asp';
	
	# ��������� ����� �������
	$entry = '<?xml version="1.0" encoding="windows-1251"?>'
			.'<digiseller.request>'
			.'<id_seller>'.$sellerID.'</id_seller>'
			.'<search_str>'.$search_str.'</search_str>'
			.'<cnt_goods>1000</cnt_goods>'
			.'<page>'.CurrentPage($cur_page).'</page>'
			.'<rows>'.$rows.'</rows>'
			.'</digiseller.request>';
	
	# ���������� ������ � ��������� XML-����� � ������
	$base = new SimpleXMLElement( SendRequest($host,$entry) );
	
	# ���� �� ������� �� ������ ������ ������� ������� � ��� ��� ������� "0"
	# ����� ������� ������� �� ������� ��������� �������
	if ( $base->cnt_goods == '0' ) {
		return '<p align="left"><strong>������� ������� - 0. '.$s_empty.'<strong></p>';

	} else {
		# � ����������� �� ���� ���������� ������� ������ ��������������� ��������
		if ( $base->retval == '0' ) {
			
			# ��������� ������ ������� ������ �������� �������
                        $page = implode('', file(PATH.'/components/digiseller/template/xmlListSearchGoods.html') );

			# �������� ������-������ �� ������������-������
			preg_match_all("/<!-- begin shablon -->.*<!-- end shablon -->/isU",$page,$tpl);
			
			# ���� ������ ������� ������-������ � ���������� �������� ������� ������ ��� 0,
			# ����� ��������� ������ �������
			if ( !empty($tpl[0][0]) && $base->cnt_goods>0 ) {
				# ������� ������ � ��������� � �������
				$macrosArray = array('{GOODS_NAME}', '{PRICE}');
				
				for ( $i=0; $i<$base->rows['cnt']; $i++ ) {
					# ������� ������ � ������� � ��������� ������, ������� ������� �� ������.
					if ( empty($base->rows->row[$i]->price) ) {
						$base->rows->row[$i]->price = '���';
					}
					$valueArray  = array('<a href="/digiseller/goods/'.$base->rows->row[$i]->id_goods.'/search?query='.$search_str.'">'.iconv('UTF-8', 'windows-1251', $base->rows->row[$i]->name_goods).'</a>',
					                     $base->rows->row[$i]->price);
					$lines .= str_replace( $macrosArray, $valueArray, $tpl[0][0] );
				}
				
				# ������� ������ �������
				for ( $i=0; $i<$base->pages; $i++ ) {
					if ( CurrentPage($cur_page) == ($i+1) ) {
						$list_pages .= "<font class=\"listing\">".($i+1)."</font>&nbsp;";
					} else {
						$list_pages .= '<a href="'.$search_result.'?query='.$search_str.'&page='.($i+1).'">'.($i+1).'</a>&nbsp; ';
					}
				}
				
				# �������� ������� ���������� � ������� �������
				return preg_replace("/<!-- begin shablon -->.*<!-- end shablon -->/isU", $lines, str_replace('{LIST_PAGES}', $list_pages, $page) );
			}
		}else{
			# ���� ��� ���������� �� "0" (������ ��������) ������� ��� ���������
			if ( !empty($base->retdesc) ) {
				ShowErrorMessage( iconv('UTF-8', 'windows-1251', $base->retdesc) );
			}else{
				ShowErrorMessage('�� ���� ������������ � ������� DigiSeller.');
			}
		}
	}
}

/**
 * ������� ������ ��������� �����
 *
 */
function ShowSearchForm($sellerID, $rows){
	
	# �������� ��������� ������ ���������� ����� GET-������
	if ( !empty($_GET['query']) ) {
		$search_str = trim($_GET['query']);
	} else {
		$search_str = '';
	};
	
	# ��������� ������ � ������ ������
        $page = implode( '', file(PATH.'/components/digiseller/template/xmlSearchGoods.html') );
	
	# �������� ������� �� �������� � ������� ����� ������ � ���������� �������� �������
	return str_replace(array('{SEARCH_URL}', '{SEARCH_STRING}'), array('/digiseller/search', $search_str), $page);
}

/**
 * ������� ������ �������� ������ + ����� ������� �� ������
 *
 */
function GoodsInfo($id_goods, $sellerID, $response_rows, $responses, $cur_page){

		# ����� �������
		$host  = 'http://shop.digiseller.ru/xml/personal_goods_info.asp';
		
                # �������� ��������� ������ ���������� ����� GET-������
		if ( !empty($_GET['query']) ) {
			$search_str = trim($_GET['query']);
		} else {
			$search_str = '';
		};
                
		# ��������� ����� �������
		$entry = '<?xml version="1.0" encoding="windows-1251"?>'
			.'<digiseller.request>'
			.'<id_goods>'.$id_goods.'</id_goods>'
			.'<search_str>'.$search_str.'</search_str>'
			.'</digiseller.request>';
			
		# ���������� ������ � ��������� XML-����� � ������
		$base = new SimpleXMLElement( str_replace( array('<![CDATA[', ']]>'), array('',''), SendRequest($host,$entry) ) );
		$item = array();
		# � ����������� �� ���� ���������� ������� ������ ��������������� ��������
		if ( $base->retval == '0' ) {
			/** ������������� �������� �������� */
			
			# �������� ������
			$tplArray['{GOODSNAME}'] = str_replace( array('[ss]','[/ss]'), array('<span style="color:white;background-color:red;">','</span>'), iconv('UTF-8', 'windows-1251', $base->name_goods) );
			
			# ID ������
			$tplArray['{ID_GOODS}'] = $id_goods;
			
			# ���� ������
			$tplArray['{GOODSPRICE}'] = $base->price_goods->wmz.' $';
			
			# ���������� � ������
			$tplArray['{GOODSINFO}'] = str_replace( array('[ss]','[/ss]'), array('<span style="color:white;background-color:red;">','</span>'), trim( nl2br( unhtmlentities( trim( iconv('UTF-8', 'windows-1251', $base->info_goods) ) ) ) ) );
			
			# �������������� ���������� � ������
			$tplArray['{GOODSINFOADDITION}'] = str_replace( array('[ss]','[/ss]'), array('<span style="color:white;background-color:red;">','</span>'), trim( nl2br( unhtmlentities( trim( iconv('UTF-8', 'windows-1251', $base->add_info_goods) ) ) ) ) );
			
			# ���� ��� ������� � ������
			$tplArray['{GOODSCREDITPRICE}'] = trim($base->credit_price->wmz);
			
			# ���� ��� ������� � ������
			$tplArray['{GOODSCREDITDAYS}'] = trim($base->credit_period);
			
			# ������� Fail Page ��� ����������� ����� ������ �� ������ ��� ������ ��� ������
			$tplArray['{FAIL_PAGE}'] = HOST.'/digiseller/goods/'.$id_goods;
                        
			
			# ID ������
			$tplArray['{ID_AGENT}'] = $sellerID;
			
			# ������������� ����� � ������� ��� ������, ���� ��� ����
			if ( $base->discount == 'yes' ) {
				$tplArray['{GOODSDISCOUNT}'] = '�� ����� ��������������� ������ ���������� �����������.';
			} else {
				$tplArray['{GOODSDISCOUNT}'] = '';
			}
			
			# ������������� ��� ������ � ����� ��������
			if ( $base->type_goods == 'text' ) {
				$tplArray['{GOODSTYPE}']     = '��������� ���������� ('.$base->text_info->size.' ����)';
				$tplArray['{GOODSDATALOAD}'] = substr($base->text_info->date_put, 0, 10);
			} else {
				$tplArray['{GOODSTYPE}']     = $base->file_info->name.' ('.$base->file_info->size.' ����)';
				$tplArray['{GOODSDATALOAD}'] = substr($base->file_info->date_put, 0, 10);
			}
			
			# ������������� ���������� ������
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
			$tplArray['{GOODSSTATISTIC}'] = '���������� ������: <strong>'.$base->statistics->cnt_sell.'</strong><br />'
							.'���������� ���������: <strong style="color:red;">'.$base->statistics->cnt_return.'</strong><br />'
							.'������ �����������:<br />'
							.'<span style="margin-left: 15px;">������������� - '.$good_href_begin.$base->statistics->cnt_goodresponses.$good_href_end.'</span><br />'
							.'<span style="margin-left: 15px;">������������� - '.$bad_href_begin.$base->statistics->cnt_badresponses.$bad_href_end.'</span><br />'
							.GoodsResponses($id_goods, $sellerID, $response_rows, $responses, $cur_page);
			
			# ������������� ���������� ��� ������ ���� �� ����
			if ( $base->previews_goods['cnt']>0 ) {
				foreach ( $base->previews_goods->preview_goods as $k ) {
					$tplArray['{GOODSPREVIEW}'] .= '<a href="'.$k->img_real.'" target="blank" alt="���������">'
					.'<img src="'.$k->img_small.'" width="'.$k->width_small.'" height="'.$k->height_small.'" border="0">'
					.'</a>'; 
				}
				$tplArray['{GOODSPREVIEW}'] .= '<br /><br />';
			} else {
				$tplArray['{GOODSPREVIEW}'] = '';
			}
			
			# ������������� �������� subTITLE � subDESCRIPTION
			$item['title'] = strip_tags($tplArray['{GOODSNAME}']);
			$item['description'] = substr( str_replace( array("\r","\n","\t"), array('','',''), strip_tags( $tplArray['{GOODSINFO}'] ) ), 0, 400 );
			
			# ��������� ������ �������
                        $page = implode( '', file(PATH.'/components/digiseller/template/xmlGoodsInfo.html') );
			
			# �������� ������-������ �� ������������-������
			preg_match_all("/<!-- begin shablon -->.*<!-- end shablon -->/isU", $page, $tpl);
			
			# ���� ������ ������� ������-������, ��������� ������ �������
			if ( count($tpl[0])>0 ) {
				for ( $i=0; $i<count($tpl[0]); $i++) {
					# �������� � ������� "����������" �� �� ��������, ���� ��� �� �������
					# ���� �������, ����� ������ � ������ �� ���������
					preg_match_all( '/\{.*\}/isU', $tpl[0][$i], $tmp);
					if ( !empty($tplArray[trim($tmp[0][0])]) || !empty($tplArray[trim($tmp[0][1])]) ) {
						for ( $j=0; $j<count($tmp[0]); $j++ ) {
							$tpl[0][$i] = str_replace($tmp[0][$j],$tplArray[trim($tmp[0][$j])],$tpl[0][$i]);
						}
						$result .= $tpl[0][$i];
					}
				}
				
				# ������� ������� "��������� � ������"
				$item['result'] = preg_replace("/<!-- begin shablon -->.*<!-- end shablon -->/is", $result, str_replace( array('{ID_GOODS}','{ID_AGENT}','{FAIL_PAGE}'), array($tplArray['{ID_GOODS}'], $sellerID, $tplArray['{FAIL_PAGE}'] ), $page ) );
                                return $item;
			}
		} else {
			# ���� ��� ���������� �� "0" (������ ��������) ������� ��� ���������
			if ( !empty($base->retdesc) ){
				return iconv( 'UTF-8', 'windows-1251', $base->retdesc);
			} else {
				ShowErrorMessage('�� ���� ������������ � ������� DigiSeller.');
			}
		}
}

function GoodsResponses($id_goods, $sellerID, $response_rows, $responses, $cur_page) {
	
	# �������� ��� �������
	if ( !empty($responses) && ( $responses == 'good' || $responses == 'bad') ) {
		
		# URL ��� XML-�������
		$xmlhost  = 'http://shop.digiseller.ru/xml/personal_responses.asp';
		
		# ��������� ����� �������
		$entry = '<digiseller.request>'
			.'<id_seller>'.$sellerID.'</id_seller>'
			.'<id_goods>'.$id_goods.'</id_goods>'
			.'<type_response>'.$responses.'</type_response>'
			.'<page>'.CurrentPage($cur_page).'</page>'
			.'<rows>'.$response_rows.'</rows>'
			.'</digiseller.request>';
			
		# ���������� ������ � ��������� XML-����� � ������
		$base = new SimpleXMLElement( SendRequest($xmlhost,$entry) );
		
		# ��������� ������ �������
		$response_tpl = implode( "", file(PATH.'/components/digiseller/template/xmlGoodsResponse.html') );
		
		# �������� ������-������ �� ������������-������
		preg_match_all("/<!-- begin shablon -->.*<!-- end shablon -->/isU", $response_tpl, $tpl );
		
		if ( $base->rows['cnt']>0 && !empty($tpl[0][0]) ) {
			foreach ( $base->rows->row as $k ) {
				$result .= str_replace( array('{DATE}', '{RESPONSE}', '{ADMIN_RESPONSE}'), array(substr($k->date_response, 0, 10), iconv('UTF-8', 'windows-1251', $k->text_response), iconv('UTF-8', 'windows-1251', $k->comment) ), $tpl[0][0] );
			}
			
			# ������� ������ ������� � ��������
			if ( $base->pages > 1 ) {
				$list_pages = '��������: ';
				for ( $i=1; $i<=$base->pages; $i++ ) {
					if ( CurrentPage($cur_page) == $i ) {
						$list_pages .= '<span style="color:#ffffff;background-color:#B2B2B2;">&nbsp;'.$i.'&nbsp;</span>&nbsp;';
					} else {
						$list_pages .= '<a href="/digiseller/goods/'.$id_goods.'/responses-'.$responses.'/page-'.$i.'" title="������. ��������: '.$i.'">'.$i.'</a>&nbsp;';
					}
				}
			}

			return preg_replace( '/<!-- begin shablon -->.*<!-- end shablon -->/isU', $result, str_replace( '{LIST_PAGES}', $list_pages, $response_tpl ) );
		}
	}
}

/////////////////////////////////////////
/* ����� ����������� ��������� ������� */
/////////////////////////////////////////

/**
 * ������� ������ ������ �������� �������
 *
 */
function ShowMenuAgent($agentID, $menuOrder){

	# ����� �������
	$host  = 'http://shop.digiseller.ru/xml/agent_groups.asp';
	
	# ��������� ����� �������
	$entry = '<?xml version="1.0" encoding="windows-1251"?>'
		.'<digiseller.request>'
		.'<id_agent>'.$agentID.'</id_agent>'
		.'<order>'.$menuOrder.'</order>'
		.'</digiseller.request>';
	
	# ���������� ������ � ��������� XML-����� � ������
	$base = new SimpleXMLElement( SendRequest($host, $entry) );
	
	# � ����������� �� ���� ���������� ������� ������ ��������������� ��������
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
 * ������� ������ ��������� �����
 *
 */
function ShowSearchFormAgent($sellerID){
	
	# ��������� ������ � ������ ������
        $page = implode( '', file(PATH.'/components/digiseller/template/xmlSearchAgentForm.html') );
	
	# �������� ������� �� �������� � ������� ����� ������ � ���������� �������� �������
	return str_replace(array('{AGENT}'), array($sellerID), $page);
}

/**
 * ������� ������ ������ ������� �� ��������� �������
 *
 */
function ListProducts($id_group, $rows, $sort, $cur_page, $sellerID, $menuOrder, $descr_link){

	# ������������� ���� ���������� �� ��������� 
	$ord = 'name';
	$name_ord = 'nameDESC';
	$price_ord = 'priceDESC';
	
	# ��������� �������� �� ����������, ������� ����������� �� ���������, ���� �������� ��������� ��
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
	
	# ����� �������
	$host  = 'http://shop.digiseller.ru/xml/agent_goods.asp';
	
	# ��������� ����� �������
	$entry = '<?xml version="1.0" encoding="windows-1251"?>'
		.'<digiseller.request>'
		.'<id_group>'.$id_group.'</id_group>'
		.'<page>'.CurrentPage($cur_page).'</page>'
		.'<rows>'.$rows.'</rows>'
		.'<order>'.$ord.'</order>'
		.'</digiseller.request>';
	
	# ���������� ������ � ��������� XML-����� � ������
	$base = new SimpleXMLElement( SendRequest($host, $entry) );
        
	$item = array();//������ ������� ����� ����������
	# � ����������� �� ���� ���������� ������� ������ ��������������� ��������
	if ( $base->retval == '0') {
		# ���� �������� ID ������ ������� - ������ �������� �� ������� �
		# ������������� ������������ TITLE ��������, ��� �������� �������
		# ����� (���� �������� �������) ������ ��� ������
		if ( !empty($id_group) and is_numeric($id_group) ) {
			$item['title'] = set_title_agent($id_group, $sellerID, $menuOrder);
		}
		
		# ��������� ������ �������
                $page = implode('', file(PATH.'/components/digiseller/template/xmlListGoods.html') );
		
		# �������� ������-������ �� ������������-������
		preg_match_all("/<!-- begin shablon -->.*<!-- end shablon -->/isU", $page, $tpl);
		
		# ���� ������ ������� ������-������ � � �������� ������ �� �������� ��������
		# ���� ������, ����� ��������� ������ �������
		if ( ( !empty( $tpl[0][0] ) ) && ( $base->rows['cnt'] > 0 ) ) {
			# ������� ������ � ��������� � �������
			$macrosArray = array('{GOODS_NAME}', '{PRICE}');
			
			for ($i=0; $i<$base->rows['cnt']; $i++) {
				# ������� ������ � ������� � ��������� ������, ������� ������� �� ������.
				if ( empty( $base->rows->row[$i]->price ) ) {
					$base->rows->row[$i]->price = '���';
				}
				$valueArray  = array('<a href="'.str_replace('{ID_GOODS}', $base->rows->row[$i]->id_goods, $descr_link).'&agent='.$sellerID.'" target="_blank">'.iconv('UTF-8', 'windows-1251', $base->rows->row[$i]->name_goods).'</a>',
				                     $base->rows->row[$i]->price);
				$lines .= str_replace($macrosArray, $valueArray, $tpl[0][0]);
			}
			
                        # ������� ������ �������
			for ( $i=0; $i < $base->pages; $i++ ) {
				if(CurrentPage($cur_page) == ($i+1)){
					$list_pages .= '<span style="color:#ffffff;background-color:#B2B2B2;">&nbsp;'.($i+1)."&nbsp;</span>&nbsp;";
				}else{
					$list_pages .= '<a href="/digiseller/products/'.$id_group.'/page-'.($i+1).'">'.($i+1).'</a>&nbsp;';
				}
			}
                        
                        //url � ��������c�� �� ���������
                        if(CurrentPage($cur_page)!=1){
                            $url = "/digiseller/products/".$id_group."/page-".$cur_page;
                        } else {
                            $url = "/digiseller/products/".$id_group;
                        }
			
			# �������� ������� ���������� � ������� �������
			$result = str_replace(
                                array('{URL}', '{SORT_NAME}', '{SORT_PRICE}', '{LIST_PAGES}'),
				array($url, $name_ord, $price_ord, $list_pages),
				$page);
			$item['result'] = preg_replace( "/<!-- begin shablon -->.*<!-- end shablon -->/isU",$lines, $result );
                        return $item;

		}
	}else{
		# ���� ��� ���������� �� "0" (������ ��������) ������� ��� ���������
		if ( !empty( $base->retdesc ) ) {
			ShowErrorMessage( $base->retdesc );
		}else{
			ShowErrorMessage( '�� ���� ������������ � ������� DigiSeller.' );
		}
	}
}

function set_title_agent($id_group, $sellerID, $menuOrder){
	
	# ����� �������
	$host  = 'http://shop.digiseller.ru/xml/agent_groups.asp';

	# ��������� ����� �������
	$entry = '<?xml version="1.0" encoding="windows-1251"?>'
		.'<digiseller.request>'
		.'<id_agent>'.$sellerID.'</id_agent>'
		.'<order></order>'
		.'</digiseller.request>';
	
	# ���������� ������ � ��������� XML-����� � ������
	$base = new SimpleXMLElement( SendRequest($host, $entry) );
	
	# � ����������� �� ���� ���������� ������� ������ ��������������� ��������
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
 * ������� ������ ����������� ���� ��� ���������� ������ �������
 *
 */
function ProductsSort($idn, $sort){
	
	# ��������� ������ �������
	$page = implode( '', file(PATH.'/components/digiseller/template/xmlGoodsSort.html') );
	
	# ������������� ���� ���������� �� ���������
	$ord = 'name';
	$name_ord = 'nameDESC';
	$price_ord = 'priceDESC';
	
	# ��������� �������� �� ����������, ������� ����������� �� ���������
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
	
	# �������� ������� �� �������� � �������
	return str_replace(
			array('{URL}','{SORT_NAME}','{SORT_PRICE}','{N_SELECTED}','{ND_SELECTED}','{P_SELECTED}','{PD_SELECTED}'),
			array('/digiseller/products/'.$idn, $name_ord, $price_ord, $n_selected, $nd_selected, $p_selected, $pd_selected),
			$page);
}
?>