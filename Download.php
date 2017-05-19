<?php

class Download
{
	function __construct(){}


	public static function Download($file)
	{
        if (file_exists($file)) 
        {
        	ignore_user_abort(true);
	    	header('Content-Description: File Transfer');
		   	header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename="'.basename($file).'"');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    header('Content-Length: '.filesize($file));
		    readfile($file);
		}
	}
}





?>