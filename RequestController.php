<?php

require '/QueryPrompter.php';



#On initie une connexion à la base de donnée MS SQL SERVER via ODBC
$server = 'ORDI-356';
$user = 'sa';
$password = 'sa';
$database = "Coptis";  ##Valeur par défaut pour avoir une faible chance de réussite en cas d'oublis
if (isset($_POST['BDD'])) {
	$database = $_POST['BDD'];
}

$conn = odbc_connect("DRIVER={SQL Server};SERVER=$server;Database=$database;", $user, $password);








###################################################################################################################################################################################################################







 $SQLCode = ''; ### Tout ce qui fait  référence à $SQLCode par la suite concatène, il est possible d'insérer un Début de requête commun ici
 $RequestType = 'Unknown'; ## Initialisation de la variable $RequestType avec une valeur par défaut

 ##### SELECT * sur une table, simple et efficace#####

if (isset($_POST['SELECTALL']))
{
 
    $SQLCode .= "SELECT * FROM ".$_POST['Table1'];
    $RequestType = 'Select All';
    $text = $database." : ".$RequestType." : ".$_POST['Table1'];
 
} 








###### Liste des tables dans lesqueles apparait un champ ######

elseif (isset($_POST['FieldAppearence']))
{
 
 	$SQLCode .= "SELECT TABLE_NAME, Column_name, DATA_TYPE
				FROM [INFORMATION_SCHEMA].COLUMNS
				WHERE Column_name LIKE '%".$_POST['Champ1']."%' 
				ORDER BY TABLE_NAME";

	$RequestType = 'Liste des tables où apparait un champ de type ';	
    $text = $database." : ".$RequestType." : ".$_POST['Champ1'];
} 









###### Liste différentes value existantes pour un champ et compte le nombre d'itération de chacune  ######

elseif (isset($_POST['CountDifferentValues'])) 
{
 	$SQLCode .= "SELECT ".$_POST['Champ1'].", COUNT(".$_POST['Champ1'].") AS 'COUNT'
			    FROM [Coptis].[dbo].".$_POST['Table1']."
			    GROUP BY ".$_POST['Champ1']."
			    ORDER BY COUNT(".$_POST['Champ1'].") DESC";

	$RequestType = 'Liste et comptage des valeurs que prends';	
    $text = $database." : ".$RequestType." : ".$_POST['Table1'].".[".$_POST['Champ1']."]";
}











########   SELECT - WHERE SIMPLE ###########
elseif (isset($_POST['SELECTWhereSimple']) AND isset($_POST['WhereOperator'])) 
{
	$RequestType = 'Sélection de la (des) ligne(s) où';	

 	$SQLCode .= "SELECT *
			 	FROM ".$_POST['Table1']."
			  	WHERE ".$_POST['Champ1'];

	if ($_POST['WhereOperator'] == "Equal") 
	{
		$SQLCode .= " = '".$_POST['Value1']."'";
		$text = $database." : ".$RequestType." : ".$_POST['Table1'].".[".$_POST['Champ1']."] = ".$_POST['Value1'];
	}

	elseif ($_POST['WhereOperator'] == "LIKE")
	{
		$SQLCode .= " LIKE '%".$_POST['Value1']."%'";
		$text = $database." : ".$RequestType." : ".$_POST['Table1'].".[".$_POST['Champ1']."] IS LIKE ".$_POST['Value1'];
	}

	elseif ($_POST['WhereOperator'] == "NotEqual")
	{
		$SQLCode .= " <> '".$_POST['Value1']."'";
		$text = $database." : ".$RequestType." : ".$_POST['Table1'].".[".$_POST['Champ1']."] <> ".$_POST['Value1'];
	}

	elseif($_POST['WhereOperator'] == "ISNULL")
	{
		$SQLCode .= " IS NULL";
		$text = $database." : ".$RequestType." : ".$_POST['Table1'].".[".$_POST['Champ1']."] IS NULL";
	}

	else
	{
		exit;
	}
} 









