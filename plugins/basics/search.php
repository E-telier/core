		<style type="text/css">
			.search_result {
				padding-bottom:20px;
				margin-top:20px;
				border-bottom: 1px solid #cccccc;
			}
		</style>
<?php

global $currentDatas;
global $current_language;
global $table_prefix;
global $search_string;
global $folderURL;

$dictionnary['fr']['résultats'] = "résultats";		
$dictionnary['fr']['mots clés'] = "mots clés";	
$dictionnary['fr']['pour'] = "pour";

$dictionnary['en']['résultats'] = "results";
$dictionnary['en']['mots clés'] = "keywords";
$dictionnary['en']['pour'] = "for";

$traductions = $dictionnary[$current_language];

$search_string = utf8_decode(urldecode(str_replace("&quot;", "\"", trim($currentDatas[1]))));

if (strlen($search_string)>0) {
	
	//include ("connect.php");
	
	$table=$table_prefix."_cms_pages";
	
	$requete= "SELECT * FROM $table ORDER BY id DESC";
	
	$result = mysqli_query($mysqli, $requete);
	
	$title = "";
	$description = "";
		
	function checkEmpty($var) {
		return (trim($var)!="");
	}
	
	// Fonction pour compter le nombre de concordances //
	function getSearchValue($chaine) {
	
		global $search_string;
		
		$result_string = $chaine;
			
		$chaine = strtolower($chaine);
		$recherche = strtolower($search_string);
		
		$expressions = explode("\"", $recherche);
		$expressions = array_filter($expressions, "checkEmpty");
		sort($expressions);	
		$nbExpressions = count($expressions);
		//print_r($expressions);
		
		// Suppression des guillemets //
		$recherche = str_replace("\"", "", $recherche);
		
		$mots = explode(" ", $recherche);
		$nbreMots = count($mots);
				
		// Expressions entre guillemets //						
		$valueExp = 0;
		for ($exp=0;$exp<$nbExpressions;$exp++) {
			$expressions[$exp] = trim($expressions[$exp]);
			$nbreExp = substr_count($chaine, $expressions[$exp]);
			if ($nbreExp>0) {
				$valueExp += 5*$nbreExp;
				//echo "valueExp";
				$result_string = boldKeywords($expressions[$exp], $result_string);
			}
		}
				
		// Chaine entière ??? //
		$valueFull = 0;
		if (substr_count($recherche, " ")>0 && strlen($recherche)>5) {
			$nbreFull = substr_count($chaine, $recherche);
			if ($nbreFull>0) {
				$valueFull = 10*$nbreFull;
				$result_string = boldKeywords($recherche, $result_string);
			}
		}
								
		// Morceaux de chaine //
		$valuePart = 0;
		for ($mot=0;$mot<$nbreMots;$mot++) {
		
			// Mot Interdit ? //
			$motsInterdits = array("le", "la", "les", "de", "du", "des", "à", "et", "ce", "se", "dans");
			$motInterdit = false;
			for ($interdit=0;$interdit<count($motsInterdits);$interdit++) {
				if ($mots[$mot]==$motsInterdits[$interdit]) {
					$motInterdit = true;
					//echo "motInterdit";
					break;
				}
			}
			
			if ($motInterdit==false) {
				
				$valuePart += substr_count($chaine, $mots[$mot]);
				if ($valuePart>0) {
					$result_string = boldKeywords($mots[$mot], $result_string);
				}
				
			}
		}
						
		$value = $valueFull+$valueExp+$valuePart;		
		$search_result = array('value'=>$value, 'result_string'=>$result_string);
			
		return $search_result;
		
	}
	
	function boldKeywords ($search, $string) {
				
		// mise en gras //
		$string = str_replace($search, "<b>".$search."</b>", $string);
		$string = str_replace(ucfirst($search), "<b>".ucfirst($search)."</b>", $string);
		$string = str_replace(strtoupper($search), "<b>".strtoupper($search)."</b>", $string);
		$string = str_replace(strtolower($search), "<b>".strtolower($search)."</b>", $string);
		// enlève balises inutiles devant-derrière //
		$string = str_replace("<b><b>".ucfirst($search)."</b>", "<b>".ucfirst($search), $string);
		$string = str_replace("<b>".ucfirst($search)."</b></b>", ucfirst($search)."</b>", $string);
		$string = str_replace("<b><b>".$search."</b>", "<b>".$search, $string);
		$string = str_replace("<b>".$search."</b></b>", $search."</b>", $string);
					
		return $string;
		
	}
	
	$resultats = array();	
	
	for($i=0; $i < $result_datas['nb'];;$i++) {
		
			$contenu = $result_datas['datas'][0];
			
			$searchValue = 0;
			
			$title=stripslashes($contenu['title']);
			$thisDatas = getSearchValue($title);
			$thisValue = $thisDatas['value'];
			$title = $thisDatas['result_string'];
			$searchValue += $thisValue;
						
			$description=stripslashes($contenu['description']);
			$thisDatas = getSearchValue($description);
			$thisValue = $thisDatas['value'];
			$description = $thisDatas['result_string'];
			$searchValue += $thisValue;
			
			$keywords=stripslashes($contenu['keywords']);
			$thisDatas = getSearchValue($keywords);
			$thisValue = $thisDatas['value'];
			$keywords = $thisDatas['result_string'];
			$searchValue += $thisValue;
			
			$content = $contenu['content'];
			$thisDatas = getSearchValue($content);
			$thisValue = $thisDatas['value'];
			$content = $thisDatas['result_string'];
			$searchValue += $thisValue;
						
			if ($searchValue>0) {
							
				$reference = $contenu ['reference'];
										
				$donnees = array("search_value"=>$searchValue, "title"=>$title, "reference"=>$reference, "description"=>$description, "keywords"=>$keywords);
				$resultats[] = $donnees;
			}
					
	}
	
	$nb_results = count($resultats);
	
?>
		<h2><?php echo $nb_results." ".styleToHTML($traductions['résultats']." ".$traductions['pour']." : ".$search_string); ?></h2>
<?php
	
	if ($nb_results>0) {
		
		// Réorganisation si nécessaire //
		for ($i=0; $i < $nb_results-1;$i++) {
			//echo $i." nbre=".$resultats[$i][0]."<br>";
			if ($resultats[$i]['search_value']<$resultats[$i+1]['search_value']) {
				$temp = $resultats[$i];
				$resultats[$i] = $resultats[$i+1];
				$resultats[$i+1] = $temp;
				$i=-1;
				//echo "<br>";
			}
		}
		
		// Affichage //
		
		for ($i=0; $i < $nb_results;$i++) {
			//echo $resultats[$i]['search_value'];
?>
			<div class="search_result">
				<a href="<?php echo $folderURL.$resultats[$i]['reference']; ?>"><?php echo $resultats[$i]['title']; ?></a>
				<br />
				<?php echo $resultats[$i]['description']; ?>
				<br /><br />
				<small><?php echo ucfirst($traductions['mots clés']); ?> : <?php echo $resultats[$i]['keywords']; ?></small>
			</div>
<?php			
		} // END OF FOR
	
	}
		
}

?>

