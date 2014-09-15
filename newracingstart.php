<?php
class horse {
	function __construct($id, $name, $speed, $endurance, $surf_pref, $dist_pref, $trait) {
		$this->id = $id;
		$this->name = $name;
		$this->speed = $speed;
		$this->endurance = $endurance;
		$this->surf_pref = $surf_pref;
		$this->dist_pref = $dist_pref;
		$this->trait = $trait;
	}

	function print_details() {
		print "Name: {$this->name}.\n";
		print "Speed: {$this->speed}.\n";
		print "Surface Preference: ".self::$surf_pref_name[$this->surf_pref]."\n";
		print "Distance Preference: ".self::$dist_pref_name[$this->dist_pref]."\n";
	}

	function surf_pref_name() {
		return self::$surf_pref_name[$this->surf_pref];
	}

	function dist_pref_name() {
		return self::$dist_pref_name[$this->dist_pref];
	}

	function get_trait() {
		return $this->trait;
	}

	function get_id() {
		return $this->id;
	}

	function get_speed() {
		return $this->speed;
	}

	function get_endurance() {
		return $this->endurance;
	}

	private $id;
	private $name;
	private $speed;
	private $endurance;
	private $surf_pref;
	private $dist_pref;
	private $trait;

	private static $surf_pref_name = array (
		1 => "Turf",
		2 => "Dirt"
	);
	private static $dist_pref_name = array (
		1 => "Sprint (5-8F)",
		2 => "Middle (7-10F)",
		3 => "Classic (9-12F)"
	);
	private static $traits = array (
		1 => "Front Runner",
		2 => "Pace Presser",
		3 => "Closer"
	);
}

function compare_positions($a, $b) {
	return $a->get_position() < $b->get_position();
}

class snapshot {
	function __construct($ts) {
		$this->ts = $ts;
		$this->details = array();
	}

	function add_horse($new_details) {
		$this->details[] = $new_details;
	}

	function sort_details() {
		usort($this->details, "compare_positions");
	}

	function print_snapshot() {
		//print "\nTimestamp: {$this->ts}\n";
		$this->sort_details();
		for ($i = 0; $i < count($this->details); $i++) {
			$horseid = $this->details[$i]->get_id();
			$placement = $this->get_placement($horseid);
			$suffix = $this->number_suffix($placement);
			/* print "ID: {$horseid}\n";
			print "Position: {$this->details[$i]->get_position()}\n";
			print "Endurance: {$this->details[$i]->get_endurance()}\n";
			print "Speed: {$this->details[$i]->get_speed()}\n";
			print "Placement: $placement$suffix Place\n";
			*/
			printf("TS: %-3d    %2d%s Place ID: %d   Position: %-5d   Endurance: %-5.2f    Speed: %-3.2f\n", 
				$this->ts,
				$placement,
				$suffix,
				$horseid,
				$this->details[$i]->get_position(),
				$this->details[$i]->get_endurance(),
				$this->details[$i]->get_speed()
				
			);

		}
		print "\n";
	}

	function get_animation_array() {
		$this->sort_details();

		$array = array();
		for ($i = 0; $i < count($this->details); $i++) {
			$foo = new stdClass();
			$foo->id = $this->details[$i]->get_id();
			$foo->pos = $this->details[$i]->get_position();
			$foo->pp = $this->details[$i]->get_post_position();
			$array[] = $foo;
		}

		return $array;
	}

	function get_ts() {
		return $this->ts;
	}

	function get_details($horseid) {
		foreach ($this->details as $detail) {
			if ($detail->get_id() == $horseid) {
				return $detail;
			}
		}
	}

	function get_ordered_details() {
		$this->sort_details();
		return $this->details;
	}

	function get_placement($horseid) {
		$i = 1;
		foreach ($this->get_ordered_details() as $detail) {
			if ($detail->get_id() == $horseid) {
				$placement = $i;
				return $placement;
			} else {
				$i++;
			}
		}
		// todo: what if we dont find the horse??
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

	private $ts;
	private $details;
}

class horse_details {
	function __construct($id, $position, $endurance, $speed, $boost, $post_position) {
		$this->id = $id;
		$this->position = $position;
		$this->endurance = $endurance;
		$this->speed = $speed;
		$this->boost = $boost;
		$this->post_position = $post_position;
	}

	function get_id() {
		return $this->id;
	}

	function get_position() {
		return $this->position;
	}

	function get_endurance() {
		return $this->endurance;
	}

	function get_speed() {
		return $this->speed;
	}

	function get_boost() {
		return $this->boost;
	}

	function get_post_position() {
		return $this->post_position;
	}

