<?php exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم '); ?>
//(0) Nama, (1) Obj, (2) Lokasi (folder pertama adalah folder utama), (3) Icon
//(4) Default false jika lokasi tidak sama maka folder utama akan dihilangkan agar dapat mengakses ke html
("Dasbor","d","v1/dash/d","fa fa-dashboard")
("Karyawan", "_kry","","fa fa-user")
	("Master", "_mstkry")
		("Master Kantor", "mcbg", "v1/kry/mktr")
		("Master Dati 1 (Provinsi)", "mdati1", "v1/kry/mdati1")
		("Master Dati 2 (Kota / Kab)", "mdati2", "v1/kry/mdati2")
		("Master Agama", "kryagm", "v1/kry/kryagm")
		("Master Pendidikan", "krypen", "v1/kry/krypen")
		("Master Divisi", "krydiv", "v1/kry/krydiv")
		("Master Jabatan", "kryjab", "v1/kry/kryjab")
	("Dashboard Karyawan", "krydat", "v1/kry/krydat")
	//("Perubahan Data Karyawan", "kryedi", "v1/kry/kryedi")
("Absensi", "_abs","","fa fa-calendar")
	("Master", "_mstabs")
		("Master Metode Absensi", "absmet", "v1/abs/absmet") 
		("Master Golongan Absensi", "absgol", "v1/abs/absgol")
		("Master Status Absensi", "abssts", "v1/abs/abssts")
		("Konfigurasi Absensi ", "abskfg", "v1/abs/abskfg")
	("Dashboard Absensi", "absdas", "v1/abs/absdas") 
	//("Data Pengajuan Izin/Lembur", "absizn", "v1/abs/absizn")
("Payroll Gaji", "_gj","","fa fa-money")
	("Master", "_mstgj")
		("Master Periode Gaji", "gjmper", "v1/gj/gjmper")
		("Master Golongan Gaji", "gjgol", "v1/gj/gjgol")
		("Master Komponen Gaji", "gjkom", "v1/gj/gjkom") 
	("Konfigurasi Gaji ", "gjkfg", "v1/gj/gjkfg")
	("Konfigurasi Periode", "gjper", "v1/gj/gjper")
	("Input Payroll", "gjpay", "v1/gj/gjpay")
	("Dashboard Gaji", "gjdrft", "v1/gj/gjdrft")   
("Konfig","_c","","fa fa-gears")
    ("Level","usr_lv","v1/cfg/usr_lv")  
    ("Pengguna","usr","v1/cfg/usr")
("AI","ai","v1/ai/aidas","fa fa-bolt")  