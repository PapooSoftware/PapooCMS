<?php

/**
 * Class reorder_class
 */
class reorder_class
{
	/**
	 * reorder_class constructor.
	 */
	function __construct()
	{
		// Einbindung globaler papoo-Klassen
		global $user, $cms, $db, $db_praefix, $checked, $menu, $content, $user;
		$this->user = & $user;
		$this->cms = & $cms;
		$this->db = & $db;
		$this->db_praefix = $db_praefix;
		$this->checked = & $checked;
		$this->menu = & $menu;
		$this->content = & $content;
		$this->user = & $user;

		// Aktions-Weiche
		// **************
		global $template;

		if (defined("admin")) {
			$this->user->check_intern();

			global $template;

			if (strpos("XXX".$template, "reorder_backend.html")) {
				IfNotSetNull($this->checked->reorder_action);
				switch ($this->checked->reorder_action) {
				case "reorder_plugin":
					$this->content->template['plugin']['devtools']['template_weiche'] = "reorder_plugin";
					$this->switch_reorder_plugin();
					break;

				case "reorder_menu":
					$this->reorder_menu();
					$this->content->template['reorder_message'] =
						$this->content->template['plugin']['devtools']['menue_ok'];
					break;

				case "reorder_artikel":
					$this->reorder_artikel();
					$this->reorder_artikel_start();
					$this->content->template['reorder_message'] =
						$this->content->template['plugin']['devtools']['artikel_ok'];
					break;

				case "reorder_images":
					$this->reorder_images();
					$this->content->template['reorder_message'] =
						$this->content->template['plugin']['devtools']['bilder_ok'];
					break;

				case "reorder_linkkat":
					$this->reorder_link_cat();
					$this->content->template['reorder_message'] =
						$this->content->template['plugin']['devtools']['kategorie_ok'];
					break;

				case "reorder_surls":
					$this->make_menu_artikel_surls();
					$this->content->template['reorder_message'] =
						$this->content->template['plugin']['devtools']['surls_ok'];
					break;

				case "reorder_rights":
					$this->make_all_rights_chef();
					$this->content->template['reorder_message'] =
						$this->content->template['plugin']['devtools']['rights_ok'];
					break;

					//reorder_action_free_urls
				case "reorder_action_free_urls":
					$this->make_all_free_urls_lang_ok();
					$this->content->template['reorder_message'] =
						$this->content->template['plugin']['devtools']['free_urls_ok'];
					break;

				case "reorder_action_alt_images":
					$this->make_all_alt_images_to_db();
					$this->content->template['reorder_message'] =
						$this->content->template['plugin']['devtools']['alt_images_urls_ok'];
					break;

				case "reorder_action_no_alt_images":
					$this->show_no_alt_images();
					$this->content->template['reorder_message'] =
						$this->content->template['plugin']['devtools']['alt_images_urls_ok'];
					break;

				case "reorder_action_artmen_chef":
					$this->reset_art_men_rechte_chef();
					$this->content->template['reorder_message'] =
						$this->content->template['plugin']['devtools']['art_men_chef_ok'];
					break;

				case "reorder_action_dl_jeder":
					$this->grant_download_rights_to_everyone();
					$this->content->template['reorder_message'] =
						$this->content->template['plugin']['devtools']['dl_rechte_jeder_ok'];
					break;

				default:
					$admin_rights = $this->check_admin_rights($this->user->userid);
					if (!$admin_rights) $this->content->template['reorder_message'] = $this->content->template['plugin']['devtools']['need_admin'];
				}
			}
		}
	}

	/**
	 * reorder_class::grant_download_rights_to_everyone()
	 *
	 * @return void
	 */
	private function grant_download_rights_to_everyone()
	{
		$jedergruppe = 10;

		$sql=sprintf("SELECT `downloadid` FROM %s",
			$this->cms->tbname['papoo_download']
		);
		$result = $this->db->get_results($sql,ARRAY_A);

		$sql=sprintf("SELECT `download_id_id` FROM %s WHERE `gruppen_id_id`=%d",
			$this->cms->tbname['papoo_lookup_download'],
			$jedergruppe
		);
		$result2 = $this->db->get_results($sql,ARRAY_A);

		if(is_array($result)) {
			foreach($result as $value) {
				$set = false;
				foreach($result2 as $value2) {
					if($value['downloadid'] == $value2['download_id_id']) {
						$set = true;
						break;
					}
				}
				if(!$set) {
					$sql=sprintf("INSERT INTO `%s` (`download_id_id`, `gruppen_id_id`) VALUES (%d, %d);",
						$this->cms->tbname['papoo_lookup_download'],
						$value['downloadid'],
						$jedergruppe
					);
					$this->db->query($sql);
				}
			}
		}
	}