	private $id;
	private $position;
	private $endurance;
	private $speed;
	private $boost;
	private $post_position;
}

//populate an array of horses
$horses = array(
	new horse(1,"Alex", 60, 50, 1, 1, 1),
	new horse(2, "Bob", 60, 50, 1, 1, 1),
	new horse(3,"Maggie", 60, 50, 1, 1, 1),
	new horse(4, "Sigboom", 60, 50, 1, 1, 2),
	new horse(5,"Peter", 60, 50, 1, 1, 2),
	new horse(6, "Janelle", 60, 50, 1, 1, 2),
	new horse(7,"Ingrid", 60, 50, 1, 1, 1),
	new horse(8, "Nea", 60, 50, 1, 1, 1),
	new horse(9,"Sarah", 60, 50, 1, 1, 1),
	new horse(10, "Christina", 60, 50, 1, 1, 2),
	new horse(11,"Julianna", 60, 50, 1, 1, 2),
	new horse(12, "Morgan", 60, 50, 1, 1, 2),
	new horse(13,"Amanda", 60, 50, 1, 1, 2),
	new horse(14, "Heather", 60, 50, 1, 1, 2)
);

$post_positions = array();
for ($i = 1; $i <= count($horses); $i++) {
	$post_positions[] = $i;
}
shuffle($post_positions);

$i = 0;
$initial_snapshot = new snapshot(0);
foreach($horses as $horse) {
	$initial_snapshot->add_horse(new horse_details($horse->get_id(), 0, $horse->get_endurance(), $horse->get_speed(), 0, $post_positions[$i]));
	$i++;
}

$animation_array = array();

$previous_snapshot = $initial_snapshot;

$done = false;
$ts = 1;
$total_distance = 7920;

while(!$done) {
	$snapshot = new snapshot($ts);
	
	$dt = $snapshot->get_ts() - $previous_snapshot->get_ts();	
	$finished = 0;
	foreach($horses as $horse) {
		// gather some useful info into one place
		$horseid = $horse->get_id();
		$horse_detail = $previous_snapshot->get_details($horseid);
		$old_pos = $horse_detail->get_position();
		$boost = $horse_detail->get_boost();
		$race_over = ($old_pos > $total_distance);
		$post_position = $horse_detail->get_post_position();

		
		$old_end = $horse_detail->get_endurance();
		$sleepy = ($old_end < 0);
		$first_call_boost = ($boost > 0);

		//calculate new position

		// calculates speed
		$race_over_multiplier = ($race_over ? 0.5 : 1);
		$sleepy_multiplier = ($sleepy ? 0.5 : 1);
		$first_call_multiplier = ($first_call_boost ? 1.0 : 0.9);

		$new_speed = $horse->get_speed() * $race_over_multiplier 
									     * $sleepy_multiplier
										 * $first_call_multiplier;

		

		// TODO: adjust current speed

		//calculate distance traveled in current ts
		$this_ts_distance = ($new_speed + rand(-20,20)) * $dt;
		$new_pos = $old_pos + $this_ts_distance;

		$first_call_pos = 1320;

		$new_boost = $boost;
		if ($old_pos < $first_call_pos && $new_pos >= $first_call_pos) {
			print "BOOST POINT: {$horse->get_trait()} ---- {$previous_snapshot->get_placement($horseid)} ";
			if ($horse->get_trait() == 1 && $previous_snapshot->get_placement($horseid) == 1) {
				print "TRAIT 1 BOOST ACTIVATED: {$horse->get_trait()} ---- {$previous_snapshot->get_placement($horseid)} ";
				$new_boost = 1;
			} else if ($horse->get_trait() == 2 && $previous_snapshot->get_placement($horseid) == 2) {
				$new_boost = 1;
				print "TRAIT 2 BOOST ACTIVATED ";
			}

		}

		//calculate new endurance
		
		$new_end = $old_end - 0.005 * $this_ts_distance;

		//create new snapshot
		$snapshot->add_horse(new horse_details($horseid, $new_pos, $new_end, $new_speed, $new_boost, $horse_detail->get_post_position()));

		//check if horse has crossed the finish line in this ts, if so increase finished counter
		if ($new_pos > $total_distance) {
			$finished++;
		}
	}

	if ($finished == count($horses)) {
		$done = true;
	}

	print $snapshot->print_snapshot();
	$animation_array[] = $snapshot->get_animation_array();
	$previous_snapshot = $snapshot;

	$ts++;

	if ($ts > 300) {
		$done = true;
	}
}



print json_encode($animation_array);


//$pos = $old_snapshot->get_pos()+($horse->speed()*$time);

?>