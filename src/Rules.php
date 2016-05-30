<?php

function validator_rule_required($fields, $value, $params) {
    return strlen($value) > 0;
}

function validator_rule_in($fields, $value, $params) {
    return in_array($value, $params);
}

function validator_rule_email($fields, $value, $params) {
    return filter_var($value, FILTER_VALIDATE_EMAIL);
}

function validator_rule_notin($fields, $value, $params) {
    return !in_array($value, $params);
}

function validator_rule_min($fields, $value, $params) {
    return strlen($value) >= $params[0];
}

function validator_rule_max($fields, $value, $params) {
    return strlen($value) <= $params[0];
}

function validator_rule_len($fields, $value, $params) {
    return strlen($value) == $params;
}

function validator_rule_min_value($fields, $value, $params) {
    return $value >= $params[0];
}

function validator_rule_max_value($fields, $value, $params) {
    return $value <= $params[0];
}

function validator_rule_matches($fields, $value, $params) {
    return $value === $fields[$params[0]]->value;
}

function validator_rule_is($fields, $value, $params) {
    return $value === $params[0];
}

function validator_rule_min_words($fields, $value, $params) {
    return str_word_count($value) >= $params[0];
}