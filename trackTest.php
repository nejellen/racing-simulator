<?php

require_once("track.php");

class trackTest extends PHPUnit_Framework_TestCase {
    protected $track;

    protected function setUp() {
        $this->track = new Track(3000, 2000, 80, 840, 2160, 840, 840, 420.169, 20);
    }

    public function testDistance_along_lane() {
        $this->assertEquals(0, $this->track->distance_along_lane(1, 840, 1262.169));
        $this->assertEquals(0, $this->track->distance_along_lane(2, 840, 1266.169));
        $this->assertEquals(100, $this->track->distance_along_lane(1, 940, 1262.169));
        $this->assertEquals(1320, $this->track->distance_along_lane(1, 2160, 1262.169));
        $this->assertEquals(1651.571, $this->track->distance_along_lane(1, 2458.519, 1138.519), '', 0.001);
        $this->assertEquals(2314.712, $this->track->distance_along_lane(1, 2458.519, 541.481), '', 0.001);
        $this->assertEquals(4297.854, $this->track->distance_along_lane(1, 541.481, 541.481), '', 0.001);
        $this->assertEquals(4600, $this->track->distance_along_lane(1, 418.856, 810.6), '', 0.001);
        $this->assertEquals(4629.425, $this->track->distance_along_lane(1, 417.831, 840), '', 0.001);
        $this->assertEquals(4960.995, $this->track->distance_along_lane(1, 541.481, 1138.519), '', 0.001);
        
        $this->assertEquals(2639, $this->track->distance_along_lane(1, 2167.283, 417.894), '', 0.001);
        $this->assertEquals(2647, $this->track->distance_along_lane(1, 2159.283, 417.883), '', 0.001);
    }

    public function testGet_coordinate_from_distance() {
        $this->assertEquals($this->track->get_coordinate_from_distance(1, 0), array(840, 1262.169));
        $this->assertEquals($this->track->get_coordinate_from_distance(2, 0), array(840, 1266.169));
        $this->assertEquals($this->track->get_coordinate_from_distance(1, 2639), array(2167.283, 417.894), '', 0.001);
        $this->assertEquals($this->track->get_coordinate_from_distance(1, 2647), array(2159.283, 417.832), '', 0.001);
        $this->assertEquals($this->track->get_coordinate_from_distance(1, 3967), array(839.283, 417.832), '', 0.001);
        
        $this->assertEquals(array(418.856, 810.6), $this->track->get_coordinate_from_distance(1, 4600), '', 0.001);
    }

    public function testWorld_to_unit() {
        $this->assertEquals($this->track->world_to_unit(2582.169, 840), array(1, 0, 422.169));
        $this->assertEquals($this->track->world_to_unit(2458.519, 541.481), array(sqrt(2)/2, sqrt(2)/2, 422.169), '', 0.001);
        $this->assertEquals($this->track->world_to_unit(2458.519, 1138.519), array(sqrt(2)/2, -1 * sqrt(2)/2, 422.169), '', 0.001);
        $this->assertEquals($this->track->world_to_unit(541.481, 541.481), array(-1 * sqrt(2)/2, sqrt(2)/2, 422.169), '', 0.001);
        $this->assertEquals($this->track->world_to_unit(541.481, 1138.519), array(-1 * sqrt(2)/2, -1 * sqrt(2)/2, 422.169), '', 0.001);
    }
    
    public function testUnit_to_world() {
        $this->assertEquals($this->track->unit_to_world(1, 0, 422.169), array(2582.169,840));
        $this->assertEquals($this->track->unit_to_world(sqrt(2)/2, sqrt(2)/2, 422.169), array(2458.519,541.481), '', 0.001);
        $this->assertEquals($this->track->unit_to_world(sqrt(2)/2, -1 * sqrt(2)/2, 422.169), array(2458.519,1138.519), '', 0.001);
        $this->assertEquals($this->track->unit_to_world(-1 * sqrt(2)/2, sqrt(2)/2, 422.169), array(541.481,541.481), '', 0.001);
        $this->assertEquals($this->track->unit_to_world(-1 * sqrt(2)/2, -1 * sqrt(2)/2, 422.169), array(541.481,1138.519), '', 0.001);
    }

    public function testGet_closest_point_to_lane() {
        $this->assertEquals($this->track->get_closest_point_to_lane(1, 940, 1300), array(940,1262.169), '', 0.001);
        $this->assertEquals($this->track->get_closest_point_to_lane(1, 2558.519, 1238.519), array(2458.519,1138.519), '', 0.001);
        $this->assertEquals($this->track->get_closest_point_to_lane(1, 2558.519, 441.481), array(2458.519,541.481), '', 0.001);
        $this->assertEquals($this->track->get_closest_point_to_lane(1, 441.481, 441.481), array(541.481,541.481), '', 0.001);
        $this->assertEquals($this->track->get_closest_point_to_lane(1, 441.481, 1238.519), array(541.481,1138.519), '', 0.001);
    }

    public function testFind_angle() {
        $this->assertEquals($this->track->find_angle(3,0,0,0,3,1), 0.322, '', 0.001);
        $this->assertEquals($this->track->find_angle(3,0,0,0,3,-1), -0.322, '', 0.001);
        $this->assertEquals($this->track->find_angle(4,1,1,1,4,0), -0.322, '', 0.001);
        $this->assertEquals($this->track->find_angle(0,3,0,0,1,3), -0.322, '', 0.001);
        $this->assertEquals($this->track->find_angle(1,3,0,0,0,3), 0.322, '', 0.001);
        $this->assertEquals(-0.644, $this->track->find_angle(-3,-1,0,0,-3,1), '', 0.001);
        $this->assertEquals(0.644, $this->track->find_angle(-3,1,0,0,-3,-1), '', 0.001);


        $this->assertEquals($this->track->find_angle(976.189, 1278.313, 957.160, 1272.161, 974, 1274.169), -0.194, '', 0.001);
        $this->assertEquals(-0.00495,$this->track->find_angle(2126.425, 409.732, 2146.424, 409.830, 2127.416, 409.831), '', 0.001);
    }

    public function testGetAdjacentPoint() {
        $this->assertEquals(array(840, 1266.169), $this->track->get_adjacent_point(1, 2, 840, 1262.169), '', 0.001);
        $this->assertEquals(array(840, 413.831), $this->track->get_adjacent_point(1, 2, 840, 417.831), '', 0.001);
        $this->assertEquals(array(2586.169, 840), $this->track->get_adjacent_point(1, 2, 2582.169, 840), '', 0.001);
        $this->assertEquals(array(2461.347, 538.653), $this->track->get_adjacent_point(1, 2, 2458.519, 541.481), '', 0.001);
        $this->assertEquals(array(413.831, 840), $this->track->get_adjacent_point(1, 2, 417.831, 840), '', 0.001);
    }
}
