<?php
include "db.php";
$branches_array = [];
$branches = mysqli_query($conn, "SELECT br_id,br_name FROM branches");
while ($row = mysqli_fetch_array($branches)) {
    $branches_array[$row['br_name']] = $row['br_id'];
}
// print_r($branches_array);
// echo $branches_array['School of Agriculture'];
//SELECT financial_trans.admission_no,financial_trans.id,financial_trans.total_amount AS fin_total,common_fee_collection.id, common_fee_collection.total_amount AS com_total FROM financial_trans,common_fee_collection WHERE financial_trans.admission_no=common_fee_collection.admission_no
//student_data
//SELECT SUM(`due_amount`), SUM(`paid_amount`), SUM(`consession_amount`), SUM(`scholarship_amount`), SUM(`reverse_consesson_amount`), SUM(`write_off_amount`), SUM(`adjusted_amount`), SUM(`refund_amount`), SUM(`fund_tran_amount`)  FROM `student_deta`

// Financial
// REVERSE CONSESSION = SELECT SUM(total_amount) FROM `financial_trans` WHERE crdr="D" AND entry_mode=16
// CONSESSION SELECT SUM(total_amount) FROM `financial_trans` WHERE crdr="C" AND entry_mode=15 AND type_of_consession=1
// SCHOLERSHP SELECT SUM(total_amount) FROM `financial_trans` WHERE crdr="C" AND entry_mode=15 AND type_of_consession=2
// DUE SELECT SUM(total_amount) FROM `financial_trans` WHERE crdr ="D" AND entry_mode = 0
// WRITE OFF SELECT SUM(total_amount) FROM `financial_trans` WHERE crdr ="C" AND entry_mode = 12;

// Common Fee
// FUNDTRANSFER SELECT SUM(total_amount) FROM `common_fee_collection` WHERE entry_mode=1 AND inactive IS NULL
// ADJUSTED SELECT SUM(total_amount) FROM `common_fee_collection` WHERE entry_mode=14
// REFUND SELECT SUM(total_amount) FROM `common_fee_collection` WHERE entry_mode=1 AND (inactive=1 OR inactive=0)
// PAID SELECT SUM(total_amount) FROM `common_fee_collection` WHERE entry_mode=0 AND (inactive=1 OR inactive=0)
//SELECT financial_trans.total_amount FROM `financial_trans_details`,financial_trans WHERE financial_trans.crdr="D" AND financial_trans.entry_mode=16 AND financial_trans.id = financial_trans_details.finance_trans_id  GROUP BY financial_trans_details.finance_trans_id;


