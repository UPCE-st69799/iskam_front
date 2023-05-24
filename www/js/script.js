
$(document).ready(function () {


    $('.alergen').click(function () {
        let text = $(this).text();
        let input = $('input[name="itemIngredients"]');
        let inputHidden = $('input[name="itemIngredients_Id"]');
        let data = $(this).attr("data-id");

        if ($(this).hasClass('disabled')) {

            let arr = input.val().split(',');

            arr = $.grep(arr, function (value) {
                return value !== text;
            });


            let dataHidden = inputHidden.val().split(';');

            dataHidden = $.grep(dataHidden, function (value) {
                return value !== data;
            });

            input.val(arr.join(','))
            inputHidden.val(dataHidden.join(';'))

            $(this).removeClass('disabled');
        } else {

            input.val(input.val() + text + ",");

            inputHidden.val(inputHidden.val() + data + ";");
            $(this).addClass("disabled");
        }
    });

    $('.filter').click(function () {
        let inputHidden = $('input[name="itemIngredientsFilter_Id"]');
        let data = $(this).attr("data-id");

        if ($(this).hasClass('disabled')) {
            let dataHidden = inputHidden.val().split(';');
            dataHidden = $.grep(dataHidden, function (value) {
                return value !== data;
            });
            inputHidden.val(dataHidden.join(';'))

            $(this).removeClass('disabled');
        } else {
            inputHidden.val(inputHidden.val() + data + ";");
            $(this).addClass("disabled");
        }


    });

});


$(document).on('click', '.edit-pencil', function() {

    var dataKey = $(this).data('key');

    $.ajax({
        url: '/?do=edit',
        method: 'POST',
        data: {
            id: dataKey
        },
        success: function(response) {
            $('.alergen').removeClass('disabled');
            $('input[name="itemIngredients"]').val("");
            $('input[name="itemIngredients_Id"]').val("");

            $('#item-name-edit').val(response.name);
            $('#item-description-edit').val(response.description);
            $('#item-price-edit').val(response.price);
            $('#item-category-edit').val(response.category.id);
            $('#hidden-edit').val(response.id);
            // $('#hidden-edit').val(response.id);


            $.each(response.ingredients, function(index, ingredient) {
                var selectedSpan = $('.alergenEdit span[data-id="' + ingredient.id + '"]');
                selectedSpan.click();

            });

        },
        error: function(xhr, status, error) {
            console.error(error);
        }
    });


});
$(document).on('click', '.save', function() {
    $('.alergen').removeClass('disabled');
    $('.ingredients').val("");
    $('.item-ingredients').val("");
    $('#item-ingredients').val("");
});
