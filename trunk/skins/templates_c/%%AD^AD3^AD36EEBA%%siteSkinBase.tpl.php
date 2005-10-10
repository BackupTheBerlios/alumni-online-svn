<?php /* Smarty version 2.6.7, created on 2005-10-06 09:02:36
         compiled from siteSkinBase.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'editMenuScript', 'siteSkinBase.tpl', 16, false),array('function', 'adminHeader', 'siteSkinBase.tpl', 17, false),)), $this); ?>
<html>
	<head>
		<title><?php echo $this->_tpl_vars['title'];  if ($this->_tpl_vars['tabTitle']): ?> &gt; <?php echo $this->_tpl_vars['tabTitle'];  endif;  if ($this->_tpl_vars['showVersion']): ?> - <?php echo $this->_tpl_vars['app_name']; ?>
 (v<?php echo $this->_tpl_vars['app_version']; ?>
)<?php endif; ?></title>
		<META http-equiv="Content-Type" content="text/html; charset=<?php echo $this->_tpl_vars['charset']; ?>
">
		<?php if (count($_from = (array)$this->_tpl_vars['styleSheetList'])):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
		<LINK id="<?php echo $this->_tpl_vars['key']; ?>
" href="<?php echo $this->_tpl_vars['item']; ?>
" type=text/css rel=stylesheet>
		<?php endforeach; endif; unset($_from); ?>
		<?php if (count($_from = (array)$this->_tpl_vars['javaScriptList'])):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
		<script language='JavaScript' src='<?php echo $this->_tpl_vars['key']; ?>
.js'></script>
		<?php endforeach; endif; unset($_from); ?>
		<?php if (count($_from = (array)$this->_tpl_vars['metaDataList'])):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
		<META name="<?php echo $this->_tpl_vars['key']; ?>
" content="<?php echo $this->_tpl_vars['item']; ?>
">
		<?php endforeach; endif; unset($_from); ?>
	</head>
	<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<?php echo smarty_function_editMenuScript(array(), $this);?>

 	<?php echo smarty_function_adminHeader(array(), $this);?>

	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['template'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>	
	</body>
</html>