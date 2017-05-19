<?php


class StringAnalyse
{
	
	function __construct(){}




	# Vérifie si la string $haystack commence par la string $needle
	# Retourne un boolean
	public static function startsWith($haystack, $needle)
	{
	     $length = strlen($needle);
	     return (substr($haystack, 0, $length) === $needle);
	}





	# Vérifie si la string $haystack fini par la string $needle
	# Retourne un boolean
	public static function endsWith($haystack, $needle)
	{
	    $length = strlen($needle);
	    if ($length == 0) {
	        return true;
	    }

	    return (substr($haystack, -$length) === $needle);
	}

}
































class StringManipulation
{
	
	function __construct(){}

	# Retire les 0 en début et fin de chaine 
	public static function SimplifyNumber($Number)
	{
		$Number = (string) self::tofloat($Number);
		return $Number;
	}













	#Convrti une string en float, quelque soit son format initial et supprime tout les caractères qui ne sont ni des chiffre ni le séparateur entre partie entière et partie décimale
	# !!!!!ATTENTION!!!!!! Il est très préférable d'envoyer des chaines contenant effectivement un nombre décimal.
	# !!!!!ATTENTION!!!!!! Si vous envoyez une chaine contenant un ENTIER au format allemand (ex : 1.326) ou américain (ex : 1,326), cela sera reconnu comme "un virgule trois cents vingt-six" alors qu'il s'agit de "mille trois cents vingt-six".
	# !!!!!ATTENTION!!!!!! Cependant la fonction ne fais pas ce genre d'erreur lorsqu'il y a plusieurs séparateurs de milliers (ex : 1.326.485 sera reconnu comme "un million et des bananes")
	public static function tofloat($num)
	{
	    $dotPos = strrpos($num, '.');
	    $NbDot = mb_substr_count($num, ".");
	    $commaPos = strrpos($num, ',');
	    $NbComma = mb_substr_count($num, ",");

	    if (isset($dotPos) && $dotPos !== FALSE && $NbDot == 1 && isset($commaPos) && $commaPos !== FALSE && $NbComma == 1)
	    {
	    	if ($dotPos > $commaPos)
	    	{
	    		$sep = $dotPos;
	    	}
	    	else
	    	{
	    		$sep = $commaPos;
	    	}
	    }
	    elseif (isset($dotPos) && $NbDot == 1)
	    {
	    	$sep = $dotPos;
	    }
	    elseif (isset($commaPos) && $NbDot == 1)
	    {
	    	$sep = $commaPos;
	    }
	    else
	    {
	    	$sep = FALSE;
	    }

	    if ($sep === FALSE) {
	        return floatval(preg_replace("/[^0-9]/", "", $num));
	    } 

	    if ($sep == 0) {
	    	$num = "0".$num;
	    }
	    return floatval(
	        preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
	        preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
    	);
	}	
	









	# prend tout le texte APRES la première itération d'un délimiteur donné
	#retourne FALSE en cas d'échec

	public static function SelectAfter($String, $Delimitor)
	{    
		$EndOfString = substr($String, strpos($String, $Delimitor) + 1); 
		return $EndOfString;
	}

}


?>