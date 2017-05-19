<?php


class ArrayAnalyse 
{	
	function __construct(){}

	### Va effectuer un is_[$Type] sur l'ensemble du tableau (de façon non récursive) et retourner TRUE seulement si l'ensemble des éléments ont validé le test is_

	public static function IsFullOf($Array, $Type)
	{
		if (is_callable("is_".$Type)) # On vérifie que le "type" passé en paramètre est compatible avec la fonction
		{
			$a = "is_".$Type;
		}
		else
		{
			return FALSE;
		}


		$all_ok = TRUE; # On part sur des bases bienveillantes
		foreach ($Array as $key) 
		{ 
		    if (!$a($key))
		    {
		        $all_ok = FALSE; # Si un seul élément échoue, la tableau ne sera pas full ok
		        break; # Il ne servira donc à rien de continuer à checker les autres derrière
			}
		}
		return $all_ok;
	}
}

?>