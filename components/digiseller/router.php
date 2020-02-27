<?php
/*==================================================*/
/*            created by soft-solution.ru           */
/*==================================================*/
function routes_digiseller() {

//�������� �������� �� ������� ����� ������������ ���������� � ������
    $routes[] = array(
        '_uri' => '/^digiseller\/pay$/i',
        'do' => 'pay'
    );
    
//�������� �������� �� ������� ����� ������������ ���������� ������
    $routes[] = array(
        '_uri' => '/^digiseller\/contact$/i',
        'do' => 'contact'
    );

//�������� �������� �������� ��������
    $routes[] = array(
        '_uri' => '/^digiseller\/about$/i',
        'do' => 'about'
    );

//�������� �������� �� ������� ����� ������������ ���������� ������
    $routes[] = array(
        '_uri' => '/^digiseller\/search$/i',
        'do' => 'search'
    );
   
    //������ � ������ ���������
    $routes[] = array(
        '_uri' => '/^digiseller\/goods\/([0-9]+)\/responses-(bad|good)\/page-([0-9]+)$/i',
        'do' => 'good',
        1 => 'id',
        2 => 'responses',
        3 => 'page' 
    );
    
    //������ � ������
    $routes[] = array(
        '_uri' => '/^digiseller\/goods\/([0-9]+)\/responses-(bad|good)$/i',
        'do' => 'good',
        1 => 'id',
        2 => 'responses'
    );
    
    //�������� �������� �� ������� ����� ������������ ���������� � ������
    $routes[] = array(
        '_uri' => '/^digiseller\/goods\/([0-9]+)\/search$/i',
        'do' => 'good',
        1 => 'id'
    );
    
//�������� �������� �� ������� ����� ������������ ���������� � ������
    $routes[] = array(
        '_uri' => '/^digiseller\/goods\/([0-9]+)$/i',
        'do' => 'good',
        1 => 'id'
    );
    
    //��� ������ ������������� ��������
    $routes[] = array(
        '_uri' => '/^digiseller\/goods$/i',
        'do' => 'goods'
    );
    
    //���������� ������ ������� �� ������������ ��������
    $routes[] = array(
        '_uri' => '/^digiseller\/products\/([0-9]+)\/page-([0-9]+)\/sort-(name|nameDESC|price|priceDESC)$/i',
        'do' => 'products',
        1 => 'id',
        2 => 'page',
        3 => 'sort'
    );
    
    //��������� � ���������
    $routes[] = array(
        '_uri' => '/^digiseller\/products\/([0-9]+)\/page-([0-9]+)$/i',
        'do' => 'products',
        1 => 'id',
        2 => 'page'
    );
    
    //���������� ������ �������
    $routes[] = array(
        '_uri' => '/^digiseller\/products\/([0-9]+)\/sort-(name|nameDESC|price|priceDESC)$/i',
        'do' => 'products',
        1 => 'id',
        2 => 'sort'
    );
    
    //��������� ���������� ��������
    $routes[] = array(
        '_uri' => '/^digiseller\/products\/([0-9]+)$/i',
        'do' => 'products',
        1 => 'id'
        
    );
    
    //��� ������ ���������� ��������
    $routes[] = array(
        '_uri' => '/^digiseller\/products$/i',
        'do' => 'products'
    );
    
//���������� ������ �������
    $routes[] = array(
        '_uri' => '/^digiseller\/([0-9]+)\/sort-(name|nameDESC|price|priceDESC)$/i',
        'do' => 'view',
        1 => 'id',
        2 => 'sort'
    );
    
    //���������� ������ ������� �� ������������ ��������
    $routes[] = array(
        '_uri' => '/^digiseller\/([0-9]+)\/page-([0-9]+)\/sort-(name|nameDESC|price|priceDESC)$/i',
        'do' => 'view',
        1 => 'id',
        2 => 'page',
        3 => 'sort'
    );
    
    //��������� � ���������
    $routes[] = array(
        '_uri' => '/^digiseller\/([0-9]+)\/page-([0-9]+)$/i',
        'do' => 'view',
        1 => 'id',
        2 => 'page'
    );
    
    //�������� ������ �������
    $routes[] = array(
        '_uri' => '/^digiseller\/([0-9]+)$/i',
        'do' => 'view',
        1 => 'id'
    );
    
//������� �������� ����������, ������ �������
    $routes[] = array(
        '_uri' => '/^digiseller\/$/i',
        'do' => 'view',
    );

    return $routes;
}

?>