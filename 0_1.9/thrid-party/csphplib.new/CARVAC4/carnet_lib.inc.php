<?php
function celda_vacuna_carnet($DnD,&$matrix,&$num_rows)
{
    global $nombre_dosis;
	$rvID = "";
    $entidadID = 0;
    $tvID = 0;
    $fecha = "";
    $existe = 0;
    $vacunaID = substr($DnD,0,2);
    $dosis = substr($DnD,2,1);
    for ( $i=1 ; $i<=$num_rows ; $i++ )
    {
        if ( ($matrix[$i]["vacunaID"] == $vacunaID) && ($matrix[$i]["dosis"] == $dosis) )
        {
			$existe = 1;
            $fecha = $matrix[$i]["fecha"];
            $rvID = $matrix[$i]["rvID"];
            $entidadID = $matrix[$i]["entidadID"];
			$tvID = $matrix[$i]['tvID'];
            break;
        }
    }
	$html_code = "\n<!--- Comineza Celda de vacuna {$DnD} --->\n";
	// rvID -- i-1
	$html_code.= "\t<input type='hidden' name='rvID_{$vacunaID}{$dosis}' value='$rvID'>\n";
	// fecha (campo de texto) -- i
	$html_code.= "\t<input type='text' class='text-fecha-vacuna' name='fecha_{$vacunaID}{$dosis}' size='10' value='{$fecha}' maxlength='10' onFocus=\"var dosis_actual = cambiar_listas(this.form,this.name);\" ondblclick=\"this.value = put_input_text_value(this.value,this.defaultValue)\">\n";
	// entidad -- i+1
	$html_code.= "\t<input type='hidden' name='entidadID_{$vacunaID}{$dosis}' value='$entidadID'>\n";
	// nombre formal -- i+2
	$html_code.= "\t<input type='hidden' name='nombre_{$vacunaID}{$dosis}' value='{$nombre_dosis[($vacunaID+0)][1]} -- dosis No. {$dosis}'>\n";
	// verificador de existencia -- i+3
	$html_code.= "\t<input type='hidden' name='e_{$vacunaID}{$dosis}' value='$existe'>\n";
	// Bandera de cambio -- i+4
	$html_code.= "\t<input type='hidden' name='cambio_{$vacunaID}{$dosis}' value='0'>\n";
	// Bandera de Aseguramiento -- i+5
	$html_code.= "\t<input type='hidden' name='seguro_{$vacunaID}{$dosis}' value='0'>\n";
	// codigo de vacuna -- i+6
	$html_code.= "\t<input type='hidden' name='tvID_{$vacunaID}{$dosis}' value='$tvID'>\n";
	$html_code.= "<!--- Termina Celda de vacuna --->\n";
	return $html_code;
}

function celda_vacuna_impresion($DnD,&$matrix,&$num_rows)
{
    $rvID="";
    $entidadID="";
    $fecha="";
    $existe = 0;
    $vacunaID = substr($DnD,0,2);
    $dosis = substr($DnD,2,1);
    for ( $i=1 ; $i<=$num_rows ; $i++ )
    {
        if ( ($matrix[$i]["vacunaID"] == $vacunaID) && ($matrix[$i]["dosis"] == $dosis) )
        {
            $fecha = $matrix[$i]["fecha"];
        }
    }
    $html_code = "\n<!--- Comineza Texto de vacuna --->\n";
    if($fecha != "")
    {
        $html_code.= "\t$fecha\n";
    }else{
        $html_code.= "\t&nbsp;\n";
    }
    $html_code.= "<!--- Termina Texto de vacuna --->\n";
    return $html_code;
}

function actualizar_registro_vacuna($rvID,$DnD,$tvID,$fecha,$entidadID,$userID)
{
    global $mysql_db;
	global $_tv_intervalos;
	$vacunaID = substr($DnD,0,2);
    $dosis = substr($DnD,2,1);
    $registro = array
    (
        0 => array('userID','vacunaID','dosis','tvID','intervalo','entidadID','fecha'),
        1 => array($userID,$vacunaID,$dosis,$tvID,$_tv_intervalos[$DnD],$entidadID,$fecha)
    );
    $cond = "rvID='$rvID'";
    $mysql_db->update('registro_vacunacion',$registro,$cond);
}

function insetar_en_lista_crear(&$nuevas_vacunas,$DnD,$tvID,$fecha,$entidadID,$userID,$sedeID,$edad)
{
    //$nuevas_vacunas = array(0 => array('fecha','vacunaID','dosis','tvID','intervalo','userID','edad_a','edad_m','edad_d','sedeID','entidadID'));
    global $_tv_intervalos;
    $vacunaID = substr($DnD,0,2);
    $dosis = substr($DnD,2,1);
    $num_nuevas_vacunas = count($nuevas_vacunas)-1;
    $nuevas_vacunas += array
    (
        ($num_nuevas_vacunas+1) => array($fecha,$vacunaID,$dosis,$tvID,$_tv_intervalos[$DnD],$userID,$edad['anos'],$edad['meses'],$edad['dias'],$sedeID,$entidadID)
    );
}
?>