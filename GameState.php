<?php

/**
 * Created by PhpStorm.
 * User: potetofurai
 * Date: 12/5/15
 * Time: 2:39 PM
 */
class GameState {
    private $horses;
    private $snapshot;
    /** @var  Track track */
    private $track;

    function __construct($horses, $track) {
        $this->horses = $horses;
        $this->track = $track;

        $post_positions = array();
        for ($i = 1; $i <= count($horses); $i++) {
            $post_positions[] = $i;
        }
        shuffle($post_positions);

        $i = 0;
        $this->snapshot = new snapshot(0, $track);
        foreach($horses as $horse) {
            $starting_lane = $post_positions[$i] + 3;
            $this->snapshot->add_horse(new horse_details($horse->get_id(), $track->get_lane_start_x($starting_lane), $track->get_lane_start_y($starting_lane), $horse->get_endurance(), $horse->get_speed(), 0, 0, $post_positions[$i], 0, $starting_lane, 0, array(0,0)));
            $i++;
        }
    }

    function get_details($horseid) {
        return $this->snapshot->get_details($horseid);
    }

    function get_ts() {
        return $this->snapshot->get_ts();
    }

    function get_placement($horseid) {
        return $this->snapshot->get_placement($horseid);
    }

    function set_snapshot($new_snapshot) {
        $this->snapshot = $new_snapshot;
    }

    function get_horses() {
        return $this->horses;
    }

    function get_adjacent_point($cur_lane, $tar_lane, $x, $y) {
        return $this->track->get_adjacent_point($cur_lane, $tar_lane, $x, $y);
    }

    function distance_along_lane($lane, $x, $y) {
        return $this->track->distance_along_lane($lane, $x, $y);
    }

    function lane_length($lane) {
        return $this->track->lane_length($lane);
    }

}