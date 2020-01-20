
				<style type="text/css">
					#header {
						margin-bottom:0px;
					}
					.middle .content {
						width:1024px;
						max-width:100%;
						margin-left:auto;
						margin-right:auto;
						text-align:left;
					}
					.middle .blocks {
						display:none;
					}
				</style>			
				<?php
								
				if (!empty($_GET['dir'])) {
				?>
				<h2><?php echo str_replace("_", " ", ucfirst($_GET['dir'])); ?></h2>
				<iframe src="<?php echo eMain::root_url(); ?>_demos/<?php echo $_GET['dir']; ?>" width="1024" height="718" style="background-color:#ffffff;">
				<?php
				} // END OF IF !EMPTY
				?>
				</iframe>
				
				<h2>Liste des d&eacute;mos</h2>
				
				<?php
				
				$dossier = "_demos";
				$repertoire = opendir($dossier);
				$liste_dossier=array();
				while ($le_dossier = readdir($repertoire)) {
					if ($le_dossier!="." && $le_dossier!="..") {
						$ext = strtolower(substr($le_dossier, strlen($le_dossier)-5, 5));
						if (substr_count($ext, ".")<1) {
							$liste_dossier[] = $le_dossier;
						}
					}
				}
				
				sort($liste_dossier);
				
				$nb = count($liste_dossier);
				for($i=0;$i<$nb;$i++) {
				?>
				<a href="?dir=<?php echo $liste_dossier[$i]; ?>"><?php echo str_replace("_", " ", ucfirst($liste_dossier[$i])); ?></a><br />
				<?php
				}
				
				?>
			