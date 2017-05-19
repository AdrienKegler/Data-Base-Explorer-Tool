<?php

require '/StringShortcut.php';
require '/DateShortcut.php';




class FileResearch
{
	function __construct(){}

	# $ResearchFolder est le full path du dossier où effectuer les recherches
	# $FileBaseNames est un array contenant les différentes bases de nom de fichier à trouver
	# $DateFormat est le format DateTime sous lequel se trouve la date concaténée dans le nom du fichier. Il doit respecter la forme défini ici -> https://secure.php.net/manual/fr/datetime.createfromformat.php
	# $ExtentionFocused est un array listant les types d'extensions à prendre en compte (txt, php, csv, ...)
	public static function DateOfMostRecentFileByName($ResearchFolder, $FileBaseNames, $DateFormat, $ExtentionFocused) 
	{
		# Initialisation de la variable LastUpdate au 0 du temps UNIX
		$lastUpdate = DateTime::createFromFormat('d/m/Y', '01/01/1970');

		# Si le dossier fourni est bien un dossier alors...
		if (is_dir($ResearchFolder))
		{	
			# Pour chacun des fichiers dans ce dossier,
			foreach (scandir($ResearchFolder) as $FileName)
			{
				# Si son extention correspond à l'une de celle que l'on cherche alors...
				if (in_array(pathinfo($FileName, PATHINFO_EXTENSION), $ExtentionFocused))
				{
					#Pour chaque nom-type donné,
					foreach ($FileBaseNames as $FileBaseName)
					{
						#On test la correspondance
						if (StringAnalyse::startsWith(pathinfo($FileName, PATHINFO_FILENAME), $FileBaseName)) 
						{
							# Si le test est un succès, on récupère la date sous forme de string 
							$prelevement = StringManipulation::SelectAfter(pathinfo($FileName, PATHINFO_FILENAME), '_');
							# ...que l'on converti à la volée en objet natif DateTime
							# ... S'il on a effectivement prélevé qqch
							if (strlen($prelevement) > 0)
							{
								$date = DateTime::createFromFormat($DateFormat, $prelevement);
								$date = $date->format('d/m/Y');
								if (DateCompare::IsDateGreaterThan($date, $lastUpdate) AND $date != date('d/m/Y'))
								{
									$lastUpdate = $date;
								}
							}

							
						}
					}
				}
			}
		}
		
		return $lastUpdate->format('d/m/Y');
	}



}


?>