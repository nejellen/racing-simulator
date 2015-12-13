<?php

class Track {

	function __construct($world_width, $world_height, $track_width, $track_left_center_x, $track_right_center_x, $track_left_center_y, $track_right_center_y, $track_radius, $lane_count) {
		$this->world_width = $world_width;
		$this->world_height = $world_height;
		$this->track_width = $track_width;
		$this->track_left_center_x = $track_left_center_x;
		$this->track_right_center_x = $track_right_center_x;
		$this->track_left_center_y = $track_left_center_y;
		$this->track_right_center_y = $track_right_center_y;
		$this->track_radius = $track_radius;
		$this->lane_count = $lane_count;
		$this->lane_width = $track_width / $lane_count;

	}

	private $world_width;
	private $world_height;
	private $track_width;
	private $track_left_center_x;
	private $track_right_center_x;
	private $track_left_center_y;
	private $track_right_center_y;
	private $track_radius;
	private $lane_count;
	private $lane_width;

	function get_section($x, $y) {
		// messy mess of if statements
		//  4 | 3 | 2
		// ---|---|---
		//  5 | 0 | 1
		if ($y < $this->track_left_center_y) {
			if ($x < $this->track_left_center_x) {
				return 4;
			} else if ($x < $this->track_right_center_x) {
				return 3;
			} else {
				return 2;
			}
		} else {
			if ($x < $this->track_left_center_x) {
				return 5;
			} else if ($x < $this->track_right_center_x) {
				return 0;
			} else {
				return 1;
			}
		}
	}

	/**
     * Calculates the angle between two vectors centered at a particular point
     *
     * The angle output is signed with positive values indicating that the target
     * is to the left of the heading.
     *
     * This function may not reliably handle situations where the angle between
     * the target point and the heading is greater than PI radians.
     *
	 * @param $x0 x coordinate of first (heading) vector 
	 * @param $y0 y coordinate of first (heading) vector
	 * @param $x1 x coordinate of center
	 * @param $y1 y coordinate of center
	 * @param $x2 x coordinate of second (target) vector
	 * @param $y2 y coordinate of second (target) vector
	 * @return float The angle between the two vectors in radians.
	 */
    function find_angle($x0, $y0, $x1, $y1, $x2, $y2) {
        $v1x = $x0 - $x1;
        $v1y = $y0 - $y1;
        $v2x = $x2 - $x1;
        $v2y = $y2 - $y1;

        $a = atan2($v2y, $v2x);
        $b = atan2($v1y, $v1x);

        // atan2 has a discontinuity at PI
        // Because we are using these angles for steering, we don't expect to ever
        // experience reflex angles between the target point and the heading point
        // this adjustment treats all angle differences greater than pi as if they
        // were actually a smaller angle in the opposite direction.
        if (abs($a - $b) > M_PI) {
            $a += ($a < 0 ? M_PI : -1 * M_PI);
            $b += ($b < 0 ? M_PI : -1 * M_PI);
            return $a - $b;
        } else {
            return $a - $b;
        }
    }

    /**
     * Get the x coordinate of the starting position for a particular lane
     */
	function get_lane_start_x($lane) {
		return $this->track_left_center_x;
	}

    /**
     * Get the y coordinate of the starting position for a particular lane
     */
	function get_lane_start_y($lane) {
		return $this->track_left_center_y + $this->lane_offset($lane);
	}

    /**
     * Get the offset from center point to the lane
     */
	function lane_offset($lane) {
		return $this->track_radius + $this->lane_width * ($lane - 1) + ($this->lane_width / 2);
	}

    /**
     * Find the closest point on the given lane to the given arbitrary coordinate
     */
	function get_closest_point_to_lane($lane, $x, $y) {
        // figure out what section we're in
		$section = $this->get_section($x, $y);

		// switch based on which section
		if ($section == 0) {
			$lane_y = $this->track_left_center_y + $this->lane_offset($lane);
            return array($x, $lane_y);
		} else if ($section == 3) {
			$lane_y = $this->track_left_center_y - $this->lane_offset($lane);
            return array($x, $lane_y);
        } else {
            if ($section == 1 || $section == 2) {
                $cx = $this->track_right_center_x;
                $cy = $this->track_right_center_y;
            } else {
                $cx = $this->track_left_center_x;
                $cy = $this->track_left_center_y;
            }

            $vx = $x - $cx;
            $vy = $y - $cy;
            $magv = sqrt($vx*$vx + $vy*$vy);
            $ax = $cx + $vx / $magv * $this->lane_offset($lane);
            $ay = $cy + $vy / $magv * $this->lane_offset($lane);
            
            return array($ax, $ay);
		}
	}

