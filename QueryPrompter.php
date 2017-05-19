 <?php

require_once  "/StringShortcut.php";
require_once  "/Colors.php";
require_once "/Arrays.php";


class QueryPrompter
{
	function __construct(){}



	# Echappe ou non les "caractères HTML" suivant l'option
	public static function EchoData($ToPrompt, $options)
	{
		if (!isset($options['NoHTMLInsertion']))
		{
			echo $ToPrompt;
		}
		else
		{
			echo htmlspecialchars($ToPrompt);
		}	
	}











	## $result doit être une valeur retournée par la fonction odbc_exec
	## $text est une string qui sera votre titre de table et votre nom d'onglet/page
	## $options permet de trigger une action, par exemple Prompt($result, 'Ceci est le texte', $options = array('NoCellFusion' => 1)) désactivera la fonctionnalité de fusion de cellules
	## Listing des options : 'NoCellFusion', 'NoImagePrompt', 'NoColumnHead', 'NoHTMLInsertion', 'NotANewPage', 'ColumnOrder'
	# NoCellFusion : désactive la fusion ce cellules linéairement contigues de même valeurs
	# NoImagePrompt : désactive la conversion d'un ntext en image si cela est possible
	# NoCollumnHead : N'affiche pas les noms de colonne
	# NoHTMLInsertion : empêche que du texte récupéré par la requête SQL ne soit interprété comme du code HTML, force par la même occasion le NoImagePrompt
		# Il est possible de faire retourner par la requête SQL une valeur imbriquée dans une balise de type <div>, dépendemment de la classe de cette balises certaines actions peuvent être effectuées
			# Classes Traitées : ColoredID, 
	# NotANewPage : supprime les echos de headers HTML, peut permettre d'afficher plusieurs tables sur une même page
	# ColumnOrder : !!!ATTENTION!!! Cette option n'est pas à activer (set à 1 ou 'a' par exemple) mais à définir, soit evec un array d'int correspondant à la colonne à afficher, soit avec le mot clé 'revert' pour afficher dans le sens
	# inverse de lecture, par défaut elle suit l'ordre croissant
	public static function Prompt($result, $text = 'This is default $text', $options = [])
	{











		##################################### INITIALISATION COLUMN ORDER #####################################


		$Conforme;
		# On joue sur l'ordre d'arrivé des elseif pour s'épargner l'écriture de certains tests et conditions 
		if (!isset($options['ColumnOrder'][0])) # Revient au même que !isset($options['ColumnOrder'] mais évite une vérification de isset juste en dessous
		{
			$Conforme = FALSE;
		}
		elseif ($options['ColumnOrder'][0] == 'revert')
		{
			$array = array();

			for ($i=odbc_num_fields($result); $i > 0 ; $i--) { 
				$array[] = $i;
			}
			$options['ColumnOrder'] = $array;
			$Conforme = TRUE;
		}


		### Certaines fonctions d'auto-complétion, en cas de non respect de la condition ci-dessous pourraient être pertinentes à rajouter
		elseif (count($options['ColumnOrder']) != odbc_num_fields($result))
		{
			$Conforme = FALSE;
		}

		# Parmis les revendications de conformités, on demande à ce que TOUT les champs de ColumnOrder soient des int
		else
		{
    		$Conforme = ArrayAnalyse::IsFullOf($options['ColumnOrder'], "int");
		}





		# Si le tableau ColumnOrder, passé en $options, n'existe pas ou n'est pas conforme, il est remplacé par array (ordre des columns comme dans la requête SQL)
		if ($Conforme !== TRUE) {
			$array = array();

			for ($i=1; $i <= odbc_num_fields($result); $i++) { 
				$array[] = $i;
			}
			$options['ColumnOrder'] = $array;
		}











		##################################### INITIALISATION DOC HTML #####################################



		if (!isset($options['NotANewPage']))
		{

			echo "<!DOCTYPE html>
				 <html>
					 <head>
						<title>$text</title>
					 	<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\" /> <!--Modifiez ici pour avoir votre bon fichier css ##-->
					 </head>
				 	<body>";
		}


























		##################################### INITIALISATION TABLEAU #####################################


		echo "<div class=\"TableContainer\">
				<table>
					<caption>".$text."</caption>";  ##Le paramètre $Text est entré en nom de table


		if (!isset($options['NoColumnHead']))
		{
			echo "<thead>";
			echo "<tr id = 'ColumNameLine'>";
			for ($i=0; $i < odbc_num_fields($result); $i++)
			{
				if (!is_null(odbc_field_name($result, $options['ColumnOrder'][$i])))
				{
					echo "<th id ='ColumName'>";  
					if (!isset($options['NoHTMLInsertion']))
					{
						echo utf8_encode(odbc_field_name($result, $options['ColumnOrder'][$i])); ### Selon l'encodage de la BDD source, il peut être recommandé de rajouter ou enlever la fonction utf8_encode()
					}
					else
					{
						echo htmlspecialchars(utf8_encode(odbc_field_name($result, $options['ColumnOrder'][$i]))); ### Selon l'encodage de la BDD source, il peut être recommandé de rejouter la fonction utf8_encode()
					}
					echo "</th>";
				}
			}
			echo "</tr>
			   </thead>";
		}




















		##################################### REMPLISSAGE TABLEAU #####################################




		while(odbc_fetch_row($result)){

			echo "<tr>";
			for($i=0;$i<count($options['ColumnOrder']);$i++) # Actuellement, il est en soit possible d'utiliser $i<odbc_num_fields($result) 
															 #mais cette solution est meilleur dans le cas où l'on veut rajouter la fonctionnalité qui n'affiache pas toutes les colonnes
			{
				##################################### INITIALISATION BOUCLE #####################################

				$data = ''; # Conternu à afficher
				$j= 1;      # Saut de champ dans l'algorythme de fusion de cellules
				$TDFormat = '<td>'; # Format par défaut des cellules, des attributs peuvent être ajoutes pas la suite pour ajuster la mise en forme selon le contexte


				#Si la valeur est u, type numérique suceptible d'avoir une virgule
				if  (in_array(odbc_field_type($result,$options['ColumnOrder'][$i]), $arrayName = array('float', 'real', 'numeric', 'decimal', 'smallmoney', 'money')))
				{

					# ARRONDI DES FLOATS et les REALS POUR GERER L'APPROXIAMTION QUI GÉNÈRE DES DÉCIMALES SUPPLÉMENTAIRES
					switch (odbc_field_type($result,$options['ColumnOrder'][$i]))
					{
						case 'float':
							$data = strval(round(floatval(odbc_result($result,$options['ColumnOrder'][$i])), 15)); # Approximation à 15 chiffres après la virgule pour les floats
							break;

						case 'real':
							$data = strval(round(floatval(odbc_result($result,$options['ColumnOrder'][$i])), 6)); # approximation à 6 chifres après la virgule pour les reals
							break;
						
						default:
							$data = odbc_result($result,$options['ColumnOrder'][$i]);
							break;
					}
					$data = StringManipulation::SimplifyNumber($data); # On converti au format de nombre anglais (pas de séparateur de milliers et un point pour séparer la partie entière des décimaux) et supprime les 0 supperflus			
				}

				else
				{
					$data = odbc_result($result,$options['ColumnOrder'][$i]);		
				}









				#Permet de retrouver certains ntext n'ayant pas réussis à être chargés.
				if ($data === FALSE AND odbc_field_type($result,$options['ColumnOrder'][$i]) != 'boolean' AND isset($data2)) {
					$data = $data2;
				}

				









				# Si le champ est de type ntext, que ni NoImagePrompt, ni NoHTMLInsertion ne sont activés (NoHTMLInsertion parce que l'image est affiché via une balise HTML, il serait idiot de surcharger la donnée avec cette balise) et que la valeur n'est pas nulle...
				# (on ne fait pas de elseif avec le test de type float se trouvant plus haut car pour l'exécution à l'intérieur du if courant, il est possible d'avoir besoin du if juste au dessus [qui doit s'exécuter aussi après le test de float])
				if (!isset($options['NoImagePrompt']) AND !isset($options['NoHTMLInsertion']) AND odbc_field_type($result,$options['ColumnOrder'][$i]) == 'ntext' AND $data !== NULL)
				{
					$imgdata = base64_decode($data);
					$f = finfo_open();
					$mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);

					# On vérifie s'il s'agit d'une image
					if (StringAnalyse::startsWith($mime_type, "image")) {
						# Et si c'est le cas, on l'affiche en tant qu'image et non en tant que texte
						$data = "<div class='PictureInCell'><img src='data:$mime_type;base64,$data'/></div>";
					}
					
				}


















				### REGROUPE plusieurs champs contigus longitudinalement s'il sont remplis de la même façon
				# Vérifie si le prochain champ n'est pas hors range
				$data2 = !isset($options['NoCellFusion']) && $i+1<odbc_num_fields($result) ? odbc_result($result,$options['ColumnOrder'][$i+$j]) : $data2;

				if(!isset($options['NoCellFusion']) AND $i+$j<odbc_num_fields($result) AND $data === $data2)
				{
					while ($i+$j<odbc_num_fields($result) AND ($data === $data2))
					{
						$j++;
						$data2 = odbc_result($result,$options['ColumnOrder'][$i+$j]);
					}
					$Cellfusion = 1; # On set la variable $Cellfusion qui permettra au reste du code de savoir que la cellule en cous de création est un <td colspan>
					$TDFormat = "<td colspan='$j' class='ColspannedTD'>";
					# On saute les champs fusionnés
					$i+= $j-1;
				}
				# $j ne sert plus à partir d'ici
				unset($j);














				if (isset($options['NoHTMLInsertion']) AND StringAnalyse::startsWith($data, "<div") AND StringAnalyse::endsWith($data, "</div>"))
				{
					$data = substr($data, 0, strlen($data)-strlen("</div>"));
					$data = strstr($data, ">");
					$data = substr($data, 1); # On ne garde la chaine qui se trouve APRÈS le premier chevron fermant
				}











				##### Permet de réaliser des opérations personnalisées sur certains champs
				### Si les insertions HTML sont autorisées et que $data est une balise div 
				if (!isset($options['NoHTMLInsertion']) AND preg_match("/\A^<div.*class=[\"|'].*[\"|'].*>.*<\/div>$/im", $data)) # Bien faire attentions aux Withespaces
				{


					if(isset($Cellfusion))
					{
						$preg_match_result = array();
						preg_match("/\A^<td\s*colspan=('|\")[0-9]*('|\")\s*class=('|\")(?P<Class>\w*)('|\")>$/im", $TDFormat, $preg_match_result); # On cherche à catch le contenu de l'attribut class
						$classList = $preg_match_result['Class'];
						$TDFormat = preg_replace("/".$classList."/im", $classList." DivTDParent", $TDFormat); # recherche du contenu de l'attribut class puis remplacement par le nouveau (qui constitue en l'ajout de la classe DivTDParent)
					}
					else
					{
						# On laisse champ libre à la balise div pour la mise en forme						
						$TDFormat = substr_replace($TDFormat," Class=\"DivTDParent\"", -1, 0);
					}







					$preg_match_result = array(); # On re/set $preg_match_result suivant le résultat du if au dessus

					#On récupère la classe du div, qui va nous indiquer les actions/modifications à effectuer par la suite
					preg_match("/\A^<.* class=(\"|')(?P<Class>\w*)(\"|').*>.*<\/.*>$\z/i", $data, $preg_match_result);
					$classList = $preg_match_result['Class'];					
					

					preg_match("/<div.*style=\"(?P<Style>.*)\">.*<\/div>/i", $data, $preg_match_result);
					$style = isset($preg_match_result['Style']) ? $preg_match_result['Style'] : NULL;







					# Si après avoir extrait le style, il est NULL, alors il n'existe pas et on le créé
					if ($style === NULL)
					{
						preg_match("/\A^<div(?P<Attributes>.*)>.*<\/div>$/i", $data, $preg_match_result); # On cherche à catch le contenu de l'attribut class
						$Attributes = $preg_match_result['Attributes'];
						$NewAttributes = substr_replace($Attributes, " Style=\"\"", strpos($Attributes, ">"), 0);
						$data = preg_replace("/".preg_quote($Attributes, "/")."/i", $NewAttributes, $data);
						$style = ""; # On s'épargne la peine de recharger via pre_match le style vu qu'ici on connais sa valeur
					}













					foreach (explode(" ", $classList) as $class) # S'il y a plusieurs classes, on applique les effets dans leur ordre d'apparition 
					{
						switch ($class)
						{
							# défini le background-color en fonction de la couleur du texte (couleur complémentaires)
							case 'ColoredID':
								if(isset($style))
								{
									preg_match("/background-color\s*:\s*(?P<hexacode>(\w|#)*);/i", $style, $preg_match_result);
									$hexacode = isset($preg_match_result['hexacode']) ? $preg_match_result['hexacode'] : null;
									$NewStyle = $style." color : ". Colors::FontColorDependingBackgroundColor($hexacode).";"; # N'ajouter ici que des éléments dynamiques si votre valeur est une constante, il faut la mettre dans le fichier CSS
									$data = preg_replace("/".$style."/i", "$NewStyle", $data); # recherche une chaine de caractère exactement comme le style originel dans $data et la remplace pas le nouveau style
								}
								break;









							case 'ColoredSubTotal': # Nécessite d'avoir un ColoredID sur la même ligne
								if (isset($hexacode))
								{
									$decimalColor = Colors::RGBHexaToRGB255($hexacode);
									$color = "rgba(".$decimalColor['R'].", ".$decimalColor['G'].", ".$decimalColor['B'].", 0.2)"; # le contenu de $color aurait pu être inséré tel quel dans les preg_replace futurs mais il est utilisé plusieurs fois et mêtre juste $color aide à la lisibilité du code, on préfère donc l'écrire de cette façon.
									preg_match("/background-color\s*:\s*(?P<backgroundColor>.*);/i", $data, $preg_match_result);
									$backgroundColor = isset($preg_match_result['backgroundColor']) ? $preg_match_result['backgroundColor'] : NULL;

									
									if ($backgroundColor === NULL) # Cas le plus courant à prioris car il y a peu d'intérêt à préset backgroundColor dans la requête et utiliser la classe ColoredSubTotal en même temps
																   # cependant les autres méthodes liées aux classes pourraient créer un backgroundColor, on fais donc le test par sécurité
									{
										$data = preg_replace("/style=\"".$style."\"/i", "style=\"".$style."background-color : $color\"", $data); # On conserve le style existant on applique la même couleur de fond que pour l'ID, mais on joue sur la composante alpha afin de la rendre moins brillante, moins saturée, plus adoucie, ...
									}
									else
									{
										$data = preg_replace("/style=\"".$style."\"/i", "style=\"".preg_replace("/background-color\s*:\s*".$backgroundColor."/i", "background-color:".$color, $style)."\"", $data); 
										# On ne prend pas le risque que la chaine $data contienne background-color c'est pourquoi on effectue le changement en deux preg_replace
									}

								}
								break;
							







							case 'PHPColoredTotal':
								$hexacode = "#000000"; # La Couleur du Total est fixe pour que les utilisateurs puissent plus facilement s'y retrouver
								$decimalColor = Colors::RGBHexaToRGB255($hexacode);
								$color = "rgba(".$decimalColor['R'].", ".$decimalColor['G'].", ".$decimalColor['B'].", 0.25)"; 
								$data = preg_replace("/style=\"".$style."\"/im", "style=\"background-color : $color; font-size : 140%;\"", $data);
								# On aurait pu tout écrire sur la ligne mais changer un code hexadécimal est plus commode que 3 paquets séparés par une virgule, de plus si l'on ne veut plus que total ait une valeur fixe (et qu'il suive le code hexadécimal de la ligne), il suffira de retirer la (re)définition d'hexacode (mais pour de tels besoins autant utiliser la classe ColoredSubTotal)
								# De plus, la structure est calqué sur celle de ColoredID et de ColoredSubTotal, ce qui en simplifie la compréhension


							default:
								break;
						}
					}
				}













				
				unset($Cellfusion); # Ne pose pas de pb si $CellFusion n'existe pas, pas besoin de test isset par conséquent
				# On affiche la cellule avant d'éventuellement refaire un tour de boucle For
				echo $TDFormat;
					self::EchoData(utf8_encode($data), $options);
				echo "</td>";
			}
			# On fini la ligne avant un tour de while
		    echo "</tr>";
		}
		# On clos la table et son conteneur
		echo "</table>
		</div>";
		# On abuse de la souplesse du HTML en ne mettant pas de </body>, </html> ici, cela évite un test IF (que l'on aurait à cause de l'option NotANewPage)
	}
}

?>