<?php /* Smarty version 2.6.7, created on 2005-10-06 09:02:36
         compiled from templates%5Cskins%5Cdefault%5Cindex.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'skinPath', 'templates\\skins\\default\\index.html', 3, false),array('function', 'links', 'templates\\skins\\default\\index.html', 11, false),array('function', 'user', 'templates\\skins\\default\\index.html', 12, false),array('function', 'login', 'templates\\skins\\default\\index.html', 12, false),array('function', 'leftPane', 'templates\\skins\\default\\index.html', 22, false),array('function', 'contentPane', 'templates\\skins\\default\\index.html', 23, false),array('function', 'rightPane', 'templates\\skins\\default\\index.html', 24, false),)), $this); ?>
<table id="Table_01" width="100%" height="100%" border="0" cellpadding="0" cellspacing="0"> 
  <tr> 
    <td height="69" background="<?php echo smarty_function_skinPath(array(), $this);?>
/images/index_01.gif" bgcolor="#CF593F" style="background-repeat:no-repeat"></td> 
  </tr> 
  <tr> 
    <td height="1" background="<?php echo smarty_function_skinPath(array(), $this);?>
/images/index_02.gif" bgcolor="#000000" style="background-repeat:no-repeat"></td> 
  </tr> 
  <tr> 
    <td height="20" background="<?php echo smarty_function_skinPath(array(), $this);?>
/images/index_03.gif" bgcolor="#DB836F" style="background-repeat:no-repeat"><table width="100%" height="20"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="80%"><?php echo smarty_function_links(array(), $this);?>
</td>
        <td width="20%" align="right"><?php echo smarty_function_user(array('cssClass' => 'MainLinks'), $this);?>
&nbsp;|&nbsp;<?php echo smarty_function_login(array('cssClass' => 'MainLinks'), $this);?>
&nbsp;</td>
      </tr>
    </table></td> 
  </tr> 
  <tr> 
    <td height="1" background="<?php echo smarty_function_skinPath(array(), $this);?>
/images/index_04.gif" bgcolor="#000000" style="background-repeat:no-repeat"></td> 
  </tr> 
  <tr> 
    <td height="100%" valign="top" background="<?php echo smarty_function_skinPath(array(), $this);?>
/images/index_05.gif" bgcolor="#FFFFFF" style="background-repeat:no-repeat"><table width="100%"  border="0" cellspacing="3" cellpadding="0"> 
        <tr> 
          <td valign="top"><?php echo smarty_function_leftPane(array(), $this);?>
</td> 
          <td valign="top"><?php echo smarty_function_contentPane(array(), $this);?>
</td> 
          <td valign="top"><?php echo smarty_function_rightPane(array(), $this);?>
</td> 
        </tr> 
      </table></td> 
  </tr> 
</table>