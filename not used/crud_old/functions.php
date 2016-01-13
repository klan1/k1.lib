<?php

namespace k1lib\crud;

function parseUrlTag($urlString, \k1lib\crud\completeEasyController $contollerObject) {
    $urlString = str_replace("[controller-key]", $contollerObject->getBoardUrlParameterValue(), $urlString);
    $urlString = str_replace("[controller-fk]", $contollerObject->getBoardFkUrlValue(), $urlString);
    $urlString = str_replace("[board]", $contollerObject->getControllerUrlRoot(), $urlString);
    $urlString = str_replace("[board-edit]", $contollerObject->getBoardEditUrl(), $urlString);
    $urlString = str_replace("[board-view]", $contollerObject->getBoardDetailUrl(), $urlString);
    $urlString = str_replace("[board-view-all]", $contollerObject->getBoardTableListUrl(), $urlString);
    $urlString = str_replace("[board-delete]", $contollerObject->getBoardDeleteUrl(), $urlString);
    return $urlString;
}
