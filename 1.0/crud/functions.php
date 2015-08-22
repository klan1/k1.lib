<?php

namespace k1lib\crud;

const CONTROLLER_TYPE_PLAIN = -1;
const CONTROLLER_TYPE_MAIN = 1;
const CONTROLLER_TYPE_FOREIGN = 2;
const CONTROLLER_TYPE_CUSTOM = 3;
const BOARD_TYPE_VIEW_ALL = 1;
const BOARD_TYPE_VIEW = 2;
const BOARD_TYPE_NEW = 3;
const BOARD_TYPE_EDIT = 4;
const BOARD_TYPE_DELETE = 5;

function parseUrlTag($urlString, \k1lib\crud\classes\completeEasyController $contollerObject) {
    $urlString = str_replace("[controller-key]", $contollerObject->getBoardUrlParameterValue(), $urlString);
    $urlString = str_replace("[controller-fk]", $contollerObject->getBoardFkUrlValue(), $urlString);
    $urlString = str_replace("[board]", $contollerObject->getControllerUrlRoot(), $urlString);
    $urlString = str_replace("[board-edit]", $contollerObject->getBoardEditUrl(), $urlString);
    $urlString = str_replace("[board-view]", $contollerObject->getBoardDetailUrl(), $urlString);
    $urlString = str_replace("[board-view-all]", $contollerObject->getBoardTableListUrl(), $urlString);
    $urlString = str_replace("[board-delete]", $contollerObject->getBoardDeleteUrl(), $urlString);
    return $urlString;
}
