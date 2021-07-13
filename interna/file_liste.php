<?php
require_once("./all_inc.php");

if (isset($checked->type)) {
	switch ($checked->type) {
	case 'media':
		$checked->reiter_active = 'videos';
		break;
	case 'file':
		$checked->reiter_active = 'files';
		break;
	case 'image':
		$checked->reiter_active = 'images';
		break;
	default:
		break;
	}
}

if ($user->check_access(1)) {
	//Galerie Liste erstellen
	if (is_object($galerie)) {
		$galerie->galerie_init("FRONT");
		$galerie->switch_front();
		$gal_liste = $galerie->galerien_liste();
		$galerie->switch_front("GALERIE");
		#$checked->galerie_id=12;
		$gal_bilder_liste = $galerie->bilder_liste($checked->galerie_id, "NOBR");
	}

	//Produkt Bilder
	if (isset($shop) and is_object($shop)) {
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

	if (isset($checked->action) && ($checked->action == "SPEICHERN")) {
		$intern_image->upload_switch($checked->action, "noreload");
		$is_uploaded_fertig = true;
		$is_uploaded = true;
	}
	?>

	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo $content->template['system_link_auswaehlen'] ?></title>

		<script language="javascript" type="text/javascript" src="./tiny_mce/tiny_mce_popup.js"></script>
		<script language="javascript" type="text/javascript" src="./tiny_mce/utils/mctabs.js"></script>
		<script src='js/jquery-1.3.2.min.js' type='text/javascript'></script>

		<link href="./css/button.css" rel="stylesheet" type="text/css"/>

		<script src='js/jquery-ui-1.7.2.custom.min.js' type='text/javascript'></script>
		<script src='js/jquery.cookie.js' type='text/javascript'></script>

		<style type="text/css">
			@import url(./css/image_list.css);
		</style>


		<script language="javascript" type="text/javascript">

			var FileBrowserDialogue =
				{
					init: function () {
						// Here goes your code for setting your custom things onLoad.
					},
					mySubmit: function (url, rel, title, cssclass) {
						var URL = url;
						var REL = rel;
						var win = tinyMCEPopup.getWindowArg("window");

						// insert information now
						win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = URL;
						try {
							win.document.getElementById("rel").value = REL;
						} catch (e) {
						}
						try {
							win.document.getElementById("title").value = title;
						} catch (e) {
						}

						// for image browsers: update image dimensions
						//if (win.ImageDialog.getImageData) win.ImageDialog.getImageData();
						//if (win.ImageDialog.showPreviewImage) win.ImageDialog.showPreviewImage(URL);

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
				<li id="baum_tab" aria-controls="baum_panel" <?php if (empty($checked->reiter_active)) {
					echo 'class="current"';
				} ?>>
					<span>
						<a href="javascript:mcTabs.displayTab('baum_tab');" onmousedown="return false;">
							<?php echo $content->template['system_link_seitenbaum'] ?>
						</a>
					</span>
				</li>
				<li id="files_tab" aria-controls="files_panel" <?php if (isset($checked->reiter_active) && ($checked->reiter_active == "files")) {
					echo 'class="current"';
				} ?>>
					<span>
						<a href="javascript:mcTabs.displayTab('files_tab');" onmousedown="return false;">
							<?php echo $content->template['system_link_files'] ?>
						</a>
					</span>
				</li>
				<li id="videos_tab" aria-controls="videos_panel" <?php if (isset($checked->reiter_active) && ($checked->reiter_active == "videos")) {
					echo 'class="current"';
				} ?>>
					<span>
						<a href="javascript:mcTabs.displayTab('videos_tab');" onmousedown="return false;">
							<?php echo isset($content->template['system_link_videos']) ? $content->template['system_link_videos'] : $content->template['system_image_alle_videoverzeichnisse']; ?>
						</a>
					</span>
				</li>
				<li id="general_tab" aria-controls="general_panel" <?php if (isset($checked->reiter_active) && ($checked->reiter_active == "images")) {
					echo 'class="current"';
				} ?>><span><a href="javascript:mcTabs.displayTab('general_tab','general_panel');"
							  onmousedown="return false;"><?php echo $content->template['system_image_alle_bilderverzeichnisse'] ?></a></span>
				</li>
				<li id="appearance_tab" aria-controls="appearance_panel" <?php if (isset($checked->reiter_active) && ($checked->reiter_active == "gal")) {
					echo 'class="current"';
				} ?>><span><a href="javascript:mcTabs.displayTab('appearance_tab','appearance_panel');"
							  onmousedown="return false;"><?php echo $content->template['system_image_alle_galerienverzeichnisse'] ?></a></span>
				</li>
				<li id="shop_tab" aria-controls="shop_panel" <?php if (isset($checked->reiter_active) && ($checked->reiter_active == "shop")) {
					echo 'class="current"';
				} ?>><span><a href="javascript:mcTabs.displayTab('shop_tab','shop_panel');"
							  onmousedown="return false;"><?php echo $content->template['system_image_alle_shop_bilder'] ?></a></span>
				</li>
				<li id="glossar_tab" aria-controls="glossar_panel" <?php if (isset($checked->reiter_active) && ($checked->reiter_active == "glossar")) {
					echo 'class="current"';
				} ?>><span><a href="javascript:mcTabs.displayTab('glossar_tab','glossar_panel');"
							  onmousedown="return false;"><?php echo $content->template['system_alle_glossar'] ?></a></span>
				</li>
			</ul>
		</div>

		<div class="panel_wrapper">
			<div id="baum_panel" class="panel <?php if (empty($checked->reiter_active)) {
				echo 'current';
			} ?>">
				<section class="image_folder_container_complete">
					<?php
					$menu->set_is_tiny();
					$sitemap->make_sitemap($_SESSION['langid_front'], "FRONT_PUBLISH");
					$site = $content->template['table_data'];
					# shop_class_sitemap

					if (isset($shop) && is_object($shop)) {
						//shop_class_sitemap
						require_once PAPOO_ABS_PFAD . "/plugins/papoo_shop/lib/shop_class_sitemap.php";
						$shop_map = new shop_class_sitemap();
						$shop_map->do_shop_sitemap();
					}

					$content->assign();
					echo $content->template['system_link_seitenbaum_text'];

					// templates parsen
					$output = $smarty->fetch("sub_templates/sitemap_tiny.html");
					print_r($output);
					?>
				</section>
			</div>

			<div id="files_panel" class="panel <?php if (isset($checked->reiter_active) && ($checked->reiter_active == "files")) {
				echo 'current';
			} ?>">
				<?php
				$intern_upload->change_upload();
				$intern_upload->get_cat_list();
				$content->assign();
				// templates parsen
				$output = $smarty->fetch("sub_templates/files_tiny.html");
				print_r($output);
				?>
			</div>

			<div id="videos_panel" class="panel <?php if (isset($checked->reiter_active) && ($checked->reiter_active == "videos")) {
				echo 'current';
			} ?>">
				<section class="image_folder_container_complete">
					<?php
					$video_class->do_video();
					$video_class->nur_flv = 0;
					$video_class->do_change();
					//Liste der Videos rausholen
					$content->assign();
					// templates parsen
					$output = $smarty->fetch("sub_templates/video2_tiny.html");
					print_r($output);
					?>
				</section>
			</div>

			<div id="general_panel" class="panel <?php if (isset($checked->reiter_active) && ($checked->reiter_active == "images")) {
				echo 'current';
			}
			$intern_image->change_liste(); ?>">
				<section class="image_folder_container_complete">
					<div class="image_folder_container_left">
						<strong><?php echo $content->template['system_image_image_ordner'] ?></strong><br/>

						<a href="file_liste.php?type=image&amp;reiter_active=images" class="bilder_verzeichnis_top">
							<?=htmlspecialchars($content->template['system_image_alle_bilderverzeichnisse'])?>
							<span style="color: #333;">(<?php echo isset($content->template['bilder_cat_id_count']['0']) ? $content->template['bilder_cat_id_count']['0'] : NULL ?>)</span>
						</a>
						<?php
						foreach ($content->template['dirlist'] as $dat) {
							echo $dat['vor_ul'];
							echo '<a ';
							if ($content->template['bilder_active_cat_id'] == ($dat['bilder_cat_id'] ?? null)) {
								echo ' class="active_cat" ';
							}
							IfNotSetNull($dat['bilder_cat_id']);
							echo 'href="./file_liste.php?type=image&amp;reiter_active=images&amp;image_dir=' . $dat['bilder_cat_id'] . '">' . $dat['bilder_cat_name'] . ' (';
							if (isset($content->template['bilder_cat_id_count'][$dat['bilder_cat_id']]) && $content->template['bilder_cat_id_count'][$dat['bilder_cat_id']] > 0) {
								echo $content->template['bilder_cat_id_count'][$dat['bilder_cat_id']];
							} else {
								echo "0";
							}
							echo ')</a>';
							echo $dat['nach_ul'];
						}
						?>
					</div>

					<div class="image_folder_container_out">
						<div class="image_folder_container">
							<div id="wrapper">
								<div id="columns">
									<?php
									foreach ($content->template['image_data'] as $dat) {
										$thumbs = "";
										if ($dat['image_width'] >= 160) {
											$thumbs = "thumbs/";
										}
										echo '<div class="pin"><div style="padding: 5px; ">';
										echo '<a href="#"><img src="../images/' . $thumbs . $dat['image_name'] . '" alt="' . $dat['image_alt'] . '"  onClick="FileBrowserDialogue.mySubmit(\'../images/' . $dat['image_name'] . '\',\'lightbox\',\'' . $dat['image_alt'] . '\'); " /></a><br />	';
										echo $dat['image_alt'] . "<br />(" . $dat['image_width'] . "x" . $dat['image_height'] . " px)";
										echo "</div></div>";
									}
									?>
								</div>
							</div>
						</div>
					</div>
				</section>
			</div>

			<div id="appearance_panel" class="panel <?php if (isset($checked->reiter_active) && ($checked->reiter_active == "gal")) {
				echo 'current';
			} ?>">
				<section class="image_folder_container_complete">
					<div class="image_folder_container_left">
						<strong><?php echo $content->template['system_image_image_ordner'] ?></strong>
						<div class="nested_ul">
							<?php
							if (is_array($gal_liste)) {
								echo "<ul>";
								foreach ($gal_liste as $dat) {
									echo '<li><a ';
									if ($content->template['bilder_active_cat_id'] == isset($dat) ? $dat['bilder_cat_id'] : NULL) {
										echo ' class="active_cat" ';
									}
									echo 'href="./file_liste.php?galerie_id=' . $dat['gal_id'] . '&reiter_active=gal">' . $dat['gal_verzeichnis'] . ' (' . $dat['gal_bilderanzahl'] . ')';
									echo '</a></li>';
								}
								echo "</ul>";
							} else {
								echo '<p style="margin-left:30px;">' . $content->template['system_keine_gal_vorhanden'] . '</p>';
							}
							?>
						</div>
					</div>

					<div class="image_folder_container_out">
						<div class="image_folder_container">
							<?php if (isset($is_gal_download) && $is_gal_download == "ok") {
								?>
								<a href="#"
								   onClick="FileBrowserDialogue.mySubmit('/index.php?download_zip_galID=<?php echo $checked->galerie_id; ?>','','zip'); "
								   class="large awesome" style="color:#fff;">Diese Galerie als Zip Datei
									verlinken...</a>
								<div class="clearfix"><br/></div>
							<?php } ?>
							<?php
							if (is_array($gal_bilder_liste)) {
								echo '<ul class="die_image_liste">';
								foreach ($gal_bilder_liste as $dat) {
									foreach ($dat as $key => $value) {
										$dat[$key] = $diverse->encode_quote($value);
									}

									$thumbs = "";
									if ($dat['bild_breite'] >= 160) {
										$thumbs = "thumbs/";
									}
									$dat['bildlang_name'] = str_replace("nobr:", "", $dat['bildlang_name']);
									$dat['bildlang_beschreibung'] = str_replace("nobr:", "", $dat['bildlang_beschreibung']);

									echo '<li><div style="padding: 5px; ">';
									echo '<a href="#"><img src="../plugins/galerie/galerien/' . $dat['gal_verzeichnis'] . "/" . $thumbs . $dat['bild_datei'] . '" alt="' . $dat['bildlang_name'] . '"  onClick="FileBrowserDialogue.mySubmit(\'../plugins/galerie/galerien/' . $dat['gal_verzeichnis'] . "/" . $dat['bild_datei'] . '\',\'lightbox\',\'' . $dat['bildlang_beschreibung'] . '\'); " /></a><br />	';
									echo $dat['bildlang_name'] . "<br />(" . $dat['bild_breite'] . "x" . $dat['bild_hoehe'] . " px)";
									echo "</div></li>";
								}
								echo '</ul>';
							} else {
								echo $content->template['system_keine_bilder_vorhanden'];
							}
							//$this->content->template['image_data']
							?>
						</div>
					</div>
			</div>
			</section>

			<div id="shop_panel" class="panel  <?php if (isset($checked->reiter_active) && ($checked->reiter_active == "shop")) {
				echo 'current';
			} ?>">
				<section class="image_folder_container_complete">
					<div class="image_folder_container_left">
						<strong><?php echo $content->template['system_image_image_ordner'] ?></strong>
						<div class="nested_ul">
							<?php
							if (isset($shop_catDat) && is_array($shop_catDat)) {
								echo '<ul';
								foreach ($shop_catDat as $dat) {
									echo '<li><a ';
									if ($content->template['bilder_active_cat_id'] == $dat['bilder_cat_id']) {
										echo ' class="active_cat" ';
									}
									echo 'href="./file_liste.php?shopcat_id=' . $dat['kategorien_id'] . '&reiter_active=shop">' . $dat['kategorien_lang_TitelderSeite'] . ' </a></li>';
								}
								echo '</ul>';
							} else {
								echo '<p style="margin-left:30px;">' . $content->template['system_keinshop_vorhanden'] . '</p>';
							}
							?>
						</div>
					</div>

					<div class="image_folder_container_out">
						<div class="image_folder_container">
							<ul class="die_image_liste">
								<?php
								if (isset($images_shop) && is_array($images_shop)) {
									foreach ($images_shop as $dat) {
										$split = explode(",", $dat['produkte_lang_image_galerie']);

										$thumbs = "";
										$size = getimagesize(PAPOO_ABS_PFAD . "/plugins/papoo_shop/images/" . $split['2']);

										$dat['bildlang_name'] = str_replace("nobr:", "", $dat['bildlang_name']);
										echo '<li><div style="padding: 5px; ">';
										echo '<a href="#"><img src="../plugins/papoo_shop/images/thumbs/' . $split['0'] . '" alt="' . $dat['produkte_lang_produktename'] . '"  onClick="FileBrowserDialogue.mySubmit(\'../plugins/papoo_shop/images/' . $split['2'] . '\',\'lightbox\',\'' . $dat['produkte_lang_produktename'] . '\'); " /></a><br />	';
										echo $dat['produkte_lang_produktename'] . "<br />(" . $size['0'] . "x" . $size['1'] . " px)";
										echo "</div></li>";
									}
								} else {
									echo $content->template['system_keine_bilder_shop_vorhanden'];
								}
								?>
							</ul>
						</div>
					</div>
				</section>
			</div>

			<div id="glossar_panel" class="panel <?php if (isset($checked->reiter_active) && ($checked->reiter_active == "glossar")) {
				echo 'current';
			} ?>">
				<section>
					<?php
					if (isset($glossar) && is_object($glossar)) {
						$glossar->glossar_front();
						$content->assign();
						echo $content->template['system_link_glossar_text'];
						$output = $smarty->fetch("sub_templates/glossar_tiny.html");
						print_r($output);
					} else {
						echo '<p>' . $content->template['system_link_glossar_text_no'] . '</p>';
					}
					?>
				</section>
			</div>
		</div>
	</div>
	</body>
	</html>
	<?php
}
?>