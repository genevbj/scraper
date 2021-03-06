<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="xml" indent="yes"/>
  <xsl:param name="updates" select="document($fileName)" />

  <xsl:variable name="updateAD" select="$updates/ADS/AD" />

  <xsl:template match="@* | node()">
    <xsl:copy>
      <xsl:apply-templates select="@* | node()"/>
    </xsl:copy>
  </xsl:template>

  <xsl:template match="ADS">
    <xsl:copy>
      <xsl:apply-templates select="AD[not(@id = $updateAD/@id)]" />
      <xsl:apply-templates select="$updateAD" />
    </xsl:copy>
  </xsl:template>
</xsl:stylesheet>
