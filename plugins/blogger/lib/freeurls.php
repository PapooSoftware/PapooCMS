<?php
/** Das wird einfach in die cms Klasse inkludiert
 * es muss also nix instantiiert werden...
 */

if (stristr($sprechende_url,"tag/")) {
	//OK, ist ein Blog Wolke KW, also KW rausholen aus der url
	$data_blog1=explode("tag/",$sprechende_url);
	$data_blog2=explode("-",$data_blog1['1']);
	$data_blog3=explode("/",$data_blog2['0']);
	$wort=trim($data_blog3['0']);

	//Dann die notwendigen IDs aus der Tabelle holen
	$sql = sprintf("SELECT * FROM `%s`
            LEFT JOIN `%s` ON
                blogger_word_article_lookup_word_id = blogger_wordlist_id
            INNER JOIN `%s` ON
                `lart_id` = `blogger_word_article_lookup_lan_repore_id`
                %s
            GROUP BY `blogger_wordlist_id`
            ORDER BY `blogger_wordlist_count` DESC, `blogger_wordlist_word` ASC
            LIMIT %d",
		DB_PRAEFIX."plugin_blogger_wordlist",
		DB_PRAEFIX."plugin_blogger_word_article_lookup",

		DB_PRAEFIX."papoo_lookup_art_cat",
		//$this->checked->menuid != 1 ? 'WHERE  `lcat_id` = \'' . $this->checked->menuid . '\'' : '',
		"WHERE blogger_wordlist_word='".$this->db->escape($wort)."'",
		1
	);


	$result=$this->db->get_results($sql,ARRAY_A);
	//Variablen für die Steuerung setzen
	$this->checked->widget = 'wordcloud';
	$this->checked->word_id=$result['0']['blogger_word_article_lookup_word_id'];
	$this->checked->menuid=(int) $result['0']['lcat_id'];

	if (!empty($result)) {
		$this->checked->do_404="";
	}
}

if (stristr($sprechende_url,"date/")) {
	$data_blog1=explode("date/",$sprechende_url);
	$data_blog2=explode("-",$data_blog1['1']);
	$data_blog3=explode("/",$data_blog2['3']);
	$blog_year=trim($data_blog2['1']);
	$blog_month=trim($data_blog2['2']);
	$blog_day=trim($data_blog3['0']);
	$this->checked->menuid=(int) $data_blog2['0'];
	$this->checked->widget="blogcal";

	if (!is_numeric($this->checked->monats_id)) {
		$this->checked->monats_id=$blog_month+12;
	}

	$this->checked->time=strtotime("$blog_year-$blog_month-$blog_day");
	$this->checked->date_time=$this->checked->time;
	$this->checked->do_404="";
}

if (stristr($sprechende_url,"archiv/")) {
	$data_blog1=explode("archiv/",$sprechende_url);
	$data_blog2=explode("-",$data_blog1['1']);

	$data_blog2['2']=str_ireplace('/',"",$data_blog2['2']);
	$blog_year=(int) trim($data_blog2['1']);
	$blog_month=(int) trim($data_blog2['2']);
	$blog_day=1;
	$this->checked->menuid=(int) $data_blog2['0'];
	$this->checked->widget="blogmonth";

	$this->checked->monats_time=strtotime("$blog_year-$blog_month-$blog_day");
	$this->checked->do_404="";
}
