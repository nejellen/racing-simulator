<?php

/**
 * Class Edge represents the line between nodes and provides a score
 */
class Edge {
    private $answer;
    private $score;
    private $next_node;

    function __construct($answer, $score, $next_node) {
        $this->answer = $answer;
        $this->score = $score;
        $this->next_node = $next_node;
    }

    /**
     * Compare this edge's answer with the other answer
     * @param $other_answer The other answer to compare
     * @return bool True if the answers are the same, false if they aren't
     */
    function compare_answers($other_answer) {
        if ($this->answer == $other_answer) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Gets the next node
     * @return DecisionTreeNode The next node
     */
    function get_next_node() {
        return $this->next_node;
    }

    function get_score() {
        return $this->score;
    }
}