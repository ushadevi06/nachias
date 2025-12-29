$(document).ready(function () {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    /*  Fetch Cities */
    $('#state_id').on('change', function () {
        var state_id = $(this).val();
        $('#city_id').html('<option value="">Select City</option>');
        if (state_id) {
            $.ajax({
                url: APP_URL + '/get-cities',
                type: 'GET',
                dataType: 'json',
                data: {
                    state_id: state_id,
                    _token: csrfToken
                },
                success: function (data) {
                    $('#city_id').empty();
                    $('#city_id').append('<option value="">-- Select City --</option>');
                    $.each(data, function (id, name) {
                        $('#city_id').append('<option value="' + id + '">' + name + '</option>');
                    });
                }
            });
        }
    });

    /* Fetch Places */
    $('#city_id').on('change', function () {
        var city_id = $(this).val();
        $('#place_id').html('<option value="">Select Place</option>');
        if (city_id) {
            $.ajax({
                url: APP_URL + '/get-places',
                type: 'GET',
                dataType: 'json',
                data: {
                    city_id: city_id,
                    _token: csrfToken
                },
                success: function (data) {
                    $('#place_id').empty();
                    $('#place_id').append('<option value="">-- Select Place --</option>');
                    $.each(data, function (id, name) {
                        $('#place_id').append('<option value="' + id + '">' + name + '</option>');
                    });
                    $('#place_id').trigger('change');
                }
            });
        }
    });

    /* Fetch Multiple Cities for Zones */
    $('#zone_state_id').on('change', function () {
        var state_id = $(this).val();
        $('#city_ids').html('<option value="">Select City</option>');

        if (state_id) {
            $.ajax({
                url: APP_URL + '/get-cities',
                type: 'GET',
                dataType: 'json',
                data: {
                    state_id: state_id,
                    _token: csrfToken
                },
                success: function (data) {
                    $('#city_ids').empty();
                    $('#city_ids').append('<option value="">-- Select City --</option>');
                    $.each(data, function (id, name) {
                        $('#city_ids').append('<option value="' + id + '">' + name + '</option>');
                    });
                    $('#city_ids').trigger('change');
                }
            });
        }
    });

    /* Store Category -> Raw Materials */
    $(document).on('change', '.store_category', function () {
        let categoryId = $(this).val();
        let materialSelect = $(this).closest('tr').find('.material');
        if (categoryId) {
            $.ajax({
                url: APP_URL + '/raw-materials-by-category/' + categoryId,
                type: 'GET',
                dataType: 'json',
                data: {
                    _token: csrfToken
                },
                success: function (data) {
                    materialSelect.html('<option value="">Select Material</option>');
                    $.each(data, function (id, name) {
                        materialSelect.append('<option value="' + id + '">' + name + '</option>');
                    });
                }
            });
        } else {
            materialSelect.html('<option value="">Select Material</option>');
        }
    });
});
