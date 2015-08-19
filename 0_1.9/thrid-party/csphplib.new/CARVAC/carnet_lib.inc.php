<?php
function celda_vacuna_carnet($DnD,&$matrix,&$num_rows)
{
    global $nombre_dosis;
	$rvID = "";
    $entidadID = 0;
    $vacunaID = 0;
    $fecha = "";
    $existe = 0;
    $dosisID = substr($DnD,0,2);
    $n_dosis = substr($DnD,2,1);
    for ( $i=1 ; $i<=$num_rows ; $i++ )
    {
        if ( ($matrix[$i]["dosisID"] == $dosisID) && ($matrix[$i]["n_dosis"] == $n_dosis) )
        {
			$existe = 1;
            $fecha = $matrix[$i]["fecha"];
            $rvID = $matrix[$i]["rvID"];
            $entidadID = $matrix[$i]["entidadID"];
			$vacunaID = $matrix[$i]['vacunaID'];
            break;
        }
    }
	$html_code = "\n<!--- Comineza Celda de vacuna {$DnD} --->\n";
	// rvID -- i-1
	$html_code.= "\t<input type='hidden' name='rvID_{$dosisID}{$n_dosis}' value='$rvID'>\n";
	// fecha (campo de texto) -- i
	$html_code.= "\t<input type='text' class='text-fecha-vacuna' name='fecha_{$dosisID}{$n_dosis}' size='10' value='{$fecha}' maxlength='10' onFocus=\"var dosis_actual = cambiar_listas(this.form,this.name);\" ondblclick=\"this.value = put_input_text_value(this.value,this.defaultValue)\">\n";
	// entidad -- i+1
	$html_code.= "\t<input type='hidden' name='entidadID_{$dosisID}{$n_dosis}' value='$entidadID'>\n";
	// nombre formal -- i+2
	$html_code.= "\t<input type='hidden' name='nombre_{$dosisID}{$n_dosis}' value='{$nombre_dosis[($dosisID+0)][1]} -- dosis No. {$n_dosis}'>\n";
	// verificador de existencia -- i+3
	$html_code.= "\t<input type='hidden' name='e_{$dosisID}{$n_dosis}' value='$existe'>\n";
	// Bandera de cambio -- i+4
	$html_code.= "\t<input type='hidden' name='cambio_{$dosisID}{$n_dosis}' value='0'>\n";
	// Bandera de Aseguramiento -- i+5
	$html_code.= "\t<input type='hidden' name='seguro_{$dosisID}{$n_dosis}' value='0'>\n";
	// codigo de vacuna -- i+6
	$html_code.= "\t<input type='hidden' name='vacunaID_{$dosisID}{$n_dosis}' value='$vacunaID'>\n";
	$html_code.= "<!--- Termina Celda de vacuna --->\n";
	return $html_code;
}

function celda_vacuna_impresion($DnD,&$matrix,&$num_rows)
{
    $rvID="";
    $entidadID="";
    $fecha="";
    $existe = 0;
    $dosisID = substr($DnD,0,2);
    $n_dosis = substr($DnD,2,1);
    for ( $i=1 ; $i<=$num_rows ; $i++ )
    {
        if ( ($matrix[$i]["dosisID"] == $dosisID) && ($matrix[$i]["n_dosis"] == $n_dosis) )
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

function actualizar_registro_vacuna($rvID,$DnD,$vacunaID,$fecha,$entidadID,$userID)
{
    global $mysql_db;
	global $_tv_intervalos;
	$dosisID = substr($DnD,0,2);
    $n_dosis = substr($DnD,2,1);
    $registro = array
    (
        0 => array('userID','dosisID','n_dosis','vacunaID','intervalo','entidadID','fecha'),
        1 => array($userID,$dosisID,$n_dosis,$vacunaID,$_tv_intervalos[$DnD],$entidadID,$fecha)
    );
    $cond = "rvID='$rvID'";
    $mysql_db->update('registro_vacunacion',$registro,$cond);
}

function insetar_en_lista_crear(&$nuevas_vacunas,$DnD,$vacunaID,$fecha,$entidadID,$userID,$sedeID,$edad)
{
    //$nuevas_vacunas = array(0 => array('fecha','dosisID','n_dosis','vacunaID','intervalo','userID','edad_a','edad_m','edad_d','sedeID','entidadID'));
    global $_tv_intervalos;
    $dosisID = substr($DnD,0,2);
    $n_dosis = substr($DnD,2,1);
    $num_nuevas_vacunas = count($nuevas_vacunas)-1;
    $nuevas_vacunas += array
    (
        ($num_nuevas_vacunas+1) => array($fecha,$dosisID,$n_dosis,$vacunaID,$_tv_intervalos[$DnD],$userID,$edad['anos'],$edad['meses'],$edad['dias'],$sedeID,$entidadID)
    );
}
?>