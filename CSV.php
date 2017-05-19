<?php



class CSVBuild
{
	function __construct(){}


	public static function ODBCResultToCSV($Result, $CSVPath)
	{


		# $Content va contenir l'ensemble des données à push dans le fichier .csv
		$Content = '';

		# On met en première ligne, le nom de chaque champs
		for ($i=1; $i < odbc_num_fields($Result); $i++)
		{ 
			$Content .= odbc_field_name($Result, $i).";";
		}
		$Content .= odbc_field_name($Result,odbc_num_fields($Result))."\r\n";


		

		# On remplis ensuite l'array:
		# Tant qu'il y'a du contenu
		while(odbc_fetch_row($Result))
		{
			#Pour chaque champ du premier au dernier...
			for($i=1;$i<=odbc_num_fields($Result);$i++){

			#On l'enregistre :

				# Si ce n'est pas le dernier, on lui ajoute ";"
				if ($i<odbc_num_fields($Result)) {
					$Content .= odbc_result($Result,$i).";";
				}
				# Si c'est le dernier, on ajoute un retour à la ligne
				else
				{
					$Content .= odbc_result($Result,$i)."\r\n";
				}
		    }
		}

		# On converti en Latin-1 (ISO-8859-1) car Excel ne comprend pas l'UTF-8
		$Content = utf8_decode($Content);
		# De cet array on créé enfin le .csv
		file_put_contents($CSVPath, $Content);
	}
}









?>