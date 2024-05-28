function activeSelect2(){
    $('#myModal5 select.select2').select2({
        theme: 'bootstrap4',
        allowClear: false,
        width: $(this).data("width")
            ? $(this).data("width")
            : $(this).hasClass("w-100")
                ? "100%"
                : "style",
    });
}