#### affiche et compare 2 champs, nécessite un champ de type ID afin de pouvoir consulter les lignes pertinentes en entier par la suite####

elseif (isset($_POST['CompareTwoFields'])) {
	$RequestType = 'Comparaison de deux champs';
	$SQLCode .= "SELECT".$_POST['Champ3']." AS 'PrimaryKey/ID', ".$_POST['Champ1'].", ".$_POST['Champ2'].", (CASE WHEN ".$_POST['Champ1']." = ".$_POST['Champ2']." THEN 'o' ELSE 'n' END) AS 'EQUALS ?'
	            FROM ".$_POST['Table1']."
	            ORDER BY ".$_POST['Champ3'];

	$text = $database." : ".$RequestType." : ".$_POST['Table1'].".".[$_POST['Champ1']]." ET ".$_POST['Table1'].".".[$_POST['Champ2']];
}






### Exécute la requête rentrée dans la textarea

elseif (isset($_POST['FullManualQuery']))
{
	$SQLCode .= $_POST['ManualQueryText'];
	$RequestType = 'Manual Query';
	$text = $database." : ".$RequestType." : ".$_POST['ManualQueryText'];
}




### Affiche l'ensemble des liens entre deux tables

elseif (isset($_POST['LinksOfTable'])) {
	$RequestType = 'Liste des liens d\'une table vers d\'autres';
	$text = $database." : ".$RequestType." : ".$_POST['Table1'];

	$SQLCode .= "SELECT fk.name AS 'Key',
					    tr.name AS 'Table Source',
					    cr.name AS 'Nom Champ d''origine',
					    cr.column_id AS 'ID Champ d''origine',
					    tp.name AS 'Host Table',
					    cp.name AS 'Key Name inside Host Table',
					    cp.column_id AS 'ID Key inside Host Table'
				FROM  
				    sys.foreign_keys fk
					INNER JOIN 
					    sys.tables tp 
					        ON fk.parent_object_id = tp.object_id
					INNER JOIN 
					    sys.tables tr 
					        ON fk.referenced_object_id = tr.object_id
					INNER JOIN 
					    sys.foreign_key_columns fkc 
					        ON fkc.constraint_object_id = fk.object_id
					INNER JOIN 
					    sys.columns cp 
					        ON fkc.parent_column_id = cp.column_id 
					           AND fkc.parent_object_id = cp.object_id
					INNER JOIN 
					    sys.columns cr 
					        ON fkc.referenced_column_id = cr.column_id 
					           AND fkc.referenced_object_id = cr.object_id

				WHERE tp.name = '".$_POST['Table1']."' OR tr.name = '".$_POST['Table1']."'
				ORDER BY CASE WHEN tr.name = '".$_POST['Table1']."' THEN 1 ELSE 2 END, cr.column_id,  tr.name, cp.column_id, tp.name";
}












else
{ 
 	exit; 
}




$options = [
    'NoCellFusion' => NULL, 
    'NoImagePrompt' => NULL, 
    'NoColumnHead' => NULL,
    'NoHTMLInsertion' => NULL,
    'ColumnOrder' => array(),
];


if(!empty($_POST['Options']))
{
	foreach($_POST['Options'] as $val)
	{
		switch ($val) {
			case 'NoCellFusion':
				$options['NoCellFusion'] = 1;
				break;
			case 'NoImagePrompt':
				$options['NoImagePrompt'] = 1;
				break;
			case 'NoColumnHead':
				$options['NoColumnHead'] = 1;
				break;
			case 'NoHTMLInsertion':
				$options['NoHTMLInsertion'] = 1;
				break;
			case 'ColumnOrderCheckBox':
				foreach (explode(',', str_replace(' ', '',$_POST['ColumnOrder'])) as $column)
				{
					$options['ColumnOrder'][] = (int)$column;
				}
				
			default:
				break;
		}
	}
}



$result=odbc_exec($conn, $SQLCode);
QueryPrompter::Prompt($result, $text, $options);

?>