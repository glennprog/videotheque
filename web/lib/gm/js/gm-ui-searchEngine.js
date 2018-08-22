function searchEngineManager(search_container_entity, search_engine_data, callback, paginator_entity, colSearch) {
    // When the user clicks the button, open the modal 
    $("#search_container_option_close_" + search_container_entity).click(function () {
        // Get the modal
        console.log($(this).attr('data-modal-id'));
        var modal = document.getElementById('search_engine_options_' + search_container_entity);
        modal.style.display = "none";
    });
    // When the user clicks on <span> (x), close the modal
    $("#search_container_option_" + search_container_entity).click(function () {
        // Get the modal
        var modal = document.getElementById('search_engine_options_' + search_container_entity);
        modal.style.display = "block";
    });
    $("#search_engine_options_" + search_container_entity + " .input-search-engine-options").click(function () {
        var x = document.querySelector('input[name="search_engine_options_like_' + search_container_entity + '"]:checked').value; //var x = document.getElementById("myRadio").value;
        search_engine_data.searchByMode = x;
        search_engine_data.orderBy = {};
        var y = document.querySelector('input[name="search_engine_options_orderby_' + search_container_entity + '"]:checked').value;
        console.log(colSearch);
        console.log(search_engine_data);
        search_engine_data.orderBy[colSearch] = y;
        if ($("#search_container_data_input_" + search_container_entity).val() == "") {
            search_engine_data.searchBy = null;
        }
        else {
            var columsSearch = $("#search_container_data_input_" + search_container_entity).attr('data-search');
            columsSearchTab = columsSearch.split(';');
            for (let index = 0; index < columsSearchTab.length; index++) {
                const element = columsSearchTab[index];
                search_engine_data.searchBy[element] = $("#search_container_data_input_" + search_container_entity).val();
            }
        }
        var data = { 'searchByMode': search_engine_data.searchByMode, 'searchBy': search_engine_data.searchBy, 'orderBy': search_engine_data.orderBy };
        callback(data);
    });
    //$("#search_container_data_" + paginator_entity).change(function () {
    $("#search_container_data_input_" + search_container_entity).keyup(function () {
        //setToOnePagePaginator(paginator_entity);
        search_data_input = search_engine_data.searchBy;
        if ($(this).val() == "") {
            search_data_input = null;
            search_engine_data.searchBy = search_data_input;
            var data = { 'searchByMode': search_engine_data.searchByMode, 'searchBy': search_engine_data.searchBy, 'orderBy': search_engine_data.orderBy };
            callback(data);
            return;
        }
        if (search_data_input == null) {
            search_data_input = {}
        }
        var columsSearch = $(this).attr('data-search');
        columsSearchTab = columsSearch.split(';');
        for (let index = 0; index < columsSearchTab.length; index++) {
            const element = columsSearchTab[index];
            search_data_input[element] = $(this).val();
        }
        search_engine_data.searchBy = search_data_input;
        var data = { 'searchByMode': search_engine_data.searchByMode, 'searchBy': search_engine_data.searchBy, 'orderBy': search_engine_data.orderBy };
        callback(data);
    });
    /*
    function setToOnePagePaginator(paginator_entity) {
        $("#paginator_container_page_" + paginator_entity).val(1);
    }
    */
}

