<?php
require_once("./all_inc.php");
if ($user->check_access(1)) {
	//Bilder Liste erstellen
	$intern_image->change_liste();

	//Galerie Liste erstellen
	if (is_object($galerie)) {
		$cms->system_config_data['config_paginierung'] = 99999;
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

	if (isset($checked->action) && ($checked->action == "SPEICHERN")) {
		$intern_image->upload_switch($checked->action, "noreload");
		$is_uploaded_fertig = true;
		$is_uploaded = true;
	}

	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>{#advimage_dlg.dialog_title}</title>
		<link rel="stylesheet" type="text/css" href="css/smoothness.css"/>

		<script language="javascript" type="text/javascript" src="./tiny_mce/tiny_mce_popup.js">


		</script>
		<script language="javascript" type="text/javascript" src="./tiny_mce/utils/mctabs.js"></script>
		<style type="text/css">
			/* edited by b.legt: Einbinden der dynamischen Men-CSS */
			@import url(./css/image_list.css);
		</style>


		<script language="javascript" type="text/javascript">

			var FileBrowserDialogue = {
				init: function () {
					// Here goes your code for setting your custom things onLoad.
				},
				mySubmit: function (url, alt, title) {
					var URL = url;
					var win = tinyMCEPopup.getWindowArg("window");

					// insert information now
					win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = URL;
					win.document.getElementById("alt").value = alt;
					win.document.getElementById("title").value = title;

					// for image browsers: update image dimensions
					if (win.ImageDialog.getImageData) {
						win.ImageDialog.getImageData();
					}
					if (win.ImageDialog.showPreviewImage) {
						win.ImageDialog.showPreviewImage(URL);
					}

					// close popup window
					tinyMCEPopup.close();
				}
			}

			tinyMCEPopup.onInit.add(FileBrowserDialogue.init, FileBrowserDialogue);

		</script>
		<base target="_self"/>

		<link rel="stylesheet" type="text/css" href="./css/font-awesome.css"/>
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


				<li id="upload_tab" <?php if (isset($checked->reiter_active) && ($checked->reiter_active == "upload")) {
					echo 'class="current"';
				} ?>><span><a href="javascript:mcTabs.displayTab('upload_tab','upload_panel');"
							  onmousedown="return false;"><?php echo $content->template['system_bilder_hochladen'] ?></a></span>
				</li>

				<li id="appearance_tab" <?php if (isset($checked->reiter_active) && ($checked->reiter_active == "gal")) {
					echo 'class="current"';
				} ?>><span><a href="javascript:mcTabs.displayTab('appearance_tab','appearance_panel');"
							  onmousedown="return false;"><?php echo $content->template['system_image_alle_galerienverzeichnisse'] ?></a></span>
				</li>
				<li id="shop_tab" <?php if (isset($checked->reiter_active) && ($checked->reiter_active == "shop")) {
					echo 'class="current"';
				} ?>><span><a href="javascript:mcTabs.displayTab('shop_tab','shop_panel');"
							  onmousedown="return false;"><?php echo $content->template['system_image_alle_shop_bilder'] ?></a></span>
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
						<br/><br/>
						<a class="bilder_verzeichnis_top"
						   href="images_liste.php"> <?php echo $content->template['system_image_alle_bilderverzeichnisse'] ?>
							<span style="color: #333;">(<?php echo $content->template['bilder_cat_id_count']['0'] ?>)</span>
						</a>
						<?php

						foreach ($content->template['dirlist'] as $dat) {
							echo $dat['vor_ul'];
							echo '<a ';
							if ($content->template['bilder_active_cat_id'] == $dat['bilder_cat_id']) {
								echo ' class="active_cat" ';
							}
							echo 'href="./images_liste.php?image_dir=' . $dat['bilder_cat_id'] . '">' . $dat['bilder_cat_name'] . ' (';
							if ($content->template['bilder_cat_id_count'][$dat['bilder_cat_id']] > 0) {
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
						<div class="sortbar">
							<?php


							if ($content->template['show'] == "list") {
								echo '<a class="sort_alpha" href="images_liste.php?type=image&show=pictures&image_dir=' . $content->template['image_dir'] . '"><i class="fa fa-th"></i></a>';
							} else {
								echo ' <a class="sort_alpha" href="images_liste.php?type=image&show=list&image_dir=' . $content->template['image_dir'] . '"><i class="fa fa-th-list"></i></a>';
							}


							if ($content->template['sort'] == "alpha_desc") {
								echo '<a class="sort_alpha" href="images_liste.php?type=image&sort=alpha_asc&show=' . $content->template['show'] . '&image_dir=' . $content->template['image_dir'] . '"><i class="fa fa-sort-alpha-desc"></i></a>';
							} else {
								echo ' <a class="sort_alpha" href="images_liste.php?type=image&sort=alpha_desc&show=' . $content->template['show'] . '&image_dir=' . $content->template['image_dir'] . '"><i class="fa fa-sort-alpha-asc"></i></a>';
							}

							if ($content->template['sort'] == "date_desc") {
								echo '<a class="sort_date" href="images_liste.php?type=image&sort=date_asc&show=' . $content->template['show'] . '&image_dir=' . $content->template['image_dir'] . '"><i class="fa fa-sort-numeric-desc"></i></a>';
							} else {
								echo '<a class="sort_date" href="images_liste.php?type=image&sort=date_desc&show=' . $content->template['show'] . '&image_dir=' . $content->template['image_dir'] . '"><i class="fa fa-sort-numeric-asc"></i></a>';
							}


							?>


						</div>
						<div class="image_folder_container">
							<div id="wrapper">
								<?php
								//$this->content->template['image_data']

								if ($content->template['show'] == "list") {
									echo '<table class="outside  table table-striped table-hover" style="margin-top:0px;width:100%;">
<tr style="text-align:left;">
<th style="width:100px;">Bild</th>
<th>Name</th>
<th>Datum</th>

</tr>';
								}
								else {
									echo '<div id="columns">';
								}
								foreach ($content->template['image_data'] as $dat) {
									if ($content->template['show'] == "list") {

										echo '
            <tr>
            <td><a href="#"
            class=""
            title=' . $dat['image_alt'] . '>';
										if (stristr($dat['image_name'], "crop_")) {
											$dat['image_name_crop'] = "thumbs/" . $dat['image_name'];
											echo '<img onClick="FileBrowserDialogue.mySubmit(\'../images/' . $dat['image_name_crop'] . '\',\'' . $dat['image_alt'] . '\',\'' . $dat['image_title'] . '\'); " src="../images/thumbs/' . $dat['image_name'] . '?' . time() . '" alt="' . $dat['image_alt'] . '" title="' . $dat['image_title'] . '" style="height:40px;" />';
										} else {

											echo '<img onClick="FileBrowserDialogue.mySubmit(\'../images/' . $dat['image_name'] . '\',\'' . $dat['image_alt'] . '\',\'' . $dat['image_title'] . '\'); " src="../images/thumbs/' . $dat['image_name'] . '?' . time() . '" alt="' . $dat['image_alt'] . '" title="' . $dat['image_title'] . '" style="height:40px;" />';
										}


										echo '</a></td>
            <td>' . $dat['image_title'] . '<br />
            <i class="fa fa-arrows-h"></i> ' . $dat['image_width'] . ' x ' . $dat['image_height'] . ' px
            </td>
            <td><i class="fa fa-clock-o"></i> ' . $dat['image_last_date'] . '</td>
            </tr>';


									} else {
										$thumbs = "";
										if ($dat['image_width'] >= 160) {
											$thumbs = "thumbs/";
										}
										$last_date = date("d.m.Y - G:i", @filemtime(PAPOO_ABS_PFAD . "/images/" . $dat['image_name']));
										echo '<div class="pin"><div style="padding: 5px; ">';
										if (stristr($dat['image_name'], "crop_")) {
											$dat['image_name_crop'] = "thumbs/" . $dat['image_name'];
											echo '<a href="#"><img src="../images/' . $thumbs . $dat['image_name'] . '?' . time() . '" alt="' . $dat['image_alt'] . '"  onClick="FileBrowserDialogue.mySubmit(\'../images/' . $dat['image_name_crop'] . '\',\'' . $dat['image_alt'] . '\',\'' . $dat['image_title'] . '\'); " /></a><br /> ';
											echo $dat['image_alt'] . "<br /><br /><i class=\"fa fa-arrows-h\"></i> " . $dat['image_width'] . " x " . $dat['image_height'] . " px <br /><i class=\"fa fa-clock-o\"></i> " . $last_date;
										}
										else {
											echo '<a href="#" title="Bild einf&uuml;gen"><img src="../images/' . $thumbs . $dat['image_name'] . '?' . time() . '" alt="' . $dat['image_alt'] . '" onClick="FileBrowserDialogue.mySubmit(\'../images/' . $dat['image_name'] . '\',\'' . $dat['image_alt'] . '\',\'' . $dat['image_title'] . '\'); " /></a><br /> ';
											echo '<a href="#" title="Thumbnail einf&uuml;gen" onClick="FileBrowserDialogue.mySubmit(\'../images/thumbs/' . $dat['image_name'] . '\',\'' . $dat['image_alt'] . '\',\'' . $dat['image_title'] . '\'); ">Nur Thumbnail einf&uuml;gen</a><br /><br /> ';
											echo $dat['image_alt'] . "<br /><br /><i class=\"fa fa-arrows-h\"></i> " . $dat['image_width'] . " x " . $dat['image_height'] . " px <br /><i class=\"fa fa-clock-o\"></i> " . $last_date;
										}

										echo "</div></div>";
									}


								}
								if ($content->template['show'] == "list") {
									echo '</table>';
								}
								else {
									echo "</div>";
								}

								//$this->content->template['image_data']
								?>

							</div>

						</div>
					</div>
				</div>
			</div>

			<!-- Galerien -->
			<div id="appearance_panel" class="panel <?php if (isset($checked->reiter_active) && $checked->reiter_active == "gal") {
				echo 'current';
			} ?>">


				<div class="image_folder_container_complete">
					<div class="image_folder_container_left">
						<strong><?php echo $content->template['system_image_image_ordner'] ?></strong>
						<!--<br />
<a class="bilder_verzeichnis_top" href="images_liste.php"> <?php echo $content->template['system_image_alle_galerienverzeichnisse'] ?> <span style="color: #333;">(<?php echo $content->template['bilder_cat_id_count']['0'] ?>)</span> </a>-->
						<div class="nested_ul">
							<ul>
								<?php
								#print_r($gal_liste);
								if (is_array($gal_liste)) {
									foreach ($gal_liste as $dat) {

										echo '<li><a ';
										if (isset($dat['bilder_active_cat_id']) && $content->template['bilder_active_cat_id'] == $dat['bilder_cat_id']) {
											echo ' class="active_cat" ';
										}
										echo 'href="./images_liste.php?galerie_id=' . $dat['gal_id'] . '&reiter_active=gal">' . $dat['gal_verzeichnis'] . ' (' . $dat['gal_bilderanzahl'];

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


							<div id="wrapper">
								<div id="columns">
									<?php
									//$this->content->template['image_data']
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
											echo '<div class="pin"><div style="padding: 5px; ">';
											echo '<a href="#"><img src="../plugins/galerie/galerien/' . $dat['gal_verzeichnis'] . "/" . $thumbs . $dat['bild_datei'] . '" alt="' . $dat['bildlang_name'] . '"  onClick="FileBrowserDialogue.mySubmit(\'../plugins/galerie/galerien/' . $dat['gal_verzeichnis'] . "/" . $dat['bild_datei'] . '\',\'' . $dat['bildlang_name'] . '\',\'' . $dat['bildlang_name'] . '\'); " /></a><br />	';
											echo $dat['bildlang_name'] . "<br />(" . $dat['bild_breite'] . "x" . $dat['bild_hoehe'] . " px)";
											echo "</div></div>";
										}
									} else {
										echo $content->template['system_keine_bilder_vorhanden'];
									}

									//$this->content->template['image_data']
									?>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
			<div id="upload_panel" class="panel  <?php if (isset($checked->reiter_active) && $checked->reiter_active == "upload") {
				echo 'current';
			} ?>">


				<!-- Lade bitte das Bild hoch. Es ird zus&auml;tzlich ein Thumbnail erzeugt. -->
				<?php if (empty($is_uploaded) || !empty($is_uploaded_fertig)) {
					if (!empty($is_uploaded_fertig)) {
						echo "<strong>" . $content->template['system_wurde_bild_hochgeladen'] . "</strong>";
					}

					?>
					<br/>
					<form action="" method="post" name="frmImage" enctype="multipart/form-data">
						<fieldset>
							<legend><?php echo $content->template['message_491'] ?></legend>
							<input type="hidden" name="action" value="EDIT"/>
							<input type="hidden" name="reiter_active" value="upload"/>
							<!-- Das Bild:-->
							<label><?php echo $content->template['message_212'] ?>:</label>
							<input type="file" name="strFile" accept="image/*"/>

							<!-- abschicken -->
							<input type="submit" class="submit_back" name="strSubmit"
								   value="<?php echo $content->template['message_213'] ?>"/>
						</fieldset>
					</form>    <br/>    <br/>    <br/>
					<p><?php echo $content->template['message_209'] ?></p>
					<!-- Maximale Dateigröße ist 100 kbyte und 800x800px! -->
					<p><?php echo $content->template['message_210'] ?></p>
					<!-- Unterstützte Formate: jpeg, jpg, pjpeg-->
					<p><?php echo $content->template['message_211'] ?></p>

					<?php
				}
				else {


					?>
					<form action="" method="post" name="frmImage" enctype="multipart/form-data">
						<input type="hidden" name="reiter_active" value="upload"/>
						<input type="hidden" name="image_modus"
							   value="<?php echo $content->template['image_modus'] ?>"/>
						<input type="hidden" name="image_name" value="<?php echo $content->template['image_name'] ?>"/>
						<input type="hidden" name="image_breite"
							   value="<?php echo $content->template['image_breite'] ?>"/>
						<input type="hidden" name="image_hoehe"
							   value="<?php echo $content->template['image_hoehe'] ?>"/>
						<input type="hidden" name="exist" value="<?php echo $content->template['exist'] ?>"/>

						<h2><?php echo $content->template['message_198'] ?></h2>
						<input name="action" value="SPEICHERN" type="hidden">

						<fieldset>

							<legend>Diese Daten wurden hochgeladen:</legend>


							<div style="width: 60%; float: right;">
								<?php echo $content->template['message_372'] ?>
								: <?php echo $content->template['image_breite'] ?> px<br/>
								<?php echo $content->template['message_373'] ?>
								: <?php echo $content->template['image_hoehe'] ?> px
							</div>
							<img src="<?php echo $content->template['dateiname_thumbnail_web'] ?>" alt="" title=""/>


						</fieldset>
						<fieldset>
							<legend><?php echo $content->template['message_492a'] ?></legend>
							<label for="image_dir "><?php echo $content->template['message_492a'] ?></label><br/>
							<select name="image_dir" size="1">
								<option value="0">keine (default)</option>
								<?php

								if (is_array($content->template['dirlist'])) {
									foreach ($content->template['dirlist'] as $key => $value) {
										echo '<option  value="' . $value['bilder_cat_id'] . '">' . $value['nbsp'] . ' ' . $value['bilder_cat_name'] . '</option>';
									}
								}

								?>

							</select>
						</fieldset>

						<script src='js/jquery.js' type='text/javascript'></script>
						<script type="text/javascript" src="js/jquery-ui-1.10.3.custom.min.js"></script>
						<script type="text/javascript" src="js/jquery.cropzoom.js"></script>

						<script type="text/javascript">
							$(document).ready(function () {
								var cropzoom = $('#crop_container').cropzoom({
									width: 400,
									height: 300,
									bgColor: '#CCC',
									enableRotation: true,
									enableZoom: true,
									zoomSteps: 5,
									rotationSteps: 1,
									selector: {
										centered: true,
										aspectRatio: false,
										startWithOverlay: true,
										borderColor: 'blue',
										borderColorHover: 'red',
										w: 183,
										h: 183,
									},
									image: {
										source: '<?php echo $content->template['dateiname_web']?>',
										width:<?php echo $content->template['image_breite']?>,
										height:<?php echo $content->template['image_hoehe']?>,
										minZoom: 10,
										maxZoom: 150
									}
								});
								cropzoom.setSelector(15, 15, 200, 200, true);
								$('#crop').click(function () {
									cropzoom.send('./resize_and_crop.php', 'POST', {}, function (rta) {
										$('.result').find('img').remove();
										var img = $('<img />').attr('src', rta);
										$('.result').find('.txt').hide().end().append(img);
										var input = $('<input type="hidden" name="new_img_src" />').attr('value', rta);
										$('.result').append(input);
									});
								});
								$('#restore').click(function () {
									$('.result').find('img').remove();
									$('.result').find('.txt').show()
									cropzoom.restore();
								})
							})

						</script>
						<fieldset>
							<legend>Thumbnail skalieren</legend>
							<div class="Post">
								<div class="Post-body">
									<div class="Post-inner">

										<div class="PostContent">
											<div class="boxes">
												<div id="crop_container"></div>
												<div class="result">
													<div class="txt">Skaliertes Thumbnail</div>
												</div>

											</div>
											<div class="clearfix"></div>
											<br/><br/>
											<div class="clearfix"></div>

											<span class="button-wrapper" id="crop">
                                                    <span class="l"> </span>
                                                    <span class="r"> </span>
                                                    <a class="btn btn-info" href="javascript:void(0)">Ausschneiden</a>
                                              </span>
											&nbsp;
											<span class="button-wrapper" id="restore">
                                                    <span class="l"> </span>
                                                    <span class="r"> </span>
                                                    <a class="btn btn-danger" href="javascript:void(0)">Zurücksetzen</a>
                                              </span>
										</div>
										<div class="clearfix"></div>
									</div>
								</div>
							</div>
						</fieldset>


						<?php
						if (is_array($content->template['menlang'])) {
							foreach ($content->template['menlang'] as $key => $lang) {
								echo '<fieldset>
				
				<legend>' . $content->template['message_203'] . "(" . $content->template['message_663'] . ":" . $lang['sprache'] . ')</legend>
				

				<p><strong>' . $content->template['message_204'] . '</strong></p>
				
				<label for="texte[' . $lang['lang_id'] . '][alt]">' . $content->template['message_205'] . ': (' . $content->template['message_206'] . ')</label><br />
				
				<input type="text" id="texte[' . $lang['lang_id'] . '][alt]" name="texte[' . $lang['lang_id'] . '][alt]" value="' . $lang['alt'] . '" size="30" /><br />
				
				<label for="texte[' . $lang['lang_id'] . '][title]" >' . $content->template['message_664'] . ': (' . $content->template['message_207'] . ')</label><br />
				<input type="text" id="texte[' . $lang['lang_id'] . '][title]" name="texte[' . $lang['lang_id'] . '][title]" value="' . $lang['title'] . '" size="50" /><br />

			

			
				<input type="hidden" name="texte[' . $lang['lang_id'] . '][lang_id]" value="' . $lang['lang_id'] . '" />
			</fieldset>';
							}
						}

						?>

						<fieldset>
							<legend><?php echo $content->template['message_224'] ?></legend>
							<input class="submit_back" name="eintrag"
								   value="<?php echo $content->template['message_224'] ?>" type="submit"/>

						</fieldset>
					</form>
					<?php
				}

				?>
			</div>


			<div id="shop_panel" class="panel  <?php if (isset($checked->reiter_active) && $checked->reiter_active == "shop") {
				echo 'current';
			} ?>">


				<div class="image_folder_container_complete">
					<div class="image_folder_container_left">
						<strong><?php echo $content->template['system_image_image_ordner'] ?></strong>
						<!--<br />
<a class="bilder_verzeichnis_top" href="images_liste.php"> <?php echo $content->template['system_image_alle_galerienverzeichnisse'] ?> <span style="color: #333;">(<?php echo $content->template['bilder_cat_id_count']['0'] ?>)</span> </a>-->
						<div class="nested_ul">
							<ul>
								<?php
								if (isset($shop_catDat) && is_array($shop_catDat)) {
									foreach ($shop_catDat as $dat) {

										echo '<li><a ';
										if ($content->template['bilder_active_cat_id'] == $dat['bilder_cat_id']) {
											echo ' class="active_cat" ';
										}
										echo 'href="./images_liste.php?shopcat_id=' . $dat['kategorien_id'] . '&reiter_active=shop">' . $dat['kategorien_lang_TitelderSeite'] . ' </a></li>';

									}
								} else {
									echo '<p style="margin-left:30px;">' . $content->template['system_kein_shop_vorhanden'] . '</p>';
								}
								?>
							</ul>
						</div>
					</div>

					<div class="image_folder_container_out">
						<div class="image_folder_container">


							<div id="wrapper">
								<div id="columns">
									<?php
									if (isset($images_shop) && is_array($images_shop)) {
										foreach ($images_shop as $dat) {
											$split = explode(",", $dat['produkte_lang_image_galerie']);

											$thumbs = "";
											$size = @getimagesize(PAPOO_ABS_PFAD . "/plugins/papoo_shop/images/" . $split['2']);

											$dat['bildlang_name'] = str_replace("nobr:", "", $dat['bildlang_name']);
											echo '<div class="pin"><div style="padding: 5px; ">';
											echo '<a href="#"><img src="../plugins/papoo_shop/images/thumbs/' . $split['0'] . '" alt="' . $dat['produkte_lang_produktename'] . '"  onClick="FileBrowserDialogue.mySubmit(\'../plugins/papoo_shop/images/' . $split['2'] . '\',\'' . $dat['produkte_lang_produktename'] . '\',\'' . $dat['produkte_lang_produktename'] . '\'); " /></a><br />	';
											echo $dat['produkte_lang_produktename'] . "<br />(" . $size['0'] . "x" . $size['1'] . " px)";
											echo "</div></div>";

										}
									} else {
										echo $content->template['system_keine_bilder_shop_vorhanden'];
									}

									//$this->content->template['image_data']
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
	</body>
	</html>
<?php } ?>