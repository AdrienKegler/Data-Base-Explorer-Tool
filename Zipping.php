<?php

class Zipping
{
	
	function __construct(){}

	# Créé un Zip et y ajoute au même niveau les fichiers passés en paramètre
	public static function GroupZip($ZipName, $FileList) #ZipName : Chemin du dossier zippé  ;  FileList : Array des chemins des fichiers à ajouter
	{
		$zip = new ZipArchive;  #On instancie un objet de type ZipArchive
		$res = $zip->open($ZipName, ZipArchive::CREATE);  #On attribue un dossier Zip à notre Objet zip, on ajoute en Flag, que si le dossier n'existe pas, il doit être créé
		if ($res === TRUE) {              #Le Triple égal compare non seulement la valeur mais aussi le type
		    foreach ($FileList as $FileName) { #Le premier élément correspond à la variable de type contenant (liste, table, objet...), le second est la façon dont sera nommée les différentes unitées de contenu à chaque itération
		    	if (file_exists($FileName)) {
		    		$zip->addFile($FileName, basename($FileName)); #Ajoute le fichier désigné à l'archive Zip
		    	}
		    	else
		    	{
		    		echo "Le fichier $FileName n'existe pas";
		    	}
			}
		    $zip->close();  #Comme en C, on ferme un fichier/dossier lorsqu'on a plus nécessité d'y accéder
		}
	}




	# Créé un Zip et y ajoute les fichiers passés en paramètre en respectant le chemin fourni
	public static function GroupZipStraightPath($ZipName, $FileList) #ZipName : Chemin du dossier zippé  ;  FileList : Array des chemins des fichiers à ajouter
	{
		$zip = new ZipArchive;  #On instancie un objet de type ZipArchive
		$res = $zip->open($ZipName, ZipArchive::CREATE);  #On attribue un dossier Zip à notre Objet zip, on ajoute en Flag, que si le dossier n'existe pas, il doit être créé
		if ($res === TRUE) {              #Le Triple égal compare non seulement la valeur mais aussi le type
		    foreach ($FileList as $FileName) { #Le premier élément correspond à la variable de type contenant (liste, table, objet...), le second est la façon dont sera nommée les différentes unitées de contenu à chaque itération
		    	if (file_exists($FileName)) {
		    		$zip->addFile($FileName); #Ajoute le fichier désigné à l'archive Zip
		    	}
			}
		    $zip->close();  #Comme en C, on ferme un fichier/dossier lorsqu'on a plus nécessité d'y accéder
		}
	}
}



?>