<html>
<head>
<title>Alumni-Online (v3.0.0.0) -&gt; Installation</title>
<META http-equiv='Content-Type' content='text/html; charset=iso-8859-9'>
<LINK id='style' href='templates/skins/default/style.css' type=text/css rel=stylesheet>
<META name='product' content='Alumni-Online'>
<META name='product_version' content='3.0.0.0'>
<META name='RESOURCE-TYPE' content='DOCUMENT'>
<META name='product_creator' content='Fatih BOY [fatih@enterprisecoding.com]'>
<META name='DISTRIBUTION' content='GLOBAL'>
<META name='AUTHOR' content='Fatih Boy'>
<META name='RATING' content='SOFTWARE'>
<META name='ROBOTS' content='NOINDEX'>
</head>
<body bgcolor='#FFFFFF' leftmargin='0' topmargin='0' marginwidth='0' marginheight='0'> 
<table width='100%' height='100%' border='0' cellpadding='0' cellspacing='0'> 
  <tr> 
    <td height='69' background='templates/skins/default/images/index_01.gif' bgcolor='#CF593F' style='background-repeat:no-repeat'></td> 
  </tr> 
  <tr> 
    <td height='1' background='templates/skins/default/images/index_02.gif' bgcolor='#000000' style='background-repeat:no-repeat'></td> 
  </tr> 
  <tr> 
    <td height='20' background='templates/skins/default/images/index_03.gif' bgcolor='#DB836F' style='background-repeat:no-repeat'>&nbsp;</td> 
  </tr> 
  <tr> 
    <td height='1' background='templates/skins/default/images/index_04.gif' bgcolor='#000000' style='background-repeat:no-repeat'></td> 
  </tr> 
  <tr> 
    <td height='100%' valign='top' background='templates/skins/default/images/index_05.gif' bgcolor='#FFFFFF' style='background-repeat:no-repeat'> <br> 
{if not $installationComplated}
      <form name="siteSettings" method="post" action="index.php"> 
        <table cellSpacing="0" cellPadding="2" border="0"> 
{if $errorMessage!=""}
		<tr class="Error">
            <td colspan="2">{$errorMessage}</td> 
          </tr>
		  <tr> 
            <td colspan="2">&nbsp;</td> 
          </tr>
{/if}
		  <tr class="tableHeader"> 
            <td colspan="2">Directory Paths</td> 
          </tr> 
		  <tr> 
            <td colspan="2" class="SubHead">(Note that usually you don't need to change them)</td> 
          </tr>
          <tr> 
            <td class="SubHead">Application Base:</td> 
            <td><input name="dirAppBase" type="text" value="{$dirAppBase}" id="dirAppBase" class="NormalTextBox" style="width:300px;" /></td> 
          </tr>
          <tr> 
            <td class="SubHead">Web root:</td> 
            <td><input name="dirwwwRoot" type="text" value="{$dirwwwRoot}" id="dirwwwRoot" class="NormalTextBox" style="width:300px;" /></td> 
          </tr>
          <tr> 
            <td class="SubHead">Contribution:</td> 
            <td><input name="dirContrib" type="text" value="{$dirContrib}" id="dirContrib" class="NormalTextBox" style="width:300px;" /></td> 
          </tr>
		  <tr> 
            <td class="SubHead">Controls:</td> 
            <td><input name="dirControls" type="text" value="{$dirControls}" id="dirControls" class="NormalTextBox" style="width:300px;" /></td> 
          </tr>
		  <tr> 
            <td class="SubHead">Smarty Base:</td> 
            <td><input name="dirSmartyBase" type="text" value="{$dirSmartyBase}" id="dirSmartyBase" class="NormalTextBox" style="width:300px;" /></td> 
          </tr>		  	  
		  <tr> 
            <td colspan="2">&nbsp;</td> 
          </tr>		  
		  <tr class="tableHeader"> 
            <td colspan="2">Database</td> 
          </tr> 
          <tr> 
            <td class="SubHead">Host:</td> 
            <td><input name="dbHost" type="text" value="localhost" id="dbHost" class="NormalTextBox" style="width:300px;" /></td> 
          </tr>
		  <tr> 
            <td class="SubHead">User:</td> 
            <td><input name="dbUser" type="text" value="root" id="dbUser" class="NormalTextBox" style="width:300px;" /></td> 
          </tr>
		  <tr> 
            <td class="SubHead">Password:</td> 
            <td><input name="dbPasswd" type="password" value="" id="dbPasswd" class="NormalTextBox" style="width:300px;" /></td> 
          </tr>
		  <tr> 
            <td class="SubHead">Type:</td> 
            <td><select name="dbType" style="width:300px;">
              <option value="access">Microsoft Access/Jet</option>
              <option value="ado">Generic ADO</option>
              <option value="ado_access">Microsoft Access/Jet using ADO</option>
              <option value="ado_mssql">Microsoft SQL Server using ADO</option>
              <option value="db2">DB2</option>
              <option value="vfp">Microsoft Visual FoxPro</option>
              <option value="fbsql">FrontBase</option>
              <option value="ibase">Interbase 6 or earlier</option>
              <option value="firebird">Firebird version of interbase</option>
              <option value="borland_ibase">Borland version of Interbase 6.5 or later</option>
              <option value="informix72">Informix databases before Informix 7.3</option>
              <option value="informix">Generic informix driver</option>
              <option value="ldap">LDAP</option>
              <option value="mssql">Microsoft SQL Server 7 and later</option>
              <option value="mssqlpo">Portable MsSql</option>
              <option selected value="mysql">MySQL without transaction support</option>
              <option value="mysqlt">MySQL with transaction support</option>
              <option value="maxsql">MySQL with transaction support</option>
              <option value="oci8">Oracle 8/9</option>
              <option value="oci805">Oracle 8.0.5</option>
              <option value="oci8po">Oracle 8/9 portable</option>
              <option value="odbc">Generic ODBC</option>
              <option value="odbc_mssql">ODBC to connect to MSSQL</option>
              <option value="odbc_oracle">ODBC to connect to Oracle</option>
              <option value="odbtp">Generic odbtp</option>
              <option value="odbtp_unicode">Odtbp with unicode support</option>
              <option value="oracle">Oracle 7 client API</option>
              <option value="netezza">Netezza</option>
              <option value="postgres">Generic PostgreSQL</option>
              <option value="postgres64">PostgreSQL 6.4 and earlier </option>
              <option value="postgres7">PostgreSQL 7</option>
              <option value="sapdb">SAP DB</option>
              <option value="sqlanywhere">Sybase SQL Anywhere</option>
              <option value="sqlite">SQLite</option>
              <option value="sybase">Sybase</option>
            </select></td>
		  </tr>
		  <tr> 
            <td class="SubHead">Database Name:</td> 
            <td><input name="dbName" type="text" value="alumnionline" id="dbName" class="NormalTextBox" style="width:300px;" /></td> 
          </tr>
		   <tr> 
            <td class="SubHead">Table Prefix:</td> 
            <td><input name="dbPrefix" type="text" value="ao_" id="dbPrefix" class="NormalTextBox" style="width:300px;" /></td> 
          </tr>
          <tr> 
            <td colspan="2">&nbsp;</td> 
          </tr>
		  <tr class="tableHeader"> 
            <td colspan="2">Web Site</td> 
          </tr> 
          <tr> 
            <td class="SubHead">Site Title:</td> 
            <td><input name="siteTitle" type="text" value="Alumni-Online" maxlength="256" id="siteTitle" class="NormalTextBox" style="width:300px;" /></td> 
          </tr> 
          <tr> 
            <td class="SubHead">Site Email:</td> 
            <td><input name="siteEmail" type="text" value="admin@yoursite.com" maxlength="256" id="siteEmail" class="NormalTextBox" style="width:300px;" /></td> 
          </tr> 
          <tr> 
            <td colspan="2">&nbsp;</td> 
          </tr> 
          <tr class="tableHeader"> 
            <td colspan="2">Look&amp;Feel</td> 
          </tr> 
          <tr> 
            <td class="SubHead">Site Skin:</td> 
            <td valign="top"><select name="siteSkin" id="siteSkin" class="NormalTextBox" style="width:300px;"> 
		{foreach key=key item=item  from=$skinList}		  		   
          <option value='{$item}'>{$item}</option>            
		{/foreach}
              </select></td> 
          </tr> 
          <tr> 
            <td class="SubHead">Site Container:</td> 
            <td valign="top"><select name="siteContainer" id="siteContainer" class="NormalTextBox" style="width:300px;"> 
		{foreach key=key item=item  from=$containerList}		  		   
          <option value='{$item}'>{$item}</option>            
		{/foreach}
              </select> </td> 
          </tr> 
          <tr> 
            <td colspan="2">&nbsp;</td> 
          </tr> 
          <tr class="tableHeader"> 
            <td colspan="2">Admininistrator User</td> 
          </tr> 
          <tr> 
            <td class="SubHead">Login:</td> 
            <td><input name="adminUser" type="text" value="admin" maxlength="256" id="adminUser" class="NormalTextBox" style="width:300px;" /></td> 
          </tr> 
          <tr> 
            <td class="SubHead">Password:</td> 
            <td><input name="adminUserPasswd" type="password"  maxlength="256" id="adminUserPasswd" class="NormalTextBox" style="width:300px;" /></td> 
          </tr> 
          <tr> 
            <td class="SubHead">Email:</td> 
            <td><input name="adminUserEmail" type="text" value="admin@yoursite.com" maxlength="256" id="adminUserEmail" class="NormalTextBox" style="width:300px;" /></td> 
          </tr> 
          <tr> 
            <td colspan="2">&nbsp;</td> 
          </tr> 
          <tr class="tableHeader"> 
            <td colspan="2">E-Mail</td> 
          </tr> 
          <tr> 
            <td class="SubHead">Mailer:</td> 
            <td><select name="mailer" id="mailer"> 
                <option>mail</option>
        		<option>sendmail</option>
        		<option>smtp</option>
              </select></td> 
          </tr> 
          <tr> 
            <td class="SubHead">Username:</td> 
            <td><input name="mailerUser" type="text" maxlength="256" id="mailerUser" class="NormalTextBox" style="width:300px;" /></td> 
          </tr> 
          <tr> 
            <td class="SubHead">Password:</td> 
            <td><input name="mailerPassword" type="password" maxlength="256" id="mailerPassword" class="NormalTextBox" style="width:300px;" /></td> 
          </tr>
		  <tr> 
            <td class="SubHead">Mail From:</td> 
            <td><input name="mailerFrom" type="text" id="mailerFrom" class="NormalTextBox" style="width:300px;" value="Your Alumni Web Site" /></td> 
          </tr> 
		  <tr> 
            <td colspan="2">&nbsp;</td> 
          </tr> 
          <tr class="tableHeader"> 
            <td colspan="2">SmartCoding Tracker Service</td> 
          </tr>
		  <tr> 
            <td class="SubHead">Username:</td> 
            <td><input name="wsUser" type="text" id="wsUser" class="NormalTextBox" style="width:300px;" value="guest" /></td> 
          </tr>
		  <tr> 
            <td class="SubHead">Password:</td> 
            <td><input name="wsPassword" type="password" id="wsPassword" class="NormalTextBox" style="width:300px;" /></td> 
          </tr> 
          <tr> 
            <td colspan="2">(If you don't have a tracker account then <a href="http://tracker.enterprisecoding.com" target="_blank">click here</a>)</td> 
          </tr>
		  <tr> 
            <td colspan="2">&nbsp;</td> 
          </tr> 
          <tr> 
            <td colSpan="2"> <input type="submit" name="Submit" value="Install" class='button'></td> 
          </tr> 
        </table> 
        <input name='event' type='hidden' id='event' value='settings'> 
      </form>
{else}
<p><b>Installation complated successfully.<br><a href="index.php">Click here to go your web site</a></b></p>
{/if}
    </td> 
  </tr> 
</table> 
</body>
</html>
