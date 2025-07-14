/**
 * Main.js 
 * for Pages Themeplate
 * 
 * @copyright   CV. ILMION KREATIF - ILMION STUDIO
 * @link        https://ilmion.com
 * @email       hi@ilmion.com | ilmionstudio@gmail.com
 * @author      Mirza Ramadhany (amir.ramadhany@gmail.com)
 * @version     0.0.1
 * @since       17 Aug 18
 */

if($.fn.datepicker !== undefined){
    $.fn.datepicker.defaults.format = "dd-mm-yyyy";
}

br  = new restApi({
    headers: {"Accept": "application/json; charset=utf-8"},
    url: burls,
    error: function(req, status, ex){
        // jika code 401 maka harus logout
        if(req.status == 401){
            app.logout() ; 
        }
    }
}) ;  

/**
 * CONST untuk app
 */
const app   = {} ; 
/**
 * setting user
 */
app.setting_user   = function(){
    bj.form( JSON.parse('{"module":"v1","name":"Profil","md5":"f208a6d6c8e5926c64e1f891ebb93a8c","obj":"me","loc":"cfg/me","path":"v1/cfg/me","icon":"ME"}') ) ; 
}


/**
 * Logout function 
 * 
 * @access  public
 * @return  bool    1. Remove localstorage
 *                  2. Back to login page
 */
app.logout  = function(){
    ///*
    br.patch({
        path : 'v1/auth/logout',
        success: function(){
            app.logoutGo() ;            
        },
        error: function(){
            app.logoutGo() ;            
        } 
    });
    //*/
}
app.logoutGo    = function(){
    localStorage.removeItem('BismillahSuksesDuniaAkhirat'); 
    localStorage.removeItem('e17e08b17cd287f2107e2c42dd440b8d') ;  // ujain auth
    localStorage.removeItem('e243e7cc3cca780475430d3925a44998') ; // ujian id

    // remove all cookie
    var cookies = document.cookie.split(";");

    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i];
        var eqPos = cookie.indexOf("=");
        var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
        document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
    }

    window.location.href = burl + "login.html";
}  

/**
 * Create menu html
 * 
 * @param   json menus
 * @return  string html
 */
app.menu_select = '';
app.menu_html   = function(menus){
    var html    = '' ;
    var n       = 0 ;
    
    $.each(menus, function(i,v){
        
        var hvch= v.child !== undefined; 
        if(hvch){ 
            // cek child pertama
            // jika child pertama memiliki titik maka tidak dianggap memiliki child
            if(v.child[0] !== undefined){
                if( (v.child[0].o.obj).indexOf(".") > -1 ){
                    hvch    = false;
                }
            }else{
                hvch    = false;
            }
        }

        var link= hvch ? 'href=""' : 'href="#' + btoa(v.o.loc) + '"' ;
        link    = v.o.loc.indexOf(':') == 0 ? 'href="src/'+v.o.loc.replace(":","")+'.html" target="new"' : link;

        var j   = hvch ? "" : JSON.stringify(v.o) ;

        var c_a = hvch ? 'class="nav-link dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false"' : 'class="nav-link"' ;

        html   += (hvch) ? '<li class="nav-item dropdown" onclick=app.menu_set_active(this)>' : '<li class="nav-item" onclick=app.menu_set_active(this)>';  
        html   +=   '<a '+c_a+' '+link+' data-obj=\''+j+'\'>';
        if(v.icon != 'pg-folder'){
            html += '<span class="nav-link-icon d-md-none d-lg-inline-block"><i class="'+v.icon+'"></i></span>';
        }

        html   +=       '<span class="nav-link-title">'+v.name+'</span>';
        html   +=   '</a>';
        if(hvch) html += app.menu_html_c(v.child,true); 
        html   += '</li>';

        n++ ;  
    }) ; 

    return html ; 
}
/**
 * for childern
 * 
 * @param   string html 
 * @param   json menus
 */
