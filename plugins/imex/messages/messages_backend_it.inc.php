<?php 
$this->content->template['message']['plugin']['test']['name'] = "Plugin \"Test"; 
$this->content->template['message']['plugin']['test']['kopf'] = "<h1>Backend del plugin di prova</h1><p>Questo modello non è accessibile, ma non ha senso e non è conforme a X-HTML. Tuttavia, dovrebbe essere utile per spiegare la programmazione dei plugin di Papoo.</p><p>Le diverse voci di menu di questo plugin sono anche senza senso. Si riferiscono sempre allo stesso template \"test_back.html\". I punti sono solo per mostrare come i punti di menu possono essere creati nel file XML del plugin.</p><p>L'integrazione del modello frontend funziona come segue: Creare una nuova voce di menu. Entrate lì sotto \"Integrazione del link o del file\" (in basso) inserire il seguente: <strong>plugin:test/templates/test_front.html</strong>. Ora il modello è disponibile nel frontend.</p><p>I moduli contenuti in questo modello possono essere inseriti con il gestore di moduli qui nell'amministrazione. Per tutti coloro che non hanno ancora scoperto la cosa, si può trovare sotto \"System -&gt; Module Manager\".</p>"; 
$this->content->template['message']['plugin']['test']['form_kopf'] = "Ed ecco un piccolo modulo:"; 
$this->content->template['message']['plugin']['test']['form_legend'] = "Valore di prova"; 
$this->content->template['message']['plugin']['test']['form_testwert_label'] = "Passare un valore di test via POST"; 
$this->content->template['message']['plugin']['test']['no_table_selected'] = "Nessun tavolo selezionato"; 

 ?>