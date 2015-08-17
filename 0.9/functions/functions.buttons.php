<?php

function k1_get_link_button($linkTo, $label, $mini = "true", $icon_pos = "left", $inline = "true") {
    if ($linkTo == null) {
        return null;
    } elseif (!(is_string($linkTo) && is_string($label))) {
        die(__FUNCTION__ . " The parameters are not string");
    }

    $possible_strings = array(
        "export" => array(
            "exportar",
            "export",
        ),
        "back" => array(
            "back",
            "volver",
            "atras",
            "retroceder",
            "regresar",
        ),
        "go" => array(
            "ir",
            "go",
        ),
        "ok" => array(
            "aceptar",
            "si",
            "yes",
            "accept",
        ),
        "cancel" => array(
            "cancelar",
            "cancel",
            "no",
            "not",
        ),
        "view" => array(
            "ver",
            "mostrar",
            "view",
            "show",
        ),
        "new" => array(
            "agregar",
            "nuev",
            "new",
            "add",
            "añadir",
            "crear",
            "generar",
        ),
        "edit" => array(
            "edit",
            "cambiar",
            "change",
        ),
        "delete" => array(
            "delete",
            "borrar",
            "eliminar",
            "suprimir",
            "quitar",
            "cancelar",
        ),
    );

    $label_low = strtolower($label);
    $possible_action = "";
    foreach ($possible_strings as $possible_action_loop => $words) {
        foreach ($words as $word) {
            if (strstr($label_low, $word) !== false) {
                $possible_action = $possible_action_loop;
                break 2;
            }
        }
    }

    $js_confirm_dialog = "";
    switch ($possible_action) {
        case "export":
            $button_icon = "fi-download";
            $theme = "secondary";
            break;
        case "back":
            $button_icon = "fi-arrow-left";
            $theme = "";
            break;
        case "go":
            $button_icon = "fi-check";
            $theme = "success";
            break;
        case "cancel":
            $button_icon = "fi-x";
            $theme = "alert";
            break;
        case "ok":
            $button_icon = "fi-check";
            $theme = "success";
            break;
        case "view":
            $button_icon = "fi-clipboard-notes";
            $theme = "";
            break;
        case "new":
            $button_icon = "fi-plus";
            $theme = "";
            break;
        case "edit":
            $button_icon = "fi-clipboard-pencil";
            $theme = "";
            break;
        case "delete":
            $button_icon = "fi-page-delete";
            $theme = "alert";
            $js_confirm_dialog = "onclick=\"return confirm('Esta seguro que desea hacer esto ?\\n\\nEsta accion no se podra deshacer.')\"";
            break;
        default:
            $button_icon = "fi-widget";
            $theme = "secondary";
            break;
    }

    if ((strstr($linkTo, "https://") === false) && (strstr($linkTo, "javascript:") === false)) {
        $linkTo = k1_get_app_link($linkTo);
    }
    $icon_pos = "ui-btn-icon-$icon_pos";
    $mini = ($mini == "true") ? "tiny" : $mini;
//    $button_html = "<a href='{$linkTo}' $js_confirm_dialog >{$label}</a>";
    $button_html = "<a href='{$linkTo}' $js_confirm_dialog class='button {$mini} {$theme} $button_icon $icon_pos " . (($inline == "true") ? "inline" : "") . "'> {$label}</a>";

    return $button_html;
}