app.menu_html_c   = function(menus,chld=true){ 
    var html    = (chld)?'<div class="dropdown-menu"><div class="dropdown-menu-columns"><div class="dropdown-menu-column">' : '<div class="dropdown-menu">';
    // var html    = '<div class="dropdown-menu">';
    
    //console.log(menus);
    $.each(menus, function(i,v){
        var hvch= v.child !== undefined; 
        if(hvch){ 
            // cek child pertama
            // jika child pertama memiliki titik maka tidak dianggap memiliki child
            if(v.child[0] !== undefined){
                if( (v.child[0].o.obj).indexOf(".") > -1 ){
                    hvch    = false;
                }
            }else{
                hvch    = false;
            }
        }
        // if(hvch) console.log(v) ; 
        var link= hvch ? 'href="#"' : 'href="#' + btoa(v.o.loc) + '"' ;
        link    = v.o.loc.indexOf(':') == 0 ? 'href="src/'+v.o.loc.replace(":","")+'.html" target="new"' : link;
        html   += (hvch) ? '<div class="dropend">':''; 
        if(hvch){
            html   += '<a class="dropdown-item dropdown-toggle" '+link+' data-obj=\'\' data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">';
        }else{
            html   += '<a class="dropdown-item" '+link+' data-obj=\''+JSON.stringify(v.o)+'\'>';
        }
        html   +=       v.name;
        html   +=   '</a>';
        
        if(hvch) html += app.menu_html_c(v.child,false) ; 
        
        html   += (hvch) ? '</div>':'';  
        
    }) ;   
    html   += (chld)?'</div></div></div>':'</div>'; 
    //html   += '</div>'; 
 
    return html ; 
}

app.li_active = null;
app.menu_set_active = function(obj_menu){
    app.li_active = obj_menu;
    
}

app.menu_select_opt     = function(hvch, v){
    if(hvch){ 
        app.menu_select += '<optgroup label="'+v.name+'">';
    }else{
        app.menu_select += '<option value="#'+btoa(v.o.loc)+'">'+v.name+'</option>';
    }
}

/**
 * 1. Pertama kali reload harus mengambil menu dan data aplikasi
 * 2. Lakukan refresh token setiap 18 menit (ping)
 * 3. Cek notifikasi setiap 10 menit
 * 4. Logout otomatis setiap 10 menit jika tidak ada transaksi
 */

app.param       = {mnotif: 0, idle: 0, mrefresh:0};
app.interval    = function(){
    /** 
     * Time per minutes
     * function to 
     * 1. Logout if not any work in 10 minutes
     * 2. Notification every 10 minutes
     * 3. Refresh token every 18 minutes
     */
    
    app.param.mnotif++;
    app.param.mrefresh++;
    app.param.idle++;
    console.log("CV Ilmion Creative بسم الله الرحمن الرحيم " + app.param.idle); 
    if(app.param.idle >= 50){
        // 1. Logout if not any work in 10 minutes
        app.logout() ;
    }

    if(app.param.mnotif%10 == 0){
        // 2. Notification every 10 minutes 
        //app.notif() ;
        app.param.mnotif = 0;
    }

    if(app.param.mrefresh >= 18){
        // 3. Refresh token every 18 minutes
        app.refresh_token();

        // set minute to 0
        app.param.mrefresh    = 0;
    }
}

/** 
 * Get Notif
 */
app.notif   = function(){
    br.get({
        path : 'v1/auth/notif/' + app.walas.id,
        success: function(d){
            $.each(d, function(i, r){
                app.notif_set(r);
            });    
        }
    });
}

app.notif_set   = function(r){
    var ops = {
        style: 'simple',
        timeout: 10000,
        type: 'info',
        position: 'top'
    }
    
    $.extend(true, ops, r);
    if(ops.style == 'simple'){
        ops.position  = 'top-right';
    }
    $('.page-content-wrapper').pgNotification(ops).show();
}
/**
 * Refresh token
 */
app.refresh_token   = function(){
    br.patch({
        path : 'v1/auth/refresh'
    }) ;
    console.log('refresh token');
}

app.set_content     = function(){
    var bhash   = window.location.hash.substr(1) ; 
    if(bhash == "logout"){
        app.logout();   
    }else if(bhash !== ''){
        var aobj    = $('a[href="#'+bhash+'"]'); 
        // open form
        bj.form( JSON.parse(aobj.attr("data-obj")) );
 
        // change menu to active
        // $("ul li").find("a").attr("onClick", "");
        $(".navbar-nav li").removeClass("active") ;

        
        $(".navbar-nav li a").removeClass("show") ;
        $(".navbar-nav li div").removeClass("show") ;

        aobj.attr("onClick", "window.location.hash=''") ; 

        if(app.li_active == null){
            aobj.parent("li").addClass("active") ; 
        }else{
            $(app.li_active).addClass("active") ; 
        }
        console.log(aobj);
    }
}

