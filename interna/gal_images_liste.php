<?php

require_once("./all_inc.php");

if ($user->check_access(1)) {

	//Bilder Liste erstellen
	$intern_image->change_liste();

	//Galerie Liste erstellen
	if (isset($galerie) && is_object($galerie)) {
		$galerie->galerie_init("FRONT");
		$galerie->switch_front();
		$gal_liste = $galerie->galerien_liste("ALL", true);
		$galerie->switch_front("GALERIE");
		#$checked->galerie_id=12;
		$gal_bilder_liste = $galerie->bilder_liste($checked->galerie_id, "NOBR");
	}
	//Produkt Bilder

	if (isset($shop) && is_object($shop)) {
		$shop_cat = new shop_class_category();
		$shop_catDat = $shop_cat->shop_get_kategorien_liste();

		if (!empty($checked->shopcat_id)) {
			$sql = sprintf("SELECT produkte_lang_produktename, produkte_lang_image_galerie FROM %s,%s 
			 				WHERE produkte_lang_lang_id='%d'
							 AND produkte_kategorie_id='%d'
							 AND produkte_produkt_id=produkte_lang_id",
				$cms->tbname['plugin_shop_produkte_lang'],
				$cms->tbname['plugin_shop_lookup_kategorie_prod'],
				$cms->lang_back_content_id,
				$db->escape($checked->shopcat_id)
			);
			$images_shop = $db->get_results($sql, ARRAY_A);
		}
	}

	if (!empty($_FILES['strFile']['name'])) {
		$is_uploaded = true;
		$intern_image->upload_picture();
	}

	if (isset($checked->action) && $checked->action == "SPEICHERN") {
		$intern_image->upload_switch($checked->action, "noreload");
		$is_uploaded_fertig = true;
		$is_uploaded = true;
	}
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Galerie auswählen</title>
		<script language="javascript" type="text/javascript" src="./tiny_mce/tiny_mce_popup.js">


		</script>
		<script language="javascript" type="text/javascript" src="./tiny_mce/utils/mctabs.js"></script>
		<style type="text/css">
			@import url(./css/image_list.css);
			@import url(./css/button.css);
		</style>
		<script language="javascript" type="text/javascript">

			var FileBrowserDialogue = {
				init: function () {
					// Here goes your code for setting your custom things onLoad.
				},
				mySubmit: function (url) {

					var URL = url;
					var win = tinyMCEPopup.getWindowArg("window");

					// insert information now
					win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = URL;

					// close popup window
					tinyMCEPopup.close();
				}
			}

			tinyMCEPopup.onInit.add(FileBrowserDialogue.init, FileBrowserDialogue);

		</script>
		<base target="_self"/>
	</head>
	<body>
	<div class="background">
		<div class="tabs">
			<ul>
				<li id="general_tab" <?php if (empty($checked->reiter_active)) {
					echo 'class="current"';
				} ?>><span><a href="javascript:mcTabs.displayTab('general_tab','general_panel');"
							  onmousedown="return false;"><?php echo $content->template['system_image_alle_bilderverzeichnisse'] ?></a></span>
				</li>
			</ul>
		</div>

		<div class="panel_wrapper">
			<div id="general_panel" class="panel <?php if (empty($checked->reiter_active)) {
				echo 'current';
			} ?>">

				<div class="image_folder_container_complete">
					<div class="image_folder_container_left">
						<strong><?php echo $content->template['system_image_image_ordner'] ?></strong>
						<!--<br />
<a class="bilder_verzeichnis_top" href="gal_images_liste.php"> <?php echo $content->template['system_image_alle_galerienverzeichnisse'] ?> <span style="color: #333;">(<?php echo $content->template['bilder_cat_id_count']['0'] ?>)</span> </a>-->
						<div class="nested_ul">
							<ul>
								<?php
								if (is_array($gal_liste)) {
									foreach ($gal_liste as $dat) {

										echo '<li><a ';
										if (isset($dat['bilder_cat_id']) && $content->template['bilder_active_cat_id'] == $dat['bilder_cat_id']) {
											echo ' class="active_cat" ';
										}
										echo 'href="./gal_images_liste.php?galerie_id=' . $dat['gal_id'] . '&reiter_active=">' . $dat['gal_verzeichnis'] . ' (' . $dat['gal_bilderanzahl'];

										echo ')</a></li>';

									}
								} else {
									echo '<p style="margin-left:30px;">' . $content->template['system_keine_gal_vorhanden'] . '</p>';
								}
								?>
							</ul>
						</div>
					</div>

					<div class="image_folder_container_out">
						<div class="image_folder_container">
							<?php if ($gal_bilder_liste['0']['bild_gal_id'] > 0) {
								?>
								<a id="hrefbrowser_link" href=""
								   onClick="FileBrowserDialogue.mySubmit('<?php echo $gal_bilder_liste['0']['bild_gal_id']; ?>'); "
								   class="large awesome" style="">Galerie ausw&auml;hlen </a>
								<?php
							} ?>
							<br/><br/><br/>
							<ul class="die_image_liste">
								<?php
								if (is_array($gal_bilder_liste)) {
									foreach ($gal_bilder_liste as $dat) {
										foreach ($dat as $key => $value) {
											$dat[$key] = $diverse->encode_quote($value);
										}

										$thumbs = "";
										if ($dat['bild_breite'] >= 160) {
											$thumbs = "thumbs/";
										}
										$dat['bildlang_name'] = str_replace("nobr:", "", $dat['bildlang_name']);
										echo '<li><div style="padding: 5px; ">';
										echo '<a href="#" onclick="return false;"><img src="../plugins/galerie/galerien/' . $dat['gal_verzeichnis'] . "/" . $thumbs . $dat['bild_datei'] . '" alt="' . $dat['bildlang_name'] . '"   /></a><br />	';
										echo $dat['bildlang_name'] . "<br />(" . $dat['bild_breite'] . "x" . $dat['bild_hoehe'] . " px)";
										echo "</div></li>";
									}
								} else {
									echo $content->template['system_keine_bilder_vorhanden'];
								}

								//$this->content->template['image_data']
								?>
							</ul>
						</div>
					</div>
				</div>
			</div>
	</body>
	</html>
<?php } ?>