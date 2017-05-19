<?php


class DateCompare
{
	function __construct(){}

	public static function IsDateGreaterThan($date1, $date2)
	{
		if ($date1 > $date2) {
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
}

?>