function sortManager(search_engine_data, callback, search_container_entity, sort_container_entity, paginator_entity) {
    $("#table_" + sort_container_entity + " .th-sortable").click(function () {
        var col_name = $(this).attr('data-sort');
        var th_parent = $(this).parent();
        var current_className = "";
        current_className = $(this).find(".ui-sort").attr('class');
        var sort_child = th_parent.find(".fa-sort-up, .fa-sort-down");
        for (let index = 0; index < sort_child.length; index++) {
            var className = sort_child[index].className;
            className = className.replace("fa-sort-up", "fa-sort");
            className = className.replace("fa-sort-down", "fa-sort");
            sort_child[index].className = className;
        }
        element = $(this).find(".ui-sort");

        search_engine_data.orderBy = {};
        if (current_className.indexOf("sort-up") != -1) {
            element.removeClass();
            element.addClass("fas fa-sort-down ui-sort");
            search_engine_data.orderBy[col_name] = "ASC";
        }
        else{
           element.removeClass();
           element.addClass("fas fa-sort-up ui-sort");
           search_engine_data.orderBy[col_name] = "DESC";
        }
        if ( $("#search_container_data_input_" + search_container_entity).val() == "") {
            search_engine_data.searchBy = null;
        }
        var data = {'searchByMode': search_engine_data.searchByMode, 'searchBy': JSON.stringify(search_engine_data.searchBy), 'orderBy': JSON.stringify(search_engine_data.orderBy)};
        
        var paginator_count_select = $("#paginator_container_count_" + paginator_entity).val();
        var paginator_prev_fast = $("#paginator_prev_fast_" + paginator_entity)[0].getAttribute('href');
        call_paginator_url = replaceParamsPageCount(paginator_prev_fast, null, paginator_count_select);

        callback(call_paginator_url, data);
    });
}