    // distance along lane (from starting post) to a particular world point
	function distance_along_lane($lane, $x, $y) {
		$section = $this->get_section($x, $y);
		$straight_len = $this->track_right_center_x - $this->track_left_center_x;
        $lane_offset = $this->lane_offset($lane);
        $curved_len = M_PI * $lane_offset;

		if ($section == 0) {
			return $x - $this->track_left_center_x;
		} else if ($section == 3) {
			return $straight_len + $curved_len + $this->track_right_center_x - $x;
		} else if ($section == 1 || $section == 2) {
            $section_offset = $straight_len;
            $x_adj = $x;
            $y_adj = $y;
		} else if ($section == 4 || $section == 5) {
            $section_offset = $straight_len * 2 + $curved_len;

            // flip these coordinates across both axes so we can use an angle range
            // that doesn't run into trig discontinuities
            $x_adj = ($this->track_left_center_x - $x + $this->track_right_center_x);
            $y_adj = ($this->track_left_center_y - $y + $this->track_right_center_y);
        }
        $angle = $this->find_angle(
            $this->track_right_center_x, $this->track_right_center_y + $lane_offset, 
            $this->track_right_center_x, $this->track_right_center_y,
            $x_adj, $y_adj);
        return -1 * $angle * $lane_offset + $section_offset;
	}

    function distance($x1, $y1, $x2, $y2) {
        return sqrt(($x1-$x2)*($x1-$x2) + ($y1-$y2)*($y1-$y2));
    }

    // convert a world coordinate on the right circle to a unit circle coordinate
    function world_to_unit($wx, $wy) {
        // calculate which circle this is on
        if ($wx > $this->track_right_center_x) {
            $tx = $wx - $this->track_right_center_x;
            $ty = $this->track_right_center_y - $wy;
        } else if ($wx < $this->track_left_center_x) {
            $tx = $wx - $this->track_left_center_x;
            $ty = $this->track_left_center_y - $wy;
        } else {
            throw new Exception('Invalid coordinate in world_to_unit, not on a circle');
        }
        
        $d = $this->distance(0,0,$tx,$ty);

        $ux = $tx / $d;
        $uy = $ty / $d;

        return array($ux, $uy, $d);
    }

    function unit_to_world($ux, $uy, $d) {
        $tx = $ux * $d;
        $ty = $uy * $d;

        if ($ux > 0) {
            $wx = $tx + $this->track_right_center_x;
            $wy = $this->track_right_center_y - $ty;
        } else if ($ux < 0) {
            $wx = $tx + $this->track_left_center_x;
            $wy = $this->track_left_center_y - $ty;
        } else {
            throw new Exception('Invalid coordinate in unit_to_world, x cant be zero');
        }

        return array($wx, $wy);
    }

    // given a lane and a distance, return the array(x,y) coordinate of the world point
    function get_coordinate_from_distance($lane, $distance) {
        $lane_length = $this->lane_length($lane);
		$straight_len = $this->track_right_center_x - $this->track_left_center_x;
		$curved_len = M_PI * $this->lane_offset($lane);
        $d = $distance % $lane_length;

        if ($d < 0) {
            $d += $lane_length;
        }

        if ($d <= $straight_len) {
            return array($this->track_left_center_x + $d, $this->track_left_center_y + $this->lane_offset($lane));
        } else if ( $d < $straight_len + $curved_len) {
            $curved_dist = $d - $straight_len;
            $curved_frac = $curved_dist / $curved_len;

            $unit_frac = $curved_frac * M_PI;
            $angle = ((3*M_PI)/2) + $unit_frac;

            return $this->unit_to_world(cos($angle), sin($angle), $this->lane_offset($lane));
        } else if ($d <= $straight_len*2 + $curved_len) {
            $sd = $d - $straight_len - $curved_len;
            return array($this->track_right_center_x - $sd, $this->track_left_center_y - $this->lane_offset($lane));
        } else {
            $curved_dist = $d - $straight_len * 2 - $curved_len;
            $curved_frac = $curved_dist / $curved_len;

            $unit_frac = $curved_frac * M_PI;
            $angle = (M_PI/2) + $unit_frac;

            return $this->unit_to_world(cos($angle), sin($angle), $this->lane_offset($lane));
        }

        return array(0,0);
    }

	function lane_length($lane) {
		$straight_section_length = $this->track_right_center_x - $this->track_left_center_x;
		return ($straight_section_length * 2) + (2 * M_PI * $this->lane_offset($lane));
	}

	function lane_percent_completed($lane, $x, $y) {
		return $this->distance_along_lane($lane, $x, $y) / $this->lane_length($lane);
	}

    function get_adjacent_point($cur_lane, $tar_lane, $x, $y) {
        if ($cur_lane == $tar_lane) {
            return array($x, $y);
        }

        $section = $this->get_section($x, $y);

        if ($section == 0) {
            $new_y = $this->lane_offset($tar_lane) + $this->track_right_center_y;
            return array($x, $new_y);
        } else if ($section == 3) {
            $new_y = $this->track_right_center_y - $this->lane_offset($tar_lane);
            return array($x, $new_y);
        } else {
            $ptu = $this->world_to_unit($x, $y);
            return $this->unit_to_world($ptu[0], $ptu[1], $this->lane_offset($tar_lane));
        }
    }

    function get_lane_count() {
        return $this->lane_count;
    }
}
