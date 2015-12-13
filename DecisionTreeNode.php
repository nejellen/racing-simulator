<?php

require_once("Edge.php");
require_once("QuestionFunctions.php");

/**
 * Class DecisionTreeNode represents a node in a Decision Tree data structure
 */
class DecisionTreeNode {
    private $question;
    private $edges;

    function __construct($question, $edges) {
        $this->node_id = $id;
        $this->question = $question;
        $this->edges = $edges;
    }

    /**
     * Finds and returns the edge with a matching answer
     * @param $answer String The answer to match with an edge
     * @return Edge The edge with the matching answer
     */
    function find_correct_edge($answer) {
        foreach ($this->edges as $edge) {
            if ($edge->compare_answers($answer) == true) {
                return $edge;
            }
        }
    }

    /**
     * Asks the question for this node
     * @return String The answer to the question
     */
    function ask_question($game_state, $horse_state) {
        return call_user_func($this->question, $game_state, $horse_state);
    }

    function get_the_stuff($game_state, $horse_state) {
        $answer = $this->ask_question($game_state, $horse_state);
        $edge = $this->find_correct_edge($answer);
        $score = $edge->get_score();
        $next_node = $edge->get_next_node();
        return [$next_node, $score];
    }
}