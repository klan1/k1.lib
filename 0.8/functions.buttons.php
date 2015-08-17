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
            $button_icon = "ui-icon-action";
            $theme = "a";
            break;
        case "back":
            $button_icon = "ui-icon-back";
            $theme = "b";
            break;
        case "go":
            $button_icon = "ui-icon-arrow-u";
            $theme = "b";
            break;
        case "view":
            $button_icon = "ui-icon-grid";
            $theme = "b";
            break;
        case "new":
            $button_icon = "ui-icon-plus";
            $theme = "e";
            break;
        case "edit":
            $button_icon = "ui-icon-edit";
            $theme = "a";
            break;
        case "delete":
            $button_icon = "ui-icon-delete";
            $theme = "d";
            $js_confirm_dialog = "onclick=\"return confirm('Esta seguro que desea hacer esto ?\\n\\nEsta accion no se podra deshacer.')\"";
            break;
        default:
            $button_icon = "ui-icon-gear";
            $theme = "b";
            break;
    }

    if ((strstr($linkTo, "https://") === false) && (strstr($linkTo, "javascript:") === false)) {
        $linkTo = k1_get_app_link($linkTo);
    }
    $icon_pos = "ui-btn-icon-$icon_pos";
//    $button_html = "<a href='{$linkTo}' $js_confirm_dialog data-role='button' data-mini='{$mini}' data-theme='{$theme}' data-icon='{$button_icon}' data-iconpos='{$icon_pos}' data-inline='{$inline}'>{$label}</a>";
    $button_html = "<a href='{$linkTo}' $js_confirm_dialog class='ui-btn ui-btn-{$theme} " . (($mini == "true") ? "ui-mini" : "") . " $button_icon $icon_pos " . (($inline == "true") ? "ui-btn-inline" : "") . "'>{$label}</a>";

    return $button_html;
}
