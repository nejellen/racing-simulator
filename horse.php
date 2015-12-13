<?php
class horse {
	function __construct($id, $name, $speed, $endurance, $agility, $scope, $surf_pref, $trait) {
		$this->id = $id;
		$this->name = $name;
		$this->speed = $speed;
		$this->endurance = $endurance;
		$this->agility = $agility;
		$this->scope = $scope;
		$this->surf_pref = $surf_pref;
		$this->trait = $trait;
	}

	function print_details() {
		print "Name: {$this->name}.\n";
		print "Speed: {$this->speed}.\n";
		print "Surface Preference: ".self::$surf_pref_name[$this->surf_pref]."\n";
	}

	function surf_pref_name() {
		return self::$surf_pref_name[$this->surf_pref];
	}

	public function get_trait() {
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

	function get_agility() {
		return $this->agility;
	}

	function get_scope() {
		return $this->scope;
	}

	private $id;
	private $name;
	private $speed;
	private $endurance;
	private $agility;
	private $scope;
	private $surf_pref;
	private $trait;

	private static $surf_pref_name = array (
		0 => "Invalid",
		1 => "Turf",
		2 => "Dirt"
	);
	private static $traits = array (
		1 => "Front Runner",
		2 => "Pace Presser",
		3 => "Closer",
		4 => "Prone to Refusal",
		5 => "Rusher",
		6 => "Poor Balance",
		7 => "Likes to Buck"
	);
}
