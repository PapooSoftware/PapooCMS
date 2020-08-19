
<?php phpinfo(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<title>Papoo conf_test</title>
	<meta name="generator" content="handmade by b." />
	<style type="text/css" title="text/css" media="all">
	/* <![CDATA[ */
		
		body
		{
			font-family: Verdana, Arial, sans-serif;
		}
		
		td {padding: 0 1EM 0 1EM;}
	/* ]]> */
	</style>
</head>
<body>
	<h1> Papoo Konfigurations-Test (Version: <?php global $version; echo $version; ?>)</h1>
	<p>PHP-Version: <?php echo phpversion(); ?></p>
	<div>
		<?php
			function check_chmod()
			{
				// Array mit Verzeichnissen, bei denen chmod getestet werden soll
				$directories = array(	"../templates_c",
										"../interna/templates_c",
										"../images",
										"../images/thumbs",
										"../dokumente",
										"../dokumente/upload",
										"../dokumente/logs",
										"../plugins",
										"../css"
									);
				
				echo '	<table summary="CHMOD-Test">
						<caption>Test ob Zugriffsrechte f&uuml;r Verzeichnisse richtig gesetzt sind</caption>
							<thead>
								<tr>
									<th id="v" abbr="Verzeichnis">Verzeichnis</th>
									<th id="r" abbr="Rechte">Rechte</th>
									<th id="b" abbr="Besitzer">Besitzer</th>
								</tr>
							</thead>
							<tbody>';
				
				foreach ($directories as $directory)
				{
					$besitzer_array = posix_getpwuid(fileowner($directory));
					$besitzer = $besitzer_array['name'];
					
					$infos = sprintf("<tr><td>%s</td><td>%o</td><td>%s</td></tr>\n",
									$directory,
									fileperms($directory),
									$besitzer
									);
					echo $infos;
				}
				echo "</tbody></table>";
			}
			check_chmod();
		?>
	</div>
	<div>
		<?php
			function check_gdlib()
			{
				$gd_infos = gd_info();
				if (!empty($gd_infos))
				{
					echo '	<table summary="GD-Lib Informationen">
								<caption>Informationen der GD-Lib auf diesem Server</caption>
									<thead>
										<tr>
											<th id="n" abbr="Name">Name</th>
											<th id="w" abbr="Wert">Wert</th>
										</tr>
									</thead>
									<tbody>';
					foreach($gd_infos as $name => $wert)
					{
						echo "<tr><td>".$name."</td><td>".$wert."</td></tr>";
					}
					echo "</tbody></table>";
				}
				else echo "Keine GD-Lib installiert !";
			}
			check_gdlib();
		?>
	</div>
    <div>Memory-Limit: 
    <?php 
    echo ini_get("memory_limit");
    ini_set("memory_limit", "32M");
    ?><br />Memory-Limit nach setzen auf 32:
    <?php 
    echo ini_get("memory_limit");
    ?>
    </div>
	<div>
		<p>
			<strong>Safe_Mode-Test (wegen set_time_limit(); in dumpnrestore-Klasse)</strong><br />
			<?php
				if(!ini_get("safe_mode")) echo "Safe_Mode ist nicht aktiv!<br />";
				else echo "Safe_Mode ist aktiv!<br />";
			?>
		</p>
	</div>
</body>
</html>