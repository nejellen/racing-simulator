<?php
require_once("DecisionTreeNode.php");

/**
 * Class DecisionTree represents a Decision Tree data structure
 */
class DecisionTree {

    private $head_node;

    function __construct($node) {
        $this->head_node = $node;
    }

    function calcScore($game_state, $horse_state) {
        $score = 0;
        /** @var DecisionTreeNode $next_node */
        $next_node = $this->head_node;

        do {
            list($next_node, $edge_score) = $next_node->get_the_stuff($game_state, $horse_state);
            if ($edge_score === null) {
                return null;
            }
            $score += $edge_score;
        } while ($next_node !== null);
        return $score;
    }
}