<?xml version="1.0" encoding="Windows-1251"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:template match="/errors">
		<table width="100%" border="0" cellpadding="1" cellspacing="0">
			<tr>
				<xsl:attribute name="class">tableHeader</xsl:attribute>
				<td><xsl:attribute name="colspan">2</xsl:attribute></td>
				<td>Error Type</td>
				<td>Time</td>	
				<td>Error Message</td>
				<td>Script</td>
			</tr>
			<xsl:apply-templates/>
			<tr class="tablefooter">
				<td>
					<xsl:attribute name="colspan">6</xsl:attribute>
					<input>
						<xsl:attribute name="type">submit</xsl:attribute>
						<xsl:attribute name="name">Submit</xsl:attribute>
						<xsl:attribute name="value">Submit Error Report</xsl:attribute>
						<xsl:attribute name="class">button</xsl:attribute>
					</input>
			</td>
		  </tr>
		</table>
  </xsl:template>
  <xsl:template match="/errors/error">
	<tr>
		<xsl:choose>
	        <xsl:when test="position() mod 2 = 0">
		        <xsl:attribute name="class">tableEvenRow</xsl:attribute>
		      </xsl:when>
		      <xsl:otherwise>
		        <xsl:attribute name="class">tableOddRow</xsl:attribute>
		      </xsl:otherwise>
	      </xsl:choose>
		<td>
			<input>
				<xsl:attribute name="type">checkbox</xsl:attribute>
				<xsl:attribute name="name">errors[]</xsl:attribute>
				<xsl:attribute name="value">
					<xsl:value-of select="errornum"/>|
					<xsl:value-of select="type"/>|
					<xsl:value-of select="time"/>|
					<xsl:value-of select="message"/>|
					<xsl:value-of select="script"/>|
					<xsl:value-of select="line"/>
				</xsl:attribute>
			</input>
		</td>
		<td>
			<img>
				<xsl:attribute name="src">images/log_<xsl:value-of select="errornum"/>.gif</xsl:attribute>
			</img>
		</td>
		<td><xsl:value-of select="type"/></td>
		<td><xsl:value-of select="time"/></td>
		<td><xsl:value-of select="message"/></td>
		<td><xsl:value-of select="script"/><br />[on line <xsl:value-of select="line"/>]</td>
	</tr>
  </xsl:template>  
</xsl:stylesheet>