var elfinder = {};
app.elfpicker = function(url, obj, id, par){
    if(par == undefined){
        par = '';
    } else{
        par = '?' + par;
    }
    elfinder.url = url + par;
    elfinder.obj = obj;
    elfinder.id = id;    
    
    $('#mdl-elfinder').modal('show');
}

br.get({
    path : 'v1/auth/info',
    success: function(d){
        //extend app
        $.extend(true, app, d) ;

        // create menu
        $("#bmenu").html(app.menu_html(d.menus)) ;
        $("#fullname").html(d.aruser.fullname) ;
        $("#lev").html(d.lvname) ;

        $("#fotouser").css({"background-image": "url("+d.aruser.datas.foto+")"});          

        if(window.location.hash == ''){
            bj.form( JSON.parse('{"loc": "dash/d", "md5": "2c6b66b825049d2e0a788ba24e0c4d9c", "obj": "d", "icon": "fa fa-dashboard", "name": "Dashboard", "path": "v1/dash/d", "module": "v1"}') ) ; 
        }else{
            app.set_content();
            /**
             * If refresh time must be refresh token
             */
            app.refresh_token();
        }

        // notif 
        app.interval();
        var appinterval     = setInterval( app.interval, 60000);
    },   
    error:function(d){
        // logout
        app.logout();  
    }
});
 
/**
 * If Url hash change # 
 */
$(window).bind('hashchange', function(){
    app.set_content();
}) ; 

/** window focus / blur get info */
$(window).on('focus blur', function(e){
    if($(this).data("prevType") !== e.type){
        if(e.type == 'focus'){
            app.param.idle = 0;
            app.param.mrefresh = 16;
            br.get({
                path : 'v1/auth/info',
                success: function(d){
                    //extend app
                    $.extend(true, app, d);
                },   
                error:function(d){
                    // logout
                    app.logout() ;  
                },  
            }) ; 
        }
    }
});

/**
 * Datatable Error throw
 */
// $.fn.dataTable.ext.errMode = function(conf, help, message){
//     // logout not auth
//     if( conf.jqXHR.status == 401 ){
//         app.logout() ;     
//     }else{
//         console.log(message) ;
//     }
// };

$.fn.datepicker.defaults.autoclose = true;
$.fn.datepicker.defaults.todayHighlight = true;

var mdl_barang = {}
mdl_barang.var = {'kode': '', 'kd_gudang': ''};
mdl_barang.init = function(bsearch){
    if(w2ui['nm_grbarang'] !== undefined){
        w2ui['nm_grbarang'].destroy(); //hancurkan grid biar bisa dilihat lagi
    }
    
    $("#_grbarang").w2grid({
        name : 'nm_grbarang',
        header: 'Silahkan Pilih Barang',
        limit : 20 ,
        url : burls + 'v1/cons/mdl_gr_barang',
        postData: {'bsearch': bsearch} ,
        show:{
            header : true,
            footer : true,
            toolbar : true,
            toolbarColumns : false
        },
        multiSearch	: false,
        columns: [
            { field: 'pilih', caption: ' ', size: '80px', sortable: false },
            { field: 'kode', caption: 'Kode', size: '100px', sortable: false},
            { field: 'nama', caption: 'Nama Barang', size: '350px', sortable: false },
            { field: 'kd_satuan', caption: 'Satuan', size: '80px', sortable: false },
            { field: 'stok', caption: 'Stok', size: '100px', render: 'float:2', sortable: false, style:'text-align:right' },
            { field: 'hj', caption: 'Harga Jual', size: '100px', render: 'int', sortable: false, style:'text-align:right' },
        ],
        onKeydown: function(event) {
            if(event.originalEvent.keyCode == 13){
                eval(w2ui['nm_grbarang'].get(mdl_barang.recid).pilih_f);
            }
        }, 
        onClick: function(event){
            mdl_barang.recid = event.recid;
        },
        onDblClick: function(event){
            mdl_barang.recid = event.recid;
            eval(w2ui['nm_grbarang'].get(mdl_barang.recid).pilih_f);
        },
        onRender: function(event){
            setTimeout(function(){
                $('#grid_nm_grbarang_search_all').focus();
                $('#grid_nm_grbarang_search_all').on('keydown', function(e) {
                    if(e.keyCode === 40) { 
                        $('#grid_nm_grbarang_search_all').blur();
                        $('#grid_nm_grbarang_records tbody tr:eq(2)').click();
                        $('#grid_nm_grbarang_focus').focus();
                    } 
                });
            }, 100);
        }
    });

    
}

