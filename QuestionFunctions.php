<?php

require_once('horse.php');

/**
 * Is there space in this lane?
 * @param game_state GameState Contains information about the game state in order to determine outcome
 * @param $horse_state StdClass Has a lane and a horseid
 */
function space_in_this_lane($game_state, $horse_state) {
    /** @var horse_details $details */
    $details = $game_state->get_details($horse_state->horseid);
    // compare location of all horses to potential location in adjacent lanes

    // calc adjacent point
    $adj_point = $game_state->get_adjacent_point(
        $details->get_lane(),
        $horse_state->lane,
        $details->get_position_x(),
        $details->get_position_y()
    );
    // calc distance along to that point
    $distance_to_adj_pt = $game_state->distance_along_lane($details->get_lane(), $adj_point[0], $adj_point[1]);
    // adjust for number of laps around
    $distance_to_adj_pt += $game_state->lane_length($details->get_lane()) * $details->get_lap_counter();

    $horses = $game_state->get_horses();
    foreach ($horses as $horse) {
        /** @var horse_details $horse_details */
        $horse_details = $game_state->get_details($horse->get_id());

        // if they're us, they don't overlap
        if ($horse->get_id() == $horse_state->horseid) {
            continue;
        }
        // if they're in an irrelevant lane, they don't overlap
        if ($horse_details->get_lane() != $horse_state->lane) {
            continue;
        }
        // otherwise check bounding boxes
        $distance_to_horse = $game_state->distance_along_lane($details->get_lane(), $horse_details->get_position_x(), $horse_details->get_position_y());
        // adjust for number of laps around
        $distance_to_horse += $game_state->lane_length($details->get_lane()) * $horse_details->get_lap_counter();
        if (abs($distance_to_adj_pt - $distance_to_horse) < 9) {
            return "no";
        }
    }
    return "yes";
}

//TODO: Write the logic
function num_lengths_ahead($game_state, $horse_state) {
    /** @var horse_details $details */
    $details = $game_state->get_details($horse_state->horseid);
    // compare location of all horses to potential location in adjacent lanes

    // calc adjacent point
    $adj_point = $game_state->get_adjacent_point(
        $details->get_lane(),
        $horse_state->lane,
        $details->get_position_x(),
        $details->get_position_y()
    );
    // calc distance along to that point
    $distance_to_adj_pt = $game_state->distance_along_lane($details->get_lane(), $adj_point[0], $adj_point[1]);
    // adjust for number of laps around
    $distance_to_adj_pt += $game_state->lane_length($details->get_lane()) * $details->get_lap_counter();

    $nearest_distance = null;

    $horses = $game_state->get_horses();
    foreach ($horses as $horse) {
        /** @var horse_details $horse_details */
        $horse_details = $game_state->get_details($horse->get_id());

        // if they're us, they don't overlap
        if ($horse->get_id() == $horse_state->horseid) {
            continue;
        }
        // if they're in an irrelevant lane, they don't overlap
        if ($horse_details->get_lane() != $horse_state->lane) {
            continue;
        }
        // otherwise check bounding boxes
        $distance_to_horse = $game_state->distance_along_lane($details->get_lane(), $horse_details->get_position_x(), $horse_details->get_position_y());
        // adjust for number of laps around
        $distance_to_horse += $game_state->lane_length($details->get_lane()) * $horse_details->get_lap_counter();

        if ($distance_to_horse < $distance_to_adj_pt) {
            continue;
        }

        if ($nearest_distance === null || $distance_to_horse < $nearest_distance) {
            $nearest_distance = $distance_to_horse;
        }
    }

    $relative_distance = $nearest_distance - $distance_to_adj_pt;
    if ($nearest_distance === null) {
        return "5+";
    } else if ($relative_distance < 9) {
        return "0";
    } else if ($relative_distance < 18) {
        return "1";
    } else if ($relative_distance < 27) {
        return "2";
    } else if ($relative_distance < 36) {
        return "3";
    } else if ($relative_distance < 45) {
        return "4";
    } else {
        return "5+";
    }
}


function num_turns_left($game_state, $horse_state) {
    /** @var horse_details $details */
    $details = $game_state->get_details($horse_state->horseid);

    if ($details->get_lap_counter() > 0) {
        return "0";
    }

    $section = $details->get_section();
    if ($section == 0 || $section == 1 || $section == 2) {
        return "2";
    } else {
        return "1";
    }

}

function where_is_lane($game_state, $horse_state) {
    /** @var horse_details $details */
    $details = $game_state->get_details($horse_state->horseid);
    $current_lane = $details->get_lane();
    $new_lane = $horse_state->lane;

    if ($new_lane < $current_lane) {
        return "left";
    } else if ($new_lane > $current_lane) {
        return "right";
    } else {
        return "same";
    }
}

/**
 * Returns yes.
 * @param $game_state
 * @param $lane
 * @return string
 */
function yes($game_state, $lane) {
    return "yes";
}

function no($game_state, $lane) {
    return "no";
}

