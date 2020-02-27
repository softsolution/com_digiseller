<?php
/*==================================================*/
/*            created by soft-solution.ru           */
/*==================================================*/
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }

	cpAddPathway('Digiseller', '?view=components&do=config&id='.$_REQUEST['id']);
	
	echo '<h3>Digiseller</h3>';
	
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'list'; }
	
	$toolmenu = array();

	$toolmenu[0]['icon'] = 'save.gif';
	$toolmenu[0]['title'] = '���������';
	$toolmenu[0]['link'] = 'javascript:document.optform.submit();';

	$toolmenu[1]['icon'] = 'cancel.gif';
	$toolmenu[1]['title'] = '������';
	$toolmenu[1]['link'] = '?view=components';

	cpToolMenu($toolmenu);

	//LOAD CURRENT CONFIG
	$cfg = $inCore->loadComponentConfig('digiseller');

	if($opt=='saveconfig'){	
		$cfg = array();
		
                //����� ������ ����������
                $cfg['mode']      = $_REQUEST['mode'];
                
                //��������� ����� ��������
                $cfg['sellerID']      = (int)$_REQUEST['sellerID'];
		$cfg['id_group']      = (int)$_REQUEST['id_group'];
                $cfg['rows']          = (int)$_REQUEST['rows'];
		$cfg['response_rows'] = (int)$_REQUEST['response_rows'];
		$cfg['menuOrder']     = $_REQUEST['menuOrder'];
                
                //seo ����� ��������
                $cfg['site_TITLE']       = $_REQUEST['site_TITLE'];
                $cfg['site_DESCRIPTION'] = $_REQUEST['site_DESCRIPTION'];
                $cfg['site_KEYWORDS']    = $_REQUEST['site_KEYWORDS'];

                //��������� ���������� ��������
                $cfg['id_group_agent']      = (int)$_REQUEST['id_group_agent'];
                $cfg['rows_agent']          = (int)$_REQUEST['rows_agent'];
                $cfg['menuOrder_agent']     = $_REQUEST['menuOrder_agent'];
                $cfg['descr_link']          = $_REQUEST['descr_link'];
                
                //seo ���������� ��������
                $cfg['site_TITLE_agent']       = $_REQUEST['site_TITLE_agent'];
                $cfg['site_DESCRIPTION_agent'] = $_REQUEST['site_DESCRIPTION_agent'];
                $cfg['site_KEYWORDS_agent']    = $_REQUEST['site_KEYWORDS_agent'];
                
                //contacts
		$cfg['fio']           = $_REQUEST['fio'];
		$cfg['wmid']          = (int)$_REQUEST['wmid'];	
                $cfg['email']         = $_REQUEST['email'];

                //post metod
                $cfg['send_post_type']    = (int)$_REQUEST['send_post_type'];
			
		$inCore->saveComponentConfig('digiseller', $cfg);
		
		$msg = '��������� ���������.';

        }

	global $_CFG;
        if(!isset($cfg['mode']))          { $cfg['mode'] = 'goods'; }
	
        if(!isset($cfg['rows']))          { $cfg['rows'] = '15'; }
        if(!isset($cfg['menuOrder']))     { $cfg['menuOrder'] = 'id'; }
        
        if(!isset($cfg['rows_agent']))      { $cfg['rows_agent'] = '15'; }
        if(!isset($cfg['menuOrder_agent'])) { $cfg['menuOrder_agent'] = 'id'; }
        if(!isset($cfg['descr_link'])) { $cfg['descr_link'] = 'http://www.digiseller.ru/asp/agent_redirect.asp?id_d={ID_GOODS}'; }
        
        if(!isset($cfg['response_rows'])) { $cfg['response_rows'] = '10'; }
        if(!isset($cfg['send_post_type'])){ $cfg['send_post_type'] = '1'; }
        
        $GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/jquery.form.js"></script>';
        $GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/tabs/jquery.ui.min.js"></script>';
        $GLOBALS['cp_page_head'][] = '<link href="/includes/jquery/tabs/tabs.css" rel="stylesheet" type="text/css" />';
        
        if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
        