mdl_barang.pilih = function(kd, kd_gudang){
    if(kd_gudang == undefined) kd_gudang = '';

    mdl_barang.var = {'kode': kd, 'kd_gudang': kd_gudang};
    mo['mdl_barang'].modal('hide');
}

mdl_barang.open = function(_cb, bsearch){
    mdl_barang.var = {'kode': '', 'kd_gudang': ''};
    if(bsearch == undefined) bsearch = {};

    this.html = '<div id="_grbarang" style="height:500px; margin-left: -15px; margin-right: -15px;"></div>';
    bj.modal({
        title: '&nbsp;',
        html: mdl_barang.html,
        size: 400,
        fshow: function(){
            mdl_barang.init(bsearch)
        },
        fhide:function(){
            if(mdl_barang.var.kode !== ''){
                br.get({
                    path : 'v1/cons/mdl_gr_barang/' + mdl_barang.var.kode + '/' + mdl_barang.var.kd_gudang + '/' + (bsearch.lso !== undefined ? 1 : 0),
                    success: function(r){
                        if(Object.keys(r).length > 0){
                            _cb(r);
                        }
                    }  
                }) ;
            }
        },
        name: 'mdl_barang'
    });
}

var mdl_pr = {}
mdl_pr.faktur = 0;
mdl_pr.init = function(){
    if(w2ui['nm_grpr'] !== undefined){
        w2ui['nm_grpr'].destroy(); //hancurkan grid biar bisa dilihat lagi
    }
    
    $("#_grpr").w2grid({
        name : 'nm_grpr',
        header: 'Silahkan Pilih Faktur PR',
        limit : 20 ,
        url : burls + 'v1/cons/gr_pr',
        postData: {} ,
        show: {
            header : true,
            footer : true,
            toolbar : true,
            toolbarColumns : false
        },
        multiSearch	: false,
        columns: [
            { field: 'pilih', caption: ' ', size: '80px', sortable: false },
            { field: 'faktur', caption: 'Faktur', size: '150px', sortable: false},
            { field: 'tgl', caption: 'Tanggal', size: '100px', sortable: false },
            { field: 'gudang', caption: 'Gudang', size: '100px', sortable: false }                    
        ]
    });
}

mdl_pr.pilih = function(faktur){
    mdl_pr.faktur = faktur;
    mo['mdl_pr'].modal('hide');
}

mdl_pr.open = function(_cb){
    this.html = '<div id="_grpr" style="height:500px; margin-left: -15px; margin-right: -15px;"></div>';
    bj.modal({
        title: '&nbsp;',
        html: mdl_pr.html,
        size: 400,
        fshow: mdl_pr.init,
        fhide:function(){
            br.get({
                path : 'v1/cons/pr/' + mdl_pr.faktur,
                success: function(r){
                    if(Object.keys(r).length > 0){
                        _cb(r);
                    }
                }  
            }) ;
        },
        name: 'mdl_pr'
    });
}

var mdl_po = {}
mdl_po.faktur = 0;
mdl_po.init = function(){
    if(w2ui['nm_grpo'] !== undefined){
        w2ui['nm_grpo'].destroy(); //hancurkan grid biar bisa dilihat lagi
    }
    
    $("#_grpo").w2grid({
        name : 'nm_grpo',
        header: 'Silahkan Pilih Faktur PO',
        limit : 20 ,
        url : burls + 'v1/cons/gr_po',
        postData: {} ,
        show: {
            header : true,
            footer : true,
            toolbar : true,
            toolbarColumns : false
        },
        multiSearch	: false,
        columns: [
            { field: 'pilih', caption: ' ', size: '80px', sortable: false },
            { field: 'faktur', caption: 'Faktur', size: '100px', sortable: false},
            { field: 'tgl', caption: 'Tanggal', size: '100px', sortable: false },
            { field: 'supplier', caption: 'Supplier', size: '100px', sortable: false },
            { field: 'gudang', caption: 'Gudang', size: '100px', sortable: false }                    
        ]
    });
}

mdl_po.pilih = function(faktur){
    mdl_po.faktur = faktur;
    mo['mdl_po'].modal('hide');
}

