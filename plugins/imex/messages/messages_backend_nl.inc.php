<?php 
$this->content->template['message']['plugin']['test']['name'] = "Plugin \"Test"; 
$this->content->template['message']['plugin']['test']['kopf'] = "<h1>Backend van de test plugin</h1><p>Dit sjabloon is niet toegankelijk, maar het slaat nergens op en is niet conform X-HTML. Toch zou het nuttig moeten zijn om het programmeren van Papoo plugins uit te leggen.</p><p>De verschillende menu items van deze plugin zijn ook zinloos. Ze verwijzen altijd naar hetzelfde sjabloon \"test_back.html\". De punten zijn alleen om te laten zien hoe men menupunten kan maken in het plugin XML bestand.</p><p>De integratie van het frontend sjabloon werkt als volgt: Maak een nieuw menu item. Vul daar in onder \"Integratie van de link of het bestand\" (onderaan) vul het volgende in: <strong>plugin:test/templates/test_front.html</strong>. Nu is het sjabloon beschikbaar in de frontend.</p><p>De modules in dit sjabloon kunnen worden ingevoegd met de module manager hier in de administratie. Voor al diegenen die het nog niet ontdekt hebben, het kan gevonden worden onder \"Systeem -&gt; Modulebeheer\".</p>"; 
$this->content->template['message']['plugin']['test']['form_kopf'] = "En hier is een klein formulier:"; 
$this->content->template['message']['plugin']['test']['form_legend'] = "Testwaarde"; 
$this->content->template['message']['plugin']['test']['form_testwert_label'] = "Geef een testwaarde door via POST"; 
$this->content->template['message']['plugin']['test']['no_table_selected'] = "Geen tabel geselecteerd"; 

 ?>