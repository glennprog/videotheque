$(document).ready(function () {
    // Init the Unique identify of Object to Manage (for example for Search Engine, Sort, Refresh Table, ...)
    var paginator_entity = 'Categorie';
    var search_container_entity = 'Categorie';
    var sort_container_entity = 'Categorie';
    // Init Mandatory viriables for UI Manager (Search Engine, Paginator, Sort).
    var searchBy_categorie = {};
    var searchByMode = 'data_percentage';
    var orderBy = {'nom' : 'ASC'};
    colSearch = 'nom';
    searchByMode = document.querySelector('input[name="search_engine_options_like_' + search_container_entity + '"]:checked').value;
    orderBy[colSearch] = document.querySelector('input[name="search_engine_options_orderby_' + search_container_entity + '"]:checked').value;
    var search_engine_data = {'searchByMode': searchByMode, 'searchBy': searchBy_categorie, 'orderBy': orderBy};

    // Set Search Engine Manager.
    searchEngineManager(search_container_entity, search_engine_data, ajaxGetCategoriesSearchEngine, paginator_entity, colSearch);
    // Set Panaginator Manager.
    PaginatorManager(paginator_entity, search_engine_data, ajaxGetCategories, search_container_entity);
    // Set Sort Manager.
    sortManager(search_engine_data, ajaxGetCategories, search_container_entity, sort_container_entity, paginator_entity);
    // Pre-Ajax call used as callback function by the Managers (Search Engine, Paginator, Sort).
    function ajaxGetCategoriesSearchEngine(data_search_engine) {
        //console.log(data_search_engine);
        var data = {'searchByMode': data_search_engine.searchByMode, 'searchBy': JSON.stringify(data_search_engine.searchBy), 'orderBy': JSON.stringify(data_search_engine.orderBy)};
        updateSearEngine(data_search_engine);
        ajaxGetCategories(getUrlAjax(), data);
    }
    // Ajax call.
    function ajaxGetCategories(url, data) {
        console.log(data);
        $.ajax({
            url: url,
            method: "post",
            data: data
        }).done(function (response) {
            refreshCategorieTable(response.data.categories);
            update_paginator_route(response.data.categories.paginator);
        });
    }
    // Get Url ajax call attached in the paginator prev-fast link. TODO : Send from Controller a specific variable for this value.
    function getUrlAjax(){
        var paginator_count_select = $("#paginator_container_count_" + paginator_entity).val();
        var paginator_prev_fast = $("#paginator_prev_fast_" + paginator_entity)[0].getAttribute('href');
        call_paginator_url = replaceParamsPageCount(paginator_prev_fast, null, paginator_count_select);
        return call_paginator_url;
    }
    // Update the Search Engine data.
    function updateSearEngine(data_search_engine){
        searchBy_categorie = data_search_engine.searchBy;
        searchByMode = data_search_engine.searchByMode;
        orderBy = data_search_engine.orderBy;
    }
    // Refresh Html Table.
    function refreshCategorieTable(categories) {
        categorie_size = Object.size(categories.result);
        $("#categories_tbody  tr").remove();
        var categorie = null;
        var data_href = "/app_dev.php/videotheque/categorie/region_id/show";
        for (let index = 0; index < categorie_size; index++) {
            var categorie = categories.result[index];
            $("<tr class='gm-clickable-row' data-href='" + data_href.replace("region_id", categorie.id) + "'>" +
                '<td>' + categorie.nom + '</td>' +
                "</tr>").appendTo("#categories_tbody");
            $(".gm-clickable-row").click(function () {
                window.location = $(this).data("href");
            });
        }
    }
    // Window onclick Manager.
    window.onclick = function (event) {
        var modal_search_options = document.getElementById('search_engine_options_' + search_container_entity);
        // Search-Engine-UI :: When the user clicks anywhere outside of the modal, close it.
        if (event.target == modal_search_options) {
            modal_search_options.style.display = "none";
        }
    }
    // Re-init the sort icon when Search-Engine:OrderBy is triggering.
    $("#search_engine_options_" + search_container_entity + " .input-search-engine-options").click(function () {
        var th_parent = $("#table_" + search_container_entity + " .th-sortable").parent();
        var sort_child = th_parent.find(".fa-sort-up, .fa-sort-down");
        for (let index = 0; index < sort_child.length; index++) {
            var className = sort_child[index].className;
            className = className.replace("fa-sort-up", "fa-sort");
            className = className.replace("fa-sort-down", "fa-sort");
            sort_child[index].className = className;
        }
    });

    $("#search_container_data_input_" + search_container_entity).keyup(function () {
        setToOnePagePaginator(paginator_entity);
    });

    function setToOnePagePaginator(paginator_entity) {
        $("#paginator_container_page_" + paginator_entity).val(1);
    }

    /*
    $('#container_hight').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Browser market shares January, 2015 to May, 2015'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            name: 'Brands',
            colorByPoint: true,
            data: [{
                name: 'Microsoft Internet Explorer',
                y: 56.33
            }, {
                name: 'Chrome',
                y: 24.03,
                sliced: true,
                selected: true
            }, {
                name: 'Firefox',
                y: 10.38
            }, {
                name: 'Safari',
                y: 4.77
            }, {
                name: 'Opera',
                y: 0.91
            }, {
                name: 'Proprietary or Undetectable',
                y: 0.2
            }]
        }]
    });
    */
});