mdl_po.open = function(_cb){
    this.html = '<div id="_grpo" style="height:500px; margin-left: -15px; margin-right: -15px;"></div>';
    bj.modal({
        title: 'Data Purchase Order (PO)',
        html: mdl_po.html,
        size: 400,
        fshow: mdl_po.init,
        fhide:function(){
            br.get({
                path : 'v1/cons/po/' + mdl_po.faktur,
                success: function(r){
                    if(Object.keys(r).length > 0){
                        _cb(r);
                    }
                }  
            }) ;
        },
        name: 'mdl_po'
    });
}


var mdl_so = {}
mdl_so.faktur = 0;
mdl_so.init = function(){
    if(w2ui['nm_grso'] !== undefined){
        w2ui['nm_grso'].destroy(); //hancurkan grid biar bisa dilihat lagi
    }
    
    $("#_grso").w2grid({
        name : 'nm_grso',
        header: 'Silahkan Pilih Faktur SO',
        limit : 20 ,
        url : burls + 'v1/cons/gr_so',
        postData: {} ,
        show: {
            header : true,
            footer : true,
            toolbar : true,
            toolbarColumns : false
        },
        multiSearch	: false,
        columns: [
            { field: 'pilih', caption: ' ', size: '80px', sortable: false },
            { field: 'faktur', caption: 'Faktur', size: '150px', sortable: false},
            { field: 'tgl', caption: 'Tanggal', size: '100px', sortable: false },
            { field: 'sales', caption: 'Sales', size: '100px', sortable: false },
            { field: 'pelanggan', caption: 'Pelanggan', size: '300px', sortable: false },
            { field: 'gudang', caption: 'Gudang', size: '100px', sortable: false }                    
        ]
    });
}

mdl_so.pilih = function(faktur){
    mdl_so.faktur = faktur;
    mo['mdl_so'].modal('hide');
}

mdl_so.open = function(_cb){
    this.html = '<div id="_grso" style="height:500px; margin-left: -15px; margin-right: -15px;"></div>';
    bj.modal({
        title: '&nbsp;',
        html: mdl_so.html,
        size: 400,
        fshow: mdl_so.init,
        fhide:function(){
            br.get({
                path : 'v1/cons/so/' + mdl_so.faktur,
                success: function(r){
                    if(Object.keys(r).length > 0){
                        _cb(r);
                    }
                }  
            }) ;
            mdl_so.faktur = '';
        },
        name: 'mdl_so'
    });
}

var mdl_pbsup = {}
mdl_pbsup.gr_data = {}; 
mdl_pbsup.faktur = 0;
mdl_pbsup.init = function(){
    if(w2ui['nm_grpbsup'] !== undefined){
        w2ui['nm_grpbsup'].destroy(); //hancurkan grid biar bisa dilihat lagi
    }
    
    $("#_grpbsup").w2grid({
        name : 'nm_grpbsup',
        header: 'Silahkan Pilih Faktur Pembelian',
        limit : 20 ,
        url : burls + 'v1/cons/gr_pbsup',
        postData: mdl_pbsup.gr_data ,
        show: {
            header : true,
            footer : true,
            toolbar : true,
            toolbarColumns : false
        },
        multiSearch	: false,
        columns: [
            { field: 'pilih', caption: ' ', size: '80px', sortable: false },
            { field: 'faktur', caption: 'Faktur', size: '150px', sortable: false},
            { field: 'tgl', caption: 'Tanggal', size: '100px', sortable: false },
            { field: 'total', caption: 'Total', size: '120px', sortable: false,render:'float:2' },                    
            { field: 'supplier', caption: 'Supplier', size: '100%', sortable: false },                    
        ]
    });
}

mdl_pbsup.pilih = function(faktur){
    mdl_pbsup.faktur = faktur;
    mo['mdl_pbsup'].modal('hide');
}

mdl_pbsup.open = function(_cb){
    this.html = '<div id="_grpbsup" style="height:500px; margin-left: -15px; margin-right: -15px;"></div>';
    bj.modal({
        title: '&nbsp;',
        html: mdl_pbsup.html,
        size: 400,
        fshow: mdl_pbsup.init,
        fhide:function(){
            br.get({
                path : 'v1/cons/pbsup/' + mdl_pbsup.faktur,
                success: function(r){
                    if(Object.keys(r).length > 0){
                        _cb(r);
                    }
                }  
            }) ;
            mdl_pbsup.faktur = '';
        },
        name: 'mdl_pbsup'
    });
}

