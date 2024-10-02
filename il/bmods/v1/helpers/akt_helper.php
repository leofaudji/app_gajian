<?php defined('BASEPATH') OR exit('بسم الله الرحمن الرحيم');

function akt_jenis($tipe){
    $re = '';
    switch($tipe){
        case 'U':
            $re = 'Uang Muka';
        break;
        case 'P':
            $re = 'Porsekot';
        break;
        case 'D':
            $re = 'Deposit';
        break;
    }

    return $re;
}

function akt_dk($tipe){
    $re = '';
    switch($tipe){
        case 'D':
            $re = 'Debet';
        break;
        case 'K':
            $re = 'Kredit';
        break;
    }

    return $re;
}
