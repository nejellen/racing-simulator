<?php
class horse {
	function __construct($id, $name, $esi, $surf_pref, $dist_pref) {
		$this->id = $id;
		$this->name = $name;
		$this->esi = $esi;
		$this->surf_pref = $surf_pref;
		$this->dist_pref = $dist_pref;
	}

	function add_xp() {
		$this->xp++;
		if ($this->xp > 10) {
			$this->esi++;
		}
	}

	function print_details() {
		print "Name: {$this->name}.\n";
		print "ESI: {$this->esi}.\n";
		print "Surface Preference: ".self::$surf_pref_name[$this->surf_pref]."\n";
		print "Distance Preference: ".self::$dist_pref_name[$this->dist_pref]."\n";
	}

	function get_esi() {
		return $this->esi;
	}

	function get_name() {
		return $this->name;
	}

	/*function set_surf_pref($new_pref) {
		
		 * List of Surface Preferences
		 * 1 - Turf
		 * 2 - Dirt
		 

		if (!array_key_exists($new_pref,self::$surf_pref_name)) {
			// print error
		}

		$this->surf_pref = $new_pref;
	}*/

	/*function set_dist_pref() {
		
		 * List of Distance Preferences
		 * 1 - Sprint (5-8F)
		 * 2 - Middle (7-10F)
		 * 3 - Classic (9-12F)
		 

		return $this->dist_pref;
	}*/

	function surf_pref_name() {
		return self::$surf_pref_name[$this->surf_pref];
	}

	function dist_pref_name() {
		return self::$dist_pref_name[$this->dist_pref];
	}

	private $id;
	private $name;
	private $esi;
	private $xp;
	private $surf_pref;
	private $dist_pref;

	private static $surf_pref_name = array (
		1 => "Turf",
		2 => "Dirt"
	);
	private static $dist_pref_name = array (
		1 => "Sprint (5-8F)",
		2 => "Middle (7-10F)",
		3 => "Classic (9-12F)"
	);
}

//populate an array of horses
$horses = array(
	new horse(1, "Alex", 75, 1, 1),
	new horse(2, "Bob", 73, 2, 2),
	new horse(3, "Charlie", 71, 2, 3)
);

class snapshot {
	function __construct() {

	}

	public $id;
	public $position;
	public $
}

function compare_final_score($a, $b) {
	return $a['final_score'] < $b['final_score'];
}

//if ends in 1 and isn't 11 - st
//ends in 2 and isn't 12 - nd
//ends in 3 and isn't 13 - rd
//rest is th
function number_suffix($i) {
	if ($i%10 == 1 && $i%100 != 11) {
		return "st";
	} else if ($i%10 == 2 && $i%100 != 12) {
		return "nd";
	} else if ($i%10 == 3 && $i%100 != 13) {
		return "rd";
	} else {
		return "th";
	}
}

/// short title
/**
 * long desc
 *
 * @param $horse An array of horses
 * @return 
 */
function calculate_results($horses) {
	$entrants = array();
	foreach ($horses as $horse) {		
		$luck = mt_rand(1,10);

		$final_score = $horse->get_esi() + $luck;

		$entrants[] = array("horse" => $horse, "luck" => $luck, "final_score" => $final_score);
	}

	usort($entrants, "compare_final_score");

	$i = 1;
	foreach ($entrants as $entrant) {
		$suffix = number_suffix($i);
		print "$i$suffix Place\n";
		print $entrant['horse']->print_details();
		print "Luck Score: {$entrant['luck']}\n";
		print "Final Score: {$entrant['final_score']}\n\n";
		$i++;
	}
}

$results = calculate_results($horses);

//for ($i = 0; $i < 220; $i++) {
	//print "$i" . number_suffix($i) . "\n";
















/*$final_esi = array (
	"a" => 1,
	"c" => 5,
	"b" => 3
);

foreach ($final_esi as $key => $score) {
	print "$key => $score.\n";
}

ksort ($final_esi);
$final_esi = array_reverse($final_esi);

foreach ($final_esi as $key => $score) {
	print "$key => $score.\n";
}*/

?>