	/**
	 * reorder_class::reset_art_men_rechte_chef()
	 *
	 * @return void
	 */
	private function reset_art_men_rechte_chef()
	{
		//Gruppenid Chefredakteure (kann man natürlich auf für andere Gruppen nutzen!)
		$chefgruppe="15";

		//ZUerst die Menüpunkte rausholen
		$sql=sprintf("SELECT * FROM %s
						GROUP BY menuid",
			$this->cms->tbname['papoo_lookup_men_ext']
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		//Die Chefrechte löschen
		$sql=sprintf("DELETE  FROM %s
						WHERE gruppenid='%d'",
			$this->cms->tbname['papoo_lookup_men_ext'],
			$chefgruppe
		);
		$this->db->query($sql);

		//Dann durchgehen und für alle neu setzen
		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				//Neue setzen
				$sql=sprintf("INSERT INTO %s
								SET gruppenid='%d',
								menuid='%d'",
					$this->cms->tbname['papoo_lookup_men_ext'],
					$chefgruppe,
					$value['menuid']
				);
				$this->db->query($sql);
			}
		}

		//Dann die artikel holen
		$sql=sprintf("SELECT * FROM %s
						GROUP BY article_wid_id",
			$this->cms->tbname['papoo_lookup_write_article']
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		//Chefrechte Artikel löschen
		$sql=sprintf("DELETE  FROM %s
						WHERE gruppeid_wid_id='%d'",
			$this->cms->tbname['papoo_lookup_write_article'],
			$chefgruppe
		);
		$this->db->query($sql);

		//Alle neu setzen
		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				//Neue setzen
				$sql=sprintf("INSERT INTO %s
								SET gruppeid_wid_id='%d',
								article_wid_id='%d'",
					$this->cms->tbname['papoo_lookup_write_article'],
					$chefgruppe,
					$value['article_wid_id']
				);
				$this->db->query($sql);
			}
		}
	}

	function switch_reorder_plugin()
	{
		global $menu;
		// .. wenn plugin-order-ids uebergeben werden, dann diese speichern und Menue neu aufbauen

		if (!empty($this->checked->menue_order)) {
			asort($this->checked->menue_order);
			$temp_order_id = 1000;
			foreach ($this->checked->menue_order as $menuid => $order_id) {
				$sql = sprintf ("UPDATE %s SET order_id='%d' WHERE menuid='%d'",
					$this->db_praefix."papoo_menuint",
					$temp_order_id,
					$menuid
				);
				$this->db->query($sql);

				$temp_order_id += 10;
			}

			// Menue neu aufbauen.
			$menu->make_menu();
		}

		// Liste der Plugin-Menue-Punkte aufbauen.
		$temp_plugin_menues = array();

		if (!empty($menu->data_back_complete)) {
			foreach ($menu->data_back_complete as $menu) {
				if ($menu['untermenuzu'] == "54") {
					$temp_plugin_menues[] = $menu;
				}
			}
		}
		$this->content->template['plugin']['devtools']['plugin_menues'] = $temp_plugin_menues;
	}

	/**
	 * reorder_class::show_no_alt_images()
	 *
	 * @return void
	 */
	private function show_no_alt_images()
	{
		$sql=sprintf("SELECT * FROM %s
						LEFT JOIN %s ON lan_image_id=image_id
						WHERE lang_id='%d'
						AND alt = ''
					 ",
			$this->cms->tbname['papoo_language_image'],
			$this->cms->tbname['papoo_images'],
			$this->db->escape($this->checked->reorder_action_lang_id)
		);
		$temp_dat1=$this->db->get_results($sql,ARRAY_A);
		debug::print_d(count($temp_dat1));
		if (is_array($temp_dat1)) {
			echo "<table>";
			echo '<tr><td colspan="2" style="padding:10px;background:#ccc;margin-top:30px;">';
			echo '<h2 style="color:#000;">'.$value['image_name']."</h2>";
			echo "</td></tr>";
			$zahl=0;
			foreach ($temp_dat1 as $key=>$value) {
				if (file_exists(PAPOO_ABS_PFAD."/images/".$value['image_name']) && !empty($value['image_name'])) {
					if ($zahl % 2 == 0) {
						//$zahl ist gerade
						$col="background:#EFEBE6;";
					}
					else {
						//$zahl ist ungerade
						$col="background:#fff;";
					}

					echo '<tr style="'.$col.'"><td>';
					echo '<a href="./image.php?menuid=21&image_id='.$value['image_id'].'&action=EDIT" target="images"><img src="http://www.professional-tent.com/images/'.$value['image_name'].'"/></a>';
					echo "</td>";
					echo '<td style="padding:5px;">alt: '.$value['alt'].'<br />';
					echo "title: ".$value['title'].'<br />';
					echo "</tr>";
					$zahl++;
				}
			}
			echo "</table>";
			exit();
		}
	}

	/**
	 * reorder_class::make_all_alt_images_to_db()
	 *
	 * @return void
	 */
	private function make_all_alt_images_to_db()
	{
		//Artikeldaten rausholen
		$sql=sprintf("SELECT * FROM %s
						WHERE lang_id='%d'
						ORDER BY lan_repore_id ASC",
			$this->cms->tbname['papoo_language_article'],
			$this->db->escape($this->checked->reorder_action_lang_id)
		);
		$temp_dat1=$this->db->get_results($sql,ARRAY_A);

		//Alle Biler
		$sql=sprintf("SELECT * FROM %s
						",
			$this->cms->tbname['papoo_images']
		);
		$temp_img_dat1=$this->db->get_results($sql,ARRAY_A);
		if (is_array($temp_img_dat1)) {
			foreach ($temp_img_dat1 as $key=>$value) {
				$neu_img[$value['image_name']]=$value['image_id'];
			}
		}

		//Dann durchgehen und Bilder rausholen
		if (is_array($temp_dat1)) {
			foreach ($temp_dat1 as $key=>$value) {
				if(!isset($value['lan_teaser'])) {
					$value['lan_teaser'] = NULL;
				}

				$data[$key]['headline']=$value['header'];
				$data[$key]['url']=$value['url_header'];
				$str=strip_tags($value['lan_article_sans'],"<img>");
				$str2=strip_tags($value['lan_teaser'],"<img>");
				$str=$str.$str2;
				preg_match_all("#<img.+?src=\"(.+?)\" />#", $str, $matches);
				$images=array();
				if (is_array($matches['1'])) {
					foreach ($matches['1'] as $key2=>$value2) {
						//src
						$is1=explode("\"",$value2);
						$images[$key2]['src']=$is1['0'];
						$basename=basename($is1['0']);

						$images[$key2]['id']=$neu_img[$basename];

						//alt
						$is1=explode("alt=\"",$value2);
						$is2=explode("\"",$is1['1']);
						$images[$key2]['alt']=$is2['0'];

						//title
						$is1=explode("title=\"",$value2);
						$is2=explode("\"",$is1['1']);
						$images[$key2]['title']=$is2['0'];
					}
				}
				$data[$key]['images']=$images;
			}
		}
		if ($this->checked->doit!="ok") {
			echo "<table>";
			if (is_array($data)) {
				foreach ($data as $key=>$value) {
					echo '<tr><td colspan="2" style="padding:10px;background:#ccc;margin-top:30px;">';
					echo '<h2 style="color:#000;">'.$value['headline']."</h2>";
					echo "</td></tr>";
					$zahl=0;
					if (is_array($value['images'])) {
						foreach ($value['images'] as $keyi=>$valuei) {
							if ($zahl % 2 == 0) {
								//$zahl ist gerade
								$col="background:#EFEBE6;";
							}
							else {
								//$zahl ist ungerade
								$col="background:#fff;";
							}

							echo '<tr style="'.$col.'"><td>';
							echo '<a href="./image.php?menuid=21&image_id='.$valuei['id'].'&action=EDIT" target="images"><img src="http://www.professional-tent.com/'.$valuei['src'].'"/></a>';
							echo "</td>";
							echo '<td style="padding:5px;">alt: '.$valuei['alt'].'<br />';
							echo "title: ".$valuei['title'].'<br />';
							echo "</tr>";
							$zahl++;
						}
					}
				}
			}
			echo "</table>";
			exit();
		}
		#$doit="ok"; && $this->checked->doit=="ok"
		if (is_numeric($this->checked->reorder_action_lang_id1111) && $this->checked->doit1111=="ok" ) {
			if (is_array($data)) {
				foreach ($data as $key=>$value) {
					if (is_array($value['images'])) {
						foreach ($value['images'] as $key2=>$value2) {
							//schauen ob eines vorhanden
							$sql=sprintf("SELECT * FROM %s
										WHERE lan_image_id='%d'
										AND lang_id='%d'",
								$this->cms->tbname['papoo_language_image'],
								$value2['id'],
								$this->checked->reorder_action_lang_id
							);
							$result=$this->db->get_results($sql,ARRAY_A);
							if (empty($result['0']['alt'])) {
								$sql=sprintf("DELETE  FROM %s
											WHERE lan_image_id='%d'
										AND lang_id='%d'",
									$this->cms->tbname['papoo_language_image'],
									$value2['id'],
									$this->checked->reorder_action_lang_id
								);
								if (empty($value2['title'])) {
									$value2['title']=$value2['alt'];
								}
								if (empty($value2['alt'])) {
									$value2['alt']=$value2['title'];
								}
								//Leer dann einbauen
								$sql=sprintf("INSERT INTO %s
										SET lan_image_id='%d',
										 lang_id='%d',
										 alt='%s',
										 title='%s'",
									$this->cms->tbname['papoo_language_image'],
									$value2['id'],
									$this->checked->reorder_action_lang_id,
									$this->db->escape($value2['alt']),
									$this->db->escape($value2['title'])
								);
							}
						}
					}
				}
			}
		}
	}

	/**
	 * reorder_class::make_all_free_urls_lang_ok()
	 *
	 * @return void
	 */
	private function make_all_free_urls_lang_ok()
	{
		//ID der Standardsprache
		#debug::print_d($this->content->template['lang_front_default']);
		$sql = sprintf("SELECT lang_id FROM %s WHERE lang_short='%s'",
			$this->cms->tbname['papoo_name_language'],
			$this->db->escape($this->content->template['lang_front_default'])
		);
		$standard_lang = $this->db->get_var($sql);

		//Sprachids und Kürzel
		$sql = sprintf("SELECT lang_id, lang_short FROM %s WHERE lang_id<>'%s'",
			$this->cms->tbname['papoo_name_language'],
			$this->db->escape($standard_lang)
		);
		$langs = $this->db->get_results($sql,ARRAY_A);
		if (is_array($langs)) {
			foreach ($langs as $key=>$value) {
				$lang_zu[$value['lang_id']]=$value['lang_short'];
			}
		}

		//Zuerst mal alle holen außer Standardsprache
		$sql=sprintf("SELECT * FROM %s
						WHERE lang_id <> '%d'",
			$this->cms->tbname['papoo_language_article'],
			$this->db->escape($standard_lang)
		);
		$temp_dat1=$this->db->get_results($sql,ARRAY_A);

		//Dann alle Module
		$sql=sprintf("SELECT * FROM %s
						WHERE lang_id<>'%s'",
			$this->cms->tbname['papoo_language_collum3'],
			$standard_lang
		);
		$result_3_spalte=$this->db->get_results($sql,ARRAY_A);

		//Dann alle 3. spalte
		$sql=sprintf("SELECT * FROM %s
						WHERE freiemodule_lang<>'%s'",
			$this->cms->tbname['papoo_freiemodule_daten'],
			$this->content->template['lang_front_default']
		);
		$result_module=$this->db->get_results($sql,ARRAY_A);
		//Dann Startseite
		$sql=sprintf("SELECT * FROM %s
						WHERE lang_id<>'%s'",
			$this->cms->tbname['papoo_language_stamm'],
			$standard_lang
		);
		$result_stamm=$this->db->get_results($sql,ARRAY_A);

		if (is_array($temp_dat1)) {
			foreach ($temp_dat1 as $key=>$value) {
				if (!stristr($value['url_header'],"/".$lang_zu[$value['lang_id']]."/")) {
					//DIe neue url
					$neu_url="/".$lang_zu[$value['lang_id']]."".$value['url_header'];
					//Die alte
					$alt_url=$value['url_header'];
					//Im Array die neue setzen
					$temp_dat1[$key]['url_header']=$neu_url;
					$temp_dat1[$key]['url_header_alt']=$alt_url;
				}
				else {
					$alt_url=str_ireplace("/".$lang_zu[$value['lang_id']]."/","/",$value['url_header']);
					$temp_dat1[$key]['url_header_alt']=$alt_url;
				}
			}
		}

		if (is_array($temp_dat1)) {
			foreach ($temp_dat1 as $key=>$value) {
				//DIe neue url
				$neu_url=trim($value['url_header']);
				//Die alte
				$alt_url=trim($value['url_header_alt']);

				if(!isset($value['lan_teaser'])) {
					$value['lan_teaser'] = NULL;
				}

				if (is_array($temp_dat1)) {
					foreach ($temp_dat1 as $keya=>$valuea) {
						$temp_dat1[$keya]['lan_teaser']=str_replace('"'.$alt_url,'"'.$neu_url,$valuea['lan_teaser']);
						$temp_dat1[$keya]['lan_article']=str_replace('"'.$alt_url,'"'.$neu_url,$valuea['lan_article']);
						$temp_dat1[$keya]['lan_article_sans']=str_replace('"'.$alt_url,'"'.$neu_url,$valuea['lan_article_sans']);
					}
				}

				if (is_array($result_module)) {
					foreach ($result_module as $keym=>$valuem) {
						$result_module[$keym]['freiemodule_code']=str_replace('"'.$alt_url,'"'.$neu_url,$valuem['freiemodule_code']);
					}
				}

				if (is_array($result_3_spalte)) {
					foreach ($result_3_spalte as $key3=>$value3) {
						$result_3_spalte[$key3]['article']=str_replace('"'.$alt_url,'"'.$neu_url,$value3['article']);
						$result_3_spalte[$key3]['article_sans']=str_replace('"'.$alt_url,'"'.$neu_url,$value3['article_sans']);
					}
				}

				if (is_array($result_stamm)) {
					foreach ($result_stamm as $keys=>$values) {
						$result_stamm[$keys]['start_text']=str_replace('"'.$alt_url,'"'.$neu_url,$values['start_text']);
						$result_stamm[$keys]['start_text_sans']=str_replace('"'.$alt_url,'"'.$neu_url,$values['start_text_sans']);
					}
				}
			}
		}
		//JEtzt die Menüpunkte rausholen
		$sql=sprintf("SELECT * FROM %s WHERE lang_id<>'%s'",
			$this->cms->tbname['papoo_menu_language'],
			$standard_lang
		);
		$result_menu=$this->db->get_results($sql,ARRAY_A);
		if (is_array($result_menu)) {
			foreach ($result_menu as $key=>$value) {
				if (!stristr($value['url_menuname'],"/".$lang_zu[$value['lang_id']]."/")) {
					//DIe neue url
					$neu_url="/".$lang_zu[$value['lang_id']]."".$value['url_menuname'];
					//Die alte
					$alt_url=$value['url_menuname'];
					//Im Array die neue setzen
					$result_menu[$key]['url_menuname']=$neu_url;
					$result_menu[$key]['url_menuname_alt']=$alt_url;
				}
				else {
					$alt_url=str_ireplace("/".$lang_zu[$value['lang_id']]."/","/",$value['url_menuname']);
					$result_menu[$key]['url_menuname_alt']=$alt_url;
				}
			}
		}
		//Menü durchgehen, umstellen und Links anpassen
		if (is_array($result_menu)) {
			foreach ($result_menu as $key=>$value) {

				$neu_url=trim($value['url_menuname']);
				//Die alte
				$alt_url=trim($value['url_menuname_alt']);

				if(!isset($value['lan_teaser'])) {
					$value['lan_teaser'] = NULL;
				}

				if (is_array($temp_dat1)) {
					foreach ($temp_dat1 as $keya=>$valuea) {
						$temp_dat1[$keya]['lan_teaser']=str_replace('"'.$alt_url,'"'.$neu_url,$valuea['lan_teaser']);
						$temp_dat1[$keya]['lan_article']=str_replace('"'.$alt_url,'"'.$neu_url,$valuea['lan_article']);
						$temp_dat1[$keya]['lan_article_sans']=str_replace('"'.$alt_url,'"'.$neu_url,$valuea['lan_article_sans']);
					}
				}

				if (is_array($result_module)) {
					foreach ($result_module as $keym=>$valuem) {
						$result_module[$keym]['freiemodule_code']=str_replace('"'.$alt_url,'"'.$neu_url,$valuem['freiemodule_code']);
					}
				}

				if (is_array($result_3_spalte)) {
					foreach ($result_3_spalte as $key3=>$value3) {
						$result_3_spalte[$key3]['article']=str_replace('"'.$alt_url,'"'.$neu_url,$value3['article']);
						$result_3_spalte[$key3]['article_sans']=str_replace('"'.$alt_url,'"'.$neu_url,$value3['article_sans']);
					}
				}

				if (is_array($result_stamm)) {
					foreach ($result_stamm as $keys=>$values) {
						$result_stamm[$keys]['start_text']=str_replace('"'.$alt_url,'"'.$neu_url,$values['start_text']);
						$result_stamm[$keys]['start_text_sans']=str_replace('"'.$alt_url,'"'.$neu_url,$values['start_text_sans']);
					}
				}
			}
		}

		// In die DB eintragen
		if (is_array($result_menu)) {
			foreach ($result_menu as $key=>$value) {
				$sql=sprintf("UPDATE %s
								SET url_menuname='%s'
								WHERE menuid_id='%d'
								AND lang_id='%d'
								",
					$this->cms->tbname['papoo_menu_language'],
					$this->db->escape($value['url_menuname']),
					$this->db->escape($value['menuid_id']),
					$this->db->escape($value['lang_id'])
				);
				$this->db->get_results($sql,ARRAY_A);
			}
		}

		if (is_array($temp_dat1)) {
			foreach ($temp_dat1 as $key=>$value) {
				if(!isset($value['lan_teaser'])) {
					$value['lan_teaser'] = NULL;
				}

				$sql=sprintf("UPDATE %s
								SET url_header='%s',
								lan_teaser='%s',
								lan_article='%s',
								lan_article_sans='%s'
								WHERE lang_id='%d'
								AND lan_repore_id='%d'
								",
					$this->cms->tbname['papoo_language_article'],
					$this->db->escape($value['url_header']),
					$this->db->escape($value['lan_teaser']),
					$this->db->escape($value['lan_article']),
					$this->db->escape($value['lan_article_sans']),
					$this->db->escape($value['lang_id']),
					$this->db->escape($value['lan_repore_id'])
				);
				$this->db->get_results($sql,ARRAY_A);
			}
		}
		if (is_array($result_module)) {
			foreach ($result_module as $key=>$value) {
				$sql=sprintf("UPDATE %s
								SET freiemodule_code='%s'
								WHERE  	freiemodule_id='%d'
								AND freiemodule_lang='%s'
								",
					$this->cms->tbname['papoo_freiemodule_daten'],
					$this->db->escape($value['freiemodule_code']),
					$this->db->escape($value['freiemodule_id']),
					$this->db->escape($value['freiemodule_lang'])
				);
				$this->db->get_results($sql,ARRAY_A);
			}
		}

		if (is_array($result_3_spalte)) {
			foreach ($result_3_spalte as $key=>$value) {
				$sql=sprintf("UPDATE %s
								SET article='%s',
								article_sans='%s'
								WHERE  	collum_id='%d'
								AND lang_id='%d'
								",
					$this->cms->tbname['papoo_language_collum3'],
					$this->db->escape($value['article']),
					$this->db->escape($value['article_sans']),
					$this->db->escape($value['collum_id']),
					$this->db->escape($value['lang_id'])
				);
				$this->db->get_results($sql,ARRAY_A);
			}
		}

		if (is_array($result_stamm)) {
			foreach ($result_stamm as $key=>$value) {
				$sql=sprintf("UPDATE %s
								SET start_text='%s',
								 	start_text_sans='%s'
								WHERE  	stamm_id='%d'
								AND lang_id='%d'
								",
					$this->cms->tbname['papoo_language_stamm'],
					$this->db->escape($value['start_text']),
					$this->db->escape($value['start_text_sans']),
					$this->db->escape($value['stamm_id']),
					$this->db->escape($value['lang_id'])
				);
				$this->db->get_results($sql,ARRAY_A);;
			}
		}
	}

	/**
	 * reorder_class::make_all_rights_chef()
	 *
	 * @return void
	 */
	function make_all_rights_chef()
	{
		//gruppenid die gesetzt werden soll
		$gruppen_id=11;

		//Zuerst alle Artikel rausholen
		$sql=sprintf("SELECT DISTINCT(reporeID) FROM %s",
			$this->cms->tbname['papoo_repore']
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		//Dann die Leserechte löschen die schon chef haben
		$sql=sprintf("DELETE  FROM %s
												WHERE gruppeid_id='%d'",
			$this->cms->tbname['papoo_lookup_article'],
			$gruppen_id
		);
		$this->db->query($sql);

		//Dann alle neu eintragen
		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				$sql=sprintf("INSERT INTO %s
												SET gruppeid_id='%d',
												 article_id='%d'",
					$this->cms->tbname['papoo_lookup_article'],
					$gruppen_id,
					$value['reporeID']
				);
				$this->db->query($sql);
			}
		}

		//Dann die Schreibrechte löschen die schon chef haben
		$sql=sprintf("DELETE  FROM %s
												WHERE gruppeid_wid_id='%d'",
			$this->cms->tbname['papoo_lookup_write_article'],
			$gruppen_id
		);
		$this->db->query($sql);

		//Dann alle neu eintragen
		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				$sql=sprintf("INSERT INTO %s
												SET gruppeid_wid_id='%d',
												 article_wid_id='%d'",
					$this->cms->tbname['papoo_lookup_write_article'],
					$gruppen_id,
					$value['reporeID']
				);
				$this->db->query($sql);
			}
		}

		//Dann alle Menüpunkte
		$sql=sprintf("SELECT DISTINCT(menuid) FROM %s",
			$this->cms->tbname['papoo_me_nu']
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		//Alle Leserechte löschen mit chef
		$sql=sprintf("DELETE  FROM %s
												WHERE gruppenid='%d'",
			$this->cms->tbname['papoo_lookup_men_ext'],
			$gruppen_id
		);
		$this->db->query($sql);

		//Leserechte mit chef setzen
		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				$sql=sprintf("INSERT INTO %s
												SET gruppenid='%d',
												 menuid='%d'",
					$this->cms->tbname['papoo_lookup_men_ext'],
					$gruppen_id,
					$value['menuid']
				);
				$this->db->query($sql);
			}
		}

		//SChreibrechte löschen
		$sql=sprintf("DELETE  FROM %s
												WHERE gruppeid_id='%d'",
			$this->cms->tbname['papoo_lookup_me_all_ext'],
			$gruppen_id
		);
		$this->db->query($sql);

		//SChrechte setzen
		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				$sql=sprintf("INSERT INTO %s
												SET gruppeid_id='%d',
												 menuid_id='%d'",
					$this->cms->tbname['papoo_lookup_me_all_ext'],
					$gruppen_id,
					$value['menuid']
				);
				$this->db->query($sql);
			}
		}

	}
	/**
	 * Menüpunkte und Artikel Felder mit den sprechenden urls füllen
	 */
	function make_menu_artikel_surls()
	{
		$sql=sprintf("SELECT * FROM %s,%s WHERE menuid=menuid_id",
			$this->cms->papoo_menu,
			$this->cms->papoo_menu_language
		);
		$result=$this->db->get_results($sql);
		foreach ($result as $menu) {
			$name=$menu->url_menuname;
			if (empty($name)) {
				$name=$menu->menuname;
			}
			//Anzahl der gleichen Menüpunkte raussuchen
			$sql=sprintf("SELECT COUNT(menuid_id) FROM %s
								WHERE 
								 url_menuname='%s'
								AND lang_id!='%d'",

				$this->cms->papoo_menu_language,
				$this->db->escape($name),
				$menu->lang_id
			);
			$varcount=$this->db->get_var($sql);
			if ($varcount>=1) {
				$varcount="-".$menu->lang_id."-".$menu->menuid;
			}
			else {
				$varcount="";
			}
			$sql = sprintf("UPDATE %s
											SET  url_menuname='%s'
											WHERE  menuid_id='%d'
											AND lang_id='%d'",
				$this->cms->papoo_menu_language,
				$this->db->escape($this->menu->replace_uml(strtolower($name.$varcount))),
				$menu->menuid,
				$menu->lang_id
			);
			$this->db->query($sql);
		}
		$sql=sprintf("SELECT reporeID, cattextid, header,lan_repore_id, lang_id
									FROM %s,%s
									WHERE reporeID=lan_repore_id",
			$this->cms->tbname['papoo_repore'],
			$this->cms->tbname['papoo_language_article']
		);
		$result=$this->db->get_results($sql);
		foreach ($result as $art) {
			//Checken ob die überschrift schon existiert
			$sql=sprintf("SELECT COUNT(reporeID)
										FROM %s,%s
										WHERE cattextid='%s'
										AND header='%s'
										AND lang_id!='%s'",
				$this->cms->tbname['papoo_repore'],
				$this->cms->tbname['papoo_language_article'],
				$this->db->escape($art->cattextid),
				$this->db->escape($art->header),
				$this->db->escape($art->lang_id)
			);
			$varcount=$this->db->get_var($sql);
			if ($varcount>=1) {
				#$varcount=$varcount+1;
				$varcount="-".$art->lang_id."-".$art->reporeID;
			}
			else {
				$varcount="";
			}
			$urlh_neu=$this->menu->replace_uml(strtolower($art->header)).$varcount;

			$sql=sprintf("UPDATE %s SET
										url_header='%s'
										WHERE lan_repore_id='%s'
										AND lang_id='%s'
										LIMIT 1",
				$this->cms->tbname['papoo_language_article'],
				$urlh_neu,
				$art->lan_repore_id,
				$art->lang_id
			);
			$this->db->query($sql);
		}
		return true;
	}
	/**
	 * LInkliste Kategorien erneuern
	 */
	function reorder_link_cat()
	{
		$sql = sprintf("SELECT cat_id FROM %s ", $this->cms->tbname['papoo_linkliste_cat'], $this->cms->tbname['papoo_linkliste_lang_cat']);
		$result = $this->db->get_results($sql, ARRAY_A);
		foreach ($result as $cat) {
			$sql=sprintf("SELECT * FROM %s WHERE cat_linkliste_id='%s' ORDER BY linkliste_order_id",
				$this->cms->tbname['papoo_linkliste_daten'],
				$cat->cat_id
			);
			$res=$this->db->get_results($sql);
			$i=0;
			if (!empty($res)) {
				foreach ($res as $link) {
					$i++;
					$sql=sprintf("UPDATE %s SET linkliste_order_id='%s' WHERE linkliste_id='%s'",
						$this->cms->tbname['papoo_linkliste_daten'],
						$i,
						$link->linkliste_id
					);
					$this->db->query($sql);
				}
			}
		}
	}

	/**
	 * Bilder neu den Kategorien zuordnen papoo_images
	 */
	function reorder_images()
	{
		$sql=sprintf("UPDATE %s SET image_dir='0'",
			$this->cms->tbname['papoo_images']
		);
		$this->db->query($sql);
		$sql=sprintf("UPDATE %s SET downloadkategorie='0'",
			$this->cms->tbname['papoo_download']
		);
		$this->db->query($sql);
	}

	/**
	 * Prüft ob Benutzer $user_id der Gruppe "Administratoren" angehört
	 *
	 * @param $user_id
	 * @return bool
	 */
	function check_admin_rights($user_id)
	{
		$ergebnis = false;

		$sql = sprintf("SELECT gruppenid FROM %s WHERE userid='%d' AND gruppenid='1'",
			$this->cms->papoo_lookup_ug,
			$user_id
		);
		$admin_yn = $this->db->get_var($sql);

		if(!empty($admin_yn)) {
			$ergebnis = true;
		}

		return $ergebnis;
	}

	/**
	 * Sortiert die Menü-Punkte neu
	 */
	function reorder_menu()
	{
		// Menü-Punkte alte Konvention anpassen
		$sql = sprintf("UPDATE %s SET untermenuzu='0' WHERE level='0' ",
			$this->cms->papoo_menu
		);
		$this->db->query($sql);

		// Menü-Struktur auslesen
		$menu = $this->menu->menu_data_read("FRONT_ORDERMENUID");

		// Menü-Punkte neu durchnummerieren
		if (!empty($menu)) {
			foreach ($menu as $menupunkt) {
				$nummer_array = explode(".", $menupunkt['nummer']);
				$order_id_neu = $nummer_array[count($nummer_array) - 1];

				$sql = sprintf("UPDATE %s SET order_id='%d' WHERE menuid='%d' ",
					$this->cms->papoo_menu,
					$order_id_neu,
					$menupunkt['menuid']
				);
				$this->db->query($sql);
			}
		}
	}

	/**
	 * Sortiert die Artikel neu
	 *
	 * @param int $untermenuid
	 */
	function reorder_artikel($untermenuid = 0)
	{
		// Menü-Punkte mit der Untermenuzu-ID $untermenuid raussuchen
		$sql = sprintf("SELECT * FROM %s WHERE untermenuzu='%d'",
			$this->cms->papoo_menu,
			$untermenuid
		);
		$menupunkte = $this->db->get_results($sql);

		if (!empty($menupunkte)) {
			foreach($menupunkte as $menupunkt) {
				// Rekursion für Untermenü-Punkte des aktuellen Menü-Punktes raussuchen
				$this->reorder_artikel($menupunkt->menuid);

				// Artikel-Liste des aktuellen Menü-Punktes zusammenstellen, geordnet nach reporeID
				$sql = sprintf("SELECT reporeID, teaser_list FROM %s WHERE cattextid='%d' ORDER BY reporeID",
					$this->cms->papoo_repore,
					$menupunkt->menuid
				);
				$artikel_liste = $this->db->get_results($sql);

				// Artikel neu durchnummerieren
				if (!empty($artikel_liste)) {
					$nummer_neu = 1;
					foreach($artikel_liste as $artikel) {
						// Prüfung ob Artikel auf der Startseite erscheint. Wenn ja $teaser_list=1 setzen
						if ($artikel->teaser_list) {
							$teaser_list = 1;
						}
						else {
							$teaser_list = 0;
						}

						$sql = sprintf("UPDATE %s SET order_id='%d', teaser_list='%d' WHERE reporeID='%d'",
							$this->cms->papoo_repore,
							$nummer_neu,
							$teaser_list,
							$artikel->reporeID
						);
						$this->db->query($sql);

						$nummer_neu += 1;
					}
				}
			}
		}
	}

	/**
	 * Sortiert die Artikel der Startseite neu
	 */
	function reorder_artikel_start()
	{
		// Artikel-Liste der Startseite zusammenstellen, geordnet nach reporeID
		$sql = sprintf("SELECT reporeID FROM %s WHERE teaser_list='1' ORDER BY reporeID",
			$this->cms->papoo_repore
		);
		$artikel_liste = $this->db->get_results($sql);

		// Artikel neu durchnummerieren
		if (!empty($artikel_liste)) {
			$nummer_neu = 1;
			foreach($artikel_liste as $artikel) {
				$sql = sprintf("UPDATE %s SET order_id_start='%d' WHERE reporeID='%d'",
					$this->cms->papoo_repore,
					$nummer_neu,
					$artikel->reporeID
				);
				$this->db->query($sql);

				$nummer_neu += 1;
			}
		}
	}
}

$reorder = new reorder_class();
