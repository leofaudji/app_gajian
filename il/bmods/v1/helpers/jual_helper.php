<?php defined('BASEPATH') OR exit('بسم الله الرحمن الرحيم');

function jual_status_faktur_text($status_faktur){
    $re = 'Belum Dikirim';
    switch($status_faktur){
        case '1':
            $re = 'Dikirim';
        break;
        case '2':
            $re = 'Diterima';
        break;
        case '3':
            $re = 'Ditagih';
        break;
        case '4':
            $re = 'Nota Kembali';
        break;
        case '5':
            $re = 'Lunas';
        break;
        case '-':
            $re = 'Belum Cetak';
        break;
    }

    return $re;
}

function jual_cara_bayar($cb){
    $re = '';
    switch($cb){
        case '0':
            $re = 'Piutang';
        break;
        case '1':
            $re = 'Cash';
        break;
        case '2':
            $re = 'BDT';
        break;
    }

    return $re;
}