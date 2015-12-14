<?php

ini_set('memory_limit','512M');

require_once('horse.php');
require_once('track.php');
require_once('DecisionTree.php');
require_once('GameState.php');

function build_compare_positions($track) {
    return function ($a, $b) use ($track) {
        if ($a->get_final_placement() == $b->get_final_placement()) {
            if ($a->get_lap_counter() == $b->get_lap_counter()) {
                return $track->lane_percent_completed($a->get_lane(), $a->get_position_x(), $a->get_position_y())
                <
                $track->lane_percent_completed($b->get_lane(), $b->get_position_x(), $b->get_position_y());
            } else {
                return $a->get_lap_counter() < $b->get_lap_counter();
            }
        } else {
            if ($a->get_final_placement() == 0) {
                return true;
            } else if ($b->get_final_placement() == 0) {
                return false;
            } else {
                return $a->get_final_placement() > $b->get_final_placement();
            }
        }
    };
}

class snapshot {
	function __construct($ts, $track) {
		$this->ts = $ts;
		$this->details = array();
		$this->track = $track;
	}

	function add_horse($new_details) {
		$this->details[] = $new_details;
	}

	function sort_details() {
		usort($this->details, build_compare_positions($this->track));
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
				$this->details[$i]->get_position_x(),
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
			$foo->pos_x = $this->details[$i]->get_position_x();
			$foo->pos_y = $this->details[$i]->get_position_y();
			$foo->dir = $this->details[$i]->get_direction();
			$foo->pp = $this->details[$i]->get_post_position();
			$foo->tx = $this->details[$i]->get_tar_x();
			$foo->ty = $this->details[$i]->get_tar_y();
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

	function get_horse_ahead($lane, $x, $y) {
		$obstacle_horses = [];
		foreach ($this->details as $horse) {
			if ($horse->get_lane() == $lane) {
				$obstacle_horse[] = $horse;
			}
		}
		// sort in order from lowest to highest
		usort($obstacle_horses, build_compare_positions($this->track));
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
	private $track;
}

class horse_details {
	function __construct($id, $position_x, $position_y, $endurance, $speed, $direction, $boost, $post_position, $section, $lane, $lap_counter, $target, $final_placement) {
		$this->id = $id;
		$this->position_x = $position_x;
		$this->position_y = $position_y;
		$this->endurance = $endurance;
		$this->speed = $speed;
		$this->direction = $direction;
		$this->boost = $boost;
		$this->post_position = $post_position;
		$this->section = $section;
		$this->lane = $lane;
		$this->lap_counter = $lap_counter;
		$this->target = $target;
        $this->final_placement = $final_placement;
	}

	function get_id() {
		return $this->id;
	}

	function get_position_x() {
		return $this->position_x;
	}

	function get_position_y() {
		return $this->position_y;
	}

	function get_endurance() {
		return $this->endurance;
	}

	function get_speed() {
		return $this->speed;
	}

	function get_direction() {
		return $this->direction;
	}

	function get_boost() {
		return $this->boost;
	}

	function get_post_position() {
		return $this->post_position;
	}

	function get_section() {
		return $this->section;
	}

	function get_lane() {
		return $this->lane;
	}

	function get_lap_counter() {
		return $this->lap_counter;
	}
    
    function get_tar_x() {
		return $this->target[0];
	}
    
    function get_tar_y() {
		return $this->target[1];
	}

    function get_final_placement() {
		return $this->final_placement;
	}

	private $id;
	private $position_x;
	private $position_y;
	private $endurance;
	private $speed;
	private $direction;
	private $boost;
	private $post_position;
	private $section;
	private $lane;
	private $lap_counter;
    private $target;
    private $final_placement;
}

//populate an array of horses
$horses = array(
	new horse(1,"Alex", 61, 50, 1, 1, 1, 1),
	new horse(2, "Bob", 61, 50, 1, 1, 1, 2),
	new horse(3,"Maggie", 61, 50, 1, 1, 1, 3),
	new horse(4, "Sigboom", 61, 50, 1, 1, 2, 4),
	new horse(5,"Peter", 60, 50, 1, 1, 2, 5),
	new horse(6, "Janelle", 60, 50, 1, 1, 2, 6),
	new horse(7,"Ingrid", 60, 50, 1, 1, 1, 7),
	new horse(8, "Nea", 60, 50, 1, 1, 1, 1),
	new horse(9,"Sarah", 60, 50, 1, 1, 1, 2),
	new horse(10, "Christina", 60, 50, 1, 1, 2, 3),
	new horse(11,"Julianna", 59, 50, 1, 1, 2, 4),
	new horse(12, "Morgan", 59, 50, 1, 1, 2, 5),
	new horse(13,"Amanda", 59, 50, 1, 1, 2, 6),
	new horse(14, "Heather", 59, 50, 1, 1, 2, 7)
 
);

function initializeLaneScoreTree() {

	$num_lengths_ahead_node = new DecisionTreeNode("num_lengths_ahead", [
		new Edge("0", 0, null),
		new Edge ("1", 1, null),
		new Edge ("2", 2, null),
		new Edge ("3", 3, null),
		new Edge ("4", 4, null),
		new Edge ("5+", 5, null)
	]);

	$right_lane_num_turns_left_node = new DecisionTreeNode("num_turns_left", [
		new Edge("0", -0, $num_lengths_ahead_node),
		new Edge ("1", -1, $num_lengths_ahead_node),
		new Edge("2", -2, $num_lengths_ahead_node)
	]);

	$left_lane_num_turns_left_node = new DecisionTreeNode("num_turns_left", [
		new Edge("0", 0, $num_lengths_ahead_node),
		new Edge ("1", 1, $num_lengths_ahead_node),
		new Edge("2", 2, $num_lengths_ahead_node)
	]);

	$where_is_lane_node = new DecisionTreeNode("where_is_lane", [
		new Edge("left", 0, $left_lane_num_turns_left_node),
		new Edge("right", 0, $right_lane_num_turns_left_node),
		new Edge ("same", 0, $num_lengths_ahead_node)
	]);

	$space_in_lane_node = new DecisionTreeNode("space_in_this_lane", [
		new Edge("yes", 0, $where_is_lane_node),
		new Edge ("no", null, null)
	]);

	return new DecisionTree($space_in_lane_node);
}

// Set up the initial snapshot


$track = new Track(3000, 2000, 80, 840, 2160, 840, 840, 420.169, 20);
$lane_score_tree = initializeLaneScoreTree();
$game_state = new GameState($horses, $track);
$animation_array = array();

$done = false;
$ts = 1;
$ts_scale = 0.01;
$total_distance = 840 + 1320;

$final_placement = 0;

while(!$done) {
	$snapshot = new snapshot($ts, $track);
	
	$dt = ($snapshot->get_ts() - $game_state->get_ts()) * $ts_scale;
	$num_horses_finished = 0;
	foreach($horses as $horse) {
		// gather some useful info into one place
		$horseid = $horse->get_id();
		/** @var horse_details $horse_detail */
		$horse_detail = $game_state->get_details($horseid);
		$old_pos_x = $horse_detail->get_position_x();
		$old_pos_y = $horse_detail->get_position_y();
		$boost = $horse_detail->get_boost();

		if ($horse_detail->get_lap_counter() > 0 && $horse_detail->get_section() > 0) {
			$this_horse_finished = true;
		} else {
			$this_horse_finished = false;
		}

		$post_position = $horse_detail->get_post_position();


		$old_end = $horse_detail->get_endurance();
		$sleepy = ($old_end < 0);
		$first_call_boost = ($boost > 0);

		//calculate new position

		// calculates speed
		$race_over_multiplier = ($this_horse_finished ? 0.5 : 1);
		$sleepy_multiplier = ($sleepy ? 0.5 : 1);
		$first_call_multiplier = ($first_call_boost ? 1.0 : 0.9);

		// calculate speed for this time step
		$new_speed = $horse->get_speed() * $race_over_multiplier
			* $sleepy_multiplier
			* $first_call_multiplier;

		// calculate direction for this time step
		// look up which section the horse is in
		$old_section = $horse_detail->get_section();
		// look up which lane the horse is in
		$old_lane = $horse_detail->get_lane();
		// run function using lane, coordinates, and section, which will return a number for how far off the lane the horse is

		// find the closest point on the current lane
		$lane_pt = $track->get_closest_point_to_lane($old_lane, $old_pos_x, $old_pos_y);

		// calculate target point (where the horse wants to go, 20 feet along the current lane)
		$target_dist = $track->distance_along_lane($old_lane, $lane_pt[0], $lane_pt[1]) + 20;
		$target_pt = $track->get_coordinate_from_distance($old_lane, $target_dist);

		// calculate heading point (where the horse will go if it doesn't turn, 20 feed straight ahead)
		$old_direction = $horse_detail->get_direction();
		$heading_x = $old_pos_x + cos($old_direction) * 20;
		$heading_y = $old_pos_y - sin($old_direction) * 20;

		// find the angle between the heading and the target
		$angle = $track->find_angle($heading_x, $heading_y, $old_pos_x, $old_pos_y, $target_pt[0], $target_pt[1]);

		// Calculate new heading (old direction adjusted by the angle between heading and target
		$new_direction = $old_direction - ($angle / 20);

		// optional debug information
		/*if ($horseid == 1) {
			print "TS: $ts pos=($old_pos_x, $old_pos_y) tar=(${target_pt[0]}, ${target_pt[1]}) head=($heading_x, $heading_y) lane=(${lane_pt[0]},${lane_pt[1]}) [$old_lane] dir=$new_direction $ angle = $angle\n";
		}*/
		// TODO: adjust current speed

        // check collision with horse ahead and slow down if needed
		$distance_to_my_nose = $track->distance_along_lane($old_lane, $lane_pt[0], $lane_pt[1]);
		// adjust for number of laps around
		$distance_to_my_nose += $track->lane_length($old_lane) * $horse_detail->get_lap_counter();

		foreach ($horses as $h) {
			/** @var horse_details $hd */
			$hd = $game_state->get_details($h->get_id());

			if ($h->get_id() == $horseid) {
				continue;
			}

			if ($hd->get_lane() != $old_lane) {
				continue;
			}

			$distance_to_their_nose = $track->distance_along_lane($hd->get_lane(), $hd->get_position_x(), $hd->get_position_y());
			// adjust for number of laps around
			$distance_to_their_nose += $track->lane_length($hd->get_lane()) * $hd->get_lap_counter();

			if ($distance_to_their_nose < $distance_to_my_nose) {
				continue;
			}

			if (abs($distance_to_my_nose - $distance_to_their_nose) < 9) {
				$new_speed = 0;
			}
        }

        // Mix up speeds a bit early on to give horses a better chance to move left quickly
        if ($horse_detail->get_lap_counter() == 0 && $distance_to_my_nose > 50 && $old_lane > 4) {
            if ($old_lane % 2 == 0) {
                $new_speed *= 0.9;
            } else {
                $new_speed *= 1.1;
            }
        }

		//calculate distance traveled in current ts
		$this_ts_distance = ($new_speed + rand(-20, 20)) * $dt;
		$dx = $this_ts_distance * cos($new_direction);
		$new_pos_x = $old_pos_x + $dx;

		$dy = $this_ts_distance * sin($new_direction);
		$new_pos_y = $old_pos_y - $dy;

		// if new pos overlaps projected new pos of next horse then reduce speed and recalculate.


		// calculate whether the horse moved into a new section
		$new_section = $track->get_section($new_pos_x, $new_pos_y);

		$horse_state = new stdClass();
		$horse_state->lane = $old_lane;
		$horse_state->horseid = $horseid;

		// 10 -> 1 % 14 -> 1
		// 20 -> 2 % 14 -> 2
		// 150 -> 15 % 14 -> 1

		// calculate decision tree score for each lane
		if ($ts % 10 == 0 && ($ts/10) % count($horses) == ($horseid - 1)) {
			$current_lane_score = $lane_score_tree->calcScore($game_state, $horse_state);

			$horse_state->lane = $old_lane + 1;
			if ($horse_state->lane > $track->get_lane_count()) {
				$right_lane_score = null;
			} else {
				$right_lane_score = $lane_score_tree->calcScore($game_state, $horse_state);
			}

			$horse_state->lane = $old_lane - 1;
			if ($horse_state->lane < 1) {
				$left_lane_score = null;
			} else {
				$left_lane_score = $lane_score_tree->calcScore($game_state, $horse_state);
			}

			if ($right_lane_score !== null && $left_lane_score !== null) {
				// switch lanes or stay in current lane based on score
				if ($current_lane_score >= $right_lane_score && $current_lane_score >= $left_lane_score) {
					$new_lane = $old_lane;
				} else if ($right_lane_score > $current_lane_score && $right_lane_score > $left_lane_score) {
					$new_lane = $old_lane + 1;
				} else {
					$new_lane = $old_lane - 1;
				}
			} else if ($right_lane_score === null && $left_lane_score === null) {
				$new_lane = $old_lane;
			} else if ($right_lane_score === null) {
				if ($current_lane_score >= $left_lane_score) {
					$new_lane = $old_lane;
				} else {
					$new_lane = $old_lane - 1;
				}
			} else if ($left_lane_score === null) {
				if ($current_lane_score >= $right_lane_score) {
					$new_lane = $old_lane;
				} else if ($right_lane_score > $current_lane_score) {
					$new_lane = $old_lane + 1;
				}
			}
            // Debugging info
            if ($ts == 10000) {
                print "TS: $ts horse: $horseid (".$horse_detail->get_post_position().") [$old_lane -> $new_lane] left_score=".($left_lane_score === null ? "null" : $left_lane_score)." current_score=".($current_lane_score === null ? "null" : $current_lane_score)." right_score=".($right_lane_score === null ? "null" : $right_lane_score)."\n";
            }
		} else {
			$new_lane = $old_lane;
		}


		// change the lap after the horse has been around the track once.
		if ($old_section == 5 && $new_section == 0) {
			$new_lap = $horse_detail->get_lap_counter() + 1;
		} else {
			$new_lap = $horse_detail->get_lap_counter();
		}

        if ($new_lap == 1 && $old_section == 0 && $new_section == 1) {
            $final_placement++;
            $this_fp = $final_placement;
        } else {
            $this_fp = $horse_detail->get_final_placement();
        }

		// this is going to change!
		$first_call_pos = 1320;

		$new_boost = $boost;
		if ($old_pos_x < $first_call_pos && $new_pos_x >= $first_call_pos) {
			print "BOOST POINT: {$horse->get_trait()} ---- {$game_state->get_placement($horseid)} ";
			if ($horse->get_trait() == 1 && $game_state->get_placement($horseid) == 1) {
				print "TRAIT 1 BOOST ACTIVATED: {$horse->get_trait()} ---- {$game_state->get_placement($horseid)} ";
				$new_boost = 1;
			} else if ($horse->get_trait() == 2 && $game_state->get_placement($horseid) == 2) {
				$new_boost = 1;
				print "TRAIT 2 BOOST ACTIVATED ";
			}

		}

		// calculate new endurance
		
		$new_end = $old_end - 0.005 * $this_ts_distance;

		// create new snapshot
		$snapshot->add_horse(new horse_details($horseid, $new_pos_x, $new_pos_y, $new_end, $new_speed, $new_direction, $new_boost, $horse_detail->get_post_position(), $new_section, $new_lane, $new_lap, $target_pt, $this_fp));

		// check if horse has crossed the finish line in this ts, if so increase finished counter
		if ($this_horse_finished) {
			$num_horses_finished++;
		}
	}

	if ($num_horses_finished == count($horses)) {
		$done = true;
	}

	//print $snapshot->print_snapshot();
	if ($ts % 1 == 0) {
		$animation_array[] = $snapshot->get_animation_array();
        print "$ts ";
	}
	$game_state->set_snapshot($snapshot);

	$ts++;

	if ($ts > 16000) {
		$done = true;
	}
}

// optional print animation data to screen
//print json_encode($animation_array);
file_put_contents("data.js", "var data = " . json_encode($animation_array) . ";");

?>
