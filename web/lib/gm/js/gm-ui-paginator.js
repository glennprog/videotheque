function PaginatorManager(paginator_entity, search_engine_data, callback, search_container_entity) {
    $("#paginator_" + paginator_entity + " .paginator-nav").click(function (e) {
        e.preventDefault();
        call_paginator_url = $(this)[0].getAttribute('href');
        var paginator_count_select = $("#paginator_container_count_" + paginator_entity).val();
        call_paginator_url = replaceParamsPageCount(call_paginator_url, null, paginator_count_select);

        if ( $("#search_container_data_input_" + search_container_entity).val() == "") {
            search_engine_data.searchBy = null;
        }
        var data = {'searchByMode': search_engine_data.searchByMode, 'searchBy': JSON.stringify(search_engine_data.searchBy), 'orderBy': JSON.stringify(search_engine_data.orderBy)};
        callback(call_paginator_url, data);
    });

    $("#paginator_container_page_" + paginator_entity).change(function () {
        var paginator_page_input = $(this).val();
        if (paginator_page_input == "") {
            paginator_page_input = 1;
            $("#paginator_container_page_" + paginator_entity).val(1);
        }
        if (Number.isInteger(parseInt(paginator_page_input)) == false) {
            paginator_page_input = 1;
            $("#paginator_container_page_" + paginator_entity).val(1);
        }
        var paginator_count_select = $("#paginator_container_count_" + paginator_entity).val();
        var paginator_prev_fast = $("#paginator_prev_fast_" + paginator_entity)[0].getAttribute('href'); // cause it's the first paginator's page
        call_paginator_url = replaceParamsPageCount(paginator_prev_fast, paginator_page_input, paginator_count_select);
        
        
        if ( $("#search_container_data_input_" + search_container_entity).val() == "") {
            search_engine_data.searchBy = null;
        }
        var data = {'searchByMode': search_engine_data.searchByMode, 'searchBy': JSON.stringify(search_engine_data.searchBy), 'orderBy': JSON.stringify(search_engine_data.orderBy)};
        callback(call_paginator_url, data);
    });

    $("#paginator_container_count_" + paginator_entity).change(function () {
        var paginator_count_select = $(this).val();
        var paginator_prev_fast = $("#paginator_prev_fast_" + paginator_entity)[0].getAttribute('href');
        call_paginator_url = replaceParamsPageCount(paginator_prev_fast, null, paginator_count_select);
        updatePaginatorPageCount(null, paginator_count_select, paginator_entity);
        $("#paginator_container_page_" + paginator_entity).val(1);

        if ( $("#search_container_data_input_" + search_container_entity).val() == "") {
            search_engine_data.searchBy = null;
        }
        var data = {'searchByMode': search_engine_data.searchByMode, 'searchBy': JSON.stringify(search_engine_data.searchBy), 'orderBy': JSON.stringify(search_engine_data.orderBy)};
        callback(call_paginator_url, data);
    });
}

function update_paginator_route(paginator) {
    $("#paginator_prev_"+paginator["entity"]).prop("href", paginator["paginator_prev"]);
    if(paginator["paginator_prev"] == null){
        $("#paginator_prev_"+paginator["entity"]).addClass('paginator-nav-not-active');
        $("#paginator_prev_fast_"+paginator["entity"]).addClass('paginator-nav-not-active');
    }
    else{
        $("#paginator_prev_"+paginator["entity"]).removeClass('paginator-nav-not-active');
        $("#paginator_prev_fast_"+paginator["entity"]).removeClass('paginator-nav-not-active');
    }

    $("#paginator_next_"+paginator["entity"]).prop("href", paginator["paginator_next"]);
    if(paginator["paginator_next"] == null){
        $("#paginator_next_"+paginator["entity"]).addClass('paginator-nav-not-active');
        $("#paginator_next_fast_"+paginator["entity"]).addClass('paginator-nav-not-active');
    }
    else{
        $("#paginator_next_"+paginator["entity"]).removeClass('paginator-nav-not-active');
        $("#paginator_next_fast_"+paginator["entity"]).removeClass('paginator-nav-not-active');
    }
    
    if(paginator['current_page'] <= paginator['total_page']){
        $("#paginator_container_page_total_text_"+paginator["entity"]).text('de ' + paginator['total_page']);
    }
    else{
        $("#paginator_container_page_total_text_"+paginator["entity"]).text('');
    }
    $("#paginator_container_total_entities_text_"+paginator["entity"]).text('Total global : ' + paginator['total_entities']);

    if(paginator['next_record_to_read'] != null){
        $("#paginator_container_next_record_to_read_text_"+paginator["entity"]).text('Reste à lire : ' + paginator['next_record_to_read']);
    }else{
        $("#paginator_container_next_record_to_read_text_"+paginator["entity"]).text('Reste à lire : zéro');
    }
    $("#paginator_container_page_"+paginator["entity"]).val(paginator['current_page']);
}

function replaceParamsPageCount(url, page, count){
    var regex_page = /page\/\d{1,}/i;
    var regex_count = /count\/\d{1,}/i;
    update_url = (page != null) ? url.replace(regex_page, 'page/' + page) : url;
    update_url = (count != null) ? update_url.replace(regex_count, 'count/' + count) : update_url;
    return update_url;
}

function replaceParamsPageCount_v2(url, page, count){
    // e.g : //"paginator_prev_fast" => "/app_dev.php/videotheque/categorie/?page=1&count=5"
    var regex_page = /page=\d{1,}/i;
    var regex_count = /count=\d{1,}/i;
    update_url = (page != null) ? url.replace(regex_page, 'page=' + page) : url;
    update_url = (count != null) ? update_url.replace(regex_count, 'count=' + count) : update_url;
    return update_url;
}

function updatePaginatorPageCount(paginator_page_input, paginator_count_select, paginator_entity)
{
    var paginator_prev_fast = $("#paginator_prev_fast_" + paginator_entity)[0].getAttribute('href');
    paginator_prev_fast = replaceParamsPageCount(paginator_prev_fast, paginator_page_input, paginator_count_select);
    $("#paginator_prev_fast_" + paginator_entity).attr("href", paginator_prev_fast);

    var paginator_prev = $("#paginator_prev_" + paginator_entity)[0].getAttribute('href'); 
    paginator_prev = replaceParamsPageCount(paginator_prev, paginator_page_input, paginator_count_select);
    if(paginator_prev == ""){
        paginator_prev = null;
    }
    else{
        $("#paginator_prev_" + paginator_entity).attr("href", paginator_prev);
    }

    var paginator_next = $("#paginator_next_" + paginator_entity)[0].getAttribute('href');
    paginator_next = replaceParamsPageCount(paginator_next, paginator_page_input, paginator_count_select);
    if(paginator_next == ""){
        paginator_next = null;
    }
    else{
        $("#paginator_next_" + paginator_entity).attr("href", paginator_next);
    }

    var paginator_next_fast_ = $("#paginator_next_fast_" + paginator_entity)[0].getAttribute('href');
    paginator_next_fast = replaceParamsPageCount(paginator_next_fast_, paginator_page_input, paginator_count_select);
    $("#paginator_next_fast_" + paginator_entity).attr("href", paginator_next_fast);
}