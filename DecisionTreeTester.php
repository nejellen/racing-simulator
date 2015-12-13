<?php
require_once("DecisionTree.php");

/**
 * Created by PhpStorm.
 * User: potetofurai
 * Date: 12/5/15
 * Time: 1:06 PM
 */
class DecisionTreeTester extends PHPUnit_Framework_TestCase {

    protected $decision_tree;

    protected function setUp() {

    }

    public function testGetScore() {
        $decision_tree = new DecisionTree(new DecisionTreeNode("yes", [new Edge("yes", 1, null)]));

        $score = $decision_tree->calcScore(null, null);

        $this->assertEquals(1, $score, "Scores didn't match.");
    }

    public function testOneNodeTwoEdges() {
        $edges = [
            new Edge("yes", 1, null),
            new Edge("no", 2, null)
        ];
        $decision_tree = new DecisionTree(new DecisionTreeNode("yes", $edges));

        $score = $decision_tree->calcScore(null, null);

        $this->assertEquals(1, $score, "Scores didn't match.");
    }

    public function testTwoNodesTwoEdgesYes() {
        $edges = [
            new Edge("yes", 1, new DecisionTreeNode("yes", [new Edge("yes", 4, null)])),
            new Edge("no", 2, new DecisionTreeNode("yes", [new Edge("yes", 8, null)])),
        ];
        $decision_tree = new DecisionTree(new DecisionTreeNode("yes", $edges));

        $score = $decision_tree->calcScore(null, null);

        $this->assertEquals(5, $score, "Scores didn't match.");
    }

    public function testTwoNodesTwoEdgesNo() {
        $edges = [
            new Edge("yes", 1, new DecisionTreeNode("yes", [new Edge("yes", 4, null)])),
            new Edge("no", 2, new DecisionTreeNode("yes", [new Edge("yes", 8, null)])),
        ];
        $decision_tree = new DecisionTree(new DecisionTreeNode("no", $edges));

        $score = $decision_tree->calcScore(null, null);

        $this->assertEquals(10, $score, "Scores didn't match.");
    }

    public function testTwoNodesTwoEdgesNull() {
        $edges = [
            new Edge("yes", null, new DecisionTreeNode("yes", [new Edge("yes", 4, null)])),
            new Edge("no", null, new DecisionTreeNode("yes", [new Edge("yes", 8, null)])),
        ];
        $decision_tree = new DecisionTree(new DecisionTreeNode("no", $edges));

        $score = $decision_tree->calcScore(null, null);

        $this->assertEquals(null, $score, "Scores didn't match.");
    }

    // TODO: test where score of first edge is zero

}