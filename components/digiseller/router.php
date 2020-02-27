<?php
/*==================================================*/
/*            created by soft-solution.ru           */
/*==================================================*/
function routes_digiseller() {

//название страницы на которой будет отображаться информация о оплате
    $routes[] = array(
        '_uri' => '/^digiseller\/pay$/i',
        'do' => 'pay'
    );
    
//название страницы на которой будут отображаться контактные данные
    $routes[] = array(
        '_uri' => '/^digiseller\/contact$/i',
        'do' => 'contact'
    );

//название страницы описания магазина
    $routes[] = array(
        '_uri' => '/^digiseller\/about$/i',
        'do' => 'about'
    );

//название страницы на которой будут отображаться результаты поиска
    $routes[] = array(
        '_uri' => '/^digiseller\/search$/i',
        'do' => 'search'
    );
   
    //отзывы о товаре пагинация
    $routes[] = array(
        '_uri' => '/^digiseller\/goods\/([0-9]+)\/responses-(bad|good)\/page-([0-9]+)$/i',
        'do' => 'good',
        1 => 'id',
        2 => 'responses',
        3 => 'page' 
    );
    
    //отзывы о товаре
    $routes[] = array(
        '_uri' => '/^digiseller\/goods\/([0-9]+)\/responses-(bad|good)$/i',
        'do' => 'good',
        1 => 'id',
        2 => 'responses'
    );
    
    //название страницы на которой будет отображаться информация о товаре
    $routes[] = array(
        '_uri' => '/^digiseller\/goods\/([0-9]+)\/search$/i',
        'do' => 'good',
        1 => 'id'
    );
    
//название страницы на которой будет отображаться информация о товаре
    $routes[] = array(
        '_uri' => '/^digiseller\/goods\/([0-9]+)$/i',
        'do' => 'good',
        1 => 'id'
    );
    
    //все товары персонального магазина
    $routes[] = array(
        '_uri' => '/^digiseller\/goods$/i',
        'do' => 'goods'
    );
    
    //сортировка списка товаров на определенной странице
    $routes[] = array(
        '_uri' => '/^digiseller\/products\/([0-9]+)\/page-([0-9]+)\/sort-(name|nameDESC|price|priceDESC)$/i',
        'do' => 'products',
        1 => 'id',
        2 => 'page',
        3 => 'sort'
    );
    
    //пагинация в категории
    $routes[] = array(
        '_uri' => '/^digiseller\/products\/([0-9]+)\/page-([0-9]+)$/i',
        'do' => 'products',
        1 => 'id',
        2 => 'page'
    );
    
    //сортировка списка товаров
    $routes[] = array(
        '_uri' => '/^digiseller\/products\/([0-9]+)\/sort-(name|nameDESC|price|priceDESC)$/i',
        'do' => 'products',
        1 => 'id',
        2 => 'sort'
    );
    
    //категория агентского магазина
    $routes[] = array(
        '_uri' => '/^digiseller\/products\/([0-9]+)$/i',
        'do' => 'products',
        1 => 'id'
        
    );
    
    //все товары агентского магазина
    $routes[] = array(
        '_uri' => '/^digiseller\/products$/i',
        'do' => 'products'
    );
    
//сортировка списка товаров
    $routes[] = array(
        '_uri' => '/^digiseller\/([0-9]+)\/sort-(name|nameDESC|price|priceDESC)$/i',
        'do' => 'view',
        1 => 'id',
        2 => 'sort'
    );
    
    //сортировка списка товаров на определенной странице
    $routes[] = array(
        '_uri' => '/^digiseller\/([0-9]+)\/page-([0-9]+)\/sort-(name|nameDESC|price|priceDESC)$/i',
        'do' => 'view',
        1 => 'id',
        2 => 'page',
        3 => 'sort'
    );
    
    //пагинация в категории
    $routes[] = array(
        '_uri' => '/^digiseller\/([0-9]+)\/page-([0-9]+)$/i',
        'do' => 'view',
        1 => 'id',
        2 => 'page'
    );
    
    //просмотр списка товаров
    $routes[] = array(
        '_uri' => '/^digiseller\/([0-9]+)$/i',
        'do' => 'view',
        1 => 'id'
    );
    
//главная страница компонента, список товаров
    $routes[] = array(
        '_uri' => '/^digiseller\/$/i',
        'do' => 'view',
    );

    return $routes;
}

?>