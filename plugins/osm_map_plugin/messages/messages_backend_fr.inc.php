<?php 
$this->content->template['plugin']['osm_map_plugin']['text']['plugin_headline'] = "Plugin carte OpenStreetMap"; 
$this->content->template['plugin']['osm_map_plugin']['text']['plugin_description'] = "Ce plugin crée des cartes partout où des adresses se trouvent dans les balises &lt;address&gt; du code source HTML."; 
$this->content->template['plugin']['osm_map_plugin']['text']['description_delete_cache'] = "Ce bouton efface tous les fichiers du cache du plugin OSM, recommandé pour les nouvelles entrées dans les balises &lt;address&gt;."; 
$this->content->template['plugin']['osm_map_plugin']['text']['button_delete_cache'] = "Effacer le cache"; 
$this->content->template['plugin']['osm_map_plugin']['text']['question_delete_cache'] = "Voulez-vous vraiment supprimer le cache du plugin OSM ?"; 
$this->content->template['plugin']['osm_map_plugin']['text']['cache_not_deleted'] = "Vous avez décidé de ne pas vider le cache."; 
$this->content->template['plugin']['osm_map_plugin']['text']['legend_delete_cache'] = "Effacer le cache"; 
$this->content->template['plugin']['osm_map_plugin']['text']['legend_settings'] = "Paramètres"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_height'] = "Hauteur des cartes"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_width'] = "Largeur de la carte"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_zoom'] = "Niveau de zoom des cartes (0-19)"; 
$this->content->template['plugin']['osm_map_plugin']['text']['button_settings_save'] = "Sauvez"; 
$this->content->template['plugin']['osm_map_plugin']['text']['description_map_settings'] = "Si vous souhaitez définir un ou plusieurs de ces paramètres sur une seule carte, veuillez spécifier l'attribut data-osm-map-height pour la hauteur, data-osm-map-width pour la largeur et/ou data-osm-map-zoom pour le niveau de zoom dans le code source HTML de la balise &lt;address&gt; correspondante. Voici un exemple : &lt;address data-osm-map-width=\"90%\"&gt; (le CSS en ligne de la carte dira alors largeur : 90%)"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_cache_time'] = "Durée de vie du cache Nominatim"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_cache_unit'] = "Unité pour la durée de vie"; 
$this->content->template['plugin']['osm_map_plugin']['success']['cache_deleted'] = "Le cache a été effacé avec succès !"; 
$this->content->template['plugin']['osm_map_plugin']['success']['settings_saved'] = "Les paramètres ont été sauvegardés !"; 
$this->content->template['plugin']['osm_map_plugin']['error']['cache_not_writeable_bitmask'] = "Le cache du plugin n'est pas accessible en écriture. Veuillez vous assurer que le dossier est accessible en écriture pour le serveur web en définissant les droits d'accès à #bitmask#."; 
$this->content->template['plugin']['osm_map_plugin']['error']['google_maps_installed'] = "Veuillez désinstaller le plugin Google Maps."; 
$this->content->template['plugin']['osm_map_plugin']['error']['curl_class_not_present'] = "La classe cURL n'existe pas, veuillez la maintenir dans le répertoire /lib/classes."; 
$this->content->template['plugin']['osm_map_plugin']['error']['cache_folder_empty'] = "Le dossier cache est vide, aucun fichier n'a été supprimé."; 
$this->content->template['plugin']['osm_map_plugin']['error']['invalid_height_value'] = "Vous avez saisi une valeur non valide pour la hauteur, mettez la valeur par défaut."; 
$this->content->template['plugin']['osm_map_plugin']['error']['invalid_width_value'] = "Vous avez saisi une valeur non valide pour la largeur, mettez la valeur par défaut."; 
$this->content->template['plugin']['osm_map_plugin']['error']['unknown_error'] = "Une erreur inconnue s'est produite."; 

 ?>