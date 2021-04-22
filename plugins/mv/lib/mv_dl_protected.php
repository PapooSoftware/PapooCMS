<?php
if ($this->checked->template != "mv/templates/mv_show_front.html") $this->content->template['errmsg'] = "Aufruf nicht erlaubt";
#elseif (!$this->checked->file) $this->content->template['errmsg'] = "Dateiname fehlt";
elseif (!$this->checked->field_id) $this->content->template['errmsg'] = "Ung&uml;tige File-Id";	
#elseif ($this->user->userid == 11) $this->content->template['errmsg'] = "Bitte melden Sie sich an";
else
{
	if ($this->checked->create_download_file)
	{
		$sql = sprintf("SELECT * FROM %s",
								$this->cms->tbname['papoo_mv_download_protocol']
						);
		$result = $this->db->get_results($sql, ARRAY_A);
		if (count($result))
		{
			$filename = PAPOO_ABS_PFAD . "/dokumente/logs/mv_download.txt";
			if (@file_exists($filename)) unlink($filename);
			$f = fopen($filename, "w");
			foreach ($result AS $key => $value)
			{
				$rec = $value['file_name'] . ";";
				$rec .= $value['mv_id'] . ";";
				$rec .= $value['mv_content_id'] . ";";
				$rec .= $value['field_id'] . ";";
				$rec .= $value['mv_content_id'] . ";";
				$rec .= $value['dl_count'] . ";";
				$rec .= "\r\n"; // und mit CR & LF abschliessen
				fwrite($f, $rec);
				$rec = "";
			}
			fclose($f);
		}
	}
	else
	{
		$extern_meta = $this->checked->extern_meta == "x" ? 1 : $this->checked->extern_meta;
		$sql = sprintf("SELECT DISTINCT(field_id)
									FROM %s, %s 
									WHERE gruppenid = group_id
									AND userid = '%d'
									AND group_read = 1
									AND field_right_meta_id = '%d'
									AND field_id = '%d'
									ORDER BY field_id ASC",
									$this->cms->tbname['papoo_lookup_ug'],
									
									$this->cms->tbname['papoo_mv']
									. "_content_"
									. $this->db->escape($this->checked->mv_id)
									. "_field_rights",
									
									$this->db->escape($this->user->userid),
									$this->db->escape($extern_meta),
									$this->db->escape($this->checked->field_id)
						);
		$felderrechte = $this->db->get_results($sql, ARRAY_A);
		if (count($felderrechte))
		{
			if(is_numeric($this->checked->mv_id) AND is_numeric($this->checked->mv_content_id))
			{
				$sql = sprintf("SELECT mvcform_name
											FROM %s
											WHERE mvcform_form_id = '%d'
											AND mvcform_id  = '%d'",
											
											$this->cms->tbname['papoo_mvcform'],
											
											$this->db->escape($this->checked->mv_id),
											$this->db->escape($this->checked->field_id)
								);
				$mvcform_name = $this->db->get_var($sql);
				if ($mvcform_name)
				{
					$fieldname = $mvcform_name . "_" . $this->checked->field_id;
					$sql = sprintf("SELECT %s FROM %s 
												WHERE mv_content_id = '%d'",
												
												$fieldname,
												
												$this->cms->tbname['papoo_mv']
												. "_content_"
												. $this->db->escape($this->checked->mv_id)
												. "_search_"
												. $this->db->escape($this->cms->lang_id),
												
												$this->db->escape($this->checked->mv_content_id)
									);
					$file = $this->db->get_var($sql);
					if ($file)
					{
						$path = PAPOO_ABS_PFAD . "/files/" . $file;
						if (file_exists($path))
						{
							$defaultmimes = array(
										'aif' => 'audio/x-aiff',
										'aiff' => 'audio/x-aiff',
										'arc' => 'application/octet-stream',
										'arj' => 'application/octet-stream',
										'ark' => 'application/octet-stream',
										'art' => 'image/x-jg',
										'asd' => 'video/x-ms-asf',
										'asf' => 'video/x-ms-asf',
										'asx' => 'video/x-ms-asf',
										'avi' => 'video/avi',
										'bin' => 'application/octet-stream',
										'bm' => 'image/bmp',
										'bmp' => 'image/bmp',
										'bz' => 'application/x-bzip',
										'bz2' => 'application/x-bzip2',
										'cab' => 'application/vnd.ms-cab-compressed',
										'class' => 'application/x-java-class',
										'css' => 'text/css',
										'csv' => 'application/excel',
										'dll' => 'application/x-msdownload',
										'doc' => 'application/msword',
										'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
										'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
										'dot' => 'application/msword',
										'dv' => 'video/x-dv',
										'dvi' => 'application/x-dvi',
										'eps' => 'application/postscript',
										'epsf' => 'application/postscript',
										'eps2' => 'application/postscript',
										'exe' => 'application/octet-stream',
										'flv' => 'video/x-flv',
										'fpx' => 'image/vnd.fpx',
										'gif' => 'image/gif',
										'gz' => 'application/x-gzip',
										'gzip' => 'application/x-gzip',
										'htm' => 'text/html',
										'html' => 'text/html',
										'ico' => 'image/vnd.microsoft.icon',
										'iso' => 'application/x-iso9660-image',
										'jam' => 'audio/x-jam',
										'jar' => 'application/java-archive',
										'java' => 'text/x-java-source',
										'jpe' => 'image/jpeg',
										'jpeg' => 'image/jpeg',
										'jpg' => 'image/jpeg',
										'js' => 'application/x-javascript',
										'lha' => 'application/x-lha',
										'log' => 'text/x-log',
										'lst' => 'text/plain',
										'lzh' => 'application/x-lzh',
										'lzx' => 'application/x-lzx',
										'mid' => 'audio/x-midi',
										'mov' => 'video/quicktime',
										'mp2' => 'audio/mpeg',
										'mp3' => 'audio/mpeg3',
										'mp4' => 'audio/mpeg4',
										'mpeg' => 'audio/mpeg',
										'mpg' => 'audio/mpeg',
										'pbm' => 'image/x-portable-bitmap',
										'pct' => 'image/x-pict',
										'pcx' => 'image/x-pcx',
										'pdf' => 'application/pdf',
										'png' => 'image/png',
										'pps' => 'application/vnd.ms-powerpoint',
										'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
										'ppt' => 'application/vnd.ms-powerpoint',
										'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
										'ppz' => 'application/vnd.ms-powerpoint',
										'ps' => 'application/postscript',
										'psd' => 'application/octet-stream',
										'qti' => 'image/x-quicktime',
										'ram' => 'audio/x-realaudio',
										'rm' => 'application/vnd.rn-realmedia',
										'rmvb' => 'application/vnd.rn-realmedia',
										'rtf' => 'application/rtf',
										'svg' => 'image/svg',
										'swf' => 'application/x-shockwave-flash',
										'tar' => 'application/x-tar',
										'text' => 'text/plain',
										'tgz' => 'application/x-compressed',
										'tif' => 'image/tiff',
										'tiff' => 'image/tiff',
										'txt' => 'text/plain',
										'xls' => 'application/vnd.ms-excel',
										'xml' => 'application/xml',
										'zip' => 'application/zip'
									);
							$datei_ext = strtolower(substr(strrchr($file, '.'), 1));
							$mime = $defaultmimes[$datei_ext];
							$mime = $mime ? $mime : "application/octet-stream"; // default mime
							#print_r($this->checked);exit;
							header("Content-type: " . $mime . "\n");
							if (!headers_sent())
							{
								// Timestamp aus dem Filenamen raus, soll beim User im Filenamen nicht mitgespeichert werden
								$filename = substr(strstr($file, "_"), 1);
								// Download zählen
								// feststellen, ob schon mal gezählt wurde
								$sql = sprintf("SELECT count(dl_idx) FROM %s 
														WHERE file_name = '%s'",
														
														$this->cms->tbname['papoo_mv_download_protocol'],
	
														$this->db->escape($file)
												);
								$dl_idx = $this->db->get_var($sql);
								if (!$dl_idx)
								{
									// nein, noch nie gezählt
									$sql = sprintf("INSERT INTO %s SET 
																	file_name = '%s',
																	mv_id = '%d',
																	mv_content_id = '%d',
																	field_id = '%d',
																	dl_count = 1",
																	
																	$this->cms->tbname['papoo_mv_download_protocol'],
																	
																	$this->db->escape($file),
																	$this->db->escape($this->checked->mv_id),
																	$this->db->escape($this->checked->mv_content_id),
																	$this->db->escape($this->checked->field_id)
													);
								}
								else
								{
									// ja, wurde schon gezählt, dann um eins inkrementieren
									$sql = sprintf("UPDATE %s SET dl_count = dl_count + 1
																WHERE dl_idx = '%d'",
																	
																$this->cms->tbname['papoo_mv_download_protocol'],
																
																$this->db->escape($dl_idx)
													);
								}
								$this->db->query($sql);
								#$filename = substr(strstr($filename, "_"), 1); // falls alles bis zum 2. underscore entfernt werden soll
								header("Content-length: " . filesize($path) . "\n");
								header('Pragma: no-cache');
								header('Cache-Control: maxage=0');
								header('Content-Disposition: attachment; filename="' . $filename . '"');
								header('Content-Transfer-Encoding: binary');
								/*if ($file = fopen($path, 'rb'))
								{
									$data = fread($file, filesize($path));
									fclose($file);
									echo $data;
								}*/
								echo file_get_contents($path);
							}
							$this->content->template['errmsg'] = "ok";
						}
						else $this->content->template['errmsg'] = "Die Datei " . $file . " ist nicht vorhanden.";
					}
				}
			}
		}
	}
}
?>