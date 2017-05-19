<?php

class Delete
{
	
	function __construct(){}

	# Supprime tout les fichiers existant dans la liste (array) fournis
	# Usage de FullPath recommandé
	public static function GroupDelete($DeleteList)
	{
		foreach ($DeleteList as $File) {
			if (file_exists($File)) {
				unlink($File);
			}
		}
	}
}

?>