<?php 
$this->content->template['message']['plugin']['test']['name'] = "Plugin \"Test"; 
$this->content->template['message']['plugin']['test']['kopf'] = "<h1>Backend del plugin de prueba</h1><p>Esta plantilla no es accesible, pero no tiene sentido y no es compatible con X-HTML. No obstante, debería ser útil para explicar la programación de los plugins de Papoo.</p><p>Los diferentes elementos del menú de este plugin tampoco tienen sentido. Siempre hacen referencia a la misma plantilla \"test_back.html\". Los puntos son sólo para mostrar cómo se pueden crear puntos de menú en el archivo XML del plugin.</p><p>La integración de la plantilla del frontend funciona de la siguiente manera: crea un nuevo elemento de menú. Entre allí en \"Integración del enlace o archivo\" (en la parte inferior) introduzca lo siguiente: <strong>plugin:test/templates/test_front.html</strong>. Ahora la plantilla está disponible en el frontend.</p><p>Los módulos contenidos en esta plantilla pueden ser insertados con el gestor de módulos aquí en la administración. Para todos aquellos que aún no lo hayan descubierto, se puede encontrar en \"Sistema -&gt; Gestor de módulos\".</p>"; 
$this->content->template['message']['plugin']['test']['form_kopf'] = "Y aquí hay un pequeño formulario:"; 
$this->content->template['message']['plugin']['test']['form_legend'] = "Valor de la prueba"; 
$this->content->template['message']['plugin']['test']['form_testwert_label'] = "Pasar un valor de prueba a través de POST"; 
$this->content->template['message']['plugin']['test']['no_table_selected'] = "No se ha seleccionado ninguna mesa"; 

 ?>