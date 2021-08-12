function selectAll()
{
    // ================Select All Method======================
    if($('#select_all:checked').length == 1){
        $('.select_data').prop('checked',true);
    }else{
        $('.select_data').prop('checked',false);
    }
    $('.select_data').is(':checked') ? $('.select_data').closest('tr').addClass('bg-warning') : $('.select_data').closest('tr').removeClass('bg-warning');
}

// ===============Select single item=====================
function selectSingleItem(id){
    var totalRow = $('.select_data').length; //Count total row
    var totalCheck = $('.select_data:checked').length; //Count total checked row
    $('#checkBox'+id+'').is(':checked') ? $('#checkBox'+id+'').closest('tr').addClass('bg-warning') : $('#checkBox'+id+'').closest('tr').removeClass('bg-warning');
    (totalRow == totalCheck) ? $('#select_all').prop('checked',true) : $('#select_all').prop('checked',false);
}
