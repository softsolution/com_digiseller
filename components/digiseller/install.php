<?php
/*==================================================*/
/*            created by soft-solution.ru           */
/*==================================================*/
function info_component_digiseller() {
    $_component['title']       = 'Digiseller';                                                                                        //название 
    $_component['description'] = 'Компонент Digiseller интегрирует площадку для продажи цифровых товаров digiseller.ru на InstantCMS';//описание
    $_component['link']        = 'digiseller';                                                                                        //ссылка (идентификатор)
    $_component['author']      = 'soft-solution.ru';                                                                                  //автор
    $_component['internal']    = '0';                                                                                                 //внутренний (только для админки)? 1-Да, 0-Нет
    $_component['version']     = '1.0';                                                                                               //текущая версия

    
     //Настройки по-умолчанию
     $_component['config'] = array(

        'mode' => 'goods',
        'rows' => '15',
        'menuOrder' => 'id',
        
        'rows_agent' => '15',
        'menuOrder_agent' => 'id',
        'descr_link' => 'http://www.digiseller.ru/asp/agent_redirect.asp?id_d={ID_GOODS}',
        
        'response_rows' => '10',
        'send_post_type' => '1'
        );
    
    return $_component;
}

function install_component_digiseller() {

    return true;
}

function upgrade_component_digiseller() {
    
    return true;

}

?>