$(document).ready(function () {
    var data_categorie = $(".categorie-data").attr('data-films');
    if(data_categorie == 0){
        return;
    }
    
    // Init the Unique identify of Object to Manage (for example for Search Engine, Sort, Refresh Table, ...)
    var paginator_entity = 'Film';
    var search_container_entity = 'Film';
    var sort_container_entity = 'Film';
    // Init Mandatory viriables for UI Manager (Search Engine, Paginator, Sort).
    var searchBy_categorie = {};
    var searchByMode = 'data_percentage';
    var orderBy = {'titre' : 'ASC'};
    colSearch = 'titre';
    searchByMode = document.querySelector('input[name="search_engine_options_like_' + search_container_entity + '"]:checked').value;
    orderBy[colSearch] = document.querySelector('input[name="search_engine_options_orderby_' + search_container_entity + '"]:checked').value;
    var search_engine_data = {'searchByMode': searchByMode, 'searchBy': searchBy_categorie, 'orderBy': orderBy};

    // Set Search Engine Manager.
    searchEngineManager(search_container_entity, search_engine_data, ajaxGetFilmsSearchEngine, paginator_entity, colSearch);
    // Set Panaginator Manager.
    PaginatorManager(paginator_entity, search_engine_data, ajaxGetFilms, search_container_entity);
    // Set Sort Manager.
    sortManager(search_engine_data, ajaxGetFilms, search_container_entity, sort_container_entity, paginator_entity);
    // Pre-Ajax call used as callback function by the Managers (Search Engine, Paginator, Sort).
    function ajaxGetFilmsSearchEngine(data_search_engine) {
        //console.log(data_search_engine);
        var data = {'searchByMode': data_search_engine.searchByMode, 'searchBy': JSON.stringify(data_search_engine.searchBy), 'orderBy': JSON.stringify(data_search_engine.orderBy)};
        updateSearEngine(data_search_engine);
        ajaxGetFilms(getUrlAjax(), data);
    }

    // Ajax call.
    function ajaxGetFilms(url, data) {
        $.ajax({
            url: url,
            method: "post",
            data: data
        }).done(function (response) {
            refreshFilmTable(response.data.films);
            update_paginator_route(response.data.films.paginator);
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

    // Refresh Html Table
    function refreshFilmTable(films) 
    {
        film_size = Object.size(films.result);
        $("#films_tbody  tr").remove();
        var film = null;
        var data_href = "/app_dev.php/videotheque/film/region_id/show";
        for (let index = 0; index < film_size; index++) {
            var film = films.result[index];
            $(
                "<tr class='gm-clickable-row' data-href='" + data_href.replace("region_id", film.id) + "'>" +
                '<td>' + film.titre + '</td>' +
                '<td>' + film.description + '</td>' +
                '<td>' + film.date_sortie.date.substring(0, 10) + '</td>' +
                "</tr>"
            ).appendTo("#films_tbody");
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

});