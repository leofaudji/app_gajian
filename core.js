/**
 * Core.js 
 * for Pages Themeplate
 * 
 * @copyright   CV. ILMION KREATIF - ILMION STUDIO
 * @link        https://ilmion.com
 * @email       hi@ilmion.com | ilmionstudio@gmail.com
 * @author      Mirza Ramadhany (amir.ramadhany@gmail.com)
 * @version     0.0.2 (Sept 13 '18)
 * @since       17 Aug 18
 */

var burl = window.location.href.replace('index.html','') ;
if(burl.indexOf("?") > -1) burl     = burl.substr(0, burl.indexOf("?")) ;
if(burl.indexOf("#") > -1) burl     = burl.substr(0, burl.indexOf("#")) ;
if(burl.indexOf("src/") > -1) burl  = burl.substr(0, burl.indexOf("src/")) ;
var burls    = burl + 'il/' ;

if (typeof jQuery === "undefined") {
    throw new Error("Bismillah, We're need jQuery");
}

/**
 * SerializeObject
 */
  
$.fn.serializeObject = function(){ 
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

/**
 * SelectJS Function Set Value
 * 
 * @param {*} val json array set value
 */
$.fn.sVal	= function(val){
    if(val == undefined || val == '') val = {} ;
    var co	  = $(this);
    if(val !== {}){
        var cop = '', ci 	= [] ;
        $.each( val, function(i, o){
            cop += '<option value="' + o.id + '">' + o.text + '</option>' ;
            ci.push(o.id) ;
        }) ;
        co.empty().append(cop).val(ci).trigger('change') ;
    }else{
        co.empty() ;
    }
}

/**
 * Select2JS Function Params
 * 
 * @param   {*} opts options  select2 js
 * @param   string data-sf      Function
 * @param   string data-sp      Is parameter get after function /function/param/param
 */
$.fn.s2     = function(opts){
    var bse = {};
    bse.co  = $(this);
    bse.opt = {};
    if(opts.url !== undefined){
        bse.opt     = {
            ajax: {
                url: function () { 
                    var dpar    = $(this).attr("data-sp") ;
                    dpar        = (typeof dpar !== typeof undefined && dpar !== false) ? "/" + dpar : "" ;
                    return burls + opts.url + "/" + $(this).attr("data-sf") + dpar ;
                },
                dataType: 'json'
            } 
        } 
    }
    $.extend(true, bse.opt, opts);
    bse.co.select2(bse.opt); 
}
 
Number.prototype.numberFormat 	= function(d, dc, uc){
	var nfn = this,
    nfc = isNaN(d = Math.abs(d)) ? 0 : d,
    nfd = dc == undefined ? "." : dc,
    nft = uc == undefined ? "," : uc,
    nfs = nfn < 0 ? "-" : "",
    nfi = String(parseInt(nfn = Math.abs(Number(nfn) || 0).toFixed(nfc))),
    nfj = (nfj = nfi.length) > 3 ? nfj % 3 : 0;
   return nfs + (nfj ? nfi.substr(0, nfj) + nft : "") +
   			nfi.substr(nfj).replace(/(\d{3})(?=\d)/g, "$1" + nft) +
   			(nfc ? nfd + Math.abs(nfn - nfi).toFixed(nfc).slice(2) : "") ;
}

Number.prototype.number_format 	= function(d, dc, uc){
    return this.numberFormat(d, dc, uc);
}

const bo = {};
const mo = {};
const bj = {
    loadPage	: function(id, p){
        $.ajaxSetup ({cache: false});
		$(id).load(p) ; 
    },
    
    getDataJson	: function(o){
		return $(o).serializeObject() ;
    },
    
    getData     : function(o){
        return $(o).serialize() ;
    },
    
    setOpt 		: function(o, n, v){
		o.find("input[name='"+ n +"'][value='" + v + "']").prop('checked', true);
    },
    
    /**
     * bj.form function for open form
     * 
     * @param   json name
     *               location   
     *               obj
     * @return  object bo.obj
     */              
    form  : function(par){
        var f = {} ;
		f.par	= {
            idcontent: "#bcontent",
			module 	: "Utama" ,
			name 	: "oweb" ,
            loc 	: "cfg/bismillah" ,
            path 	: "cfg/bismillah" ,
			data 	: "" ,
            obj		: "bismillahsuksesduniaakhirat",
            attr    : ""
		}

		$.extend(true,f.par,par) ;

		if(f.par.attr !== "") f.par.attr.replace("'",'"') ;

		if( $(f.par.idcontent).find(".bwrap-content").length > 0 ){
			f.lid 	= $(f.par.idcontent).find(".bwrap-content").attr("id") ;
			f.lobj	= $(f.par.idcontent).find(".bwrap-content").attr("data-bo") ;
			$("#"+f.lid).trigger("remove").remove() ;
			eval(" " + f.lobj+ " = null ; ") ;
		}
		$(f.par.idcontent).html("") ;

		//class
		f.par.obj 	= f.par.obj.replace(" ","") ;
		f.obj 		= "bo." + f.par.obj ; 
        f.id 		= "bo-form-" + f.par.obj ;

        f.html  = '' ; 

		f.html += '<script type="text/javascript">' ;
		f.html += 	' ' + f.obj +' = ({ ';
		f.html +=		'id  : "'+ f.id +'" , ';
		f.html +=		'obj : $("#'+ f.id +'") , ';
        f.html +=		'url : "'+ f.par.path +'" , ';
        f.html +=		'path: "'+ f.par.path +'" , ';
		f.html +=		'reload	: function(){ bj.loadPage("#'+f.id+'","src/'+f.par.loc+'.html") } ';
		f.html +=	'}) ;  ' ;
 
		f.html +=	' bj.loadPage("#'+f.id+'","src/'+f.par.loc+'.html") ; ' ;
        f.html += '</script>' ;
        $(f.par.idcontent).append('<div id="'+f.id+'" class="bwrap-content" data-bo="'+ f.obj +'" '+f.par.attr+'></div>'+f.html) ;

		console.log("OBJECT FORM -> " + f.obj) ;
		console.log( eval(f.obj) ) ;
    },

    pdf_style: {
        // default style
        th:{
            alignment: 'center',
            bold: true
        }
    },

    pdf_open: function(title, content, style, direct){
        if(style == undefined){
            style = bj.pdf_style;
        }else{
            $.extend(true, style, bj.pdf_style) ;    
        }

        if(direct == undefined){
            direct == false ;
        }

        var par = {
            info : {
                title : title,
                subject: title,
                author: 'ILMION KREATIF',
                creator: 'ILMION KREATIF',
                producer: 'ILMION KREATIF'
            }, 
            pageSize: 'A4',
            styles: style
        }

        $.extend(true, par, content) ;    

        if(!direct){
            pdfMake.createPdf(par).getDataUrl(function(outDoc){
                $("#id-modal-rpt").modal('show');
                $("#id-modal-rpt").on("shown.bs.modal", function(){
                    $(this).find("iframe").attr("src", outDoc);
                }) ;
            });
        }else{
            pdfMake.createPdf(par).print();
        }
    }, 

    excel_open: function(content, sheetName, fileName){
        /*create sheet data & add to workbook*/
        var wb = XLSX.utils.book_new();
        if($.isArray(sheetName) || $.isPlainObject(sheetName)){
	        sheetName.forEach(function (key) {
                var opt = {} ;
                if(content['merges'] != undefined && content['merges'] != null && content['merges'][key] != null){
                    opt['merges'] = content['merges'][key] ;
                }
                if(content['origin'] != undefined && content['origin'] != null && content['origin'][key] != null){
                    opt['origin'] = content['origin'][key] ;
                }
                var ws = XLSX.utils.json_to_sheet(content['data'][key], opt);
                if(content['cols'] != null && content['cols'][key] != null) ws['!cols'] = content['cols'][key] ;
                if(content['tambahan'] != null && content['tambahan'][key] != null) $.extend(ws, content['tambahan'][key]);
                XLSX.utils.book_append_sheet(wb, ws, key);    
            });
        }else{
            var opt = {} ;
            if(content['merges'] != undefined && content['merges'] != null){
                opt['merges'] = content['merges'] ;
            }
            if(content['origin'] != undefined && content['origin'] != null){
                opt['origin'] = content['origin'] ;
            }
            var cnt = content ;
            if(content.data != undefined && content.data != null){
                cnt = content.data ;
            }
            var ws = XLSX.utils.json_to_sheet(cnt, opt);
            if(content.cols != null) ws['!cols'] = content.cols ;
            if(content.tambahan != null) $.extend(ws, content.tambahan);

            XLSX.utils.book_append_sheet(wb, ws, sheetName);
        }

        /* generate an XLSX file */
        XLSX.writeFile(wb, fileName);
    },

    modal: function(opts){
        var _mopts = {
            title: '',
            html: '',
            size: 200,
            fshow: '',
            fhide: '',
            style: 'z-index:9990',
            ohtml: '',
            show: true,
            name: 'my',
            id: 'bismillahmodal_'
        }
        $.extend(true, _mopts, opts);
        if(isNaN(_mopts.size)){// modal class
            _mopts.dclass = _mopts.size + '"';
        }else{ // style
            _mopts.dclass = '" style="min-width: calc(100% - '+_mopts.size+'px) !important;"';
        }
    
        _mopts.id   += _mopts.name;
        _mopts.ohtml = '<div class="modal modal-blur fade" id="'+_mopts.id +'" role="dialog" aria-labelledby="'+_mopts.id+'" aria-hidden="true" style="'+_mopts.style+'">';
        _mopts.ohtml+=  '<div class="modal-dialog modal-lg modal-dialog-centered '+_mopts.dclass+'>';
        _mopts.ohtml+=   '<div class="modal-content">';
        _mopts.ohtml+=    '<div class="modal-header"><h5 class="modal-title">'+_mopts.title+'</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>';
        _mopts.ohtml+=    '<div class="modal-body"><div id="body-info">'+_mopts.html+'</div></div>';
        _mopts.ohtml+=   '</div>';
        _mopts.ohtml+=  '</div>';
        _mopts.ohtml+= '</div>';
    
        $('body').append(_mopts.ohtml);
    
        $('#' + _mopts.id).modal('show');
        if(typeof _mopts.fshow === 'function'){
            $('#' + _mopts.id).on('shown.bs.modal', _mopts.fshow);
        }
    
        if(typeof _mopts.fhide === 'function'){
            $('#' + _mopts.id).on('hide.bs.modal', _mopts.fhide);
        }
        $('#' + _mopts.id).on('hidden.bs.modal', function(e){
            $('body').find('#'+_mopts.id).remove();
            delete mo[_mopts.name];
        });
    
        mo[_mopts.name] = $('#' + _mopts.id);
    }
}

/**
 * Session hanya berpengaruh di setiap tap browser
 * untuk tab lainnya session berbeda dengan php
 */
const sess    = {
    set: function(n, v){
        if(v == ""){
            sessionStorage.removeItem(n) ;
        }else{
            sessionStorage.setItem(n, v) ;
        }
    }, 
    get: function(n, d){
        var re = sessionStorage.getItem(n) ; 
        if(re == null) re = d ; 
        return re ; 
    },
    clear: function(){
        sessionStorage.clear() ; 
    }
}

_rmp = function(htmlStr){
    if( htmlStr.indexOf('<p>') == 0 && htmlStr.lastIndexOf('</p>') == (htmlStr.length - 4)){ 
        htmlStr = htmlStr.substring( htmlStr.indexOf('<p>')+3, htmlStr.lastIndexOf('</p>') );
    } 

    // ganti div karena membuat error
    htmlStr = htmlStr.replace(/<div/gi, '<p');
    htmlStr = htmlStr.replace(/<\/div/gi, '</p');

    // cek tag img harus ada heightnya
    htmlStr = htmlStr.replace(/height: 0px;/gi, 'height: auto;');
    htmlStr = htmlStr.replace(/min-height: 100%;/gi, 'min-height: auto;');
    htmlStr = htmlStr.replace(/max-height: 100%;/gi, '');
    htmlStr = htmlStr.replace(/width: 0px;/gi, 'height: auto;');
    htmlStr = htmlStr.replace(/min-width: 100%;/gi, 'min-width: auto;');
    htmlStr = htmlStr.replace(/max-width: 100%;/gi, '');
    htmlStr = htmlStr.replace(/position: absolute;/gi, '');
    htmlStr = htmlStr.replace(/top: 0px;/gi, '');
    htmlStr = htmlStr.replace(/left: 0px;/gi, '');
    htmlStr = htmlStr.replace(/bottom: 0px;/gi, '');
    htmlStr = htmlStr.replace(/right: 0px;/gi, '');

    //ganti dengan copas ulang wkkw
    htmlStr = htmlStr.replace(/b-a b-grey padding-5 b-rad-sm m-t-5/gi, '');
    htmlStr = htmlStr.replace(/b-a b-success bg-success-lighter padding-5 b-rad-sm m-t-5/gi, '');

    // a new tab
    htmlStr = htmlStr.replace(/<a /gi, '<a target="_blank" ');

    return htmlStr;
}

function getBlob(url, callback) {
    var xhr = new XMLHttpRequest();
    xhr.onload = function() {
      var reader = new FileReader();
      reader.onloadend = function() {
        callback(reader.result);
      }
      reader.readAsDataURL(xhr.response);
    };
    xhr.open('GET', url);
    xhr.responseType = 'blob';
    xhr.send();
}

function string_2n(nom){
    nom = nom.toString().split(',');
    nom = nom.join('');
    nom = parseFloat(nom);
    return nom;
}

function number_2s(nom) {
    return nom.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

/**
 * RestApi Ajax
 * https://gist.github.com/kamranzafar/4977499
 */
class restApi {
    constructor(goptions) {
        this.goptions = goptions;
        this.post = function (opts) {
            doAjax('POST', this.goptions, opts);
        };
        this.put = function (opts) {
            doAjax('PUT', this.goptions, opts);
        };
        this.get = function (opts) {
            doAjax('GET', this.goptions, opts);
        };
        this.patch = function (opts) {
            doAjax('PATCH', this.goptions, opts);
        }; 
        this.delete = function (opts) {
            doAjax('DELETE', this.goptions, opts);
        };
        var constants = {
            dataType: 'json',
            contentType: 'application/json',
            processData: true, 
            cache: true,
            timeout: 120000,
            synchronous: false,
            errorRedirect: false
        };
        var doAjax = function (method, gopts, opts) {
            var dt = opts.dataType ? opts.dataType : (gopts.dataType ? gopts.dataType : constants.dataType);
            // redirect on error if HTTP status code matches
            var errorRedirect = opts.errorRedirect ? opts.errorRedirect : (gopts.errorRedirect ? gopts.errorRedirect : constants.errorRedirect);
            var errorRedirectCodes = opts.errorRedirectCodes ? opts.errorRedirectCodes : gopts.errorRedirectCodes;
            var errorRedirectUrl = opts.errorRedirectUrl ? opts.errorRedirectUrl : gopts.errorRedirectUrl;
            var errorCallback = opts.error ? opts.error : (gopts.error ? gopts.error : function (req, status, ex) { });
            var errorFunction = errorCallback;
            if (errorRedirect) {
                errorFunction = function (req, status, ex) {
                    if (jQuery.inArray(req.status, errorRedirectCodes)) {
                        window.location.href = errorRedirectUrl;
                    }
                    else {
                        errorCallback(req);
                    }
                }; 
            }
            $.ajax({
                type: method,
                url: gopts.url.lastIndexOf('/') == gopts.url.length - 1 ? gopts.url + opts.path : gopts.url + '/' + opts.path,
                headers: opts.headers ? opts.headers : (gopts.headers ? gopts.headers : {}),
                data: opts.data ? /^json/i.test(dt) ? (method == 'GET' ? opts.data : JSON.stringify(opts.data) ) : opts.data : '',
                dataType: dt,
                contentType: opts.contentType ? opts.contentType : (gopts.contentType ? gopts.contentType : constants.contentType),
                processData: opts.processData ? opts.processData : (gopts.processData ? gopts.processData : constants.processData),
                cache: opts.cache ? opts.cache : (gopts.cache ? gopts.cache : constants.cache),
                timeout: opts.timeout ? opts.timeout : (gopts.timeout ? gopts.timeout : constants.timeout),
                success: opts.success ? opts.success : (gopts.success ? gopts.success : function (data) { }),
                error: errorFunction,
                complete: opts.complete ? opts.complete : (gopts.complete ? gopts.complete : function () { }),
                async: opts.synchronous ? !opts.synchronous : (gopts.synchronous ? !gopts.synchronous : !constants.synchronous)
            });
        };
    }
}