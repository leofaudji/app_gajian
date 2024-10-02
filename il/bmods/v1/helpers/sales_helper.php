<?php defined('BASEPATH') OR exit('بسم الله الرحمن الرحيم');

function sales_tipe_text($tipe){
    $re = 'Batas Piutang';
    switch($tipe){
        case '1':
            $re = 'Target Piutang';
        break;
        case '2':
            $re = 'Target Pembayaran';
        break;
        case '3':
            $re = 'Batas Sales Order';
        break;
    }

    return $re;
}