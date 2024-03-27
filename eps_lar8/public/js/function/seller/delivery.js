$(function () {
    $("#example1").dataTable();
    $("#example2").dataTable({
        bPaginate: true,
        bLengthChange: true,
        bFilter: true,
        bSort: true,
        bInfo: true,
        bAutoWidth: true,
    });
});

function showDescription(description) {
    Swal.fire({
        title: 'Description',
        text: description,
        icon: 'info',
        confirmButtonText: 'OK'
    });
}