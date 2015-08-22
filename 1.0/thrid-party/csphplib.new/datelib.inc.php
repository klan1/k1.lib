<?php

$G_today = date("Y-m-d");
$G_now = date("Y-m-d G:i:s");

$G_year_act = date("Y");
$G_month_act = date("m");
$G_day_act = date("d");

$G_months[0] = 0;
$G_months[1] = 31;
if ((($G_year_act % 4) == 0) || (($G_year_act % 4) == 0)) {
    $G_months[2] = 29;
    $dt = 366;
} else {
    $G_months[2] = 28;
    $dt = 365;
}
$G_months[3] = 31;
$G_months[4] = 30;
$G_months[5] = 31;
$G_months[6] = 30;
$G_months[7] = 31;
$G_months[8] = 31;
$G_months[9] = 30;
$G_months[10] = 31;
$G_months[11] = 30;
$G_months[12] = 31;

$G_months_names[0] = '';
$G_months_names[1] = 'Enero';
$G_months_names[2] = 'Febrero';
$G_months_names[3] = 'Marzo';
$G_months_names[4] = 'Abril';
$G_months_names[5] = 'Mayo';
$G_months_names[6] = 'Junio';
$G_months_names[7] = 'Julio';
$G_months_names[8] = 'Agosto';
$G_months_names[9] = 'Septiembre';
$G_months_names[10] = 'Octube';
$G_months_names[11] = 'Noviembre';
$G_months_names[12] = 'Diciembre';

function calcular_edad($fecha_n, $tipo=1, $fecha_ref = '') {
    $ano_n = substr($fecha_n, 0, 4) + 0;
    $mes_n = substr($fecha_n, 5, 2) + 0;
    $dia_n = substr($fecha_n, 8, 2) + 0;

    if ($fecha_ref == '') {
        $hoy = $GLOBALS['G_today'];
    } else {
        $hoy = $fecha_ref;
    }
    $ano_act = substr($hoy, 0, 4) + 0;
    $mes_act = substr($hoy, 5, 2) + 0;
    $dia_act = substr($hoy, 8, 2) + 0;

    $JD_n = GregorianToJD($mes_n, $dia_n, $ano_n);
    $JD_act = GregorianToJD($mes_act, $dia_act, $ano_act);

    $edad = JDToGregorian(($JD_act - $JD_n) + 1721426);

    $ano_edad = substr(strrchr($edad, '/'), 1) - 1;
    $pos = strrpos($edad, '/');
    $edad = substr_replace($edad, '', $pos, strlen($edad) - 1);
    $dia_edad = substr(strrchr($edad, '/'), 1) - 1;
    $pos = strrpos($edad, '/');
    $edad = substr_replace($edad, '', $pos, strlen($edad) - 1);
    $mes_edad = $edad - 1;

    switch ($tipo) {
        case 1: // cadena completa
            $edad = "$ano_edad años $mes_edad meses con $dia_edad dias";
            break;
        case 2: // fecha para mysql
            $edad = "$ano_edad-$mes_edad-$dia_edad";
            break;
        case 3: // array para desarrollo
            $edad = array
                (
                'anos' => $ano_edad,
                'meses' => $mes_edad,
                'dias' => $dia_edad
            );
            break;
        case 4: // numero de meses desde nacido
            $edad = ($ano_edad * 12) + $mes_edad;
            break;
        case 5: // numero de dias desde nacido
            $edad = ($ano_edad * 365) + ($mes_edad * 30) + $dia_edad;
            break;
    }
    return $edad;
}

function ubicar_intervalo() {
    
}
?>