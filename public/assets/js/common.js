$(document).ready(function () {
    loadAgentsTable(); 
    /*  Fetch Cities */
    $('#state_id').change(function () {
        var stateId = $(this).val();
        $('#city_id').html('<option value="">Select City</option>');
        if (stateId) {
            $.get('/get-cities/' + stateId, function (data) {
                $.each(data, function (key, city) {
                    $('#city_id').append('<option value="' + city.id + '">' + city .city_name + '</option>');
                });
            });
        }
    });

    /* Fetch Places */
    $('#city_id').change(function () {
        var cityId = $(this).val();
        $('#place_id').html('<option value="">Select Place</option>');
        if (cityId) {
            $.get('/get-places/' + cityId, function (data) {
                $.each(data, function (key, place) {
                    $('#place_id').append('<option value="' + place.id + '">' + place
                        .place_name + '</option>');
                });
                $('#place_id').trigger('change');
            });
        }
    });
    /* Fetch Multiple Cities */
    $('#zone_state_id').change(function () {
        var stateId = $(this).val();
        $('#city_ids').html('<option value="">Select City</option>');

        if (stateId) {
            $.get('/get-cities/' + stateId, function (data) {
                $.each(data, function (key, city) {
                    $('#city_ids').append('<option value="' + city.id + '">' + city.city_name + '</option>');
                });
                $('#city_ids').trigger('change');
            });
        }
    });
    $(document).on('change', '.store_category', function () {
        let categoryId = $(this).val();
        let materialSelect = $(this).closest('tr').find('.material');
        if (categoryId) {
            $.ajax({
                url: '/raw-materials-by-category/' + categoryId,
                type: 'GET',
                success: function (data) {
                    console.log(data);
                    materialSelect.html('<option value="">Select Material</option>');
                    $.each(data, function (key, material) {
                        materialSelect.append('<option value="' + material.id + '">' +
                            material.name + ' (' + material.code + ')</option>');
                    });
                }
            });
        } else {
            materialSelect.html('<option value="">Select Material</option>');
        }
    });
});
function loadAgentsTable() {

    if ($.fn.DataTable.isDataTable('#agentsTable')) {
        $('#agentsTable').DataTable().clear().destroy();
    }
    $('#agentsTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "{{ url('purchase_commission_agent') }}",
            type: "GET",
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'code' },
            { data: 'email' },
            { data: 'mobile_no' },
            { data: 'state' },
            { data: 'city' },
            { data: 'service_point' },
            { data: 'status' },
            { data: 'actions' }
        ],
    });
}