var mdl_pjpel = {}
mdl_pjpel.faktur = 0;
mdl_pjpel.init = function(bsearch){
    if(w2ui['nm_grpjpel'] !== undefined){
        w2ui['nm_grpjpel'].destroy(); //hancurkan grid biar bisa dilihat lagi
    }
    
    $("#_grpjpel").w2grid({
        name : 'nm_grpjpel',
        header: 'Silahkan Pilih Faktur Penjualan',
        limit : 20 ,
        url : burls + 'v1/cons/mdl_gr_pjpel',
        postData: {'bsearch': bsearch},
        show: {
            header : true,
            footer : true,
            toolbar : true,
            toolbarColumns : false
        },
        multiSearch	: false,
        columns: [
            { field: 'pilih', caption: ' ', size: '80px', sortable: false },
            { field: 'faktur', caption: 'Faktur', size: '150px', sortable: false},
            { field: 'tgl', caption: 'Tanggal', size: '100px', sortable: false },
            { field: 'total', caption: 'Total', size: '120px', sortable: false,render:'float:2' },                    
            { field: 'piutang', caption: 'Piutang', size: '120px', sortable: false,render:'float:2' },                    
            { field: 'pelanggan', caption: 'Pelanggan', size: '100%', sortable: false },                    
        ],
        onKeydown: function(event) {
            if(event.originalEvent.keyCode == 13){
                eval(w2ui['nm_grpjpel'].get(mdl_pjpel.recid).pilih_f);
            }
        }, 
        onClick: function(event){
            mdl_pjpel.recid = event.recid;
        },
        onDblClick: function(event){
            mdl_pjpel.recid = event.recid;
            eval(w2ui['nm_grpjpel'].get(mdl_pjpel.recid).pilih_f);
        },
        onRender: function(event){
            setTimeout(function(){
                $('#grid_nm_grpjpel_search_all').focus();
                $('#grid_nm_grpjpel_search_all').on('keydown', function(e) {
                    if(e.keyCode === 40) { 
                        console.log('ioio')
                        $('#grid_nm_grpjpel_search_all').blur();
                        $('#grid_nm_grpjpel_records tbody tr:eq(2)').click();
                        $('#grid_nm_grpjpel_focus').focus();
                    } 
                });
            }, 100);
        }
    });
}

mdl_pjpel.pilih = function(faktur){
    mdl_pjpel.faktur = faktur;
    mo['mdl_pjpel'].modal('hide');
}

mdl_pjpel.open = function(_cb, bsearch){
    if(bsearch == undefined) bsearch = {};

    this.html = '<div id="_grpjpel" style="height:500px; margin-left: -15px; margin-right: -15px;"></div>';
    bj.modal({
        title: '&nbsp;',
        html: mdl_pjpel.html,
        size: 400,
        fshow: function(){
            mdl_pjpel.init(bsearch)
        },
        fhide:function(){
            if(mdl_pjpel.faktur !== '0'){
                br.get({
                    path : 'v1/cons/mdl_gr_pjpel/' + mdl_pjpel.faktur,
                    data: {
                        "bsearch": bsearch
                    },
                    success: function(r){
                        if(Object.keys(r).length > 0){
                            _cb(r);
                        }
                    }  
                }) ;
                mdl_pjpel.faktur = '0';
            }
        },
        name: 'mdl_pjpel'
    });
}

/* Produksi */
var mdl_bm = {}
mdl_bm.faktur = 0;
mdl_bm.init = function(){
    if(w2ui['nm_grbm'] !== undefined){
        w2ui['nm_grbm'].destroy(); //hancurkan grid biar bisa dilihat lagi
    }
    
    $("#_grbm").w2grid({
        name : 'nm_grbm',
        header: 'Silahkan Pilih Faktur Bill of Material',
        limit : 20 ,
        url : burls + 'v1/cons/gr_bm',
        postData: {} ,
        show: {
            header : true,
            footer : true,
            toolbar : true,
            toolbarColumns : false
        },
        multiSearch	: false,
        columns: [
            { field: 'pilih', caption: ' ', size: '80px', sortable: false },
            { field: 'faktur', caption: 'Faktur', size: '130px', sortable: false},
            { field: 'tgl', caption: 'Tanggal', size: '100px', sortable: false },
            { field: 'pelanggan', caption: 'Pelanggan', size: '200px', sortable: false },
            { field: 'gudang', caption: 'Gudang', size: '120px', sortable: false }                    
        ]
    });
}

