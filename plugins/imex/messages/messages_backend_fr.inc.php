<?php 
$this->content->template['message']['plugin']['test']['name'] = "Plugin \"Test"; 
$this->content->template['message']['plugin']['test']['kopf'] = "<h1>Backend du plugin de test</h1><p>Ce modèle n'est pas accessible, mais il n'a aucun sens et n'est pas conforme à X-HTML. Néanmoins, il devrait être utile pour expliquer la programmation des plugins Papoo.</p><p>Les différents éléments de menu de ce plugin n'ont pas non plus de sens. Ils font toujours référence au même modèle \"test_back.html\". Les points sont uniquement destinés à montrer comment les points de menu peuvent être créés dans le fichier XML du plugin.</p><p>L'intégration du modèle frontal fonctionne comme suit : créer un nouvel élément de menu. Entrez-y sous \"Inclure le lien ou le fichier\" (en bas) entrez ce qui suit : <strong>plugin:test/templates/test_front.html</strong>. Maintenant le modèle est disponible dans le frontend.</p><p>Les modules contenus dans ce modèle peuvent être insérés avec le gestionnaire de modules ici dans l'administration. Pour tous ceux qui ne l'ont pas encore découvert, il se trouve sous \"Système -&gt; Gestionnaire de modules\".</p>"; 
$this->content->template['message']['plugin']['test']['form_kopf'] = "Et voici un petit formulaire :"; 
$this->content->template['message']['plugin']['test']['form_legend'] = "Valeur d'essai"; 
$this->content->template['message']['plugin']['test']['form_testwert_label'] = "Transmettre une valeur de test via POST"; 
$this->content->template['message']['plugin']['test']['no_table_selected'] = "Aucune table sélectionnée"; 

 ?>