const showLoadingIndicator = (element) => {
    $(`.${element}`).removeClass('d-none');
}

const hideLoadingIndicator = (element) => {
      $(`.${element}`).addClass('d-none');
}