mdl_bm.pilih = function(faktur){
    mdl_bm.faktur = faktur;
    mo['mdl_bm'].modal('hide');
}

mdl_bm.open = function(_cb){
    this.html = '<div id="_grbm" style="height:500px; margin-left: -15px; margin-right: -15px;"></div>';
    bj.modal({
        title: 'Data Bill Of Material (BOM)',
        html: mdl_bm.html,
        size: 400,
        fshow: mdl_bm.init,
        fhide:function(){
            br.get({
                path : 'v1/cons/bm/' + mdl_bm.faktur,
                success: function(r){
                    if(Object.keys(r).length > 0){
                        _cb(r);
                    }
                }  
            }) ;
        },
        name: 'mdl_bm'
    });
}

var mdl_brg_prd = {}
mdl_brg_prd.var = {'kode': '', 'kd_gudang': ''};
mdl_brg_prd.init = function(bsearch){
    if(w2ui['nm_grbrgprd'] !== undefined){
        w2ui['nm_grbrgprd'].destroy(); //hancurkan grid biar bisa dilihat lagi
    }
    
    $("#_grbrgprd").w2grid({
        name : 'nm_grbrgprd',
        header: 'Silahkan Pilih Barang',
        limit : 20 ,
        url : burls + 'v1/cons/mdl_gr_brg_prd',
        postData: {'bsearch': bsearch} ,
        show:{
            header : true,
            footer : true,
            toolbar : true,
            toolbarColumns : false
        },
        multiSearch	: false,
        columns: [
            { field: 'pilih', caption: ' ', size: '80px', sortable: false },
            { field: 'kode', caption: 'Kode', size: '100px', sortable: false},
            { field: 'nama', caption: 'Nama Barang', size: '350px', sortable: false },
            { field: 'kd_satuan', caption: 'Satuan', size: '80px', sortable: false }
        ],
        onKeydown: function(event) {
            if(event.originalEvent.keyCode == 13){
                eval(w2ui['nm_grbrgprd'].get(mdl_brg_prd.recid).pilih_f);
            }
        }, 
        onClick: function(event){
            mdl_brg_prd.recid = event.recid;
        },
        onDblClick: function(event){
            mdl_brg_prd.recid = event.recid;
            eval(w2ui['nm_grbrgprd'].get(mdl_brg_prd.recid).pilih_f);
        },
        onRender: function(event){
            setTimeout(function(){
                $('#grid_nm_grbrgprd_search_all').focus();
                $('#grid_nm_grbrgprd_search_all').on('keydown', function(e) {
                    if(e.keyCode === 40) { 
                        $('#grid_nm_grbrgprd_search_all').blur();
                        $('#grid_nm_grbrgprd_records tbody tr:eq(2)').click();
                        $('#grid_nm_grbrgprd_focus').focus();
                    } 
                });
            }, 100);
        }
    });

    
}

mdl_brg_prd.pilih = function(kd, kd_gudang){
    if(kd_gudang == undefined) kd_gudang = '';

    mdl_brg_prd.var = {'kode': kd, 'kd_gudang': kd_gudang};
    mo['mdl_brg_prd'].modal('hide');
}

mdl_brg_prd.open = function(_cb, bsearch){
    mdl_brg_prd.var = {'kode': '', 'kd_gudang': ''};
    if(bsearch == undefined) bsearch = {};

    this.html = '<div id="_grbrgprd" style="height:500px; margin-left: -15px; margin-right: -15px;"></div>';
    bj.modal({
        title: '&nbsp;',
        html: mdl_brg_prd.html,
        size: 400,
        fshow: function(){
            mdl_brg_prd.init(bsearch)
        },
        fhide:function(){
            if(mdl_brg_prd.var.kode !== ''){
                br.get({
                    path : 'v1/cons/mdl_gr_brg_prd/' + mdl_brg_prd.var.kode + '/' + mdl_brg_prd.var.kd_gudang,
                    success: function(r){
                        if(Object.keys(r).length > 0){
                            _cb(r);
                        }
                    }  
                }) ;
            }
        },
        name: 'mdl_brg_prd'
    });
}