?>
<form action="index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="form1">
        
    <div id="config_tabs" style="margin-top:12px;">

    <ul id="tabs">
        <li><a href="#general"><span>�����</span></a></li>
        <li><a href="#digiseller"><span>��������� ����� ��������</span></a></li>
        <li><a href="#agent"><span>��������� ���������� ��������</span></a></li>
        <li><a href="#contacts"><span>���������� ������</span></a></li>
    </ul>
        
    <div id="general">
        
         <table width="800" border="0" cellpadding="10" cellspacing="0" class="proptable" style="margin-top:2px">
          <tr>
            <td colspan="2"><strong>����� ������ ����������</strong><br />
                <span class="hinttext">����� ����� �������� ����� ������������ �� ������ <a href="<?php echo HOST; ?>/digiseller/" target="_blank"><?php echo HOST; ?>/digiseller</a> ��-���������,<br />
                ��� ���� <b>����������� �������</b> ����� ������ �������� �� ������ <a href="<?php echo HOST; ?>/digiseller/goods" target="_blank"><?php echo HOST; ?>/digiseller/goods</a>,<br />
                � <b>��������� �������</b> ������ ����� �������� �� ������ <a href="<?php echo HOST; ?>/digiseller/products" target="_blank"><?php echo HOST; ?>/digiseller/products</a></span>
            </td>
          </tr>
          <tr>
            <td>
                <label><input name="mode" type="radio" value="goods"  <?php if (@$cfg['mode']=='goods') { echo 'checked="checked"'; } ?> /> ����������� �������</label><br />
		<label><input name="mode" type="radio" value="products"  <?php if (@$cfg['mode']=='products') { echo 'checked="checked"'; } ?> /> ��������� �������</label>
            </td>
          </tr>
          <tr>
            <td colspan="2"><strong>����� ������� ��������</strong></td>
          </tr>
          <tr>
            <td><select name="send_post_type">
                    <option value="1" <?php if(@$cfg['send_post_type']=='1'){ echo "selected";}?>>������������ ���������� CURL</option>
                    <option value="0" <?php if(@$cfg['send_post_type']=='0'){ echo "selected";}?>>������������ ������ (fsockopen)</option>
                <select></td>
            <td></td>
          </tr>
       </table>
    
      </div> 
   </div>
    
    <div id="digiseller">
    <table width="800" border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td><strong>��� ��������������� ����� (ID ������):</strong><br />
                <span class="hinttext">����� ����� �� <a taget="_blank" href="https://my.digiseller.ru/inside/my_info.asp">���� ��������</a></span>
            </td>
            <td width="300" valign="top">
              <input name="sellerID" type="text" id="sellerID" size="45" value="<?php echo @$cfg['sellerID'];?>"/></td>
          </tr>
          <tr>
            <td><strong>ID ������ �������, ������� ����� ��������� �� ���������:</strong><br />
                <span class="hinttext">����� ����� �� <a taget="_blank" href="https://my.digiseller.ru/inside/myshop.asp">���� ��������</a><br />*id_n ������ �� �������</span>
            </td>
            <td width="300" valign="top">
              <input name="id_group" type="text" id="id_group" size="45" value="<?php echo @$cfg['id_group'];?>"/></td>
          </tr>
          <tr>
            <td><strong>���������� ����� � ������� �� ��������</strong></td>
            <td><input name="rows" type="text" id="rows" size="6" value="<?php echo @$cfg['rows'];?>"/></td>
          </tr>
          <tr>
          <tr>
            <td><strong>���������� ������� ������������ �� ��������</strong></td>
            <td><input name="response_rows" type="text" id="response_rows" size="6" value="<?php echo @$cfg['response_rows'];?>"/></td>
          </tr>
          <tr>
          <tr>
            <td><strong>��� ���������� ���� (������ ����� �������)</strong></td>
            <td><select name="menuOrder">
                    <option value="id" <?php if(@$cfg['menuOrder']=='id'){ echo "selected";}?>>�� �������������� ������</option>
                    <option value="idDESC" <?php if(@$cfg['menuOrder']=='idDESC'){ echo "selected";}?>>�� �������������� ������ (�����.)</option>
                    <option value="name" <?php if(@$cfg['menuOrder']=='name'){ echo "selected";}?>>�� �������� ������</option>
                    <option value="nameDESC" <?php if(@$cfg['menuOrder']=='nameDESC'){ echo "selected";}?>>�� �������� ������ (�����.)</option>
                <select>
            </td>
          </tr>
        </table>
        
        <table width="800" border="0" cellpadding="10" cellspacing="0" class="proptable" style="margin-top:2px">
          <tr>
            <td><strong>�������� ������ ��������</strong><br />
                <span class="hinttext">��������: <i>������� ������. ������� ��������.</i></span>
            </td>
            <td width="300" valign="top"><input name="site_TITLE" type="text" id="site_TITLE" size="45" value="<?php echo @$cfg['site_TITLE'];?>"/></td>
          </tr>
          <tr>
            <td><strong>�������� ������ ��������</strong><br />
                <span class="hinttext">��������: <i>�������� ������� �������� � ���������� ��������� ������</i></span>
                <td width="300" valign="top"><textarea name="site_DESCRIPTION" id="site_DESCRIPTION" row="5" cols="34"><?php echo @$cfg['site_DESCRIPTION'];?></textarea></td>
          </tr>
          <tr>
            <td><strong>�������� ����� ������ ��������</strong><br />
                <span class="hinttext">��������: <i>�������� ��������, ���������, ���������� ��������, ������, ������� ������, webmoney, �������, �������� ������</i></span>
            <td width="300" valign="top"><textarea name="site_KEYWORDS" id="site_KEYWORDS" row="5" cols="34"><?php echo @$cfg['site_KEYWORDS'];?></textarea></td>
          </tr>
          </table>
    </div>
    
    <div id="agent">
    <table width="800" border="0" cellpadding="10" cellspacing="0" class="proptable">
         <tr>
            <td><strong>��� ��������������� ����� (ID ������):</strong></td>
            <td width="300" valign="top"><span class="hinttext">��������� � ��������������� ������� (��. ������ �������)</span></td>
         </tr>
         <tr>
            <td><strong>ID ������ �������, ������� ����� ��������� �� ���������:</strong><br />
                <span class="hinttext">����� ����� �� <a taget="_blank" href="https://my.digiseller.ru/inside/program_agent_goods.asp">���� ��������</a><br />*id ������ �� �������</span>
            </td>
            <td width="300" valign="top">
              <input name="id_group_agent" type="text" id="id_group_agent" size="45" value="<?php echo @$cfg['id_group_agent'];?>"/></td>
          </tr>
          <tr>
            <td><strong>���������� ����� � ������� �� ��������</strong></td>
            <td><input name="rows_agent" type="text" id="rows_agent" size="6" value="<?php echo @$cfg['rows_agent'];?>"/></td>
          </tr>
          <tr>
          <tr>
          <tr>
            <td><strong>��� ���������� ���� (������ ����� �������)</strong></td>
            <td><select name="menuOrder_agent">
                    <option value="id" <?php if(@$cfg['menuOrder_agent']=='id'){ echo "selected";}?>>�� �������������� ������</option>
                    <option value="idDESC" <?php if(@$cfg['menuOrder_agent']=='idDESC'){ echo "selected";}?>>�� �������������� ������ (�����.)</option>
                    <option value="name" <?php if(@$cfg['menuOrder_agent']=='name'){ echo "selected";}?>>�� �������� ������</option>
                    <option value="nameDESC" <?php if(@$cfg['menuOrder_agent']=='nameDESC'){ echo "selected";}?>>�� �������� ������ (�����.)</option>
                <select>
            </td>
          </tr>
          
             <tr>
            <td colspan="2"><strong>URL ��� ���������:</strong></td>
           </tr> <tr>
            <td colspan="2"><input name="descr_link" type="text" id="descr_link" size="100" value="<?php echo @$cfg['descr_link'];?>"/><br />
                <span class="hinttext">������ � ������� http://www.digiseller.ru/asp/agent_redirect.asp?id_d={ID_GOODS}, ��� {ID_GOODS} - id ������ (������������� ���������)</span>
            </td>
           </tr>
        </table>
        
         <table width="800" border="0" cellpadding="10" cellspacing="0" class="proptable" style="margin-top:2px">
          <tr>
            <td><strong>�������� ������ ���������� ��������</strong><br />
                <span class="hinttext">��������: <i>PIN-����. ������� PIN-�����.</i></span>
            </td>
            <td width="300" valign="top"><input name="site_TITLE_agent" type="text" id="site_TITLE_agent" size="45" value="<?php echo @$cfg['site_TITLE_agent'];?>"/></td>
          </tr>
          <tr>
            <td><strong>�������� ������ ���������� ��������</strong><br />
                <span class="hinttext">��������: <i>�������� ������� PIN-����� � ���������� ��������� ������</i></span>
                <td width="300" valign="top"><textarea name="site_DESCRIPTION_agent" id="site_DESCRIPTION_agent" row="5" cols="34"><?php echo @$cfg['site_DESCRIPTION_agent'];?></textarea></td>
          </tr>
          <tr>
            <td><strong>�������� ����� ������ ���������� ��������</strong><br />
                <span class="hinttext">��������: <i>PIN-����,��������� ����,��������,life,UMC,������,������ �������,�������,���,�������� �������, ����������� ��������, �������, ���������, webmoney, ��������� ����, �������� ������</i></span>
            <td width="300" valign="top"><textarea name="site_KEYWORDS_agent" id="site_KEYWORDS_agent" row="5" cols="34"><?php echo @$cfg['site_KEYWORDS_agent'];?></textarea></td>
          </tr>
          </table>
    </div>
        
    <div id="contacts">

        <table width="800" border="0" cellpadding="10" cellspacing="0" class="proptable" style="margin-top:2px">
          <tr>
            <td colspan="2"><span class="hinttext">������������ �� �������� ��������� ��������, ��� ����� � ����</span></td>
          </tr>
          <tr>
            <td><strong>��� ���������� �����</strong></td>
            <td width="300" valign="top"><input name="fio" type="text" id="fio" size="45" value="<?php echo @$cfg['fio'];?>"/></td>
          </tr>
          <tr>
            <td><strong>WMID (WebMoney ID) ���������� �����</strong></td>
            <td width="300" valign="top"><input name="wmid" type="text" id="wmid" size="45" value="<?php echo @$cfg['wmid'];?>"/></td>
          </tr>
          <tr>
            <td><strong>E-mail ��� ���������</strong></td>
            <td width="300" valign="top"><input name="email" type="text" id="email" size="45" value="<?php echo @$cfg['email'];?>"/></td>
          </tr>
          </table>
        
    </div>
    
   <script type="text/javascript">$('#config_tabs > ul#tabs').tabs();</script>
        
        <p>
          <input name="opt" type="hidden" value="saveconfig" />
          <input name="save" type="submit" id="save" value="���������" />
          <input name="back" type="button" id="back" value="������" onclick="window.location.href='index.php?view=components';"/>
        </p>
</form>