<?php
$directorio = 'public_html/';
showFiles($directorio);

function showFiles($path)
{
    if (substr($path, -1) != '/') $path .= '/';
	echo "Leyendo directorio $path . \n";
	$dir = opendir($path);
    $files = array();
    while ($current = readdir($dir))
	{
        if( $current != "." && $current != "..") 
		{
            if(is_dir($path.$current))
			{
                echo "Leyendo directorio : " . $path.$current . "\n";
				showFiles($path.$current.'/');
            }
            else 
			{
                if (strpos($current, ".php") != False) $files[] = $path.$current;
            }
        }
    }

    for($i=0; $i < count( $files ); $i++)
	{
        modificar_consulta($files[$i]);
	}
}

function modificar_consulta($archivo)
{
    $codigo_original = "";
    $codigo_original = file_get_contents($archivo);	
    $cantidad_caracteres_originales = strlen($codigo_original);
    $codigo_modificado = $codigo_original;
        
    preg_match_all('/mysql_(.*);/', $codigo_original, $resultado);

    for($x = 0; $x <= count($resultado[0]); $x++)
    {
        $a =  $resultado[0][$x];

        $texto_final = str_replace('mysql_error', 'mysqli_error',$a);
        $texto_final = str_replace('mysql_fetch_array', 'mysqli_fetch_array',$texto_final);


        preg_match_all('/mysql_query\((.*?)\)/', $texto_final, $resultado_query);
        if(strlen($resultado_query[1][0]) > 0 )
        {
            $explode = explode(',',$resultado_query[1][0]);
            $texto_final = str_replace('mysql_query('.$resultado_query[1][0].')', 'mysqli_query('.$explode[1].','.$explode[0].')',$texto_final);    
        }

        $codigo_modificado = str_replace($a, $texto_final, $codigo_modificado);

    }

    $codigo_modificado = str_replace('mysql_', 'mysqli_',$codigo_modificado);    

    if ($codigo_modificado != $codigo_original) 
    {
        file_put_contents($archivo, $codigo_modificado);
        echo "El archivo " . $archivo . " es modificable \n";
    }
    else echo "El archivo " . $archivo . " NO FUE MODIFICADO \n	";

}

?>
