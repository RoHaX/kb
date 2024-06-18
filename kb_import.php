<?php
$row = 1;
if (($handle = fopen($_FILES['csv_file']['tmp_name'], "r")) !== FALSE) {

    $header = array('Datum', 'Betreff', 'Betrag', 'Icons');
    $width = array('80','200','50','100');

    $num = 4;
    //Column headers and title
    for ($c=0; $c < $num; $c++) {
        $col_head[] = array("data" => $header[$c], "title" => $header[$c], "width" => $width[$c], "visible" => true );
    }
    //zeilenweise einlesen der CSV-Datei
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        
        // Elba Datum: 2, Zahlungsreferenz: 1, Betrag: 3
        $arr_tmp = array();
        //Datum
        $str = strval($data[2]);
        $str = str_replace(" ", "", $str);
        $str_val = date_create($str)->format('Y-m-d'); 
        $arr_tmp[$header[0]] = $str_val;
        $str_val = $data[1];

        // Zahlungsreferenz: ...
        if (substr($str_val, 0, 17) == "Zahlungsreferenz:") {
            $str_val = str_replace("Zahlungsreferenz: ", "", $str_val);
        }
        //ONLINE BANKING VOM...
        if (substr($str_val, 0, 18) == "ONLINE BANKING VOM") {
            $pos1 = strpos($str_val, "Empfänger:");
            $pos1 = $pos1 + strlen("Empfänger: "); // Position + Em
            $pos2 = strpos($str_val, "Zahlungsreferenz:");
            $pos3 = $pos2 + strlen("Zahlungsreferenz: ");
            
            $pos2 = $pos2 - $pos1;
            if ($pos2 < 0) {
                $pos2 = strpos($str_val, "Verwendungszweck:");
                $pos3 = $pos2 + strlen("Verwendungszweck: ");
                $pos2 = $pos2 - $pos1;
        }

            $pos4 = strpos($str_val, " IBAN ");
            $pos4 = $pos4 - $pos3;
            $str_tmp = substr($str_val, $pos1, $pos2) . " " . substr($str_val,$pos3,$pos4);
            //$str_val = strval($pos1) . " " . strval($pos2);
            $str_val = $str_tmp;
        }

        $arr_tmp[$header[1]] = $str_val;
        $arr_tmp[$header[2]] = $data[3];
        $arr_tmp[$header[3]] = '<button type="button" name="take" class="btn btn-warning btn-sm update"><i class="far fa-edit"></i></button>';

        $data_csv[] = $arr_tmp;
        unset($arr_tmp);
    }

    /* ELBA */
    /*
    $header = array('Datum', 'Betreff', 'Datum2', 'Betrag', 'Währung', 'Zeitstempel');
    $hidden_columns = array("Währung", "Zeitstempel");
    $num = 6;

    for ($c=0; $c < $num; $c++) {
        if (in_array($header[$c], $hidden_columns)) {                    
            $visible = false;
        } else {
            $visible = true;
        }
        $col_head[] = array("data" => $header[$c], "title" => $header[$c], "visible" => $visible );
    }

    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        // Elba 
        $arr_tmp = array();
        for ($c=0; $c < $num; $c++) {
            if ($header[$c] == 'Datum' || $header[$c] == 'Datum2') {
                $str = strval($data[$c]);
                $str = str_replace(" ", "", $str);
                //echo "x" . $str . "x";
                //$str_val = $data[$c];
                $str_val = date_create($str)->format('Y-m-d'); 
            } else {
                $str_val =  $data[$c];
            }
            $arr_tmp[$header[$c]] = $str_val;
            
        }
        $data_csv[] = $arr_tmp;
        unset($arr_tmp);
*/
        /* Sparkasse */
        /*
        if ($row == 1 ) {
            $num = count($data);
            $header = $data;
            $hidden_columns = array("Partner IBAN", "Partner IBAN", "BIC/SWIFT", "Partner Kontonummer", "Bankleitzahl", "Eigener Kontoname", "Eigene IBAN" );
            for ($c=0; $c < $num; $c++) {

                //if ($data[$c] == "Partner IBAN") {
                if (in_array($data[$c], $hidden_columns)) {                    
                    $visible = false;
                } else {
                    $visible = true;
                }

                $col_head[] = array("data" => $data[$c], "title" => $data[$c], "visible" => $visible );
                // echo $data[$c] . "<br />\n";
            }
            
        } else {
            
            $arr_tmp = array();
            for ($c=0; $c < $num; $c++) {
                //echo $data[$c] . "<br />\n";

                $arr_tmp[$header[$c]] = $data[$c];
                //$data_csv[$row][$header[$c] ] = $data[$c];
            }
            $data_csv[] = $arr_tmp;
            unset($arr_tmp);

        }
        

        $row++;
        
    }
        */
/*    
    fclose($handle);
    echo "<pre>";
    print_r($header);
    echo "</pre>";

    echo "DATEN:";
    echo "<pre>";
    print_r($data_csv);
    echo "</pre>";
*/

}


$result['column_count'] = $num;
$result['column'] = $col_head;
$result['data'] = $data_csv;
header('Content-type: application/json');
echo json_encode($result);

?>