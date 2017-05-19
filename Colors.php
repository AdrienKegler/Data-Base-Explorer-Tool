<?php

require_once "/StringShortcut.php";

### Pour mieux comprendre les histoires de RGB, HSL... voir le site suivant : http://www.la-photo-en-faits.com/2013/05/RVB-CMJN-TSL-conversion-definition.html



class Colors 
{
	
	function __construct(){}


	### Prend une couleur HEXA (sous forme de string) et retourne le code HEXA de la couleur coplémentaire
	# Retourne FAUX si le Code couleur passé en paramètre est invalide
	public static function ComplementaryColor($EColor)
	{

		if (!ctype_xdigit($EColor))
		{
			return FALSE;
		}

		# Décomposition suivant les 3 composantes RGB 
		$redhex  = substr($EColor,0,2);
	    $greenhex = substr($EColor,2,2);
	    $bluehex = substr($EColor,4,2);


	    # Conversion RGB vers HSL (Hue, Saturation , Lightness [Teinte Saturation Luminosité])
	    $var_r = (hexdec($redhex)) / 255;
	    $var_g = (hexdec($greenhex)) / 255;
	    $var_b = (hexdec($bluehex)) / 255;
		$EColorHSL = self::RGBToHSL($var_r, $var_g, $var_b);

		$H = $EColorHSL['H'];
		$S = $EColorHSL['S'];
		$L = $EColorHSL['L'];

	    # Calcul de la couleur complémentaire
	    # Calcul de la Teinte opposée
	    $H2 = $H + 0.5;

        if ($H2 > 1)
        {
           	$H2 -= 1;
        }


        # Dans le cas où la saturation est à 0 , c'est le plus simple
        if ($S == 0)
        {
                $R = $L * 255;
                $G = $L * 255;
                $B = $L * 255;
        }
        else #Sinon
        {	
    		# Suivant l'intancité de luminosité, 
            if ($L < 0.5)
            {	
            		# On applique la formule adéquate
                    $var_2 = $L * (1 + $S);
            }
            else
            {
                    $var_2 = ($L + $S) - ($S * $L);
            }

			$var_1 = 2 * $L - $var_2;

			### On obtiens notre touvell couleur RGB, mais en format 255
			$R = 255 * self::hue_2_rgb($var_1,$var_2,$H2 + (1 / 3));
			$G = 255 * self::hue_2_rgb($var_1,$var_2,$H2);
			$B = 255 * self::hue_2_rgb($var_1,$var_2,$H2 - (1 / 3));
		}

        return self::RGB255ToRGBHEXA($R, $G, $B);
	}



	# Prend en entrée les 3 composantes R, G et B et retourne un array contenant les composantes H, S, et L
	public static function RGBToHSL($var_r, $var_g, $var_b)
	{
		$var_min = min($var_r,$var_g,$var_b);
	    $var_max = max($var_r,$var_g,$var_b);
	    $del_max = $var_max - $var_min;

	    $L = ($var_max + $var_min) / 2;

	    if ($del_max == 0)
	    {
	        $H = 0;
	        $S = 0;
	    }
	    else
	    {
	        if ($L < 0.5)
	        {
	                $S = $del_max / ($var_max + $var_min);
	        }
	        else
	        {
	                $S = $del_max / (2 - $var_max - $var_min);
	        };

	        $del_r = ((($var_max - $var_r) / 6) + ($del_max / 2)) / $del_max;
	        $del_g = ((($var_max - $var_g) / 6) + ($del_max / 2)) / $del_max;
	        $del_b = ((($var_max - $var_b) / 6) + ($del_max / 2)) / $del_max;

	        if ($var_r == $var_max)
	        {
	                $H = $del_b - $del_g;
	        }
	        elseif ($var_g == $var_max)
	        {
	                $H = (1 / 3) + $del_r - $del_b;
	        }
	        elseif ($var_b == $var_max)
	        {
	                $H = (2 / 3) + $del_g - $del_r;
	        };

	        if ($H < 0)
	        {
	                $H += 1;
	        };

	        if ($H > 1)
	        {
	                $H -= 1;
	        };
	        return array('H' => $H, 'S' => $S, 'L' => $L);
	    }
	}

	### .... ça marche ####
	public static function hue_2_rgb($v1,$v2,$vh)
	{
        if ($vh < 0)
        {
        	$vh += 1;
        };

        if ($vh > 1)
        {
        	$vh -= 1;
        };

        if ((6 * $vh) < 1)
        {
    		return ($v1 + ($v2 - $v1) * 6 * $vh);
        };

        if ((2 * $vh) < 1)
        {
        	return ($v2);
        };

        if ((3 * $vh) < 2)
        {
        	return ($v1 + ($v2 - $v1) * ((2 / 3 - $vh) * 6));
        };

        return ($v1);
	}




	### Retourne un booléen sur la luminosité d'une couleur en prenant en compte la sensibilité de l'oeil humain suivant les couleurs
	public static function colourIsLight($R, $G, $B)
	{
		$a = 1 - (0.299 * $R + 0.587 * $G + 0.114 * $B) / 255;
		return ($a < 0.5);
	} 


	#Retourne un array contenant un code couleur 255 aléatoire
	static public function randomRgb255()
	{
		$R = rand(0, 255);
		$G = rand(0, 255);
		$B = rand(0, 255);
		return array('R' => $R, 'G' => $G, 'B' => $B);
	}   


	# Retourne une string étant un code couleur Hexadécimal
	static public function randomRgbHexa()
	{
		$color = self::randomRgb255();
		return self::RGB255ToRGBHEXA($color['R'], $color['G'], $color['B']);
	}   



	# Transforme un code RGB base 255 en code RGB base Heaxadécimale
	public static function RGB255ToRGBHEXA($R, $G, $B)
	{
	  	$rhex = sprintf("%02X",round($R));
	    $ghex = sprintf("%02X",round($G));
	    $bhex = sprintf("%02X",round($B));

	    $rgbhex = "#".$rhex.$ghex.$bhex;
	    return $rgbhex;
	}


	# Retourne Noir ou blanc en Hexa suivant si le background-color est lumineux ou sombre
	public static function FontColorDependingBackgroundColor($EColor)
	{
		if (!is_array($EColor)) {
			$EColor = self::RGBHexaToRGB255($EColor);
		}
		return self::colourIsLight($EColor['R'], $EColor['G'], $EColor['B']) ? "#000000" : "#FFFFFF";
	}


	# Retourne un array RGB base 255 à partir d'un code RGB base Heaxadécimale
	public static function RGBHexaToRGB255($EColor)
	{
		$EColor = self::cleanHexa($EColor);
		# Décomposition suivant les 3 composantes RGB 
		$redhex  = substr($EColor,0,2);
	    $greenhex = substr($EColor,2,2);
	    $bluehex = substr($EColor,4,2);


	    # Conversion système hexa vers système Décimal
	    $R = hexdec($redhex);
	    $G = hexdec($greenhex);
	    $B = hexdec($bluehex);

	    return array('R' => $R, 'G' => $G, 'B' => $B);
	}


	public static function cleanHexa($EColor)
	{
		switch (strlen($EColor))
		{
			case 6:
				break;

			case 7:
				$EColor = substr($EColor, 1);
				break;
			
			default:
				return FALSE;
		}
		return $EColor;
	}
}

?>