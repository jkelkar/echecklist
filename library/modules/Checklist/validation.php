<?php

// all known validations are here

function str_min_4($data) {
  return (is_string($data) && strlen($data) >= 4) ? null: "Must be at least 4 characters long";
}

function str_min_6($data) {
  return (is_string($data) && strlen($data) >= 6) ? null: "Must be at least 6 characters long";
}
function str_nempty($data) {
  return (is_string($data) && strlen($data) > 0 && $data != '-') ? null: "Must not be empty";
}

function str_not_default($data) {
  return (is_string($data) && $data != '-') ? null: "Must select a valid choice";
}