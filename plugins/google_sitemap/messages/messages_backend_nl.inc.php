<?php 
$this->content->template['plugin']['google_sitemap']['text'] = "<h2>De Google Sitemap Plugin</h2><p>Met deze plugin kunt u de Google Sitemap maken.</p>"; 
$this->content->template['plugin']['google_sitemap']['change'] = "Veranderen van de gegevens voor de google sitemap"; 
$this->content->template['plugin']['google_sitemap']['text2'] = "Geef aan hoe vaak uw pagina wordt bijgewerkt en welke prioriteit de vermeldingen moeten hebben:"; 
$this->content->template['plugin']['google_sitemap']['changefreq'] = "Selecteer de wijzigingsfreq:"; 
$this->content->template['plugin']['google_sitemap']['prioritaet'] = "Kies de prioriteit:"; 
$this->content->template['plugin']['google_sitemap']['eintragen'] = "Ga naar"; 
$this->content->template['plugin']['google_sitemap']['erlaeuterung'] = "Uitleg:"; 
$this->content->template['plugin']['google_sitemap']['text3'] = "<p><b>changefreq:</b> <br />De frequentie waarmee de pagina naar verwachting zal veranderen. Deze waarde geeft zoekmachines algemene informatie. Het heeft niet noodzakelijk te maken met de frequentie waarmee u de pagina crawlt. Geldige waarden zijn:<br />"; 
$this->content->template['plugin']['google_sitemap']['text4'] = "De waarde \"altijd\" wordt gebruikt om documenten te beschrijven die bij elke toegang veranderen. De waarde \"nooit\" wordt gebruikt om gearchiveerde URL's te beschrijven. <br /> <br /> De waarde van deze tag wordt opgevat als een hint, niet als een opdracht. Crawlers van zoekmachines houden met deze informatie rekening bij het nemen van beslissingen. Zij kunnen echter pagina's met de tag \"ieder uur\" minder vaak dan ieder uur crawlen, of pagina's met de tag \"ieder jaar\" vaker dan ieder jaar. Zelfs pagina's die als \"nooit\" zijn gemarkeerd, zullen waarschijnlijk met bepaalde tussenpozen door crawlers worden gecrawld om onverwachte veranderingen op dergelijke pagina's op te sporen. <br /></p><p><b>Prioriteit:</b> <br />De prioriteit van deze URL boven andere URL's op uw site. Geldige waarden variëren van 0.0 tot 1.0. Deze waarde heeft geen invloed op een vergelijking van uw pagina's met pagina's op andere websites, het informeert de zoekmachines alleen welke pagina's voor u de hoogste prioriteit hebben. De pagina's worden vervolgens op deze basis gecrawld. <br /> <br /> De standaard prioriteit van een pagina is 0.5. <br /> <br />De prioriteit die u aan een pagina toekent heeft geen invloed op de positie van uw URL's in de resultatenpagina's van een zoekmachine. Deze informatie wordt alleen door zoekmachines gebruikt om te selecteren tussen URL's op dezelfde website. Het gebruik van deze tag verhoogt dus de kans dat uw belangrijkere pagina's in de zoekindex worden opgenomen. <br /> <br /> Ook het toekennen van een hoge prioriteit aan alle URL's van uw site is geen goed idee. Aangezien prioriteit relatief is, wordt het alleen gebruikt om te kiezen tussen URL's binnen uw eigen site. De prioriteit van uw pagina's wordt niet vergeleken met de prioriteit van pagina's op andere websites. <br /></p>"; 
$this->content->template['plugin']['google_sitemap']['ready'] = "De Google Sitemap is aangemaakt."; 
$this->content->template['plugin']['google_sitemap']['link'] = "Link voor uw Google-account:"; 
$this->content->template['plugin']['google_sitemap']['error'] = "Er kon geen Google Sitemap worden gemaakt."; 
$this->content->template['plugin']['google_sitemap']['datei'] = "Het bestand "; 
$this->content->template['plugin']['google_sitemap']['datei2'] = " bestaat, maar kon niet worden overschreven. Verander de toegangsrechten (publieke toestemming om te schrijven) van het bestand."; 
$this->content->template['plugin']['google_sitemap']['gespeichert'] = "De sitemap is opgeslagen."; 
$this->content->template['plugin']['google_sitemap']['ordner'] = "De map "; 
$this->content->template['plugin']['google_sitemap']['ordner2'] = " kon niet worden geschreven. Verander de toegangsrechten via ftp. Of sla een leeg bestand op \" . $filename . \" in de htdocs directory en verander de toegangsrechten (publieke toestemming om te schrijven) van het bestand."; 
$this->content->template['plugin']['google_sitemap']['geaendert'] = "De data zijn veranderd"; 

 ?>