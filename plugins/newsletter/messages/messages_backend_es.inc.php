<?php 
$this->content->template['message_20001'] = "Enviar boletín de noticias"; 
$this->content->template['message_20001a'] = "El idioma "; 
$this->content->template['message_20002'] = "Asunto"; 
$this->content->template['message_20003'] = " Contenido del boletín "; 
$this->content->template['message_20004'] = "Mensaje de texto alternativo"; 
$this->content->template['message_20005'] = "Configuración del boletín de noticias"; 
$this->content->template['message_20006'] = " Contenido de la impresión:"; 
$this->content->template['message_20007'] = "Contenido"; 
$this->content->template['message_20008'] = "Gestión de suscriptores de boletines informativos"; 
$this->content->template['message_20009'] = "Añadir un nuevo abonado"; 
$this->content->template['message_20010'] = ""; 
$this->content->template['message_20011'] = "Sí "; 
$this->content->template['message_20012'] = "No"; 
$this->content->template['message_20013'] = "Activo"; 
$this->content->template['message_20014'] = "Dirección de correo electrónico"; 
$this->content->template['message_20015'] = "Envíe un correo electrónico a"; 
$this->content->template['news_message_1'] = "<h2>Editar el boletín de noticias</h2><p>Aquí puede editar el boletín, editar los suscriptores y el pie de imprenta.</p><p>Si quiere incluir el boletín, puede hacerlo<br/><ol><li>Crear un elemento de menú. Cuando lo cree, puede añadir manualmente la siguiente entrada en \"Incluir el enlace o archivo\": <br /><strong>plugin:newsletter/templates/subscribe_newsletter.html</strong><br /></li><li>Si no quieres tener un elemento de menú separado, puedes crear un enlace en cualquier artículo a través de la función de enlace en el editor. El enlace debe tener el siguiente contenido: /plugin.php?menuid=1&amp;template=newsletter/templates/subscribe_newsletter.html .<br /></li><li>También puede utilizar el gestor de módulos para incluir el formulario de suscripción en cualquier lugar que desee. <br /></li><li>Además, también puede incluir un archivo en su página con el siguiente enlace en un elemento del menú:<br /><strong>plugin:newsletter/templates/news_archiv.html</strong></li><li>Con el marcador de posición #Enlace_Online# puede enlazar con la entrada del archivo en el sitio web. El enlace correcto se introducirá allí automáticamente.</li><li>Para el boletín se pueden utilizar los siguientes marcadores de posición: #título# (saludo) #nombre# (apellido) #Newsletter_Kuendigen# (enlace de cancelación)</li></ol>"; 
$this->content->template['news_message_2'] = "<h2 style=\"color:red;\">El boletín se ha enviado.</h2>"; 
$this->content->template['news_message_3'] = "<h2>Guardar el boletín de noticias</h2><p>Haga clic en Guardar boletín y todos los datos relevantes del boletín se guardarán en un archivo de volcado. Este almacenamiento es independiente del almacenamiento general.</p>"; 
$this->content->template['news_message_4'] = "Guardar el boletín de noticias"; 
$this->content->template['message_20016'] = "Dirección de correo electrónico con la que se envía:"; 
$this->content->template['message_20016a'] = "Varios ajustes"; 
$this->content->template['message_20017'] = "El nombre de la parte *de:*:"; 
$this->content->template['message_20018'] = "<p>Puede suscribirse a nuestro boletín de noticias aquí. Para ello, rellene el siguiente formulario. A continuación, recibirá un correo electrónico de confirmación, al que deberá responder.</p>
<p>Sólo entonces estará inscrito en el boletín.</p>"; 
$this->content->template['message_20018_1'] = "Archivo de boletines"; 
$this->content->template['message_20018_a'] = "nodecode<h2>:Suscríbase al boletín de noticias.</h2>"; 
$this->content->template['message_20019'] = "Por favor, introduzca sus datos."; 
$this->content->template['message_20020'] = "Suscribirse al boletín de noticias"; 
$this->content->template['message_20021'] = "Enviar"; 
$this->content->template['message_20021d'] = "Enviar a la siguiente lista de distribución"; 
$this->content->template['message_20021c'] = "Vista previa"; 
$this->content->template['message_20021a'] = "Correcto"; 
$this->content->template['newsmessage_20122'] = "Añadir archivos adjuntos"; 
$this->content->template['newsmessage_20122a'] = "Archivos adjuntos"; 
$this->content->template['message_20023'] = "El sujeto ha desaparecido."; 
$this->content->template['message_20024'] = "Crear un nuevo boletín de noticias"; 
$this->content->template['message_20025'] = "El mensaje ha desaparecido."; 
$this->content->template['message_20026'] = "Idioma no seleccionado."; 
$this->content->template['message_20027'] = "Crear una nueva lista de distribución de boletines"; 
$this->content->template['message_21027'] = "¿Mostrar la lista de distribución en el frontend?"; 
$this->content->template['message_21028'] = "¿Lista de distribución moderada?"; 
$this->content->template['message_20028'] = "Todos los abonados, incluidas las listas de distribución del sistema"; 
$this->content->template['message_20029'] = "Todas las listas de distribución de boletines"; 
$this->content->template['message_20030'] = "Listas de distribución del sistema"; 
$this->content->template['message_20030a'] = " y el resultado de la búsqueda Flex"; 
$this->content->template['message_20031'] = "Listas de distribución de boletines"; 
$this->content->template['message_20032'] = "No se ha especificado ninguna lista de distribución"; 
$this->content->template['message_20033'] = "En caso de que la lista de distribución del boletín informativo "; 
$this->content->template['message_20034'] = " ¿realmente se borrará?"; 
$this->content->template['message_20035'] = "En caso de que el boletín "; 
$this->content->template['message_20036'] = "Abonados activos "; 
$this->content->template['message_20037'] = "Si el abonado "; 
$this->content->template['message_20038'] = "\"Todos...\" o las listas de distribución individuales sólo pueden seleccionarse"; 
$this->content->template['message_20039'] = "La lista de distribución \"Test\" debe ser la única seleccionada."; 
$this->content->template['message_20040'] = "\"Abonados"; 
$this->content->template['message_20041'] = "Puede configurar la lista de distribución \"Prueba\" para enviar un boletín como prueba. Sólo las personas que asigne a esta lista de distribución recibirán el boletín enviado a la lista de distribución \"Prueba\" como vista previa. La lista de distribución \"Test\" no se muestra en el frontend, por lo que no es posible suscribirse a esta lista de distribución en el frontend. Los boletines de prueba enviados tampoco se muestran en el archivo de boletines del frontend."; 
$this->content->template['message_20042'] = "Activar la recepción de boletines"; 
$this->content->template['message_20043'] = "Desactivar la recepción de boletines"; 
$this->content->template['message_20044'] = "La letra \"A\" que precede a la fecha de inicio de sesión indica un abonado introducido por el admin... <br /> La letra \"I\" delante de la fecha de inicio de sesión indica que se trata de un abonado añadido a través de la importación de direcciones."; 
$this->content->template['erneut_versenden'] = "Reenviar."; 
$this->content->template['datum'] = "Creado"; 
$this->content->template['senddate'] = "Enviado"; 
$this->content->template['kundensuchen'] = "Buscar suscriptores del boletín de noticias"; 
$this->content->template['useranzahl'] = "# Suscribirse."; 
$this->content->template['gruppe'] = "Lista de distribución"; 
$this->content->template['newsletter_texthtml'] = "HTML-WYSIWYG"; 
$this->content->template['news_message1'] = "<h2>Seleccione un idioma</h2><p>Seleccione aquí el idioma en el que se va a crear un boletín.</p>"; 
$this->content->template['news_message2'] = "Seleccione"; 
$this->content->template['news_loeschen'] = "Borrar"; 
$this->content->template['news_loeschene'] = "Borrar este boletín"; 
$this->content->template['news_grp_loeschene'] = "Eliminar esta lista de distribución de boletines"; 
$this->content->template['news_edit'] = "Editar"; 
$this->content->template['news_edite'] = "Editar este boletín"; 
$this->content->template['news_grpname'] = "Lista de distribución del boletín"; 
$this->content->template['news_grpnamen'] = "Listas de distribución de boletines"; 
$this->content->template['news_grpdescript'] = "Descripción"; 
$this->content->template['news_grpfehlt'] = "No se ha seleccionado ninguna lista de distribución"; 
$this->content->template['grp_edite'] = "Editar la lista de distribución de este boletín"; 
$this->content->template['abo_loeschene'] = "Eliminar este abonado"; 
$this->content->template['abo_edite'] = "Editar la configuración de los abonados"; 
$this->content->template['message_news_is_del'] = "La entrada ha sido eliminada con éxito."; 
$this->content->template['message_news_not_del'] = "Esta lista de distribución no puede ser editada ni eliminada."; 
$this->content->template['news_imptext1'] = "
-- Para darse de baja, pulse aquí: http://#url#/plugin.php?menuid=1&amp;activate=#key#&amp;news_message=de_activate&amp;template=newsletter/templates/subscribe_newsletter.html #imp#"; 
$this->content->template['news_imptext2'] = "<hr/>Para cancelar el boletín de noticias, haga clic aquí: <br /> <a href=\"http://#url#/plugin.php?menuid=1&amp;activate=#key#&amp;news_message=de_activate&amp;template=newsletter/templates/subscribe_newsletter.html\" rel=\"unsubscribe nofollow\">Boletín de noticias cancelar</a><br />"; 
$this->content->template['news_mail1'] = "Boletín de noticias suscrito por seitenurl."; 
$this->content->template['news_mail2'] = "Te has suscrito al boletín de seitenurl. Si no te has suscrito a este boletín o no lo quieres, ignora este correo, no recibirás más. Para activar el boletín, haga clic en el siguiente enlace"; 
$this->content->template['news_mail3'] = "Un nuevo suscriptor se ha inscrito en una o varias listas moderadas"; 
$this->content->template['news_front1'] = "<h2>Boletín de noticias suscrito</h2><p>Se ha suscrito a nuestro boletín de noticias. En unos minutos debería recibir un correo electrónico con un enlace de confirmación.</p><p>Haga clic en el enlace del correo electrónico para suscribirse finalmente a este boletín.</p>"; 
$this->content->template['news_front2'] = "<h2>Boletín de noticias </h2><p>Su suscripción a nuestro boletín de noticias ha sido activada. A partir de hoy empezarás a recibir nuestro boletín. Si desea darse de baja, sólo tiene que hacer clic en el enlace para darse de baja de cualquier correo electrónico que reciba de nosotros.</p>"; 
$this->content->template['news_front3'] = "<h2>Boletín de noticias cancelado</h2>\",<p>\"El boletín de noticias ha sido cancelado y sus datos han sido eliminados</p>."; 
$this->content->template['news_front4'] = "Sus datos"; 
$this->content->template['news_front5'] = "El Sr"; 
$this->content->template['news_front6'] = "La Sra"; 
$this->content->template['news_front7'] = "Nombre"; 
$this->content->template['news_front8'] = "Apellido"; 
$this->content->template['news_front9'] = "Calle y número de casa"; 
$this->content->template['news_front10'] = "Código postal"; 
$this->content->template['news_front11'] = "Residencia"; 
$this->content->template['news_front12'] = "Idioma"; 
$this->content->template['news_front13'] = "Estado"; 
$this->content->template['news_front14'] = " Falta la especificación"; 
$this->content->template['news_front15'] = " Especificación no válida"; 
$this->content->template['news_front16'] = " ya existe. El abonado ha sido asignado a las listas de distribución seleccionadas."; 
$this->content->template['news_front17'] = "Miembro de la IAKS"; 
$this->content->template['news_front18'] = "sb abonado"; 
$this->content->template['news_front19'] = "Empresa"; 
$this->content->template['news_show_recipients'] = "Muestra las direcciones de correo a las que se ha enviado el boletín."; 
$this->content->template['news_message3'] = "Idioma"; 
$this->content->template['message_aboeintragen'] = "Introducir/cambiar la configuración de los abonados"; 
$this->content->template['plugin']['newsletter']['alle'] = "Todo"; 
$this->content->template['plugin']['newsletter']['allow_delete'] = "Si se activa este interruptor, los suscriptores se eliminan irremediablemente (manualmente o al darse de baja del boletín), de lo contrario, un suscriptor simplemente se marca como eliminado y deja de estar disponible para su procesamiento. Este último sirve de prueba para la ley."; 
$this->content->template['plugin']['newsletter']['altnewsletter'] = "Administración del boletín de noticias"; 
$this->content->template['plugin']['newsletter']['inhalt_text'] = "Contenido como texto"; 
$this->content->template['plugin']['newsletter']['inhalt_html'] = "Contenido como HTML"; 
$this->content->template['plugin']['newsletter']['userdaten'] = "Datos avanzados del usuario"; 
$this->content->template['plugin']['newsletter']['sprachwahl'] = "¿Permitir la selección del idioma para la suscripción al boletín?"; 
$this->content->template['plugin']['newsletter']['text'] = "¿Mostrar texto sobre el inicio de sesión?"; 
$this->content->template['plugin']['newsletter']['html_mails'] = "¿Correos HTML?"; 
$this->content->template['plugin']['newsletter']['editor'] = "Editor WYSIWYG tinymce?"; 
$this->content->template['plugin']['newsletter']['sprache'] = "Idioma"; 
$this->content->template['plugin']['newsletter']['daten'] = "Fechas."; 
$this->content->template['plugin']['newsletter']['vorname'] = "Nombre"; 
$this->content->template['plugin']['newsletter']['nachname'] = "Apellido"; 
$this->content->template['plugin']['newsletter']['strasse'] = "Calle y número de casa"; 
$this->content->template['plugin']['newsletter']['postleitzahl'] = "Código postal"; 
$this->content->template['plugin']['newsletter']['wohnort'] = "Residencia"; 
$this->content->template['plugin']['newsletter']['staat'] = "Estado"; 
$this->content->template['plugin']['newsletter']['phone'] = "Teléfono"; 
$this->content->template['plugin']['newsletter']['speichern'] = "Entre en"; 
$this->content->template['plugin']['newsletter']['email'] = "Envíe un correo electrónico a"; 
$this->content->template['plugin']['newsletter']['eingabe_datei'] = "Introduzca el archivo:"; 
$this->content->template['plugin']['newsletter']['dokument'] = "El documento:"; 
$this->content->template['plugin']['newsletter']['durchsuchen'] = "Navegar..."; 
$this->content->template['plugin']['newsletter']['datei_upload'] = "Sube el archivo:"; 
$this->content->template['plugin']['newsletter']['upload'] = "cargar"; 
$this->content->template['plugin']['newsletter']['sicherung'] = "<h3>Crear una copia de seguridad de la base de datos</h3><p> Aquí puede crear una copia de seguridad de la base de datos, que puede restaurar después de una nueva instalación o en cualquier otro momento.</p>"; 
$this->content->template['plugin']['newsletter']['sicherung_einspielen'] = "Importar una copia de seguridad"; 
$this->content->template['plugin']['newsletter']['sicherung_ready'] = "Se ha importado el archivo de copia de seguridad."; 
$this->content->template['plugin']['newsletter']['hinweis'] = "Para importar una copia de seguridad, seleccione el archivo de copia de seguridad:"; 
$this->content->template['plugin']['newsletter']['warnung'] = "ATENCIÓN - Si importa una copia de seguridad, todos los datos actuales se borrarán irremediablemente. Por lo tanto, es esencial que cree una copia de seguridad de antemano"; 
$this->content->template['plugin']['newsletter']['make_dump'] = "Crear una copia de seguridad ahora"; 
$this->content->template['plugin']['newsletter']['anzahlgef'] = "Número de abonados encontrados:"; 
$this->content->template['plugin']['newsletter']['anzahlgefgrp'] = "Número de listas de distribución encontradas:"; 
$this->content->template['plugin']['newsletter']['anzahlgefnl'] = "Número de boletines encontrados:"; 
$this->content->template['plugin']['newsletter']['asc'] = "ascendente"; 
$this->content->template['plugin']['newsletter']['desc'] = "descendente"; 
$this->content->template['plugin']['newsletter']['sort'] = "Clasificación"; 
$this->content->template['plugin']['newsletter']['Ihr_Suchbegriff'] = "Su término de búsqueda"; 
$this->content->template['plugin']['newsletter']['aktivjn'] = "Activado"; 
$this->content->template['plugin']['newsletter']['Newsletter_Kunden'] = "Suscriptores del boletín de noticias"; 
$this->content->template['plugin']['newsletter']['Anrede'] = "Saludo"; 
$this->content->template['plugin']['newsletter']['groups'] = "Gestión de listas de distribución de boletines"; 
$this->content->template['plugin']['newsletter']['errmsg']['attachment_already_exist'] = "El archivo adjunto ya ha sido cargado para este boletín."; 
$this->content->template['plugin']['newsletter']['errmsg']['file_fehlt'] = "Archivo no encontrado."; 
$this->content->template['plugin']['newsletter']['errmsg']['kein_filename'] = "Falta el nombre del archivo adjunto."; 
$this->content->template['plugin']['newsletter']['imgtext']['news_edit_attachment'] = "Borrar el archivo adjunto:"; 
$this->content->template['plugin']['newsletter']['label']['language'] = "Seleccione los idiomas que desea que estén disponibles para la suscripción al boletín."; 
$this->content->template['plugin']['newsletter']['label']['timeout'] = "Protección de tiempo de espera: número de correos enviados a la vez en intervalos de 10 segundos"; 
$this->content->template['plugin']['newsletter']['linktext']['news_edit_attachment'] = "Mostrar archivo adjunto en una nueva ventana."; 
$this->content->template['plugin']['newsletter']['linktext']['sync'] = "En caso de que este registro esté marcado con el Id "; 
$this->content->template['plugin']['newsletter']['linktext']['sync2'] = " ¿realmente se borrará?"; 
$this->content->template['plugin']['newsletter']['message']['attachment_loaded'] = "El archivo fue subido como un adjunto. <br /> Por favor, guarde todos los cambios."; 
$this->content->template['plugin']['newsletter']['message']['attachment_deleted'] = "El archivo adjunto ha sido eliminado. <br /> Por favor, guarde todos los cambios."; 
$this->content->template['plugin']['newsletter']['message']['nl_saved'] = "Los datos de su boletín se han guardado."; 
$this->content->template['plugin']['newsletter']['registration'] = "Registro"; 
$this->content->template['plugin']['newsletter']['submit']['cancel'] = "Cancelar"; 
$this->content->template['plugin']['newsletter']['submit']['save'] = "Guardar"; 
$this->content->template['plugin']['newsletter']['submit']['send'] = "Enviar"; 
$this->content->template['plugin']['newsletter']['text2']['groups_nl_send'] = "Nota: El número que se muestra en cada caso es el número de entradas de abonados existentes, pero no marcadas, en la base de datos. Las direcciones de correo electrónico no válidas y las direcciones duplicadas que puedan existir no se envían. Por lo tanto, el número total de suscriptores que reciben el boletín mostrado en el resumen puede diferir de los valores indicados aquí."; 
$this->content->template['plugin']['newsletter']['text2']['mails_per_step'] = "Número de correos electrónicos por paso de envío:"; 
$this->content->template['plugin']['newsletter']['text2']['news_new_attachment'] = "La carga de archivos adjuntos sólo es posible después de introducir el asunto y el mensaje."; 
$this->content->template['plugin']['newsletter']['text2']['news_edit_attachment2'] = "Uno o varios de sus archivos sólo están introducidos en la BD, pero ya no se encuentran en el directorio. Para eliminar el error, puede subir estos archivos aquí o a través de FTP o eliminarlos inmediatamente si es necesario. Tenga en cuenta que los archivos deben tener el mismo nombre y el mismo tamaño al subirlos (esto último no a través de FTP)."; 
$this->content->template['plugin']['newsletter']['text2']['news_edit'] = "Editar el boletín de noticias"; 
$this->content->template['plugin']['newsletter']['text2']['news_send_tip'] = "Nota: También se enviarán los archivos adjuntos y la impresión que haya creado."; 
$this->content->template['plugin']['newsletter']['link']['grp_std'] = "NL Norma de la lista de distribución"; 
$this->content->template['plugin']['newsletter']['link']['grp_std_descr'] = "Lista de distribución estándar de NL"; 
$this->content->template['plugin']['newsletter']['used_file'] = "Nombre del archivo"; 
$this->content->template['plugin']['newsletter']['size_text'] = "Tamaño"; 
$this->content->template['plugin']['newsletter']['datum'] = "Fecha"; 
$this->content->template['plugin']['newsletter']['loeschen3'] = "Borrar"; 
$this->content->template['plugin']['newsletter']['export'] = "Exportar CSV"; 
$this->content->template['plugin']['newsletter']['header01'] = "Archivos cargados"; 
$this->content->template['plugin']['newsletter']['datei_loeschen'] = "Borrar la selección"; 
$this->content->template['plugin']['newsletter']['das_dokument'] = "El documento:"; 
$this->content->template['plugin']['newsletter']['import_starten'] = "Iniciar la importación"; 
$this->content->template['plugin']['newsletter']['datei_hochladen'] = "Cargar archivo"; 
$this->content->template['plugin']['newsletter']['text03'] = "Si su archivo ya existe, puede borrarlo ahora antes de importarlo para evitar problemas de carga."; 
$this->content->template['plugin']['newsletter']['text04'] = "La primera línea del archivo de importación debe contener estos nombres de campo en cualquier orden: NOMBRE, APELLIDO, CALLE, CÓDIGO POSTAL, CIUDAD, CORREO. El archivo de importación debe ser un archivo CSV. Los campos deben estar separados con HT (Tab) (x09, t), las líneas deben terminar con CR LF (x0D0A, rn)."; 
$this->content->template['plugin']['newsletter']['datei_importieren'] = "1. Paso: Importar el archivo"; 
$this->content->template['plugin']['newsletter']['datei_ist_oben'] = "2. Paso: Importar"; 
$this->content->template['plugin']['newsletter']['liste_waehlen'] = "Seleccione la(s) lista(s) de distribución"; 
$this->content->template['plugin']['newsletter']['leeren_waehlen'] = "¿Lista(s) de distribución vacía(s) en la importación?"; 
$this->content->template['plugin']['newsletter']['datei_ist_oben_text'] = "El archivo se ha cargado con éxito."; 
$this->content->template['plugin']['newsletter']['importprotokoll'] = "Registro de importación"; 
$this->content->template['plugin']['newsletter']['importprotokoll3'] = "Resumen de los registros de errores de importación"; 
$this->content->template['plugin']['newsletter']['daten_eingetragen'] = "Los registros han sido introducidos."; 
$this->content->template['plugin']['newsletter']['daten_del'] = "Los registros han sido eliminados."; 
$this->content->template['plugin']['newsletter']['daten_nicht_eingetragen'] = "No se han introducido registros"; 
$this->content->template['plugin']['newsletter']['daten_nicht_eingetragen2'] = "Registros de datos no introducidos"; 
$this->content->template['plugin']['newsletter']['pageheader']['error_report'] = "Resumen del registro de errores de importación"; 
$this->content->template['plugin']['newsletter']['pageheader']['error_report2'] = "Detalles del registro de errores de importación"; 
$this->content->template['plugin']['newsletter']['report_deleted'] = "Registro de errores borrado"; 
$this->content->template['plugin']['newsletter']['id'] = "Id"; 
$this->content->template['plugin']['newsletter']['import_time'] = "Tiempo"; 
$this->content->template['plugin']['newsletter']['normaler_user'] = "Usuario"; 
$this->content->template['plugin']['newsletter']['records_to_import'] = "Total"; 
$this->content->template['plugin']['newsletter']['error_count'] = "Error #"; 
$this->content->template['plugin']['newsletter']['success_count'] = "Éxito"; 
$this->content->template['plugin']['newsletter']['import_error_report_show_details'] = "Mostrar detalles"; 
$this->content->template['plugin']['newsletter']['alttext']['sync'] = "Borrar este registro de errores"; 
$this->content->template['plugin']['newsletter']['error_count2'] = "Número total de errores"; 
$this->content->template['plugin']['newsletter']['error_no'] = "Lfd. #"; 
$this->content->template['plugin']['newsletter']['import_file_record_no'] = "Set #"; 
$this->content->template['plugin']['newsletter']['import_file_field_position'] = "Campo #"; 
$this->content->template['plugin']['newsletter']['import_file_excel_field_position'] = "Excel pos."; 
$this->content->template['plugin']['newsletter']['import_file_field_name'] = "Nombre del campo"; 
$this->content->template['plugin']['newsletter']['import_error_msg'] = "Mensaje de error"; 
$this->content->template['plugin']['newsletter']['completion_code'] = "Código"; 
$this->content->template['plugin']['newsletter']['email_error'] = "No hay dirección de correo electrónico válida"; 
$this->content->template['plugin']['newsletter']['max255_4'] = "Se ha superado la longitud máxima de entrada de 255 caracteres."; 
$this->content->template['plugin']['newsletter']['email_schon_da'] = "Esta dirección de correo electrónico ya existe."; 
$this->content->template['plugin']['newsletter']['feldanzahl'] = "Falta un nombre de campo: NOMBRE, APELLIDOS, CALLE, ZONA, CIUDAD, CORREO."; 
$this->content->template['plugin']['newsletter']['feldnamefalsch'] = "Nombre de campo erróneo: NOMBRE, NOMBRE, CALLE, CIF, CIUDAD, CORREO..."; 
$this->content->template['plugin_glossar_dubletten_entfernen'] = "Eliminar dobles"; 
$this->content->template['plugin_newsletter_dubletten_entfernen_text'] = "Eliminar las direcciones de correo duplicadas de la base de datos."; 
$this->content->template['plugin_newsletter_dubletten_entfernen_field'] = "Eliminar dobles"; 
$this->content->template['plugin_newsletter_import'] = "Importar direcciones"; 
$this->content->template['plugin_newsletter_export'] = "Direcciones de exportación"; 
$this->content->template['plugin_newsletter_import_text'] = "Importar direcciones (archivo CSV)"; 
$this->content->template['plugin_newsletter_export_text'] = "Exportar direcciones (archivo CSV)"; 
$this->content->template['plugin_newsletter_inaktive_lschen'] = "Borrar inactivo"; 
$this->content->template['plugin_newsletter_blacklist_lschen'] = "Eliminar abonados mediante la importación de listas negras"; 
$this->content->template['plugin_newsletter_inaktive_lschen_text'] = "Elimina todos los suscriptores inactivos sin confirmación"; 
$this->content->template['plugin_newsletter_inaktive_eintrge_lschen'] = "Eliminar los abonados inactivos"; 
$this->content->template['plugin_newsletter_inaktive_geloescht'] = "Se han eliminado los abonados inactivos."; 
$this->content->template['plugin_newsletter_dubletten_geloescht'] = "Se han eliminado las direcciones de correo duplicadas."; 
$this->content->template['newsletter_verteilerliste'] = "Lista de distribución"; 

 ?>