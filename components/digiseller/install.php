<?php
/*==================================================*/
/*            created by soft-solution.ru           */
/*==================================================*/
function info_component_digiseller() {
    $_component['title']       = 'Digiseller';                                                                                        //�������� 
    $_component['description'] = '��������� Digiseller ����������� �������� ��� ������� �������� ������� digiseller.ru �� InstantCMS';//��������
    $_component['link']        = 'digiseller';                                                                                        //������ (�������������)
    $_component['author']      = 'soft-solution.ru';                                                                                  //�����
    $_component['internal']    = '0';                                                                                                 //���������� (������ ��� �������)? 1-��, 0-���
    $_component['version']     = '1.0';                                                                                               //������� ������

    
     //��������� ��-���������
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