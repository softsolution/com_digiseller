<?php
/*==================================================*/
/*            created by soft-solution.ru           */
/*==================================================*/
if (!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function digiseller() {
    global $_LANG;
    $inCore = cmsCore::getInstance();
    $inPage = cmsPage::getInstance();
    $inUser = cmsUser::getInstance();

    $cfg = $inCore->loadComponentConfig('digiseller');

    // Проверяем включен ли компонент
    if (!$cfg['component_enabled']) {
        cmsCore::error404();
    }
    
    $inCore->loadLanguage('components/digiseller');

    $id        = $inCore->request('id', 'int', 0);
    $do        = $inCore->request('do', 'str', 'view');
    $cur_page  = $inCore->request('page', 'int');
    $cfg['mode'] = $cfg['mode'] ? $cfg['mode'] : 'goods';


/* ==================================================================================================== */
/* ========================== ГЛАВНАЯ СТРАНИЦА DIGISELLER ============================================= */
/* ==================================================================================================== */
    
    if ($do == 'view') {
        //режим работы компонента по-умолчанию
        if($cfg['mode']=='products'){
            $do = 'products'; 
        } else {
            $do = 'goods'; 
        }
        
    }
    
/* ==================================================================================================== */
/* ========================== ГЛАВНАЯ СТРАНИЦА ПЕРСОНАЛЬНОГО МАГАЗИНА ================================= */
/* ==================================================================================================== */
    
    if ($do == 'goods') {

        $inCore->includeFile("components/digiseller/includes/myshopXML.php");
        $sort        = $inCore->request('sort', 'str');

        # если передано ID группы товаров, то присваиваем его переменной
	# если - нет, тогда будет выводится группа указанная по умолчанию в конфигурации
        $id_group = $id ? $id : $cfg['id_group'];

        $GoodsSort = GoodsSort($id_group, $sort);
        $ShowSearchForm = ShowSearchForm($cfg['sellerID'], $cfg['rows']);
        $ShowMenu  = ShowMenu($cfg['sellerID'], $cfg['menuOrder'], $cfg['send_post_type']);
        
        $ListGoods = ListGoods($id_group, $cfg['rows'], $sort, $cur_page, $cfg['sellerID'], $cfg['menuOrder']);
        
        if($ListGoods['title']) {
            if($id=='') {$title = $cfg['site_TITLE'];} else { $title = $ListGoods['title']; }
            $inPage->setKeywords($ListGoods['title']);
            $inPage->setDescription($ListGoods['title']);
        } else {
            $title = $cfg['site_TITLE'];
            $inPage->setKeywords($cfg['site_KEYWORDS']);
            $inPage->setDescription($cfg['site_DESCRIPTION']);
        }

        $smarty = $inCore->initSmarty('components', 'com_digiseller_view.tpl');
        $smarty->assign('title', $title);
        $smarty->assign('GoodsSort', $GoodsSort);
        $smarty->assign('ShowSearchForm', $ShowSearchForm);
        $smarty->assign('ShowMenu', $ShowMenu);
        $smarty->assign('ListGoods', $ListGoods['result']);
        $smarty->assign('cfg', $cfg);
        $smarty->display('com_digiseller_view.tpl');
    }
    
/* ==================================================================================================== */
/* ========================== ИНФОРМАЦИЯ О ТОВАРЕ ===================================================== */
/* ==================================================================================================== */
    
    if ($do == 'good') {

        $inCore->includeFile("components/digiseller/includes/myshopXML.php");
        
        $responses = $inCore->request('responses', 'str');

        $ShowSearchForm = ShowSearchForm($cfg['sellerID'], $cfg['rows']);
        $ShowMenu  = ShowMenu($cfg['sellerID'], $cfg['menuOrder'], $cfg['send_post_type']);
        $GoodsInfo = GoodsInfo($id, $cfg['sellerID'], $cfg['response_rows'], $responses, $cur_page);

        $title = $GoodsInfo['title'];
        $inPage->setKeywords($GoodsInfo['title']);
        $inPage->setDescription($GoodsInfo['description']);
        $inPage->setTitle($title);
	$inPage->addPathway($title, '/digiseller/');

        $smarty = $inCore->initSmarty('components', 'com_digiseller_goods.tpl');
        $smarty->assign('title', $title);
        $smarty->assign('ShowSearchForm', $ShowSearchForm);
        $smarty->assign('ShowMenu', $ShowMenu);
        $smarty->assign('GoodsInfo', $GoodsInfo['result']);
        $smarty->assign('cfg', $cfg);
        $smarty->display('com_digiseller_goods.tpl');
    }
    
/* ==================================================================================================== */
/* ========================== О МАГАЗИНЕ ============================================================== */
/* ==================================================================================================== */
    
    if ($do == 'about') {

        $inCore->includeFile("components/digiseller/includes/myshopXML.php");

        if ($cfg['site_KEYWORDS'])    { $inPage->setKeywords($cfg['site_KEYWORDS']); }
        if ($cfg['site_DESCRIPTION']) { $inPage->setDescription($cfg['site_DESCRIPTION']); }
        
        $inPage->setTitle('О магазине');
	$inPage->addPathway('О магазине');
        
        $ShowSearchForm = ShowSearchForm($cfg['sellerID'], $cfg['rows']);
        $ShowMenu  = ShowMenu($cfg['sellerID'], $cfg['menuOrder'], $cfg['send_post_type']);
            
        $smarty = $inCore->initSmarty('components', 'com_digiseller_about.tpl');
        $smarty->assign('ShowSearchForm', $ShowSearchForm);
        $smarty->assign('ShowMenu', $ShowMenu);
        $smarty->assign('cfg', $cfg);
        $smarty->display('com_digiseller_about.tpl');
            
    }
    
/* ==================================================================================================== */
/* ========================== СПОСОБЫ ОПЛАТЫ ========================================================== */
/* ==================================================================================================== */
    
    if ($do == 'pay') {

        $inCore->includeFile("components/digiseller/includes/myshopXML.php");

        if ($cfg['site_KEYWORDS'])    { $inPage->setKeywords($cfg['site_KEYWORDS']); }
        if ($cfg['site_DESCRIPTION']) { $inPage->setDescription($cfg['site_DESCRIPTION']); }
        
        $inPage->setTitle('Способы оплаты');
	$inPage->addPathway('Способы оплаты');
        
        $ShowSearchForm = ShowSearchForm($cfg['sellerID'], $cfg['rows']);
        $ShowMenu  = ShowMenu($cfg['sellerID'], $cfg['menuOrder'], $cfg['send_post_type']);
            
        $smarty = $inCore->initSmarty('components', 'com_digiseller_pay.tpl');
        $smarty->assign('ShowSearchForm', $ShowSearchForm);
        $smarty->assign('ShowMenu', $ShowMenu);
        $smarty->assign('cfg', $cfg);
        $smarty->display('com_digiseller_pay.tpl');
            
    }
    
/* ==================================================================================================== */
/* ========================== КОНТАКТНАЯ ИНФОРМАЦИЯ =================================================== */
/* ==================================================================================================== */
    
    if ($do == 'contact') {

        $inCore->includeFile("components/digiseller/includes/myshopXML.php");

        if ($cfg['site_KEYWORDS'])    { $inPage->setKeywords($cfg['site_KEYWORDS']); }
        if ($cfg['site_DESCRIPTION']) { $inPage->setDescription($cfg['site_DESCRIPTION']); }
        
        $inPage->setTitle('Контакты');
	$inPage->addPathway('Контакты');
        
        $ShowSearchForm = ShowSearchForm($cfg['sellerID'], $cfg['rows']);
        $ShowMenu  = ShowMenu($cfg['sellerID'], $cfg['menuOrder'], $cfg['send_post_type']);
        
        $smarty = $inCore->initSmarty('components', 'com_digiseller_contact.tpl');
        $smarty->assign('ShowSearchForm', $ShowSearchForm);
        $smarty->assign('ShowMenu', $ShowMenu);
        $smarty->assign('cfg', $cfg);
        $smarty->display('com_digiseller_contact.tpl');
            
    }
    
/* ==================================================================================================== */
/* ========================== ПОИСК =================================================================== */
/* ==================================================================================================== */
    
    if ($do == 'search') {

        $inCore->includeFile("components/digiseller/includes/myshopXML.php");

        if ($cfg['site_KEYWORDS'])    { $inPage->setKeywords($cfg['site_KEYWORDS']); }
        if ($cfg['site_DESCRIPTION']) { $inPage->setDescription($cfg['site_DESCRIPTION']); }
        
        $inPage->setTitle('Результаты поиска');
	$inPage->addPathway('Результаты поиска');
        
        $ShowSearchForm = ShowSearchForm($cfg['sellerID'], $cfg['rows']);
        $ShowMenu  = ShowMenu($cfg['sellerID'], $cfg['menuOrder'], $cfg['send_post_type']);
        $SearchGoods = SearchGoods($cfg['sellerID'], $cfg['rows'], $search_str);
        
        $smarty = $inCore->initSmarty('components', 'com_digiseller_search.tpl');
        $smarty->assign('ShowSearchForm', $ShowSearchForm);
        $smarty->assign('ShowMenu', $ShowMenu);
        $smarty->assign('SearchGoods', $SearchGoods);
        $smarty->assign('cfg', $cfg);
        $smarty->display('com_digiseller_search.tpl');
            
    }
    
/* ==================================================================================================== */
/* ========================== Главная страница агентского магазина ==================================== */
/* ==================================================================================================== */
    
    if ($do=='products'){
        
        $inCore->includeFile("components/digiseller/includes/myshopXML.php");
        $sort        = $inCore->request('sort', 'str');

        # если передано ID группы товаров, то присваиваем его переменной
	# если - нет, тогда будет выводится группа указанная по умолчанию в конфигурации
        $id_group = $id ? $id : $cfg['id_group_agent'];

        $ProductsSort = ProductsSort($id_group, $sort);
        $ShowSearchFormAgent = ShowSearchFormAgent($cfg['sellerID']);
        $ShowMenuAgent  = ShowMenuAgent($cfg['sellerID'], $cfg['menuOrder_agent'], $cfg['send_post_type']);
       
        $descr_link = $cfg['descr_link'] ? $cfg['descr_link'] : 'http://www.digiseller.ru/asp/agent_redirect.asp?id_d={ID_GOODS}';
        $ListProducts = ListProducts($id_group, $cfg['rows_agent'], $sort, $cur_page, $cfg['sellerID'], $cfg['menuOrder_agent'], $descr_link);
       
        if($ListProducts['title']) {
            if($id=='') {$title = $cfg['site_TITLE_agent'];} else { $title = $ListProducts['title']; }
            $inPage->setKeywords($ListProducts['title']);
            $inPage->setDescription($ListProducts['title']);
        } else {
            $title = $cfg['site_TITLE_agent'];
            $inPage->setKeywords($cfg['site_KEYWORDS_agent']);
            $inPage->setDescription($cfg['site_DESCRIPTION_agent']);
        }
        
        $inPage->setTitle($title);
	$inPage->addPathway($title, '/digiseller/');
        
        $smarty = $inCore->initSmarty('components', 'com_digiseller_agent_view.tpl');
        $smarty->assign('title', $title);
        $smarty->assign('ProductsSort', $ProductsSort);
        $smarty->assign('ShowSearchForm', $ShowSearchFormAgent);
        $smarty->assign('ShowMenu', $ShowMenuAgent);
        $smarty->assign('ListProducts', $ListProducts['result']);
        $smarty->assign('cfg', $cfg);
        $smarty->display('com_digiseller_agent_view.tpl');
    }
}

?>