var mdl_btklop_prd = {}
mdl_btklop_prd.var = {'id_btklop': ''};
mdl_btklop_prd.init = function(bsearch){
    if(w2ui['nm_grbtklopprd'] !== undefined){
        w2ui['nm_grbtklopprd'].destroy(); //hancurkan grid biar bisa dilihat lagi
    }
    
    $("#_grbtklopprd").w2grid({
        name : 'nm_grbtklopprd',
        header: 'Silahkan Pilih BTKL - BOP',
        limit : 20 ,
        url : burls + 'v1/cons/mdl_gr_btklop_prd',
        postData: {'bsearch': bsearch} ,
        show:{
            header : true,
            footer : true,
            toolbar : true,
            toolbarColumns : false
        },
        multiSearch	: false,
        columns: [
            { field: 'pilih', caption: ' ', size: '80px', sortable: false },
            { field: 'keterangan', caption: 'Keterangan', size: '300px', sortable: false},
            { field: 'hp', caption: 'HP', size: '100px', sortable: false }
        ],
        onKeydown: function(event) {
            if(event.originalEvent.keyCode == 13){
                eval(w2ui['nm_grbtklopprd'].get(mdl_btklop_prd.recid).pilih_f);
            }
        }, 
        onClick: function(event){
            mdl_btklop_prd.recid = event.recid;
        },
        onDblClick: function(event){
            mdl_btklop_prd.recid = event.recid;
            eval(w2ui['nm_grbtklopprd'].get(mdl_btklop_prd.recid).pilih_f);
        },
        onRender: function(event){
            setTimeout(function(){
                $('#grid_nm_grbtklopprd_search_all').focus();
                $('#grid_nm_grbtklopprd_search_all').on('keydown', function(e) {
                    if(e.keyCode === 40) { 
                        $('#grid_nm_grbtklopprd_search_all').blur();
                        $('#grid_nm_grbtklopprd_records tbody tr:eq(2)').click();
                        $('#grid_nm_grbtklopprd_focus').focus();
                    } 
                });
            }, 100);
        }
    });

    
}

mdl_btklop_prd.pilih = function(idbtklop){
    mdl_btklop_prd.var = {'idbtklop': idbtklop};
    mo['mdl_btklop_prd'].modal('hide');
}

mdl_btklop_prd.open = function(_cb, bsearch){
    mdl_btklop_prd.var = {'idbtklop': ''};
    if(bsearch == undefined) bsearch = {};

    this.html = '<div id="_grbtklopprd" style="height:500px; margin-left: -15px; margin-right: -15px;"></div>';
    bj.modal({
        title: '&nbsp;',
        html: mdl_btklop_prd.html,
        size: 400,
        fshow: function(){
            mdl_btklop_prd.init(bsearch)
        },
        fhide:function(){
            if(mdl_btklop_prd.var.idbtklop !== ''){
                br.get({
                    path : 'v1/cons/mdl_gr_btklop_prd/' + mdl_btklop_prd.var.idbtklop,
                    success: function(r){
                        if(Object.keys(r).length > 0){
                            _cb(r);
                        }
                    }  
                }) ;
            }
        },
        name: 'mdl_btklop_prd'
    });
}
/* End Produksi */

var mdl_dtl_pdf = {}
mdl_dtl_pdf.initpdf    = function(){mdl_dtl_pdf.pdf = {content:[]}}
mdl_dtl_pdf.open = function(faktur){
    
    mdt_dtl_tipe = faktur.substr(0, 2);
    // console.log(mdt_dtl_tipe);
    mdl_dtl_pdf.path = ""

    if(mdt_dtl_tipe == "PJ"){
        mdl_dtl_pdf.path = "v1/gd_r/r_jual/cetak_detil";
    }else if(mdt_dtl_tipe == "RJ"){
        mdl_dtl_pdf.path = "v1/gd_r/r_jual_rt/cetak_detil";
    }else if(mdt_dtl_tipe == "PB"){
        mdl_dtl_pdf.path = "v1/gd_r/r_beli/cetak_detil";
    }else if(mdt_dtl_tipe == "RB"){
        mdl_dtl_pdf.path = "v1/gd_r/r_beli_rt/cetak_detil";
    }

    if(mdl_dtl_pdf.path !== ""){
        br.get({
            path: mdl_dtl_pdf.path,
            data: {
                    "faktur": faktur
                },
            success: function(d){
                if(!$.isEmptyObject(d)){
                    mdl_dtl_pdf.initpdf();
                    $.merge(mdl_dtl_pdf.pdf.content, d.content);
                    bj.pdf_open("Detail Transaksi", mdl_dtl_pdf.pdf);
                }else{
                    swal.fire("Data Tidak Ditemukan");
                }
            }
        });
    }
    
}