//SELECT financial_trans.id, financial_trans.total_amount FROM financial_trans WHERE (SELECT SUM(financial_trans_details.amount) FROM financial_trans_details WHERE financial_trans_details.finance_trans_id=financial_trans.id) != financial_trans.total_amount
$start_row = 1;
ini_set('memory_limit', '1024M');
if (($csv_file = fopen("sort/fundtransfer1.csv", "r")) !== false) {
    $c1 = 1;
    $skip = 6;
    while (($read_data = fgetcsv($csv_file, 1000, ",")) !== false) {
        if ($c1++ > $skip) {
            /*
            // Start Insert Into Fee_types table
            $br_id=$branches_array[$read_data[11]];
            $check_fee_type_exist= mysqli_query($conn, "SELECT id FROM fee_tyes where fee_head='".$read_data[16]."' AND br_id='".$br_id."'");
            $fees_count = mysqli_num_rows($check_fee_type_exist);
            if($fees_count == 0) {
                $insertquery_fee_types = "INSERT INTO `fee_tyes` (fee_head, br_id) VALUES ";
                $subquery_fee_types = "";
                $subquery_fee_types = $subquery_fee_types . " (";
                $subquery_fee_types = $subquery_fee_types . '\'' . $read_data[16] . '\',';
                $subquery_fee_types = $subquery_fee_types . '\'' . $br_id . '\'';
                $subquery_fee_types = substr($subquery_fee_types, 0, strlen($subquery_fee_types) - 1);
                $subquery_fee_types = $subquery_fee_types . '\')' . " , ";
                $insertquery_fee_types = $insertquery_fee_types . $subquery_fee_types;
                $insertquery_fee_types = substr($insertquery_fee_types, 0, strlen($insertquery_fee_types) - 2);
                if (mysqli_query($conn, $insertquery_fee_types)) {
                    $insertquery_fee_types = "INSERT INTO `fee_tyes` (fee_head, br_id) VALUES  ";
                    $subquery_fee_types = "";
                }
            }
            // End Insert Into Fee_types table
            */
            /*
            // Start Insert Into financial_trans table
            $due_amount=$read_data[17]; $consession_amount=$read_data[19]; $scholership_amount=$read_data[20]; $rev_consession_amount=$read_data[21]; $write_off_amount=$read_data[22];
           // echo "<br>due =".$due_amount."con =".$consession_amount;
            if($due_amount != 0 || $consession_amount != 0 || $scholership_amount != 0 || $rev_consession_amount != 0 || $write_off_amount != 0)
            {
               $check_fin_tran_exist= mysqli_query($conn, "SELECT total_amount,voucher_no FROM financial_trans where voucher_no='".$read_data[6]."'");
                $fin_tran_count = mysqli_num_rows($check_fin_tran_exist);
                if($fin_tran_count == 0) {
                    $insertquery_fina_tran = "INSERT INTO `financial_trans` (module_id, tran_id, admission_no, total_amount, crdr, tran_date, acadamic_year, entry_mode, voucher_no, br_id, type_of_consession) VALUES ";
                    $subquery_fina_tran = "";
                    $subquery_fina_tran = $subquery_fina_tran . " (";
                    if(preg_match('(Fine|FIne)', $read_data[16]) === 1) {
                        $module_id = 11;
                    }else if(preg_match('(Hostel)', $read_data[16]) === 1) {
                        $module_id = 2;
                    }else{
                        $module_id = 1;
                    }

                    $tran_id = random_int(100000, 999999);
                    $admission_no = $read_data[8];
                    if($due_amount != 0){
                        $total_amount = $due_amount;
                    }
                    else if($consession_amount != 0){
                        $total_amount = $consession_amount;
                    }
                    else if($scholership_amount != 0){
                        $total_amount = $scholership_amount;
                     }
                     else if($rev_consession_amount != 0){
                        $total_amount = $rev_consession_amount;
                     }
                     else if($write_off_amount != 0) {
                        $total_amount = $write_off_amount;
                     }


                    $entry_mode= mysqli_query($conn, "SELECT crdr,entry_mode_no FROM entry_mode where entry_modename='".$read_data[5]."'");
                    $result_entrymode = mysqli_fetch_array($entry_mode);
                    $entry_mode_count = mysqli_num_rows($entry_mode);
                    if($entry_mode_count > 0) {
                        $crdr = $result_entrymode['crdr'];
                        $entry_mode_no = $result_entrymode['entry_mode_no'];
                    }
                    else{
                        $crdr ="NULL";
                        $entry_mode_no ="NULL";
                    }

                    $tran_date = $read_data[1];
                    $acadamic_year = $read_data[2];
                    $voucher_no = $read_data[6];
                    $br_id=$branches_array[$read_data[11]];

                    if($consession_amount > 0)
                    $type_of_consession = 1;
                    else if($scholership_amount > 0)
                    $type_of_consession = 2;
                    else
                    $type_of_consession = 0;
                    $subquery_fina_tran = "(".$module_id .','.$tran_id.',"'.$admission_no.'",'.$total_amount.',"'.$crdr.'","'.$tran_date.'","'.$acadamic_year.'","'.$entry_mode_no.'","'.$voucher_no.'",'.$br_id.','.$type_of_consession.',';

                    $subquery_fina_tran = substr($subquery_fina_tran, 0, strlen($subquery_fina_tran) - 1);
                    $subquery_fina_tran = $subquery_fina_tran . ')' . " , ";

                    $insertquery_fina_tran = $insertquery_fina_tran . $subquery_fina_tran;
                    $insertquery_fina_tran = substr($insertquery_fina_tran, 0, strlen($insertquery_fina_tran) - 2);

                    if(mysqli_query($conn, $insertquery_fina_tran)) {
                        $insertquery_fina_tran = "INSERT INTO `financial_trans` (module_id, tran_id, admission_no, total_amount, crdr, tran_date, acadamic_year, entry_mode, voucher_no, br_id, type_of_consession) VALUES";
                        $subquery_fina_tran = "";
                    }
                }else{
                    $result_fin_tran = mysqli_fetch_array($check_fin_tran_exist);
                    $total_amount =$result_fin_tran['total_amount'];
                    $exist_voucher_no =$result_fin_tran['voucher_no'];
                    if($due_amount != 0){
                        $total_amount = $total_amount + $due_amount;
                    }
                    else if($consession_amount != 0){
                        $total_amount = $total_amount + $consession_amount;
                    }
                    else if($scholership_amount != 0){
                        $total_amount = $total_amount + $scholership_amount;
                     }
                     else if($rev_consession_amount != 0){
                        $total_amount = $total_amount + $rev_consession_amount;
                     }
                     else if($write_off_amount != 0) {
                        $total_amount = $total_amount + $write_off_amount;
                     }
                    $update_fin_tran= mysqli_query($conn, "UPDATE `financial_trans` SET `total_amount`='".$total_amount."' WHERE voucher_no='".$exist_voucher_no."'");
                }


                // Start Insert Into financial_trans_details table
                $check_fin_tran_details_exist= mysqli_query($conn, "SELECT id,module_id,crdr FROM financial_trans where voucher_no='".$read_data[6]."'");
                $fin_tran_count = mysqli_num_rows($check_fin_tran_details_exist);
                if($fin_tran_count > 0) {
                $insertquery_fina_tran_details = "INSERT INTO `financial_trans_details` (finance_trans_id, module_id, amount, head_id, crdr,br_id, head_name ,sr_no) VALUES ";
                $subquery_fina_tran_details = "";
                $subquery_fina_tran_details = $subquery_fina_tran_details . " (";
                $result_fin_tran_details = mysqli_fetch_array($check_fin_tran_details_exist);
                $fin_trans_id =$result_fin_tran_details['id'];
                $module_id =$result_fin_tran_details['module_id'];
                $crdr =$result_fin_tran_details['crdr'];
                $total_amount_in_child = $due_amount + $consession_amount + $scholership_amount + $rev_consession_amount + $write_off_amount;
                $br_id=$branches_array[$read_data[11]];

                $check_fee_type_exist= mysqli_query($conn, "SELECT id FROM fee_tyes where fee_head='".$read_data[16]."' AND br_id='".$br_id."'");
                $result_fee_types = mysqli_fetch_array($check_fee_type_exist);
                $head_id =$result_fee_types['id'];
                $head_name =$read_data[16];

                $sr_no =$read_data[0];
                $subquery_fina_tran_details = "(".$fin_trans_id .','.$module_id.',"'.$total_amount_in_child.'",'.$head_id.',"'.$crdr.'","'.$br_id.'","'.$head_name.'","'.$sr_no.'",';

                $subquery_fina_tran_details = substr($subquery_fina_tran_details, 0, strlen($subquery_fina_tran_details) - 1);
                $subquery_fina_tran_details = $subquery_fina_tran_details . ')' . " , ";

                $insertquery_fina_tran_details = $insertquery_fina_tran_details . $subquery_fina_tran_details;
                $insertquery_fina_tran_details = substr($insertquery_fina_tran_details, 0, strlen($insertquery_fina_tran_details) - 2);

                if (mysqli_query($conn, $insertquery_fina_tran_details)) {
                    $insertquery_fina_tran_details = "INSERT INTO `financial_trans_details` (finance_trans_id, module_id, amount, head_id, crdr,br_id, head_name, sr_no) VALUES  ";
                    $subquery_fina_tran_details = "";
                }
             }
             // End Insert Into financial_trans_details table
            }
            // End Insert Into financial_trans table
*/

            // Start Insert Into common_fee_collection table
            $paid_amount = $read_data[18];
            $adjusted_amount = $read_data[23];
            $refund_amount = $read_data[24];
            $fund_trans_amount = $read_data[25];
            // echo "<br>due =".$due_amount."con =".$consession_amount;
            if ($paid_amount != 0 || $adjusted_amount != 0 || $refund_amount != 0 || $fund_trans_amount != 0) {
                $check_com_fee_exist = mysqli_query($conn, "SELECT total_amount,voucher_no FROM common_fee_collection where voucher_no='" . $read_data[6] . "'");
                $com_fee_count = mysqli_num_rows($check_com_fee_exist);
                if ($com_fee_count == 0) {
                    $insertquery_com_fee = "INSERT INTO `common_fee_collection` (module_id, tran_id, admission_no,roll_no,voucher_no, total_amount, br_id,acadamic_year, financial_year, receipt_no, entry_mode, paid_date, inactive) VALUES ";
                    $subquery_com_fee = "";
                    $subquery_com_fee = $subquery_com_fee . " (";
                    if (preg_match('(Fine|FIne)', $read_data[16]) === 1) {
                        $module_id = 11;
                    } else if (preg_match('(Hostel)', $read_data[16]) === 1) {
                        $module_id = 2;
                    } else {
                        $module_id = 1;
                    }

                    $tran_id = rand(100000, 999999);
                    $admission_no = $read_data[8];
                    $roll_no = $read_data[7];
                    $voucher_no = $read_data[6];
                    //$total_amount = $paid_amount + $adjusted_amount + $refund_amount + $fund_trans_amount;
                    if ($paid_amount != 0) {
                        $total_amount = $paid_amount;
                    } else if ($adjusted_amount != 0) {
                        $total_amount = $adjusted_amount;
                    } else if ($refund_amount != 0) {
                        $total_amount = $refund_amount;
                    } else if ($fund_trans_amount != 0) {
                        $total_amount = $fund_trans_amount;
                    }

                    $br_id = $branches_array[$read_data[11]];
                    $acadamic_year = $read_data[2];
                    $financial_year = $read_data[2];
                    $receipt_no = $read_data[15];

                    $entry_mode = mysqli_query($conn, "SELECT entry_mode_no FROM entry_mode where entry_modename='" . $read_data[5] . "'");
                    $result_entrymode = mysqli_fetch_array($entry_mode);
                    //$entry_mode_no = $result_entrymode['entry_mode_no'];
                    $entry_mode_count = mysqli_num_rows($entry_mode);
                    if ($entry_mode_count > 0) {
                        $entry_mode_no = $result_entrymode['entry_mode_no'];
                    } else {
                        $entry_mode_no = "NULL";
                    }

                    $paid_date = $read_data[1];

                    if ($paid_amount != 0) {
                        if ($read_data[5] == "RCPT")
                            $inactive = 0;
                        else if ($read_data[5] == "REVRCPT")
                            $inactive = 1;
                    } else if ($adjusted_amount != 0) {
                        if ($read_data[5] == "JV")
                            $inactive = 0;
                        else if ($read_data[5] == "REVJV")
                            $inactive = 1;
                    } else if ($refund_amount != 0) {
                        if ($read_data[5] == "PMT")
                            $inactive = 0;
                        else if ($read_data[5] == "REVPMT")
                            $inactive = 1;
                    } else {
                        $inactive = "NULL";
                    }

                    $subquery_com_fee = "(" . $module_id . ',' . $tran_id . ',"' . $admission_no . '","' . $roll_no . '","' . $voucher_no . '","' . $total_amount . '","' . $br_id . '","' . $acadamic_year . '","' . $financial_year . '","' . $receipt_no . '","' . $entry_mode_no . '","' . $paid_date . '",' . $inactive . ',';

                    $subquery_com_fee = substr($subquery_com_fee, 0, strlen($subquery_com_fee) - 1);
                    $subquery_com_fee = $subquery_com_fee . ')' . " , ";

                    $insertquery_com_fee = $insertquery_com_fee . $subquery_com_fee;
                    $insertquery_com_fee = substr($insertquery_com_fee, 0, strlen($insertquery_com_fee) - 2);
                    //echo "<br> Inserted common_fee_collection".$insertquery_com_fee;
                    if (mysqli_query($conn, $insertquery_com_fee)) {
                        $insertquery_com_fee = "INSERT INTO `common_fee_collection` (module_id, tran_id, admission_no,roll_no,voucher_no, total_amount, br_id,acadamic_year, financial_year, receipt_no, entry_mode, paid_date, inactive) VALUES";
                        $subquery_com_fee = "";
                    }
                } else {
                    // echo "<br> Updated common_fee_collection".$admission_no;
                    $result_com_fee = mysqli_fetch_array($check_com_fee_exist);
                    $total_amount = $result_com_fee['total_amount'];
                    $exist_voucher_no = $result_com_fee['voucher_no'];
                    //$total_amount = $total_amount + $paid_amount + $adjusted_amount + $refund_amount + $fund_trans_amount;
                    if ($paid_amount != 0) {
                        $total_amount = $total_amount + $paid_amount;
                    } else if ($adjusted_amount != 0) {
                        $total_amount = $total_amount + $adjusted_amount;
                    } else if ($refund_amount != 0) {
                        $total_amount = $total_amount + $refund_amount;
                    } else if ($fund_trans_amount != 0) {
                        $total_amount = $total_amount + $fund_trans_amount;
                    }
                    $update_com_fee = mysqli_query($conn, "UPDATE `common_fee_collection` SET `total_amount`='" . $total_amount . "' WHERE voucher_no='" . $exist_voucher_no . "'");
                }


                // Start Insert Into common_fee_collection_details table
                $check_com_fee_details_exist = mysqli_query($conn, "SELECT id,module_id FROM common_fee_collection where voucher_no='" . $read_data[6] . "'");
                $com_fee_count = mysqli_num_rows($check_com_fee_details_exist);
                if ($com_fee_count > 0) {
                    $insertquery_com_fee_details = "INSERT INTO `common_fee_collection_details` (common_fee_collection_id,module_id, head_id, head_name,br_id, amount,sr_no) VALUES ";
                    $subquery_com_fee_details = "";
                    $subquery_com_fee_details = $subquery_com_fee_details . " (";
                    $result_com_fee_details = mysqli_fetch_array($check_com_fee_details_exist);
                    $com_fees_id = $result_com_fee_details['id'];
                    $module_id = $result_com_fee_details['module_id'];
                    //$total_amount_in_child = $paid_amount + $adjusted_amount + $refund_amount + $fund_trans_amount;
                    if ($paid_amount != 0) {
                        $total_amount_in_child = $paid_amount;
                    } else if ($adjusted_amount != 0) {
                        $total_amount_in_child = $adjusted_amount;
                    } else if ($refund_amount != 0) {
                        $total_amount_in_child = $refund_amount;
                    } else if ($fund_trans_amount != 0) {
                        $total_amount_in_child = $fund_trans_amount;
                    }
                    $br_id = $branches_array[$read_data[11]];

                    $check_fee_type_exist = mysqli_query($conn, "SELECT id FROM fee_tyes where fee_head='" . $read_data[16] . "' AND br_id='" . $br_id . "'");
                    $result_fee_types = mysqli_fetch_array($check_fee_type_exist);
                    $head_id = $result_fee_types['id'];
                    $head_name = $read_data[16];
                    $sr_no = $read_data[0];
                    $subquery_com_fee_details = "(" . $com_fees_id . ',' . $module_id . ',"' . $head_id . '","' . $head_name . '",' . $br_id . ',"' . $total_amount_in_child . '","' . $sr_no . '",';

                    $subquery_com_fee_details = substr($subquery_com_fee_details, 0, strlen($subquery_com_fee_details) - 1);
                    $subquery_com_fee_details = $subquery_com_fee_details . ')' . " , ";

                    $insertquery_com_fee_details = $insertquery_com_fee_details . $subquery_com_fee_details;
                    $insertquery_com_fee_details = substr($insertquery_com_fee_details, 0, strlen($insertquery_com_fee_details) - 2);

                    //echo "<br> Inserted common_fee_collection_details".$insertquery_com_fee_details;
                    if (mysqli_query($conn, $insertquery_com_fee_details)) {
                        $insertquery_com_fee_details = " INSERT INTO `common_fee_collection_details` (common_fee_collection_id,module_id, head_id, head_name,br_id, amount,sr_no) VALUES";
                        $subquery_com_fee_details = "";
                    }
                }
                // End Insert Into common_fee_collection_details table
            }
            // End Insert Into common_fee_collection table
        }
        echo "<br>Last record of Sr=" . $read_data[0];
    }
    //  echo $insertquery;
    fclose($csv_file);
    //mysqli_close($conn);
}
echo "</table>";
